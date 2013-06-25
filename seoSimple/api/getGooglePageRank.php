<?php

require_once "../class/helpers/ApiResponse.php";
require_once "../class/helpers/Vars.php";
require_once "../3rdParty/SEOstats-master/src/seostats.php";

$url = Vars::get('url');

if(empty($url)){
	(new ApiResponseJSON())->failure("Request must contain the 'url' variable.");
} else {
	$stats = new SEOstats($url);
	$rank = $stats->Google()->getPageRank();
	$rank = intval($rank);
	(new ApiResponseJSON())->success("Success", array('pageRank'=>$rank));
}
?>