<?php
require_once SEO_PATH_VENDORS . 'seostats/src/seostats.php';

class SemRushWrap{
	public $url;
	public $sem;
	public $semDR; //domain rank
	public $semOK; //organic key words
	public $stats;
	
	public $params;
	public $paramsExt = array(		
		"DR" => array(
				"Ac" => "Estimated Ad Expense",
				"Ad" => "TOP 20 Key Word Count",
				"At" => "Estimated Ad Traffic",
				"Dn" => "Site Name",
				"Dt" => "Report Date",
				"Np" => "The number of keywords for which the site is displayed in search results next to the analyzed site.",
				"Oa" => "Number of Ad Traffic Buyers",
				"Oc" => "Cost of purchasing traffic",
				"Oo" => "Number of Competitors",
				"Or" => "TOP20 Organic Key Word Count",
				"Ot" => "Estimated traffic from keywords",
				"Rk" => "The SEMRush Rank",
				//before and after
				"Oc-before"=>'$',"Oc-after"=>'/month',
		),
		"OK" => array(
				"Co" => "Competition for Search Term",
				"Cp" => "Average Pay Per Click",
				"Nr" => "Search Result Count",
				"Nq" => "Search Frequency",
				"Ph" => "Search Query in TOP 20",
				"Po" => "Search Result Position",
				"Pp" => "Search Result Position (prior)",
				"Tc" => "Organic Traffic Compared to Ad Traffic",
				"Tr" => "Organic Traffic to All Traffic Ratio",
				"Ur" => "Search Landing Page",
				//before and after
				'Cp-before'=>'$',
				'Nq-after'=>'/month',
				'Tc-after'=>'/month'
		)
	);
	
	public function SemRushWrap($url){
		$this->stats = new SEOstats($url);
		$this->sem =  $this->stats->SEMRush();
		$this->url = $url;
		$this->params = $this->sem->getParams();
	}
	
	private function getPhrase($type='OK', $key=null, $data=null){
		if(isset($this->paramsExt[$type][$key.'-before']))
			$data = $this->paramsExt[$type][$key.'-before'] . $data;
		
		if(isset($this->paramsExt[$type][$key.'-after']))
			$data .= $this->paramsExt[$type][$key.'-after'];
		
		return $data;
	}
	
	public function getKeyWordsReport(){
		$data = $this->sem->getOrganicKeywords();
		
		$result = array();
		foreach($data['data'] as $index=>$entry){
			$result[$index] = array();
			foreach($entry as $key=>$val){
				$temp = array(
						'short'=>$this->paramsExt['OK'][$key],
						'desc'=>$this->params['OrganicKeywordReports'][$key],
						'data'=>$this->getPhrase('OK',$key,$val)
					);
				$result[$index][$key]=$temp;
			}
		}
		return $result;
	}
	
	/**
	 * Run the DomainReport
	 * @return multitype:
	 */
	public function getDomainReport(){
		$data = $this->sem->getDomainRank();
		
		$result = array();
		
		if(!empty($data)){
			
			foreach($data as $key=>$val){
				if(isset($this->params['DomainReports'][$key])){
					$temp = array(
							'short'=>$this->paramsExt['DR'][$key],
							'desc'=>$this->params['DomainReports'][$key],
							'data'=>$this->getPhrase('DR',$key,$val)
						);
					array_push($result,$temp);
				}
			}
		}	
		return $result;
	}
}
?>
