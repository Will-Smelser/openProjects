<?php
echo getcwd();
require_once "./3rdParty/SEOstats-master/src/seostats.php";

class Google{
	private $cx = "AIzaSyA_wkenQWzwHcxuaozcfIc_gcuMo9E09TM";//google for SEO project key
	private $url;
	private $stats;
	private $google;
	
	public function Google($url){
		if(empty($url)) return;
		
		$this->stats = new SEOstats($url);
		$this->google = $this->stats->Google();
		$this->url = $url;
	}
	
	public function setStats($obj){
		$this->stats = $obj;
		$this->url = $obj->getUrl();
	}
	
	public function setGoogle($obj){
		$this->google = $obj;
	}
	
	public function getPageRank(){
		return $this->google->getPageRank();
	}
	
	public function getBacklinks($max){
		$obj = $this->google->getBacklinks(false, $max);
		var_dump($obj);
	}
	
	public function getUrl(){
		return $this->url;
	}
	
	public function setUrl(){
		$this->obj->setUrl($this->url);
	}
}

?>