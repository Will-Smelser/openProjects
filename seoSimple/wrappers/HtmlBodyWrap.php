<?php

include SEO_PATH_CLASS . 'WordCount.php';

class HtmlBodyWrap{
	protected $anchors;
	public $parser;
	
	public $wc;
	
	/**
	 * Constructor
	 * @param HtmlParser $parser The html parser to use.
	 */
	public function HtmlBodyWrap(HtmlParser $parser){
		$this->parser = $parser;
	}
	
	/**
	 * Get all h1 tags
	 * @see HtmlParser->getTags()
	 * @see Node
	 * @return Array An array of Node elements of 'h1' type
	 */
	public function checkH1(){
		return $this->parser->getTags('h1');		
	}
	
	/**
	 * Get all h2 tags
	 * @see HtmlParser->getTags()
	 * @see Node
	 * @return Array An array of Node elements of h2 type
	 */
	public function checkH2(){
		return $this->parser->getTags('h2');
	}

	/**
	 * Get all h3 tags
	 * @see HtmlParser->getTags()
	 * @see Node
	 * @return Array An array of Node elements of h3 type
	 */
	public function checkH3(){
		return $this->parser->getTags('h3');
	}
	
	/**
	 * Get all h4 tags
	 * @see HtmlParser->getTags()
	 * @see Node
	 * @return Array An array of Node elements of h4 type
	 */
	public function checkH4(){
		return $this->parser->getTags('h4');
	}
	
	/**
	 * Get a list of key words, top 25
	 * @return Array An array of Word
	 * @see Word
	 */
	public function getKeyWords($count=25){
		if(is_array($count))
			$count = (count($count) < 1) ? 0 : 1 * $count[0];
		if($count < 1)
			$count = 25;
		return array_slice($this->getWC()->getCount(), 0, $count);
	}
	
	/**
	 * Get phrases for the top words
	 * @param number $words The top words to search, defaul is 5
	 * @return array An array of <word> => <array of phrases>. <word> is the normalized word;  
	 */
	public function getPhrases($words=5){
		if(is_array($words))
			$words = (count($words) < 1) ? 0 : 1 * $words[0];
		if($words < 1)
			$words = 5;
		
		$result = array();
		foreach($this->getKeyWords($words) as $word){
			$result[$word->normal] = $this->getWC()->getPhrasesWithWord($word->normal);
		}
		
		return $result;
	}
	
	private function getDefault($arg, $arg1, $argIndex, $default){
		//came from api, arguments are an array
		if(is_array($arg1)){
			return (count($arg1) > $argIndex && $arg1[$argIndex] > 0) ? $arg1[$argIndex]*1 : $default;
		}
		return ($arg*1 > 0) ? $arg*1 : $default;
	}
	
	/**
	 * Look at top X words and get phrases in the document which
	 * match the given word.
	 * 
	 * @param number $count Number of results to return in result set
	 * 
	 * @param number $thresh Default is 3.<br/<br/>
	 * 
	 * The threshold for minimum number of words
	 * that must exist for it to be considered a phrase.  This is becuase
	 * some single words, like a navigation link, are seen as a single
	 * word phrase.<br/><br/>
	 * 
	 * Threshold must be considered delicately.  Phrases are determined by their
	 * normalized word count.  For example, "today is a good day", normalized becomes
	 * "today good day".  The threshold will look at this normalized phrase.<br/><br/>
	 * 
	 * It should also be considered that internally phrases are built with a normalized
	 * phrase length of 3.  So this means there will be no normalized phrases longer
	 * than 3, but there can be phrases shorted.
	 * 
	 * @return  array An array of phrase
	 * @see Phrase
	 */
	public function getTopPhrases($count=10, $thresh = 2){
		$count = $this->getDefault($count, $count, 0, 10);
		$thresh = $this->getDefault($thresh, $count, 1, 2);
		
		$result = array();
		$temp = $this->getWC()->getSortedPhrases();
		
		$size = 0;
		foreach($temp as $entry){
			if(str_word_count($entry->normal) >= $thresh){
				array_push($result,$entry);
				$size++;
			}
			if($size >= $count) break;
		}
		return $result;
	}
	
	/**
	 * Get phrases which contain the give normalized word.  Will default to top word if empty.
	 * @param string The word to normalize and lookup matching phrases on.
	 * @return array An array of string (phrases) which contain the normalized word.
	 */
	public function getPhrasesSpecific($word=''){
		if(is_array($word))
			$word = (count($word) < 1) ? '' : $word[0];
		
		$temp = $this->getWC();
		if(empty($word)){
			$temp2 = $temp->getCount();
			$word = $temp2[0]->normal;
		}
		
		
		return $temp->getPhrasesWithWord($word);
	}
	
	private function getWC(){
		if(!is_object($this->wc))
			$this->wc = new WordCount($this->parser->dom);
		
		return $this->wc;
	}
	
	/**
	 * Check for all inline css
	 * @return string[] An array of inline css content
	 */
	public function checkInlineCSS(){
		preg_match_all('@style[\s+]?=[\s+]?[\'|"].*?[\'|"]i', $this->parser->dom, $matches);
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
	public function checkInlineStyle(){
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
		if(empty($object)) return false;
		
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
	
	/**
	 * Get a list of all anchors
	 * @return Array An array of Node elements
	 * @uses Node
	 */
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