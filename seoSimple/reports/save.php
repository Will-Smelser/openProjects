<?php
header('Content-Type: application/octet-stream');

//header("X-XSS-Protection: 0");
if(get_magic_quotes_gpc())
	echo  stripslashes($_POST['data']);
else
	echo $_POST['data'];
?>