<?php

include SEO_API_PATH . "/class/WordCount.php";

class HtmlBody{
	protected $anchors;
	public $parser;
	
	/**
	 * Constructor
	 * @param HtmlParser $parser The html parser to use.
	 */
	public function Htmlbody(HtmlParser $parser){
		$this->parser = $parser;
	}
	
	/**
	 * Get all h1 tags
	 * @see HtmlParser->getTags()
	 * @see Node
	 * @return Array An array of Node elements of 'h1' type
	 */
	public function checkH1(){
		return count($this->parser->getTags('h1'));		
	}
	
	/**
	 * Get all h2 tags
	 * @see HtmlParser->getTags()
	 * @see Node
	 * @return Array An array of Node elements of h2 type
	 */
	public function checkH2(){
		return count($this->parser->getTags('h2'));
	}

	/**
	 * Get all h3 tags
	 * @see HtmlParser->getTags()
	 * @see Node
	 * @return Array An array of Node elements of h3 type
	 */
	public function checkH3(){
		return count($this->parser->getTags('h3'));
	}
	
	/**
	 * Get all h4 tags
	 * @see HtmlParser->getTags()
	 * @see Node
	 * @return Array An array of Node elements of h4 type
	 */
	public function checkH4(){
		return count($this->parser->getTags('h4'));
	}
	
	/**
	 * Get a list of key words
	 * @return Array An array of Word
	 * @see Word
	 */
	public function getKeyWords(){
		$wc = new WordCount();
		return $wc->getCount($this->parser->dom);
	}
	
	/**
	 * Check for all inline css
	 * @return string[] An array of inline css content
	 */
	public function checkInlineCSS(){
		preg_match_all('@style[\s+]?=[\s+]?[\'|"].*?[\'|"]@i',$this->parser->dom, $matches);
		return $matches[0];
	}
	
	/**
	 * Get an array of stylesheet link tag Node grouped by host
	 * @return object Array(<host>=>array(Node1, Node2, ..., NodeX)
	 */
	public function checkLinkTags(){
		//check link tags
		$links = array();
		foreach($this->parser->getTags('link') as $node){
			if(isset($node->attributes['rel']) && $node->attributes['rel'] === 'stylesheet'){
				if(!isset($links[$node->host]))
					$links[$node->host] = array();
				
				array_push($links[$node->host], $node);
			}
		}
		return $links;
	}
	
	/**
	 * Get an array of style Nodes.
	 */
	public function checkForInlineCSS(){
		return $this->parser->getTags('style');		
	}
	
	/**
	 * returns true, if there are frames
	 * @return boolean
	 */
	public function checkForFrames(){
		return (count($this->parser->getTags('frame')) > 0);
	}
	
	/**
	 * returns true f there are iframes
	 * @return boolean
	 */
	public function checkForIframes(){
		return (count($this->parser->getTags('iframe')) > 0);
	}
	
	/**
	 * Return true if there is flash
	 */
	public function checkForFlash(){
		$object = $this->parser->getTags('object');
		
		return preg_match($object->raw, '/shockwave\-flash/i');
	}
	
	/**
	 * Get internal anchor tags
	 * @return unknown
	 */
	public function getInternalAnchor(){
		$anchors = $this->getAnchors();
		$result = array();
		foreach($anchors as $a){
			if(isset($a->attributes['href'])){
				$href = $a->attributes['href'];
				$info = parse_url($href);
				
				//relative internal link
				if(!isset($info['host']) || empty($info['host'])){
					array_push($result, $a);
				}else if($a->host === $info['host'])
					array_push($result, $a);
			}
		}
		return $result;
	}
	
	/**
	 * get external anchor tags
	 * @return unknown
	 */
	public function getExternalAnchors(){
		$anchors = $this->getAnchors();
		$result = array();
		foreach($anchors as $a){
			if(isset($a->attributes['href'])){
				$href = $a->attributes['href'];
				$info = parse_url($href);
		
				//relative internal link
				if(!isset($info['host']) || empty($info['host'])){
					//do nothing
				}else if($a->host !== $info['host'])
					array_push($result, $a);
				
			}
		}
		return $result;
	}
	
	private function getAnchors(){
		if(!empty($this->anchors))
			return $this->anchors;
		else
			return $this->parser->getTags('a');
	}
	
	/**
	 * Check document image dimensions are set and good.  This will take
	 * as long as the longest image takes to load
	 * 
	 * {code}
	 * class ImageLoadResponse{
	 * 	public $url; //url of image requested.  Should be able to use hash to find Node
	 * 	public $result; //1=good, -1=failed to check, 0=sizes did not match
	 * 	public $hash; //Node hash
	 *  public $time; //in seconds
	 * }
	 * {/code}
	 * 
	 * @return ImageLoadResponse[] An array of ImageLoadResponse
	 * 
	 * @see ImageLoadResponse
	 * @see Node
	 */
	public function checkImages(){
		require_once "../class/ImageParser.php";
		
		$imgs = $this->parser->getTags('img');
		return ImageParser::checkActualDimsThreaded($imgs);
	}
	
	
}
?>