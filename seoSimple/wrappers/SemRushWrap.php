<?php
require_once SEO_API_PATH . "/vendors/seostats/src/seostats.php";

class SemRushWrap{
	public $url;
	public $sem;
	public $stats;
	
	public function SemRushWrap($url){
		$this->stats = new SEOstats($url);
		$this->sem =  $this->stats->SEMRush()->getDomainRank();
		$this->url = $url;
	}
	
	/**
	 * SEMrush Domain Rank
	 */
	public function getDomainRank(){
		return $this->sem['Or'];
	}
	
	/**
	 * Number of Keywords this site has in the TOP20 organic results
	 */
	public function getTopKeywords(){
		return $this->sem['Or'];
	}
	
	/**
	 * Estimated number of visitors coming from the first 20 search results (per month)
	 */
	public function getNumberVisitsFromTopKeyWords(){
		return $this->sem['Ot'];
	}
	
	/**
	 * Estimated cost of purchasing the same number of visitors through Ads
	 */
	public function getCostVisitsByAds(){
		return $this->sem['Oc'];
	}
	
	/**
	 * Estimated number of competitors in organic search
	 */
	public function getCompetitorCount(){
		return $this->sem['Oo'];
	}
	
	/**
	 * Estimated number of potential ad/traffic buyers
	 */
	public function getAdTraffic(){
		return $this->sem['Oa'];
	}
}
?>
