<?php
require_once '../config.php';

$url = str_replace('\\','/',$_GET['url']);
$_VARS = explode('/',$url);

$_CONTROLLER = isset($_VARS[0]) ? ucfirst($_VARS[0]) : 'Error';
$_METHOD = isset($_VARS[1]) ? $_VARS[1] : 'no_method';

@array_shift($_VARS);
@array_shift($_VARS);

//verify the controller and method exist
if(!file_exists(SEO_PATH_CONTROLLERS . $_CONTROLLER . '.php'))
	$_CONTROLLER = 'Error';

//cleanup the request var
if(!preg_match('@^(https?://)@i',$_GET['request']))
	$_GET['request'] = 'http://'.ltrim($_GET['request'],'/');

?>