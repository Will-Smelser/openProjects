<?php
include_once '../class/PageLoad.php';
include_once '../class/HtmlParser.php';
include_once '../class/Utils.php';


//TODO: validate $_GET['arg0'] is set
//TODO: handle curl error


$resp = new PageLoadResponse();

//get start
$start = microtime(true);

$result = '';
if(isset($_GET['arg0'])){
    //make a curl request
    $curl = curl_init($_GET['arg0']);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($curl);
    curl_close($curl);
}

//get finish
$finish = microtime(true);
$total_time = round(($finish - $start), 4);

$resp->url = $_GET['arg0'];
$resp->start = $start;
$resp->finish = $finish;
$resp->totalSeconds = $total_time;

//no follow?
$obeyNoFollow = true;
if(isset($_GET['arg1']) && $_GET['arg1'] == 'false')
    $obeyNoFollow = false;

//host
$host = Utils::getBaseHost($_GET['arg0']);

//parse the page
$parser = new HtmlParser($result,$_GET['arg0']);
$links = $parser->getTags('a');

$resp->links = array();
foreach($links as $node){
    if(
        !empty($node->attributes['href']) &&
        !(isset($node->attributes['rel']) && strtolower($node->attributes['rel']) === 'nofollow' && $obeyNoFollow)// &&
        //$host === getBaseHost($node->attributes['href'])
    ){
        $link = $node->attributes['href'];

        //validate hosts
        $lHost = Utils::getBaseHost($link);
        if(empty($lHost))
            $lHost = $host;

        if($lHost !== $host) break;

        $normal = Utils::normalizePath($link,$_GET['arg0']);

        if(!in_array($normal,$resp->links)){
            array_push($resp->links, $normal);
        }

    }
}
echo json_encode($resp);

?>