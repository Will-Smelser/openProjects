<?php

require_once SEO_PATH_WRAPPERS . 'GoogleWrap.php';

class Google extends Controller{
	
	public $skip = array('GoogleInfo','setStats','setGoogle', 'getUrl', 'setUrl');
	
	public function Google($method, $args=null){
		
		$google = new GoogleWrap($_GET['request']);
		
		$this->exec($google, $method, $args);
	}

}
?>