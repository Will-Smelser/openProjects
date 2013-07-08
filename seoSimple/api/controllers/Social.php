<?php
require_once SEO_API_PATH . '/wrappers/SocialWrap.php';

class Social extends Controller{
	
	public function Social($method){
		$social = new SocialWrap($_GET['request']);
		$this->exec($social, $method);
	}

}
?>