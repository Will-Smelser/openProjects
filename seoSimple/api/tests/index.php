<!DOCTYPE html>
<head>
<link rel="stylesheet" href="http://www.w3.org/StyleSheets/Core/Swiss" type="text/css">
<script src="http://code.jquery.com/jquery-1.10.2.min.js"></script>


<style>
td{border:solid #555 1px;vertical-align:top}
.seperator td{
	background-color:#555;
}
</style>

</head>

<body>
<form action='index.php' method='GET'>
<p>
  Choose Class: <select name='class'>
  	<option value='HtmlHeadWrap|head'>HtmlHead</option>
  	<option value='HtmlBodyWrap|body'>HtmlBody</option>
  	<option value='ServerWrap|server'>Server</option>
  	<option value='SocialWrap|social'>Social</option>
  </select>
</p>
<p>
   Choose Request: File <input id='rfile' name='type' group='type' type='radio' value='file' />
   URL <input id='rurl' name='type' group='type' type='radio' value='url' />
</p>
<p id="urlWrap" style="display:none">
   URL: <input type="text" name="url" />
</p>
<p id="fileWrap" style="display:none">
   FILE: <select name='file'>
   	<?php 
   	foreach(scandir('data') as $file){
		if($file[0] !== '.')
			echo "<option value='$file'>$file</option>\n";
	}
   	?>
   </select>
</p>
<input type="submit" value="submit" />
</form>
<?php

if(!isset($_GET['class'])) goto END;

require_once '../../config.php';
require_once 'Test.php';

$test = new Test();

$tests = array('sample1.html');


$class = explode('|',$_GET['class']);

//get all the class methods
require_once SEO_PATH_WRAPPERS .$class[0].'.php';

echo '<table border="1">';

foreach(get_class_methods($class[0]) as $method){
	if($method !== '__construct' && strtolower($method) !== strtolower($class[0])){
		
		$url = TEST_URL . '/data/'.$_GET['file'];
				
		$request;
		if($_GET['type']==='file'){
			$request = 'http://'.$_SERVER['SERVER_NAME'] . '/seoSimple' . '/api/'.$class[1].'/'.$method.'?request='.urlencode($url);
		}else{
			$request = $_GET['url'];
		}
		
		$decoded = $test->doTest($class[0], $method, $request);
	}
}

echo '</table>';

END:

?>
<script>
$('#rfile').click(function(){
	$('#urlWrap').hide();
	$('#fileWrap').show();
});
$('#rurl').click(function(){
	$('#urlWrap').show();
	$('#fileWrap').hide();
});
</script>
</body>