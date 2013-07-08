<?php

require_once SEO_API_PATH . '/wrappers/ServerWrap.php';

class Server extends Controller{
	
	public $skip = array('ServerInfo');
	
	public function Server($method){
		
		$server = new ServerWrap($_GET['request']);
		
		$this->exec($server, $method);
	}

}
?>