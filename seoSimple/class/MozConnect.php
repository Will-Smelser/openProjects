<?php

class MozServices{
	const OSE = 'http://www.opensiteexplorer.org/links?site=';
	const JD = 'http://www.opensiteexplorer.org/just-discovered?site=';
}

class MozConnect{
	
	private $formKeyRedirect = 'data[User][redirect]';
	private $formKeyEmail = 'data[User][login_email]';
	private $formKeyPass = 'data[User][password]';
	
	private $user, $pass;
	
	private $base64user;
	private $cookieFile;
	
	private $mozCookie = 'MozCookie.txt';
	private $mozLogin = 'https://moz.com/login';
	
	
	public function MozConnect($user, $pass){
		$this->user = $user;
		$this->pass = $pass;
		$this->base64user = base64_encode($user);
		$this->cookieFile = 'moz-'.$this->base64user.'.txt'; 
	}
	
	private function loadCookies(){
		if(!file_exists($this->cookieFile)) {
			$fh = fopen($this->cookieFile, "w");
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
			CURLOPT_COOKIEFILE      =>$this->cookieFile,
			CURLOPT_COOKIEJAR       =>$this->cookieFile,
		));
		
	}
	
	/**
	 * Login to data
	 * @return unknown
	 */
	private function login(){
		$ch = curl_init($this->$mozLogin);
		$this->setCurlOpts($ch);
		
		$temp = array(
			$this->formKeyRedirect=>'/',
			$this->formKeyEmail=>$this->user,
			$this->formKeyPass=>$this->pass
		);
		
		$data = http_build_query($temp);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		
		$response = curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		
		return $response;
	}
	
	/**
	 * Wraps the curl request to load the given page
	 * @param MozServices $service The service to request
	 * @param String $url The non urlencoded url to request moz data on.
	 * @return unknown
	 */
	private function getSite($service, $url){
		$ch = curl_init($service.urlencode($url));

		$this->setCurlOpts($ch);
		
		$response = curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		
		return $response;
	}
	
	/**
	 * Attempt to load the HTML page from SEOmoz, will
	 * login if forced to.  This means multiple page requests, so
	 * this can take some time.
	 * @param MozServices $service The moz service endpoint attempt to get HTML from
	 * @param String $url The url you want to load data from.  Raw url, no encoded urls.
	 * @return String A string of HTML data
	 */
	public function getData($service, $url){
		$data = $this->getSite($service, $url);
		
		//been forwarded to a login page
		if(strpos('hit your daily report limit',$data) > 0){
			$this->login();
			$data = $this->getSite($service, $url);
		}
		
		return $data;
	}
}

?>