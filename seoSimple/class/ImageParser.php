<?php
require_once 'HtmlParser.php';
include_once "PageLoad.php";

class ImageLoadResponse{
	public $url;
	public $result;
	public $hash;
}

class ImageParser{
	private static $GOOD = 1;
	private static $BAD = 0;
	private static $FAIL = -1;
	
	/**
	 * Image checking
	 * @param Node $node
	 * @return array(width, height) width/height will be 0 if none set.
	 */
	public static function checkWidthHeight(Node $node){
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
	 * 
	 * @param unknown $image A php image resource
	 * @param unknown $width int
	 * @param unknown $height int
	 * @return 1=good, -1=failed to check, 0=sizes did not match
	 */
	public static function respond($image, $width, $height){
		if($image === false)
			return self::$FAIL;
		
		$x = imagesx($image);
		$y = imagesy($image);
		
		if($x === false || $x === false)
			return self::$FAIL;
		
		if($x === $width && $height === $y)
			return self::$GOOD;
		
		return self::$BAD;
	}
	
	/**
	 * Download an image and compare its dimensions
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
		
		$resp = new ImageLoadResponse();
		$resp->hash = $img->hash;
		$resp->url = $url;
		$resp->result = self::respond($image, $width, $height);
		
		return $resp;
	}
	
	/**
	 * 
	 * @param unknown $imgNodes  An array of img Nodes
	 * @return ImageLoadResponse An array of ImageLoadResponse Objects
	 */
	public static function checkActualDimsThreaded($imgNodes){
		$loader = new PageLoad('ImageParserThread.php');
		$result = array();
		foreach($imgNodes as $node){
			$data = self::checkWidthHeight($node);
			$width = $data[0];
			$height = $data[1];
			
			$url = $node->attributes['src'];
			
			//bad image dont need to bother checking
			if(empty($url) || $width === 0 || $height == 0){
				$resp = new ImageLoadResponse();
				$resp->result = self::$BAD;
				$resp->url = $url;
				$resp->hash = $node->hash;
				$result[$node->hash] = $resp;
				
			//image had the entire http
			}elseif(preg_match('@^https?://@i',$url)){
				$loader->addPage($url, $node->hash, $width, $height);
				
			//data type of image
			}elseif(preg_match('/^data/',$url)){
				//data:[<MIME-type>][;charset=<encoding>][;base64],<data>
				$url = ltrim(strstr($url,','),',');
				$image = imagecreatefromstring($url);
				$resp = new ImageLoadResponse();
				$resp->result = self::respond($image, $width, $height);
				$resp->url = $url;
				$resp->hash = $node->hash;
				$result[$node->hash] = $resp;
				
			//no host given
			}else{
				$url = 'http://'.$node->host.'/'.ltrim($node->attributes['src'],'/\\');
				$loader->addPage($url, $node->hash, $width, $height);
			}
		}
		
		//get the multithread request response
		$temp = $loader->exec();
		foreach($temp as $val){
			$resp = new ImageLoadResponse();
			$resp->result = $val['result'];
			$resp->url = $val['url'];
			$resp->hash = $val['hash'];
			$result[$node->hash] = $resp;
		}
		
		return $result;
	}
}
?>
