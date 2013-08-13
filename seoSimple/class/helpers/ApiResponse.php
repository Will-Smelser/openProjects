<?php
class ApiCodes {
	static $success = array("200 OK","Success");
	static $badRequest = array("400 Bad Request","Invalid Request");
	static $systemError = array("500 Internal Server Error", "Internal Error");
}

class ApiResponse{
	protected $apiCode;//=ApiCodes::success;
	protected $error=false;
	protected $msg="Default Response";
	protected $data; //should be an associative array
	
	public function success($msg, $data, $error=false){
		$resp = $this;
		$resp->apiCode = ApiCodes::$success;
		$resp->data = $data;
		$resp->msg = $msg;
		$resp->error = $error;
		$resp->header();
		return $this;
	}
	
	public function failure($msg, $apiCode=null){
		$resp = $this;
		$resp->apiCode = (empty($apiCode))?ApiCodes::$badRequest:$apiCode;
		$resp->error = true;
		$resp->msg = $msg;
		$resp->data = null;
		$resp->header();
		return $this;
	}
	
	public function setData($data){
		$this->data = $data;
	}
	
	private function header(){
		@header("HTTP/1.1 ".$this->apiCode[0]);
	}
	
	function doPrint(){
		print_r($this->toArray());
	}
	
	public function toArray(){
		return array(
			'response'=>$this->apiCode[1],
			'error'=>$this->error,
			'msg'=>$this->msg,
			'data'=>$this->data
		);
	}
	
	/**
	 * jsonpp - Pretty print JSON data
	 *
	 * In versions of PHP < 5.4.x, the json_encode() function does not yet provide a
	 * pretty-print option. In lieu of forgoing the feature, an additional call can
	 * be made to this function, passing in JSON text, and (optionally) a string to
	 * be used for indentation.
	 *
	 * @param string $json  The JSON data, pre-encoded
	 * @param string $istr  The indentation string
	 *
	 * @return string
	 */
	public function jsonpp($json, $istr="    "){
		$result = '';
		for($p=$q=$i=0; isset($json[$p]); $p++){
			$json[$p] == '"' && ($p>0?$json[$p-1]:'') != '\\' && $q=!$q;
			if(strchr('}]', $json[$p]) && !$q && $i--){
				strchr('{[', $json[$p-1]) || $result .= "\n".str_repeat($istr, $i);
			}
			$result .= $json[$p];
			if(strchr(',{[', $json[$p]) && !$q){
				$i += strchr('{[', $json[$p])===FALSE?0:1;
				strchr('}]', $json[$p+1]) || $result .= "\n".str_repeat($istr, $i);
			}
		}
		return $result;
	}
}


class ApiResponseJSON extends ApiResponse{
	function doPrint(){
		return $this->jsonpp(json_encode($this->toArray()));
	}
}

?>