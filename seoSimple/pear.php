<?php
require_once 'class/ServerInfo.php';

$info = new ServerInfo('www.inedo.com');


var_dump($info->isGzip());
var_dump($info->header);
var_dump($info->validateW3C());
var_dump($info->getValidateW3Cerrors());
var_dump($info->getValidateW3Cwarnings());
?>
