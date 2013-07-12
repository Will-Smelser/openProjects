<?php
require_once SEO_API_PATH . "/vendors/seostats/src/seostats.php";

class GoogleInfo{
	public $cx = "AIzaSyA_wkenQWzwHcxuaozcfIc_gcuMo9E09TM";//google for SEO project key
	public $url;
	public $stats;
	public $google;
	
	public function GoogleInfo($url){
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
	
	public function getBacklinks($max=10){
		return $this->google->getBacklinks($this->url, $max);
	}
	
	public function getBacklinksTotal(){
		return $this->google->getBacklinksTotal($this->url);
	}
	
	public function getUrl(){
		return $this->url;
	}
	
	public function setUrl(){
		$this->obj->setUrl($this->url);
	}
}

?>