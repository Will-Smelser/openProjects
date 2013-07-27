<?php

require_once 'header.php';
require_once SEO_PATH_CONTROLLERS . 'Controller.php';
require_once SEO_PATH_CONTROLLERS . $_CONTROLLER . '.php';

//controller will always exist, thanks header.php
//controller will handle actual work and call method
$controller = new $_CONTROLLER($_METHOD, $_VARS);

?>