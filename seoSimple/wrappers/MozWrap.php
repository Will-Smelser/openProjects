<?php

require_once SEO_API_PATH . '/class/MozConnect.php';
require_once SEO_API_PATH . '/class/HtmlParser.php';

class MozWrap{
	
	public $url;
	public $moz;
	
	public function MozWrap($url){
		$this->url = $url;
		
		$this->moz = new MozConnect();
		
	}
	
	public function getMozLinks(){
		
		$parser = new HtmlParser($this->moz->getData(MozServices::OSE, $this->url),$this->url);
		
		$data = $parser->getTags('td');
		
		return array(
			'domainAuthority'=>trim(strip_tags($data[0]->text)),
			'pageAuthority'=>trim(strip_tags($data[1]->text)),
			'linkingRootDomains'=>trim(strip_tags($data[2]->text)),
			'totalInboundLinks'=>trim(strip_tags($data[3]->text)),
		);
	}
	
	public function getMozJustDisc(){
		$parser = new HtmlParser($this->moz->getData(MozServices::JD, $this->url),$this->url);
		
		$tables = $parser->getTags('table');
		
		$results = array();
		
		if(count($tables) > 0){
			$table;
			foreach($tables as $tbl){
				if($tbl->attributes['id']=='results'){
					$table = $tbl;
					break;	
				}
			}
			
			$p2 = new HtmlParser($table->raw, $this->url);
			$rows = $p2->getTags('tr');
			
			foreach($rows as $tr){
				$p3 = new HtmlParser($tr->raw, $this->url);
				$tds = $p3->getTags('td');
				
				if(!empty($tds[0]->text)){
					array_push($results, array(
						'link'=>trim(strip_tags($tds[0]->text)),
						'text'=>trim(strip_tags($tds[1]->text)),
						'pageAuthority'=>trim(strip_tags($tds[2]->text)),
						'DomainAuthority'=>trim(strip_tags($tds[3]->text)),
						'DiscoveryTime'=>preg_replace('/\s+/',' ',trim(strip_tags($tds[4]->text)))
					));
				}
				
			}
		}
		return $results;
	}
	
	
	/*
	public function getInboundLinks(){
		$xpath = new DOMXpath($this->doc);
		
		$elements = $xpath->query("/html/body/div[@id='content']/table[@id='results']/tbody/tr");
		foreach($elements as $el){
			$children = $el->childNodes;
			for($i=0; $i < $children->length; $i++){
				var_dump($children->item($i));
			}
		}
		
	}
	*/
}

?>
