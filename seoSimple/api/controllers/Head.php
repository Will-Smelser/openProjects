<?php
require_once SEO_API_PATH . '../class/HtmlParser.php';
require_once SEO_API_PATH . '../wrappers/HtmlHead.php';

class Head extends Controller{
	public function Head($method){
		$content = file_get_contents($_GET['request']);
		$parser = new HtmlParser($content, $_GET['request']);		
		$head = new HtmlHead($parser);
		
		$this->exec($head, $method);
	}
}
?>