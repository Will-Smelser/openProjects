<?php

require_once SEO_API_PATH . '/wrappers/SemRushWrap.php';

class SemRush extends Controller{
	
	public $skip = array();
	
	public function SemRush($method){
		
		$obj = new SemRushWrap($_GET['request']);
		
		$this->exec($obj, $method);
	}

}
?>

