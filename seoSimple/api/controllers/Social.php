<?php
require_once SEO_PATH_WRAPPERS . 'SocialWrap.php';

class Social extends Controller{
	
	public function Social($method, $args=null){
		$social = new SocialWrap($_GET['request']);
		$this->exec($social, $method, $args);
	}

}
?>