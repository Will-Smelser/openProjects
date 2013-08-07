<?php

/**
 * Response class that PageLoadTime.php uses
 * @author Will
 *
 */
class PageLoadResponse{
	public $url;
	public $start;
	public $finish;
	public $totalSeconds;
}

/**
 * This class is used for making a Threaded curl requests.  Since PHP
 * doesn't support threading, this can be used to simulate that.
 * 
 * Designed explicitly for downloading internet content.
 * @author Will
 *
 */
class PageLoad{
	private $loadPage;
	
	private $mh;
	private $curls = array();
	
	/**
	 * Constructor
	 * 
	 * @param string $page The php page to make requests to, but relative
	 * to self::$loadPage
	 * 
	 * @see $loadPage
	 */
	public function PageLoad($page='PageLoadTime.php'){
		$this->loadPage = 'http://' . SEO_HOST . '/' . SEO_URI_HELPERS . $page;
		$this->mh = curl_multi_init();
	}
	
	/**
	 * Add a page request.
	 * @param Variable arguments input.  Each argument is
	 * urlencoded and sent as GET request.  Each arguemnt is given
	 * the following variable names: arg0, arg1, arg2, etc...
	 */
	public function addPage(){
		$url;
		
		$args = func_get_args();
		$url = array_shift($args);
		
		$url = $this->loadPage . '?arg0=' . urlencode($url);
		
		foreach($args as $key=>$arg){
			$temp = $key+1;
			$url .= "&arg{$temp}=" . urlencode($arg);
		}
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
		curl_multi_add_handle($this->mh, $ch);
		
		array_push($this->curls, $ch);
	}
	
	/**
	 * Actually perform the request
	 * @return returns an array of page request bodies once all
	 * pages have finished loading.
	 */
	public function exec(){
		// execute the handles
		$running = null;
		do {
			curl_multi_exec($this->mh, $running);
		} while($running > 0);
		
		$result = array();
		
		// get content and remove handles
		foreach($this->curls as $ch) {
			array_push($result, json_decode(curl_multi_getcontent($ch)));
			curl_multi_remove_handle($this->mh, $ch);
		}
			
		// all done
		curl_multi_close($this->mh);
		
		return $result;

	}
	
}

?>