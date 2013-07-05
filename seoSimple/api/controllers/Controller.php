<?php

require_once SEO_API_PATH . "../class/helpers/ApiResponse.php";
require_once SEO_API_PATH . "../class/helpers/Vars.php";

interface Control{
	public function no_method();
}

class Controller implements Control{
	public function no_method(){
		(new ApiResponseJSON())->failure("Invalid Request - No Method or Class");
	}
	
	public function exec(&$obj, $method){
		
		if(method_exists($obj, $method)){
			$result = $obj->$method();
			(new ApiResponseJSON())->success("Success", $result);
		}else if(method_exists($obj, $method)){
			$result = $obj->$method();
			(new ApiResponseJSON())->success("Success", $result);
		}else{
			$this->no_method();
		}
	}
}