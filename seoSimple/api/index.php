<?php

require_once 'header.php';
require_once 'controllers/Controller.php';
require_once SEO_API_PATH . 'api/controllers/' . $_CONTROLLER . '.php';

//controller will always exist, thanks header.php
//controller will handle actual work and call method
$controller = new $_CONTROLLER($_METHOD, $_VARS);

?>