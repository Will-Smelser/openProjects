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
}