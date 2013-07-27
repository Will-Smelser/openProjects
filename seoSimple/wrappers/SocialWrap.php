<?php

require_once SEO_PATH_VENDORS . 'seostats/src/seostats.php';

class SocialWrap{
	private $url;
	private $stats;
	private $social;
	private $fb;
	
	/**
	 * Constructor
	 * @param String $url The url of the page to run social tests on
	 */
	public function SocialWrap($url){
		if(empty($url)) return;
	
		$this->stats = new SEOstats($url);
		$this->social = $this->stats->Social();
		$this->url = $url;
	}
	
	/**
	 * Get google plus share count
	 * @return int The google plus count
	 */
	public function googlePlus(){
		return $this->social->getGoogleShares();
	}
	
	/**
	 * Get facebook likes
	 * @return int The number of likes
	 */
	public function getFbLikeCount(){
		$temp = $this->getFb();
		return $temp['like_count'];
	}
	
	/**
	 * Get the facebook share count.
	 * @return int Get the facebook share count
	 */
	public function getFbShareCount(){
		$temp = $this->getFb();
		return $temp['share_count'];
	}
	
	/**
	 * Get the facebook comment count
	 * @return int the facebook comment count
	 */
	public function getFbCommentCount(){
		$temp = $this->getFb();
		return $temp['comment_count'];
	}
	
	/**
	 * Get total number of twitter shares
	 * @return int Total number of twitter shares
	 */
	public function getTwitterShares(){
		return $this->social->getTwitterShares();
	}
	
	/**
	 * Get total delicious shares
	 * @return int The total delicious shares
	 */
	public function getDeliciousShares(){
		return $this->social->getDeliciousShares();
	}
	
	/**
	 * Get total number of Digg shares
	 * @return int Get the total number of Digg shares
	 */
	public function getDiggShares(){
		return $this->social->getDiggShares();
	}
	
	/**
	 * Get total number of linked in shares
	 * @return int Get the total number of linked in shares
	 */
	public function getLinkedInShares(){
		return $this->social->getLinkedInShares();
	}
	
	/**
	 * Get the total number of pinterest shares
	 * @return int Total number of pinterest shares
	 */
	public function getPinterestShares(){
		return $this->social->getPinterestShares();
	}
	
	/**
	 * Get the total number of StumbleUpon shares
	 * @return int Get the total number of stumble upon shares
	 */
	public function getStumbleUponShares(){
		return $this->social->getStumbleUponShares();
	}
	
	/**
	 * get the VKontakte share count
	 * @return int The total number of VKontakte share count
	 */
	public function getVKontakteShares(){
		return $this->social->getVKontakteShares();
	}
	
	private function getFb(){
		if(empty($this->fb))
			$this->fb = $this->social->getFacebookShares();
			
		return $this->fb;
	}
}