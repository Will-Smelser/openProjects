<?php
class ApiCodes {
	static $success = array("200 OK","Success");
	static $badRequest = array("400 Bad Request","Invalid Request");
}

class ApiResponse{
	protected $apiCode;//=ApiCodes::success;
	protected $error=false;
	protected$msg="Default Response";
	protected $data; //should be an associative array
	
	function success($msg, $data){
		$resp = $this;
		$resp->apiCode = ApiCodes::$success;
		$resp->data = $data;
		$resp->msg = $msg;
		$resp->header();
		$resp->doPrint();
	}
	
	function failure($msg){
		$resp = $this;
		$resp->apiCode = ApiCodes::$badRequest;
		$resp->error = true;
		$resp->msg = $msg;
		$resp->data = null;
		$resp->header();
		$this->doPrint();
	}
	
	private function header(){
		header("HTTP/1.1 ".$this->apiCode[0]);
	}
	
	function doPrint(){
		var_dump($this->toArray());
	}
	
	protected function toArray(){
		return array(
			'response'=>$this->apiCode[1],
			'error'=>$this->error,
			'msg'=>$this->msg,
			'data'=>$this->data
		);
	}
}
class ApiResponseJSON extends ApiResponse{
	function doPrint(){
		echo json_encode($this->toArray());
	}
}

?>