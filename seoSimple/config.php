<?php
//look at current directory to determine root directory
$base_path = __DIR__;
if (realpath( $base_path ) !== false) {
	$base_path = realpath($base_path);
}
$base_path = rtrim($base_path, '/').'/';
$base_path = str_replace('\\', '/', $base_path);

define('SEO_PATH_ROOT', $base_path);
define('SEO_PATH_API',SEO_PATH_ROOT.'api/');
define('SEO_PATH_CONTROLLERS',SEO_PATH_API.'controllers/');
define('SEO_PATH_CLASS',SEO_PATH_ROOT.'class/');
define('SEO_PATH_WRAPPERS',SEO_PATH_ROOT.'wrappers/');
define('SEO_PATH_HELPERS',SEO_PATH_CLASS.'helpers/');
define('SEO_PATH_VENDORS',SEO_PATH_ROOT.'vendors/');


$root = $_SERVER['DOCUMENT_ROOT'];
if(realpath($root) !== false)
	$root = realpath($root);

$docRoot = str_replace('\\','/',$root);
$docRoot = str_replace($docRoot,'',SEO_PATH_ROOT);

define('SEO_HOST', $_SERVER['HTTP_HOST']);
define('SEO_URI_BASE',ltrim($docRoot,'/'));
define('SEO_URI_API', SEO_URI_BASE.'api/');
define('SEO_URI_CLASS',SEO_URI_BASE.'class/');
define('SEO_URI_HELPERS', SEO_URI_CLASS.'helpers/');
define('SEO_URI_REPORTS', SEO_URI_BASE . 'reports/');

error_reporting(E_ALL);
?>
