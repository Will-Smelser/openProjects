<?php

require_once 'Node.php';

/**
 * Parsing HTML, no DOM building
 * Does not handle nested tags.
 * @author Will
 *
 */
class HtmlParser{
	public $dom = null;
	public $host = null;
	
	/**
	 * Constructor
	 * @param unknown $str Raw HTML to parse for a tag
	 * @param unknown $url The url where HTML came from.
	 */
	public function HtmlParser($str, $url){
		$this->dom =  $str;
		
		$temp = parse_url($url);
		$this->host = $temp['host'];
	}
	
	public function getHost(){
		return $this->host;
	}

	/**
	 * Finds the given tags in the raw HTML and returns
	 * an array of Nodes.
	 * @param unknown $tag
	 * @return Array of Node
	 */
	public function getTags($tag){
		$result = array();
		foreach($this->findTags($tag) as $entry){
			$node = new Node($tag,$entry);
			$node->host = $this->host;
			array_push($result, $node);
		}
	
		return $result;
	}
	
	/**
	 * Search string for matching tags.  Cannot support nested tags.
	 * @param unknown $tag
	 * @return unknown|string|multitype:
	 */
	private function findTags($tag){
		if(preg_match_all("@<{$tag}[^>]*>.*?</{$tag}[\s+]?>@is",$this->dom, $matches)){
			return $matches[0];
		}else if(preg_match_all("@<{$tag}[^>]*@is", $this->dom, $matches)){
			//we missed the '>'
			foreach($matches[0] as $key=>$match)
				$matches[0][$key] .= '>';
			
			return $matches[0];
		}else{
			return array();
		}
	}
	

}