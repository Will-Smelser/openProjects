<?php
require_once SEO_API_PATH . 'class/HtmlParser.php';
require_once SEO_API_PATH . 'wrappers/HtmlBody.php';

class Body extends Controller{
	public function Body($method){
		$content = file_get_contents($_GET['request']);
		$parser = new HtmlParser($content, $_GET['request']);		
		$html = new HtmlBody($parser, $_GET['request']);
		
		$this->exec($html, $method);
	}
}
?>