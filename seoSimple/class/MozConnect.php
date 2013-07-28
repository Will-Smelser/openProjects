<?php

/**
 * A list of moz opensite explorer endpoint to request data from.
 * @author Will
 *
 */
class MozServices{
	const OSE = 'http://www.opensiteexplorer.org/links?site=';
	const JD = 'http://www.opensiteexplorer.org/just-discovered?site=';
}

/**
 * Will attempt to get load data anonymously, but some
 * things require a login.  It a login is required this
 * will login using the given credentials.
 * 
 * Once login is complete, the session is stored to be used
 * later.
 * 
 * This operation can take some time, since if a login is required,
 * moz redirects several time, and then a login has to happen, which
 * then a page request cab be made.  Not very efficient.
 * @author Will
 *
 */
class MozConnect{
	
	private $formKeyRedirect = 'data[User][redirect]';
	private $formKeyEmail = 'data[User][login_email]';
	private $formKeyPass = 'data[User][password]';
	
	private $user, $pass;
	
	private $base64user;
	private $cookieFile;
	
	private $mozCookie = 'MozCookie.txt';
	private $mozLogin = 'https://moz.com/login';
	
	private $loginAttempt = 0;
	
	
	public function MozConnect($user, $pass){
		$this->user = $user;
		$this->pass = $pass;
		$this->base64user = base64_encode($user);
		$this->cookieFile = SEO_PATH_HELPERS . 'moz-'.$this->base64user.'.txt'; 
		
		$this->loadCookies();
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
			CURLOPT_MAXREDIRS       => 5,
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
		if($this->loginAttempt > 2){
			echo "LOGIN FAILED\n\n";
			return 'Failed to login.';
		}
		
		$this->loginAttempt++;
		
		$ch = curl_init($this->mozLogin);
		$this->setCurlOpts($ch);
		
		$temp = array(
			$this->formKeyRedirect=>'/',
			$this->formKeyEmail=>$this->user,
			$this->formKeyPass=>$this->pass,
			'data[User][remember]'=>1
		);
		
		
		$data = http_build_query($temp);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		
		$response = curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		
		if(curl_errno($ch))
		{
			$response = 'error:' . curl_error($ch);
			//TODO: Handle Error
		}
		
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
		
		if(curl_errno($ch))
		{
			//TODO: Handle this error
			$response = 'error:' . curl_error($ch);
		}
		
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
		if(strpos($data,'hit your daily report limit') > 0){
			$data = $this->getSite($service, $url);
		}
		return $data;
	}
}

?>