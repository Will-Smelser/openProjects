<?php

require_once SEO_PATH_CLASS . 'MozConnect.php';
require_once SEO_PATH_CLASS . 'HtmlParser.php';

class MozWrap{
	
	public $url;
	public $moz;
	
	public function MozWrap($url){
		$this->url = $url;
		
		$this->moz = new MozConnect('willsmelser@gmail.com','Will1480');
		
	}
	
	/**
	 * This is the moz link main page data
	 * @return multitype:NULL
	 */
	public function getMozLinks(){
		$html = $this->moz->getData(MozServices::OSE, $this->url);
		file_put_contents('moz-links.txt',$html);
		$parser = new HtmlParser($html,$this->url);
		$data = $parser->getTags('td');
		
		
		
		return array(
			'domainAuthority'=>trim(strip_tags($data[0]->text)),
			'pageAuthority'=>trim(strip_tags($data[1]->text)),
			'linkingRootDomains'=>trim(strip_tags($data[2]->text)),
			'totalInboundLinks'=>trim(strip_tags($data[3]->text)),
		);
	}
	
	/**
	 * These are the SEOmoz just discovered data
	 * @return multitype:
	 */
	public function getMozJustDiscovered(){
		$html = $this->moz->getData(MozServices::JD, $this->url);
		file_put_contents('just-discovered.txt',$html);
		$parser = new HtmlParser($html,$this->url);
		
		$tables = $parser->getTags('table');
		
		$results = array();
		
		if(count($tables) > 0){
			$table = null;
			foreach($tables as $tbl){
				if(isset($tbl->attributes['id']) && $tbl->attributes['id']==='results'){
					$table = $tbl;
					break;	
				}
			}
			
			//moz has data
			if(!empty($table)){
				
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
			//no moz data
			}else{
				array_push($results, array(
				'link'=>'No Data',
				'text'=>'No Data',
				'pageAuthority'=>'No Data',
				'DomainAuthority'=>'No Data',
				'DiscoveryTime'=>'No Data'
				));
			}
		}
		return $results;
	}
}

?>
