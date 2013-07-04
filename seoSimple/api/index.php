<?php

require_once 'header.php';
require_once 'controllers/Controller.php';
require_once SEO_API_PATH . 'controllers/' . $_CONTROLLER . '.php';

//controller will always exist, thanks header.php
$controller = new $_CONTROLLER($_METHOD);

?>