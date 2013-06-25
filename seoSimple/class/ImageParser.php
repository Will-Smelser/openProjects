<?php
require_once 'HtmlParser.php';

class ImageParser{
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
	 * Download an image and compare its dimensions
	 * @param unknown $src The url or file to check against
	 * @param unknown $width Width to compare against
	 * @param unknown $height Height to compare against
	 * @return number
	 */
	public static function checkActualDims(Node $img, $width, $height){
		
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
		
		echo "$url\t\t\t";
		
		if($image === false)
			return -1;
		
		$x = imagesx($image);
		$y = imagesy($image);
		
		if($x === false || $x === false)
			return -1;
		
		if($x === $width && $height === $y)
			return 1;
		
		return 0;
	}
}
?>
