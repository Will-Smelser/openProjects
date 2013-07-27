<?php

$base_path = __DIR__;
if (realpath( $base_path ) !== false) {
	$base_path = realpath($base_path).'/';
}
$base_path = rtrim($base_path, '/').'/';
$base_path = str_replace('\\', '/', $base_path);

define('SEO_API_PATH', $base_path);
define('SEO_HOST', $_SERVER['HTTP_HOST']);

if(preg_match('/\.local/i',$_SERVER['HTTP_HOST'])){
	define('SEO_CLASS_HELPERS','/seoSimple/class/helpers/');
}else{
	define('SEO_CLASS_HELPERS','/openProjects/seoSimple/class/helpers/');
}


$host = str_replace('\\','/',$_SERVER['SERVER_NAME']);
$base_path = str_replace('\\','/',__DIR__);

$last = array_pop(explode('/',$_SERVER['DOCUMENT_ROOT']));
$start = strpos($base_path,$last)+strlen($last);

$base_path = substr($base_path, $start);

if (realpath( $base_path ) !== false) {
	$base_path = realpath($base_path).'/';
}

$base_path = '/'.trim($base_path, '/').'/';

$host_loc = $host . $base_path;

define('SEO_API_PATH_URL', $base_path . 'api/');


?>
