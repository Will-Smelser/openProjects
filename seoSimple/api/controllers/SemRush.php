<?php

require_once SEO_PATH_WRAPPERS . 'SemRushWrap.php';

class SemRush extends Controller{
	
	public $skip = array();
	
	public function SemRush($method){
		
		$obj = new SemRushWrap($_GET['request']);
		
		$this->exec($obj, $method);
	}

}
?>

