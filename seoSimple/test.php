<?php

/*
include "class/WordCount.php";

$str = file_get_contents("http://www.inedo.com/devops");
		
$counter = new WordCount();

print_r($counter->getCount($str));
*/


//include "class/HtmlParser.php";

//$str = file_get_contents("http://gregfranko.com/jquery.selectBoxIt.js/index.html");

//var_dump($str);

//$str = "<div id='test'>hello<div id='next'>world</div></div> ";

//$parser = new HtmlParser($str);
//var_dump($parser->getTags('meta'));
//print_r($parser->getMeta());


//preg_match('@(?:\<title[\s+]?\>)@i',
//	"<title>hello mother</title> fucker", $matches);
//print_r($matches);
/*
require_once "class/Google.php";

$url = "http://inedo.com";
$stats =  new Google($url);


var_dump($stats->getPageRank());
$stats->getBacklinks(100);
*/
/*
include "class/PageLoad.php";

$loader = new PageLoad();
$loader->addPage("http://www.google.com");
$loader->addPage("http://www.bing.com");
$loader->addPage("http://www.yahoo.com");
$loader->addPage("http://www.kafekerouac.com");
$result = $loader->exec();

var_dump($result);
*/

include "class/HtmlParser.php";
include "class/ImageParser.php";

$url = "http://www.inedo.com";
$str = file_get_contents($url);

$parser = new HtmlParser($str, $url);
$imgs = $parser->getTags('img');

foreach($imgs as $img){
	$result = ImageParser::checkWidthHeight($img);
	var_dump(ImageParser::checkActualDims($img, $result[0], $result[1]));
}
var_dump($imgs);
?>