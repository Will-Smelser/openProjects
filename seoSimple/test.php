<?php
//get start
$time = microtime();
$time = explode(' ', $time);
$time = $time[1] + $time[0];
$start = $time;


include "class/WordCount.php";

$str = file_get_contents("http://www.inedo.com/devops");

$counter = new WordCount($str);

print_r($counter->getSortedPhrases());

print_r($counter->getPhrasesWithWord('inedo'));

//include "class/HtmlParser.php";
//include "wrappers/HtmlHead.php";

//$str = file_get_contents("http://php.net/manual/en/function.preg-match.php");

//var_dump($str);

//$str = "<div id='test'>hello<div id='next'>world</div></div> ";

//$parser = new HtmlParser($str,'http://php.net/manual/en/function.preg-match.php');
//$head = new HtmlHead($parser);


//var_dump($parser->getFavicon());
//var_dump($head->getDoctype());
//var_dump($head->getEncoding());

//var_dump($parser->getTags('meta'));
//print_r($parser->getMeta());


//preg_match('@(?:\<title[\s+]?\>)@i',
//	"<title>hello mother</title> fucker", $matches);
//print_r($matches);

//include 'config.php';
//require_once "class/GoogleInfo.php";

//$url = "http://inedo.com";
//$stats =  new GoogleInfo($url);


//var_dump($stats->getPageRank());
//var_dump($stats->getBacklinksTotal());
//var_dump($stats->getBacklinks(null,17));
//$stats->getBacklinks(17);

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

/*
 * TEST THE IMAGEPARSER
 
include "class/HtmlParser.php";
include "class/ImageParser.php";

$url = "http://www.inedo.com/devops";
$str = file_get_contents($url);

$parser = new HtmlParser($str, $url);
$imgs = $parser->getTags('img');

$result = array();
/*
foreach($imgs as $img){
	$res = ImageParser::getWidthHeight($img);
	array_push($result, ImageParser::checkActualDimsSingle($img, $res[0], $res[1]));
}*/
//var_dump($result);

//$result = ImageParser::checkActualDimsThreaded($imgs);
//var_dump($result);


/*
//get finish
$time = microtime();
$time = explode(' ', $time);
$time = $time[1] + $time[0];
$finish = $time;
$total_time = round(($finish - $start), 4);

echo "\nTOTAL_TIME: $total_time sec.\n\n";
*/

//include "class/Whois.php";

//var_dump(Whois::lookup('inedo.com'));
?>