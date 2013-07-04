<?php

class Htmlhead{
	
	private $parser;
	private $meta;
	
	private $html; //html or xhtml
	private $version;
	private $type; //transition, strict
	
	public function Htmlhead(HtmlParser $parser){
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
	 * Get the Meta Description tag
	 * @return NULL
	 */
	public function getMetaDesc(){
		foreach($this->parser->getTags("meta") as $entry){
			if(isset($entry->attributes['name']) && $entry->attributes['name'] === 'description'){
				return $entry->attributes['name'];
			}
		}
		
		return null;
	}
	
	public function getMetaKeywords(){
		foreach($this->parser->getTags("meta") as $entry){
			if(isset($entry->attributes['name']) && $entry->attributes['name'] === 'description'){
				return $entry->attributes['name'];
			}
		}
		
		return null;
	}

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
	
	public function getLang(){
		//need to check what type this is
		$this->getDocttype();
		$html = $this->parser->getTags('html');
		
		if(empty($html))
			return null;
		
		$key = ($this->html === 'html') ? 'lang' : 'xml:lang';
		
		return (isset($html->attributes[$key])) ? $html->attributes[$key] : null; 
	}
	
	/**
	 * Returns a fully qualified link (http://... included) to the favicon or NULL
	 * @return string|NULL
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
	
	public function getFaviconNoTag(){
		//check for favicon
		if(file_get_contents('http://'.$this->parser->host.'/favicon')){
			return 'http://'.$this->parser->host.'/favicon';
		}
		
		return null;
	}
	
	private function getMeta(){
		if(empty($this->meta))
			$this->meta = $this->parser->getTags('meta');
		
		return $this->meta;
	}
}