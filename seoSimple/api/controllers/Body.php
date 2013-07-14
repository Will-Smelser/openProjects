<?php
require_once SEO_API_PATH . 'class/HtmlParser.php';
require_once SEO_API_PATH . 'wrappers/HtmlBodyWrap.php';

class Body extends Controller{
	$skip = array('getPhrases');
	public function Body($method){
		$content = file_get_contents($_GET['request']);
		$parser = new HtmlParser($content, $_GET['request']);		
		$html = new HtmlBodyWrap($parser, $_GET['request']);
		
		$this->exec($html, $method);
	}
}
?>