<?php 
require_once SEO_PATH_CLASS . 'GoogleInfo.php';

class GoogleWrap extends GoogleInfo {
	public function GoogleWrap($url){
		$url = preg_replace('@https?://@i','',$url);
		parent::__construct($url);
	}
	
	/**
	 * Get backlinks from google.  This can be a costly request
	 * requiring multiple requests.  Therefor a max of 100, or 10 requests
	 * is allowed.
	 * @param number $max
	 * @return multitype:
	 */
	public function getBacklinks($max=100){
		if(is_array($max))
			if(count($max) > 0)
				$max = array_shift($max)*1.0;
			else
				$max = 100;
		if($max > 100)
			$max = 100;
		
		$uniqueDomains = array();
		$result = array();
		$composite = "";

		$backlinks = $this->google->getBacklinks($this->url, $max);
		
		if(!empty($backlinks)){
			foreach($backlinks as $entry){
				
				array_push($result,
					array(
						'title'=>$entry->title,
						'link'=>$entry->link,
						'snippet'=>$entry->snippet
					)
				);
				
				//track domains
				$domain = parse_url($entry->link);
				$host1 = trim($domain['host']);
				if(!isset($uniqueDomains[$host1]))
					$uniqueDomains[$host1] = 0;
				
				$uniqueDomains[$host1]++;
			}
			
			//make some composite data
			foreach($uniqueDomains as $host=>$count)
				$composite .= $host . "($count), ";
			
			$composite = rtrim($composite,', ');
		};
		return array(
				'domainTotals'=>count($uniqueDomains),
				'domainData'=>$uniqueDomains,
				'domainComposite'=>$composite,
				'backlinks'=>$result
		);
	}
}

?>