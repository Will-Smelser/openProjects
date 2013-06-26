<?php
include_once "../PageLoad.php";

$resp = new PageLoadResponse();

//get start
$time = microtime();
$time = explode(' ', $time);
$time = $time[1] + $time[0];
$start = $time;


//make a curl request
$curl = curl_init($_GET['arg0']);
curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($curl);
curl_close($curl);

//get finish
$time = microtime();
$time = explode(' ', $time);
$time = $time[1] + $time[0];
$finish = $time;
$total_time = round(($finish - $start), 4);

$resp->url = $_GET['url'];
$resp->start = $start;
$resp->finish = $finish;
$resp->totalSeconds = $total_time;

echo json_encode($resp);

?>