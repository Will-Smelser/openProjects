<?php

require_once SEO_API_PATH . "/class/helpers/ApiResponse.php";
require_once SEO_API_PATH . "/class/helpers/Vars.php";

interface Control{
	public function no_method();
}

class Controller implements Control{
	public $skip = array();
	
	public function no_method(){
		$api = new ApiResponseJSON();
		echo $api->failure("Invalid Request - No Method or Class")->doPrint();
	}
	
	public function exec(&$obj, $method, $args=null){
		//run several api methods
		if(strstr($method, '|')){
			$results = array();
			
			foreach(explode('|', $method) as $mthd){
				$api = new ApiResponseJSON();
				if($this->isValidMethod($obj, $mthd, $this->skip))
					$results[$mthd] = $api->success("Success", $obj->$mthd($args))->toArray();
				else
					$results[$mthd] = $api->success("Success", $this->no_method($args))->toArray();
			}
			$api = new ApiResponseJSON();
			echo $api->success("Success", $results)->doPrint();
		//run all api methods
		}else if(stripos($method,'all')!==false){
			$results = array();
			
			foreach(get_class_methods($obj) as $mthd){
				if(stripos($method,'~'.$mthd) === false && $this->isValidMethod($obj, $mthd, $this->skip)){
					$api = new ApiResponseJSON();
					$results[$mthd] = $api->success("Success", $obj->$mthd($args))->toArray();
				}
			}
			
			$api = new ApiResponseJSON();
			echo $api->success("Success", $results)->doPrint();
			
		//run a specific api method
		}else if(!$this->isValidMethod($obj, $method, $this->skip)){
			$this->no_method();
		
		//method did not exist for the given class
		}else{
			$result = $obj->$method($args);
			$api = new ApiResponseJSON();
			echo $api->success("Success", $result)->doPrint();
		}
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