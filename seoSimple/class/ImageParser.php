<?php
require_once 'HtmlParser.php';
include_once "PageLoad.php";

/**
 * This is the result of an image dimension check
 * @author Will
 *
 */
class ImageLoadResponse{
	/**
	 * @var string The url of the image
	 */
	public $url;
	
	/**
	 * @var int 1=good, -1=failed to check, 0=sizes did not match
	 */
	public $result;
	
	/**
	 * @var string A hash which corresponds to the Node which image check was ran on
	 */
	public $hash;
	
	/**
	 * @var int Load time in seconds
	 */
	public $time;
	
	
	public $actualWidth;
	
	public $actualHeight;
	
	public $htmlWidth;
	
	public $htmlHeight;
	
	public $alt;
	
	public $title;
}

/**
 * Basic utilities to calculate information about images
 * @author Will
 *
 */
class ImageParser{
	private static $GOOD = 1;
	private static $BAD = 0;
	private static $FAIL = -1;
	
	/**
	 * Process an image Node object to get the width and height from
	 * either the width/height attributes or from the style attributes.
	 * @param Node $node
	 * @return array(width, height) width/height will be 0 if none set.
	 */
	public static function getWidthHeight(Node $node){
		$width = 0;
		$height = 0;
		
		//check attributes
		if(isset($node->attributes['width']))
			$width = $node->attributes['width'] * 1;
		
		if(isset($node->attributes['height']))
			$height = $node->attributes['height'] * 1;
		
		//overwrite size with style if exists
		if(isset($node->attributes['style'])){
			$style = $node->attributes['style'];
			if(preg_match('/width[\s+]?:[\s+]?(?P<width>\d+)[\s+]?(?P<format>[a-z]+);/i',$style,$mWidth))
				$width = $mWidth['width'] * 1;
			
			if(preg_match('/height[\s+]?:[\s+]?(?P<height>\d+)[\s+]?(?P<format>[a-z]+);/i',$style,$mHeight)){
				$height = $mHeight['height'] * 1;
			}
		}
		
		return array($width, $height);
	}
	
	/**
	 * Response used within this class
	 * @param resource $image A php image resource
	 * @param int $width
	 * @param int $height
	 * @return 1=good, -1=failed to check, 0=sizes did not match
	 */
	public static function respond($image, $width, $height){
		
		if(empty($image) || $image === false)
			return self::$FAIL;
		
		$x = imagesx($image);
		$y = imagesy($image);
		
		if($x === false || $x === false)
			return self::$FAIL;
		
		if($x*1 === $width*1 && $height*1 === $y*1)
			return self::$GOOD;
		
		return self::$BAD;
	}
	
	/**
	 * Download an image and compare its actual dimensions to 
	 * Node attribute dimensions
	 * 
	 * @param unknown $src The url or file to check against
	 * @param unknown $width Width to compare against
	 * @param unknown $height Height to compare against
	 * @return ImageLoadResponse
	 */
	public static function checkActualDimsSingle(Node $img, $width, $height){
		
		$image;
		$url = $img->attributes['src'];
		if(preg_match('@^https?://@i',$url)){
			$image = imagecreatefromstring(file_get_contents($url));
		}elseif(preg_match('/^data/',$url)){
			$image = imagecreatefromstring($url);
		}else{
			$url = 'http://'.$img->host.'/'.ltrim($img->attributes['src'],'/\\');
			$image = imagecreatefromstring(file_get_contents($url));
		}
		
		//echo "$url\t\t\t";
		$x = imagesx($image);
		$y = imagesy($image);
		
		$resp = new ImageLoadResponse();
		$resp->hash = $img->hash;
		$resp->url = $url;
		$resp->result = self::respond($image, $width, $height);
		$resp->htmlWidth = $width;
		$resp->htmlHeight = $height;
		$resp->actualWidth = $x;
		$resp->actualHeight = $y;
		$resp->alt = (isset($img->attributes['alt'])) ? $img->attributes['alt'] : null;
		$resp->title = (isset($img->attributes['title'])) ? $img->attributes['title'] : null;
		
		return $resp;
	}
	
	/**
	 * Takes a list of image nodes and downloads images checking their
	 * actual dimensions vs. image Node attributes dimensions
	 * 
	 * @see checkActualDimsSingle
	 * @param unknown $imgNodes  An array of img Nodes
	 * @return ImageLoadResponse An array of ImageLoadResponse Objects
	 */
	public static function checkActualDimsThreaded($imgNodes){
		
		$loader = new PageLoad('ImageParserThread.php');
		$result = array();
		foreach($imgNodes as $node){
			$data = self::getWidthHeight($node);

			$width = $data[0];
			$height = $data[1];
			
			$url = $node->attributes['src'];
			if(!preg_match('@^https?://@i',$url) && !preg_match('/^data/',$url))
				$url = 'http://'.$node->host.'/'.ltrim($url,'/\\');
			
			$resp = new ImageLoadResponse();
			$resp->htmlWidth = $width;
			$resp->htmlHeight = $height;
			$resp->result = self::$BAD;
			$resp->url = $url;
			$resp->hash = $node->hash;
			$resp->actualWidth = -1;
			$resp->actualHeight = -1;
			$resp->time = -1;
			$result[$node->hash] = $resp;
			$resp->alt = (isset($node->attributes['alt'])) ? $node->attributes['alt'] : null;
			$resp->title = (isset($node->attributes['title'])) ? $node->attributes['title'] : null;
			
			//bad image dont need to bother checking
			if(empty($url) || $width === 0 || $height == 0){
				//do nothing				
				
			//image had the entire http
			}elseif(preg_match('@^https?://@i',$url)){
				$loader->addPage($url, $node->hash, $width, $height);
								
			//data type of image
			}elseif(preg_match('/^data/',$url)){
				//data:[<MIME-type>][;charset=<encoding>][;base64],<data>
				$url = ltrim(strstr($url,','),',');
				$image = imagecreatefromstring($url);
				
				$x = imagesx($image);
				$y = imagesy($image);
				
				$resp->result = self::respond($image, $width, $height);
				$resp->actualWidth = $x;
				$resp->actualHeight = $y;
				
			//no host given
			}else{
				
				$url = 'http://'.$node->host.'/'.ltrim($node->attributes['src'],'/\\');
				$loader->addPage($url, $node->hash, $width, $height);
				
			}
			
		}
		
		
		//get the multithread request response
		$temp = $loader->exec();
		
		foreach($temp as $val){
			if(empty($val)) continue;
			
			$resp = new ImageLoadResponse();
			$resp->result = $val->result;
			$resp->url = $val->url;
			$resp->hash = $val->hash;
			$resp->htmlWidth = $val->htmlWidth;
			$resp->htmlHeight = $val->htmlHeight;
			$resp->actualWidth = $val->actualWidth;
			$resp->actualHeight = $val->actualHeight;
			$resp->time = $val->time;
			$resp->title = $result[$val->hash]->title;
			$resp->alt = $result[$val->hash]->alt;
			
			$result[$val->hash] = $resp;
		}
		
		return $result;
	}
}
?>
