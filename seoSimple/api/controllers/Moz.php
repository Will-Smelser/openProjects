<?php

require_once SEO_PATH_WRAPPERS . 'MozWrap.php';

class Moz extends Controller{
	
	public $skip = array();
	
	public function Moz($method){
		
		$moz = new MozWrap($_GET['request']);
		
		$this->exec($moz, $method);
	}

}
?>
