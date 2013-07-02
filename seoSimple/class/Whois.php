<?php
class whois
{
	const timeout = 30;
	const whoishost = 'whois.internic.net';
	
	const keys = 'domain name,registrar,whois server,referral url,name server,name server,updated date,creation date,expiration date';

	
	
	public static function lookup($domain){

		$result = "";
		$errno = 0;
		$errstr='';

		$fd = fsockopen(whois::whoishost,43, $errno, $errstr, whois::timeout);

		if ($fd){
			fputs($fd, $domain."\015\012");
			while (!feof($fd))    {
				$result .= fgets($fd,128) . "<br />";
			}
			fclose($fd);
		}
		 
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