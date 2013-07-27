<?php 
require_once SEO_PATH_CLASS . 'GoogleInfo.php';

class GoogleWrap extends GoogleInfo {
	public function GoogleWrap($url){
		parent::__construct($url);
	}
	
	public function getBacklinks($max=100){
		if($max > 100)
			$max = 100;
		
		$result = array();
		foreach($this->google->getBacklinks($this->url, $max) as $entry){	
			array_push($result,
				array(
					'title'=>$entry->title,
					'link'=>$entry->link,
					'snippet'=>$entry->snippet
				)
			);
		}
		return $result;
	}
}

?>