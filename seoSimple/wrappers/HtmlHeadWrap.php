<?php

class HtmlHeadWrap{
	
	private $parser;
	private $meta;
	
	private $html; //html or xhtml
	private $version;
	private $type; //transition, strict
	
	public function HtmlHeadWrap(HtmlParser $parser){
		$this->parser = $parser;	
	}
	
	/**
	 * Get the title element
	 * @return unknown|NULL
	 */
	public function getTitle(){
		$titles = $this->parser->getTags('title');
		if(count($titles)){
			return $titles[0];
		}
		return null;
	}
	
	/**
	 * Get the Meta Description tag content
	 * @return String|NULL The "content" attribute in the meta tag with attribute "name" or NULL if none exists
	 */
	public function getMetaDesc(){
		foreach($this->parser->getTags("meta") as $entry){
			if(isset($entry->attributes['description']) && $entry->attributes['description'] === 'description'){
				return $entry->attributes['content'];
			}
		}
		
		return null;
	}
	
	/**
	 * Get the meta keywords content
	 * @return String|NULL The "content" attribute of meta tag with attribute "keywords"
	 */
	public function getMetaKeywords(){
		foreach($this->parser->getTags("meta") as $entry){
			if(isset($entry->attributes['keywords']) && $entry->attributes['keywords'] === 'description'){
				return $entry->attributes['content'];
			}
		}
		
		return null;
	}

	/**
	 * Determine the doctype.
	 * @return string|NULL The doctype in following format :<br/>
	 * {code}
	 * <html> <version> [type]
	 * 
	 * html = "HTML", "XHTML", "XML", etc...
	 * varsion = 5, 4.01, etc...
	 * type = [optional] transitional, strict, Frameset, etc... 
	 * {/code} 
	 * 
	 */
	public function getDoctype(){
		if(!empty($this->html))
			return $this->html . ' ' . $this->version . ' ' . $this->type;
		
		foreach($this->parser->getTags('!DOCTYPE') as $doc){
			
			//parse the doctype
			$raw = $doc->raw;
			//check HTML 5
			if(preg_match('/html\>$/i',$raw)){
				return 'HTML 5';
			}else{
				//version
				preg_match('@(?P<html>\w+)\s+(?P<version>\d+\.\d+)\s+?(?P<type>\w+)?//@i',$raw,$matches);
				
				$this->html = strtolower($matches['html']);
				$this->version = strtolower($matches['version']);
				$this->type = strtolower($matches['type']);
				
				return $this->html . ' ' . $this->version . ' ' . $this->type;
			}
		}
		
		return null;
	}
	
	/**
	 * Match the meta tag with attribute "http-equiv" and return the charset value
	 * @return String|NULL Returns null if none is found
	 */
	public function getEncoding(){
		foreach($this->getMeta() as $meta){
			if(isset($meta->attributes['http-equiv']) && strtolower($meta->attributes['http-equiv']) === 'content-type'){
				if(isset($meta->attributes['content']) && 
						preg_match('@charset\=(?P<charset>.*);?@i',$meta->attributes['content'],$matches)){
					return $matches['charset'];
				}
			}
		}
		return null;
	}
	
	/**
	 * Attempt to find the lang attribute or xml:lang attribute of document
	 * @return String|NULL Returns null if no lang attribute is found
	 */
	public function getLang(){
		//need to check what type this is
		$this->getDoctype();
		$html = $this->parser->getTags('html');
		
		if(empty($html))
			return null;
		
		$key = ($this->html === 'html') ? 'lang' : 'xml:lang';
		
		return (isset($html->attributes[$key])) ? $html->attributes[$key] : null; 
	}
	
	/**
	 * Returns a fully qualified link (http://... included) to the favicon or NULL. 
	 * @return String|NULL The fully qualified url or NULL if none found
	 */
	public function getFavicon(){
		$result = false;
		
		//check link mechanish
		foreach($this->parser->getTags('link') as $link){
			if(isset($link->attributes['rel']) && $link->attributes['rel'] === 'icon'){
				if(preg_match('@^http@i',$link->attributes['href'])){
					return $link->attributes['href'];
				}else{
					return 'http://' . $link->host . '/' . ltrim($link->attributes['href'],'/\\');	
				}
			}
		}
		
		return null;
	}
	
	/**
	 * Verify that the "default" favicon exists or not.  It is not preferred to set your favicon
	 * this way.  However, by default browsers will attempt this.
	 * @return String|NULL Make a request to default favicon location.  If this request fails, return null, 
	 * otherwise return the default fully qualified favicon location. 
	 */
	public function getFaviconNoTag(){
		//check for favicon
		@$url = file_get_contents('http://'.$this->parser->host.'/favicon');
		if(!empty($url)){
			return 'http://'.$this->parser->host.'/favicon';
		}
		
		return null;
	}
	
	/**
	 * Used as a helper function to check for the meta tag.  Used to save
	 * repeated parsing if the meta information has already been parsed.
	 */
	private function getMeta(){
		if(empty($this->meta))
			$this->meta = $this->parser->getTags('meta');
		
		return $this->meta;
	}
}