<?php

require_once SEO_API_PATH . "/vendors/seostats/src/seostats.php";

class MozWrap{
	
	public $url;
	public $moz;
	public $stats;
	
	public function MozWrap($url){
		$this->stats = new SEOstats($url);
		$this->moz = $this->stats->OpenSiteExplorer()->getPageMetrics();
		$this->url = $url;
	}
	
	public function getPageAuthority(){
		return $this->moz['pageAuthority'];
	}
	
	public function getDomainAuthority(){
		return $this->moz['domainAuthority'];
	}
	
	public function getTotalInboundLinks(){
		return $this->moz['totalInboundLinks'];
	}
	
	public function getTotalInboundDomains(){
		return $this->moz['linkingRootDomains'];
	}
}

?>
