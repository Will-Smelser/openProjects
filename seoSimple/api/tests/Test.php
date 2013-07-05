<?php

$host = $_SERVER['SERVER_NAME'];

$root = realpath($_SERVER['DOCUMENT_ROOT']);
$dir = str_replace($root, '', realpath(__DIR__));
$dir = str_replace('\\','/',$dir);

define('TEST_URL', 'http://'.$host.$dir);

class Test {
	public function doTest($ctrl, $method, $request){
		$resp = $this->doRequest($request);
		
		echo "<tr><td colspan='3'><h3>$request</h3></td>";
		echo "<tr><td valign='top'>$ctrl<td valign='top'>$method<td>";
		
		if(empty($resp)){
			echo "ERROR - EMPTY RESPONSE";
		}else{
			echo ''. str_replace(" ","&nbsp;&nbsp;",str_replace("\n","<br>\n",htmlentities($resp)));
		}
		
		echo '<tr class="seperator"><td colspan="3">&nbsp;';
	}
	
	public function doRequest($request){
		
		//make a curl request
		$curl = curl_init($request);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($curl);
		curl_close($curl);
		
		return $result;
	}
}
?>
