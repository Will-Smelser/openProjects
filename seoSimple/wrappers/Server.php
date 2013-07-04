<?php
require_once '../class/ServerInfo.php';
require_once '../class/Whois.php';

class Server extends ServerInfo {
	public function ServerInfo($url){
		parent::__construct($url);
	}
	
	public function getWhois($domain){
		return Whois::lookup($domain);
	}
}
?>
