<?php

require_once SEO_API_PATH . '/wrappers/GoogleWrap.php';

class Google extends Controller{
	
	public $skip = array('GoogleInfo','setStats','setGoogle', 'getUrl', 'setUrl');
	
	public function Google($method){
		
		$google = new GoogleWrap($_GET['request']);
		
		$this->exec($google, $method);
	}

}
?>