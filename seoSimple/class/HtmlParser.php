<?php

/**
 * My representation of an HTML DOM node.  Holds a bunch of data.
 * 
 * @author Will
 *
 */
class Node{
	public $hash;
	public $host;
	public $raw;
	public $tag;
	public $attributes;
	
	public $textStart;
	public $textEnd;
	public $text;
	
	/**
	 * Constructor
	 * @param unknown $tag The HTML tag
	 * @param unknown $raw The raw HTML that tag comes from
	 */
	public function Node($tag, $raw){
		$this->raw = $raw;
		$this->tag = $tag;
		
		$this->processAttrs();
		$this->processContent();
		
		$this->hash = spl_object_hash($this);
	}
	
	/**
	 * Parse out the content of the tag
	 */
	private function processContent(){
		$this->textEnd = strpos($this->raw,"</{$this->tag}");
		$this->text = trim(substr($this->raw, $this->textStart, $this->textEnd - $this->textStart));
	}
	
	/**
	 * Overly complicated method of parsing all the attributes.
	 * Didnt mean for it to get this way, but kind of just happened.
	 * TODO: Clean this up.
	 */
	private function processAttrs(){
		
		$start = strlen($this->tag)+1;
		$temp = substr($this->raw,$start);
		
		//echo $temp . "\n";

		$i=0;
		for(; $i < strlen($temp); ){
			if($temp[$i] == '>') break;
			
			$pos  = $this->skipWs($temp, $i);
			
			//echo "should be r-{$temp[$pos]}\n";
			
			$pos2 = $this->skipToEqualOrWs($temp, $pos);
			
			//echo "should be =-{$temp[$pos2]}\n";
			
			$attr = trim(substr($temp, $pos, $pos2 - $pos));
			
			//echo "Got attribute-$attr\n";
			
			if(!empty($attr) && $pos2 < strlen($temp)  && $temp[$pos2] == '='){
				
				//skip =
				$pos2++;
				
				$pos = $this->skipWs($temp, $pos2); //should be ' or "
				
				if($temp[$pos] != '"' && $temp[$pos] != "'"){
					//echo "FAILED-{$temp[$pos]}";
					break;
				}
				
				$pos++; //skip the ' or "
				
				$pos2 = $this->skipToQuote($temp, $pos, $temp[$pos-1]);
				
				$val = trim(substr($temp, $pos, $pos2-$pos),'\'"');
				
				$pos2++; //get past ' or "
				
				$this->attributes[$attr] = $val;
				
				//echo "$attr:$val\n";
			}
			$i = $pos2;
		}
		$this->textStart = $i+$start+1;
	}
	
	/**
	 * On the processAttributes function is cleaned up
	 * hopefully many of these will go away. 
	 */
	
	private function skipWs($str, $start){
		while($start < strlen($str) && $str[$start] == ' ')
			$start++;
		
		return $start;
	}
	
	private function skipToEqualOrWs($str, $start){
		while($start < strlen($str) && $str[$start] != '='  && $str[$start] != ' ')
			$start++;
		
		return $start;
	}
	
	private function skipTillWs($str, $start){
		while($start < strlen($str) && $str[$start] != ' ')
			$start++;
		
		return $start;
	}
	
	private function skipTagEnd($str, $start){
		while($start < strlen($str) && $str[$start] != '>')
			$start++;
		
		return ++$start;
	}
	
	private function skipToQuote($str, $start, $quote){
		while($start < strlen($str) && $str[$start] != $quote)
			$start++;
		
		return $start;
	}
}

/**
 * Parsing HTML, no DOM building
 * Does not handle nested tags.
 * @author Will
 *
 */
class HtmlParser{
	private $dom = null;
	private $host = null;
	
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
	
	public function getTitle(){
		return $this->getTags('title');
	}
	
	public function getMeta(){
		return $this->getTags("meta");
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
	
	/**
	 * Returns a fully qualified link (http://... included) to the favicon or NULL
	 * @return string|NULL
	 */
	public function getFavicon(){
		$result = false;
		
		//check link mechanish
		foreach($this->getTags('link') as $link){
			if(isset($link->attributes['rel']) && $link->attributes['rel'] === 'icon'){
				if(preg_match('@^http@i',$link->attributes['rel'])){
					return $link->attributes['rel'];
				}else{
					return 'http://' . $link->host . '/' . ltrim($link->attributes['rel'],'/\\');	
				}
			}
		}
		
		//check for favicon
		if(file_get_contents('http://'.$this->host.'/favicon')){
			return 'http://'.$this->host.'/favicon';
		}
		
		return null;
	}
}