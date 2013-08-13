<?php

require_once SEO_PATH_WRAPPERS . 'ServerWrap.php';

class Server extends Controller{
	
	public $skip = array('ServerInfo','getHeaderField');
	
	public function Server($method, $args=null){
		parent::__construct($method, $args);
		
		$server = new ServerWrap($_GET['request']);
		
		$this->exec($server, $method, $args);
	}

}
?>