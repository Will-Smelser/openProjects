<?php
require_once SEO_API_PATH . '/class/ServerInfo.php';
require_once SEO_API_PATH . '/class/Whois.php';

class ServerWrap extends ServerInfo {
	public function ServerWrap($url){
		parent::__construct($url);
	}
	
	public function getWhois(){
		$info = parse_url($this->url);
		return Whois::lookup($info['host']);
	}
}
?>
