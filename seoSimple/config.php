<?php

$base_path = __DIR__;
if (realpath( $base_path ) !== false) {
	$base_path = realpath($base_path).'/';
}
$base_path = rtrim($base_path, '/').'/';
$base_path = str_replace('\\', '/', $base_path);

define('SEO_API_PATH', $base_path);

?>
