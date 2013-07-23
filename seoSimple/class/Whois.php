<?php
class Whois
{
	const timeout = 30;
	const whoishost = 'reports.internic.net/cgi/whois';//'whois.internic.net';
	
	const keys = 'domain name,registrar,whois server,referral url,name server,name server,updated date,creation date,expiration date';

	
	
	public static function lookup($domain){

		$result = "";
		$errno = 0;
		$errstr='';

		$result = file_get_contents('http://'.Whois::whoishost.'?type=domain&whois_nic=inedo.com');
		 
		$raw = strtolower(strip_tags($result));
		return self::parse($raw);
	}
	
	private static function parse($str){
		$result = array();
		foreach(explode(',',self::keys) as $key){
			preg_match('@'.$key.':[\s+]?(?P<value>.*)@i', $str, $matches);
			
			if(count($matches) && isset($matches['value']))
				$result[str_replace(' ','_',$key)] = trim($matches['value']);
		}
		return $result;
	}
}

?>