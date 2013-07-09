<?php
require_once SEO_API_PATH . '/class/ServerInfo.php';
require_once SEO_API_PATH . '/class/Whois.php';

class ServerWrap extends ServerInfo {
	public function ServerWrap($url){
		parent::__construct($url);
	}
	
	/**
	 * Get the whois information for a host.
	 * @return Array|NULL Return a key=>value array with all host information
	 * @link whois.internic.net
	 */
	public function getWhois(){
		$info = parse_url($this->url);
		return Whois::lookup($info['host']);
	}
}
?>
