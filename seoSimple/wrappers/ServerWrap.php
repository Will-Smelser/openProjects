<?php
require_once SEO_PATH_CLASS . 'ServerInfo.php';
require_once SEO_PATH_CLASS . 'Whois.php';

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
		
		//make sure there is no subdomains
		$parts = explode('.',$info['host']);
		$ltd = array_pop($parts);
		$name = array_pop($parts);
		
		return Whois::lookup($name.'.'.$ltd);
	}
}
?>
