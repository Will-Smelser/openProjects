<?php
require_once SEO_API_PATH . '../wrappers/Social.php';

class Social extends Controller{
	
	public function Social($method){
		$social = new Social($parser, $_GET['request']);
		$this->exec($social, $method);
	}

}
?>