<?php 
require_once SEO_API_PATH . '/class/GoogleInfo.php';

class GoogleWrap extends GoogleInfo {
	public function GoogleWrap($url){
		parent::__construct($url);
	}
}

?>