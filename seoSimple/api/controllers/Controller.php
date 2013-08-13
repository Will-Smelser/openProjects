<?php

require_once SEO_PATH_HELPERS . 'ApiResponse.php';
require_once SEO_PATH_HELPERS . 'Vars.php';

class Controller{
	public $skip = array();
	
	private $error = false;
		
	private function callWrapper(&$obj, $method, $args=null){
		
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
				$results[$mthd] = $temp->failure($e->getMessage())->toArray();
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
					$results[$mthd] = $temp->failure($e->getMessage())->toArray();
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
	
	public function handleException(Exception &$e, &$obj, $method, $args){
		$this->error = true;
		if($e instanceof BadMethodCallException){
			return array();
		}elseif($e instanceof InvalidArgumentException){
			
		}elseif($e instanceof OutOfRangeException){
			
		}elseif($e instanceof Exception){
			
		}
	}
	
	public function exec(&$obj, $method, $args=null){
		$result = null;
		$api = new ApiResponseJSON();
		
		try{
			$result = $this->execWrapper($obj, $method, $args);
			
		//if the exception made its way up here then it is a top level error
		//meaning it is most likely the failure of a single method call
		}catch(Exception $e){
			$this->error = true;
			$result = $this->handleException($e, $obj, $method, $args);
			echo $api->failure("Method Execution Failed - $method")->doPrint();
			return;
		}
		echo $api->success("Success",$result,$this->error)->doPrint();
	}
	
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