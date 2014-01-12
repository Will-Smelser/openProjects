<?php

require_once 'Node.php';

/**
 * Parsing HTML, no DOM building
 * Does not handle nested tags.
 * @author Will
 *
 */
class HtmlParser{
	/**
	 * @ignore
	 * @var unknown
	 */
	public $dom = null;
	
	/**
	 * @ignore
	 * @var unknown
	 */
	public $host = null;
	
	/**
	 * Constructor
	 * @param unknown $str Raw HTML to parse for a tag
	 * @param unknown $url The url where HTML came from.
	 * @ignore
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
	 * Math on <tag ... />
	 * @param String $tag
	 * @param String $str The string to search on.
	 * @return Array An array of strings containing the raw matched text
	 */
	public function findTags1($tag, &$str){
		preg_match_all("@<{$tag}[^>]*/>@is",$str, $matches);
		return $matches[0];
	}
	
	/**
	 * Math on <tag ..>...</tag ...>
	 * @param String $tag
	 * @param String $str The string to search on.
	 * @return Array An array of strings containing the raw matched text
	 */
	public function findTags2($tag, &$str){
		preg_match_all("@<{$tag}[^>]*>.*?</{$tag}[\s+]?>@is",$str, $matches);
		return $matches[0];
	}
	
	/**
	 * Match on no closing tag given, incase we got bad xml
	 * @param String $tag
	 * @param String $str
	 * @return Array An array of string matches
	 */
	public function findTags3($tag, &$str){
		preg_match_all("@<{$tag}[^>]*@is", $str, $matches);
		foreach($matches[0] as $key=>$match)
			$matches[0][$key] .= '>';//we match up to '>', so have to add it back
			
		return $matches[0];
	}
	
	/**
	 * Go through matches and remove each match from search string used for
	 * other matches
	 * @param Array $subs An array of search matches to remove from search string
	 * @param String $str The search string to be altered.
	 * @return String The search string with matches removed
	 */
	public function clean($subs, &$str){
		$result = array();
		foreach($subs as $temp){
			array_push($result, $temp);
			$str = str_replace($temp, '', $str);
		}
		return $str;
	}

	/**
	 * Search string for matching tags.  Cannot support nested tags.
	 * @param unknown $tag
	 * @return unknown|string|multitype:
	 */
	public function findTags($tag){
		$str = $this->dom;
		
		$result = $this->findTags1($tag, $str);
		$str = $this->clean($result, $str);
		
		$result2 = $this->findTags2($tag,$str);
		$str = $this->clean($result2, $str);
		
		$result3 = $this->findTags3($tag,$str);
		$str = $this->clean($result3, $str);
		
		return array_merge($result, $result2, $result3);
	}
	
	/**
	 * Search string for matching tags.  Cannot support nested tags.
	 * @depricated
	 * @param unknown $tag
	 * @return unknown|string|multitype:
	 */
	public function findTagsOld($tag){
		if(preg_match_all("@<{$tag}[^>]*>.*?</{$tag}[\s+]?>@is",$this->dom, $matches)){
			return $matches[0];
		
		//we have an open tag, then closing tag
		}else if(preg_match_all("@<{$tag}[^>]*>.*?</{$tag}[\s+]?>@is",$this->dom, $matches)){
			return $matches[0];
			
		//we have no closing tag
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