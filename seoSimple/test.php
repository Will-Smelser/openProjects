<?php
//get start
$time = microtime();
$time = explode(' ', $time);
$time = $time[1] + $time[0];
$start = $time;

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

$result = array();

//foreach($imgs as $img){
	//$res = ImageParser::checkWidthHeight($img);
	//array_push($result, ImageParser::checkActualDimsSingle($img, $res[0], $res[1]));
//}

$result = ImageParser::checkActualDimsThreaded($imgs);

var_dump($result);

//get finish
$time = microtime();
$time = explode(' ', $time);
$time = $time[1] + $time[0];
$finish = $time;
$total_time = round(($finish - $start), 4);

echo "\nTOTAL_TIME: $total_time sec.\n\n";

?>