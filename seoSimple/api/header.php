<?php
$url = str_replace('\\','/',$_GET['url']);
$_VARS = explode('/',$url);

$base_path = __DIR__;
if (realpath( $base_path ) !== false) {
	$base_path = realpath($base_path).'/';
}
$base_path = rtrim($base_path, '/').'/';
$base_path = str_replace('\\', '/', $base_path);

$_CONTROLLER = isset($_VARS[0]) ? ucfirst($_VARS[0]) : 'Error';
$_METHOD = isset($_VARS[1]) ? $_VARS[1] : 'no_method';
define('SEO_API_PATH', $base_path);

@array_shift($_VARS);
@array_shift($_VARS);

//verify the controller and method exist
if(!file_exists(SEO_API_PATH . 'controllers/' . $_CONTROLLER . '.php'))
	$_CONTROLLER = 'Error';

?>