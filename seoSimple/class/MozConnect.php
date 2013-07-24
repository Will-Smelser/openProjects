<?php

class MozServices{
	const OSE = 'http://www.opensiteexplorer.org/links?site=';
	const JD = 'http://www.opensiteexplorer.org/just-discovered?site=';
}

class MozConnect{
	
	private $loginData = array(
		'data[User][redirect]'=>'/',
		'data[User][login_email]'=>'willsmelser@gmail.com',
		'data[User][password]'=>'Will1480'		
	);
	
	private $mozCookie = 'MozCookie.txt';
	private $mozLogin = 'https://moz.com/login';
	
	private function loadCookies(){
		if(!file_exists($this->mozCookie)) {
			$fh = fopen($this->mozCookie, "w");
			fwrite($fh, "");
			fclose($fh);
		}
	}
	
	private function setCurlOpts(&$ch){
		curl_setopt_array($ch, array(
			//CURLOPT_USERAGENT => sprintf('SEOstats %s https://github.com/eyecatchup/SEOstats', SEOstats::BUILD_NO),
			CURLOPT_USERAGENT=>'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/28.0.1500.72 Safari/537.36',
			CURLOPT_RETURNTRANSFER  => 1,
			CURLOPT_CONNECTTIMEOUT  => 30,
			CURLOPT_FOLLOWLOCATION  => 1,
			CURLOPT_MAXREDIRS       => 2,
			CURLOPT_SSL_VERIFYPEER  => 0,
			CURLOPT_COOKIEFILE      =>$this->mozCookie,
			CURLOPT_COOKIEJAR       =>$this->mozCookie,
		));
		
	}
	
	private function login(){
		$ch = curl_init("https://moz.com/login");
		$this->setCurlOpts($ch);
		
		$data = http_build_query($this->loginData);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		
		$response = curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		
		return $response;
	}
	
	private function getSite($service, $url){
		$ch = curl_init($service.urlencode($url));

		$this->setCurlOpts($ch);
		
		$response = curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		
		return $response;
	}
	
	public function getData($service, $url){
		$data = $this->getSite($service, $url);
		if(strpos('hit your daily report limit',$data) > 0){
			$this->login();
			$data = $this->getSite($service, $url);
		}
		
		return $data;
	}
}

?>