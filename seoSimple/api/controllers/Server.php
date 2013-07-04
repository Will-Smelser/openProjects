<?php

require_once SEO_API_PATH . '../wrappers/Server.php';

class Server extends Controller{
	public function Server($method){
		$server = new Server($parser, $_GET['request']);
		
		$this->exec($server, $method);
	}

}
?>