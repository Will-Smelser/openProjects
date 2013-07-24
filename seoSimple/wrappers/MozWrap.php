<?php

require_once SEO_API_PATH . "/class/MozConnect.php";
require_once SEO_API_PATH . "/class/HtmlParser.php";

class MozWrap{
	
	public $url;
	public $moz;
	
	public function MozWrap($url){
		$this->url = $url;
		
		$moz = new MozConnect();
		$parser = new HtmlParser($moz->getData($url), $url);
		
		$tags = $parser->getTags('td');
		$this->moz['domainAuthority'] = strip_tags($tags[0]->text);
		$this->moz['pageAuthority'] = strip_tags($tags[1]->text);
		$this->moz['linkingRootDomains'] = strip_tags($tags[2]->text);
		$this->moz['totalInboundLinks'] = strip_tags($tags[3]->text);
		
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
