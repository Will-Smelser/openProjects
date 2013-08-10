<?php

/**
 * Requires pear and following pear package:
 * 		http://pear.php.net/package/Services_W3C_HTMLValidator/
 * 		$>pear install Services_W3C_HTMLValidator
 */

require_once 'Services/W3C/HTMLValidator.php';
require_once SEO_PATH_HELPERS.'Utility.php';

class ServerInfo{
	protected $url;
	public $rawHeader;
	
	private $response;
	public $header;
	
	private $lastW3Cerrors;
	private $lastW3Cwarnings;
	
	private $loadTime;
	
	public function ServerInfo($url){
		$this->url = $url;	
		$this->doRequest();
		$this->parseHeader();
	} 
	
	//TODO:check for robots.txt
	
	/**
	 * Just wraps making curl requests
	 */
	private function doRequest(){
		
		$ch = curl_init($this->url);
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_NOBODY, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Accept-Encoding: gzip, deflate'
		));
		
		$start = Utility::getTime();
		
		$this->rawHeader = curl_exec($ch);
		
		$this->loadTime = Utility::getEnd($start);
		
		curl_close($ch);
	}
	
	/**
	 * Get the page load time in seconds
	 */
	public function getLoadTime(){
		return $this->loadTime;
	}
	
	/**
	 * Parse header, first line is put $this->request and
	 * other headers are stored in $this->header as key:value
	 * pairs where the key has been mad lowercase.
	 *    - strtolower(<field name>) => <value>
	 */
	private function parseHeader(){
		$lines = explode("\n", $this->rawHeader);
		$this->response = rtrim(array_shift($lines),"\r");
		
		foreach($lines as $line){
			$line = trim($line);
			$parts = explode(':', $line);
			if(count($parts) > 0){
				$key = strtolower(array_shift($parts));
				if(!empty($key) && count($parts) > 0)
					$this->header[$key] = trim(implode(':', $parts));
			}
		}
	}
	
	/**
	 * Check if the server supports gzip compression
	 * @return boolean True if is does, false otherwise
	 */
	public function isGzip(){
		if(!isset($this->header['content-encoding']))
			return false;
		else
			return (preg_match('/gzip/i',$this->header['content-encoding']));
	}
	
	/**
	 * Return the HTTP "Server" field name or NULL if none existed.
	 * 
	 * @return String|NULL
	 */
	public function getServer(){
		return $this->getHeaderField('server');
	}
	
	
	/**
	 * Get any header field returned by server
	 * @param unknown $field
	 * @return String|NULL
	 */
	public function getHeaderField($field){
		$field = strtolower($field);
		return (isset($this->header[$field]) ? $this->header['server'] : null);
	}
	
	/**
	 * Get the first line of http response header
	 */
	public function getHeaderResponseLine(){
		return $this->response;
	}
	
	/**
	 * Check if site has robots.txt file
	 * @return boolean
	 */
	public function checkRobots(){
		$info = parse_url($this->url);
		@$result = file_get_contents('http://'.$info['host'].'/robots.txt');
		return ($result === false) ? false : true;
	}
	
	private $w3cCalled = false;
	private $w3cValid = false;
	private function initW3c(){
		if(!$this->w3cCalled){
			$this->w3cCalled = true;
			$v = new Services_W3C_HTMLValidator();
			$r = $v->validate($this->url);
			if($r !== false){
				$this->lastW3Cerrors = $r->errors;
				$this->lastW3Cwarnings = $r->warnings;
				$this->w3cValid = $r->isValid();
			}else{
				throw new Exception("Request to W3C failed.");
			}
		}
	}
	
	/**
	 * Validates using W3C pear package
	 * @return boolean True on success, False on failure
	 * @throws Exception
	 */
	public function validateW3C(){
		$this->initW3c();
		return $this->w3cValid;
	}
	
	/**
	 * Return the error array from last validateW3C() request.
	 * @see validateW3C()
	 * @see http://pear.php.net/package/Services_W3C_HTMLValidator/docs/latest/Services_W3C_HTMLValidator/Services_W3C_HTMLValidator_Error.html
	 * @see http://pear.php.net/package/Services_W3C_HTMLValidator/docs/latest/Services_W3C_HTMLValidator/Services_W3C_HTMLValidator_Message.html
	 */
	public function getValidateW3Cerrors(){
		$this->initW3c();
		return $this->lastW3Cerrors;
	}
	
	/**
	 * Return the error array from last validateW3C() request.
	 * @see validateW3C()
	 * @see http://pear.php.net/package/Services_W3C_HTMLValidator/docs/latest/Services_W3C_HTMLValidator/Services_W3C_HTMLValidator_Error.html
	 * @see http://pear.php.net/package/Services_W3C_HTMLValidator/docs/latest/Services_W3C_HTMLValidator/Services_W3C_HTMLValidator_Message.html
	 */
	public function getValidateW3Cwarnings(){
		$this->initW3c();
		return $this->lastW3Cwarnings;
	}
}

?>