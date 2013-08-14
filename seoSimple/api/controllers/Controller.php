<?php

require_once SEO_PATH_HELPERS . 'ApiResponse.php';
require_once SEO_PATH_HELPERS . 'Vars.php';

$FATAL_ERROR = true;

class Controller{
	public $skip = array();
	
	private $error = false;
	
	public function __construct(){
		global $FATAL_ERROR;
		set_error_handler('Controller::handleError');
		register_shutdown_function('Controller::shutdown');
		ERROR_REPORTING(0);
	}
	
	public function __destruct(){
		restore_error_handler();
	}
	
	public static function shutdown(){
		global $FATAL_ERROR;
		if($FATAL_ERROR){
			$api = new ApiResponseJSON();
			echo $api->failure("Fatal Internal System Error - No Trace Available",ApiCodes::$systemError)->doPrint();
		}
	}
	
	private function execGroup(&$obj, $method, $args){
		$results = array();
			
		foreach(explode('|', $method) as $mthd){
			$api = new ApiResponseJSON();
			
			try{
				if($this->isValidMethod($obj, $mthd, $this->skip))
					$results[$mthd] = $api->success("Success", $obj->$mthd($args))->toArray();
				else{
					throw new BadMethodCallException("No Method - $mthd");
				}
			}catch(Exception $e){
				$this->error = true;
				$temp = new ApiResponseJSON();
				$err = Controller::errMsg($e->getMessage(),$e->getLine(),$e->getFile());
				$results[$mthd] = $temp->failure($err)->toArray();
			}
		}
			
		return $results;
	}
	
	public function execAll(&$obj, $method, $args){
		$results = array();
			
		foreach(get_class_methods($obj) as $mthd){
			if(stripos($method,'~'.$mthd) === false && $this->isValidMethod($obj, $mthd, $this->skip)){
				try{
					$results[$mthd] = $api->success("Success", $obj->$mthd($args))->toArray();
				}catch(Exception $e){
					$this->error = true;
					$temp = new ApiResponseJSON();
					$err = Controller::errMsg($e->getMessage(),$e->getLine(),$e->getFile());
					$results[$mthd] = $temp->failure($err)->toArray();
				}
			}
		}
			
		return $results;
	}
	
	public function execWrapper(&$obj, $method, $args){
		if(strstr($method, '|'))
			return $this->execGroup($obj, $method, $args);
			
		//run all api methods
		else if(stripos($method,'all')!==false)
			return $this->execAll($obj, $method, $args);
				
		//method doesnt exist, or is a skip method
		else if(!$this->isValidMethod($obj, $method, $this->skip))
			throw new BadMethodCallException("No Method - $method");
			
		//just run method
		return $obj->$method($args);
	}
	
	public function exec(&$obj, $method, $args=null){
		global $FATAL_ERROR;
		
		$result = null;
		$api = new ApiResponseJSON();
		
		try{
			$result = $this->execWrapper($obj, $method, $args);
			
		//if the exception made its way up here then it is a top level error
		//meaning it is most likely the failure of a single method call
		}catch(Exception  $e){
			$this->error = true;
			
			$api->setData($result);
			echo $api->failure(Controller::errMsg($e->getMessage(),$e->getLine(),$e->getFile()))->doPrint();
			
			$FATAL_ERROR = false;
			return;
		}

		//let the shutdown function know there were no untrapped errors
		$FATAL_ERROR = false;
		
		echo $api->success("Success", $result, $this->error)->doPrint();
	}

	/**
	 * Just pretty print an error into a single string.
	 * @param unknown $msg
	 * @param unknown $line
	 * @param unknown $file
	 * @return string
	 */
	public static function errMsg($msg, $line, $file){
		return 'CLASS: '.str_replace('.php','',basename($file)).', LINE: '.$line.', MSG: '.$msg;
	}
	
	/**
	 * This error handler exists only to capture warnings and ensure they get
	 * passed to user.
	 * 
	 * Otherwise warnings would either get hidden or printed before returning JSON object
	 * and breaking the api.  This allows the api to function regardless.  
	 * 
	 * Also, we are still in BETA and we want information about
	 * errors, not just to hide them.  Normal exceptions are passed on as usual.
	 * 
	 * @param unknown $errno
	 * @param unknown $errstr
	 * @param unknown $errfile
	 * @param unknown $errline
	 * @throws ErrorException
	 * @return boolean
	 */
	public static function handleError($errno, $errstr, $errfile, $errline){
		
		//trap warnings also
		switch($errno){
			case E_WARNING:
			case E_NOTICE:
			case E_USER_NOTICE:
			case E_USER_WARNING:
				throw new ErrorException(Controller::errMsg($errstr,$errline,$errfile), 0, $errno, $errfile, $errline);
		}
		
		return false;
	}
	
	/**
	 * Check if the method exists
	 * @param unknown $obj
	 * @param unknown $method
	 * @param unknown $skip
	 * @return boolean
	 */
	public function isValidMethod($obj, $method, &$skip){
		if(get_class($obj) === $method){
			return false;
		}else if(!method_exists($obj, $method))
			return false;
		else if(isset($skip) && in_array($method, $skip))
			return false;
		else
			return true;
	}
}