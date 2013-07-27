<?php
require_once SEO_PATH_CLASS . 'HtmlParser.php';
require_once SEO_PATH_WRAPPERS . 'HtmlHeadWrap.php';

class Head extends Controller{
	public function Head($method){
		$content = file_get_contents($_GET['request']);
		$parser = new HtmlParser($content, $_GET['request']);		
		$head = new HtmlHeadWrap($parser);
		
		$this->exec($head, $method);
	}
}
?>