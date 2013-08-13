<?php

require_once SEO_PATH_WRAPPERS . 'GoogleWrap.php';

class Google extends Controller{
	
	public $skip = array('GoogleInfo','setStats','setGoogle', 'getUrl', 'setUrl');
	
	public function Google($method, $args=null){
		parent::__construct($method, $args);
		
		$url = preg_replace('@https?://@i','',$_GET['request']);
		$google = new GoogleWrap($url);
		
		$this->exec($google, $method, $args);
	}

}
?>