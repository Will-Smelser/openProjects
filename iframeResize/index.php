<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>

<script type="text/javascript" src="http://code.jquery.com/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="required/js/jquery.iframeresize.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	$('iframe').iframeresize();
});


function changeIframeUrl(){
	var url = 'testpages/test2.html';
	$('#page').html(url);
	$('#myframe').attr('src',url);
	$('#myframe').trigger('iframeresize');
}

</script>
</head>

<body>
<h2>Documentation</h2>
<p>
<a href="docs/index.html" >Documentation</a>
</p>
<h2>Example</h2>
<p>Current Page: <span id="page">testpages/test1.html</span></p>
<p>
<input value="Change the Iframe URL" type="button" onclick="changeIframeUrl()" />
</p>
<iframe id="myframe" src="testpages/test1.html" style="margin:0px;padding:0px;"></iframe>
</body>
</html>