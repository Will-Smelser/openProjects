<?php
if(!preg_match('/^http\:\/\//i',$_GET['url'])){
	echo '<p>Invalid URL requested.';
}else{
	$html = file_get_contents($_GET['url']);
	echo $html;
}
?>