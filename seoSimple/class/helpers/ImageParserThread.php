<?php
include '../ImageParser.php';

$url = urldecode($_GET['url']);
$hash = $_GET['arg1'];
$width= $_GET['arg2'];
$height = $_GET['arg3'];

$image;
if(preg_match('@^https?://@i',$url)){
	$turl = str_replace('https://','http://',$url);
	$image = imagecreatefromstring(file_get_contents($turl));
}elseif(preg_match('/^data/',$url)){
	$image = imagecreatefromstring($url);
}else{
	$url = 'http://'.$img->host.'/'.ltrim($img->attributes['src'],'/\\');
	$image = imagecreatefromstring(file_get_contents($url));
}

$resp = new ImageLoadResponse();
$resp->hash = $hash;
$resp->url = $url;
$resp->result = ImageParser::respond($image, $width, $height);

echo json_encode($resp);
?>