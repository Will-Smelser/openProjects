<?php

require_once SEO_API_PATH . 'vendors/seostats/src/seostats.php';

class SocialWrap{
	private $url;
	private $stats;
	private $social;
	private $fb;
	
	public function SocialWrap($url){
		if(empty($url)) return;
	
		$this->stats = new SEOstats($url);
		$this->social = $this->stats->Social();
		$this->url = $url;
	}
	
	public function googlePlus(){
		return $this->social->getGoogleShares();
	}
	
	public function getFbLikeCount(){
		return $this->getFb()['like_count'];
	}
	
	public function getFbShareCount(){
		return $this->getFb()['share_count'];
	}
	
	public function getFbCommentCount(){
		return $this->getFb()['comment_count'];
	}
	
	public function getTwitterShares(){
		return $this->social->getTwitterShares();
	}
	
	public function getDeliciousShares(){
		return $this->social->getDeliciousShares();
	}
	
	public function getDiggShares(){
		return $this->social->getDiggShares();
	}
	
	public function getLinkedInShares(){
		return $this->social->getLinkedInShares();
	}
	
	public function getPinterestShares(){
		return $this->social->getPinterestShares();
	}
	
	public function getStumbleUponShares(){
		return $this->social->getStumbleUponShares();
	}
	
	public function getVKontakteShares(){
		return $this->social->getVKontakteShares();
	}
	
	private function getFb(){
		if(empty($this->fb))
			$this->fb = $this->social->getFacebookShares();
			
		return $this->fb;
	}
}