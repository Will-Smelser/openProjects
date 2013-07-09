<?php
require_once SEO_API_PATH . '/class/HtmlParser.php';
require_once SEO_API_PATH . '/wrappers/HtmlHeadWrap.php';

class Head extends Controller{
	public function Head($method){
		$content = file_get_contents($_GET['request']);
		$parser = new HtmlParser($content, $_GET['request']);		
		$head = new HtmlHeadWrap($parser);
		
		$this->exec($head, $method);
	}
}
?>