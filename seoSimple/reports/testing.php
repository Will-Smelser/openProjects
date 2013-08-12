<?php 

require_once '../config.php';

if(isset($_GET['data'])){
	$vars = explode('/', $_GET['data']);
	
	if(isset($vars[0]) && $vars[0] === 'dosave'){
		include 'save.php';
		exit;
	}
}

?><!DOCTYPE html>
<head>

<title>SimpleSEO Report</title>

<link href="http://<?php echo SEO_HOST . '/' . SEO_URI_REPORTS; ?>css/custom-theme/jquery-ui-1.10.3.custom.css" rel="stylesheet">

<link rel="stylesheet" type="text/css" href="http://www.w3.org/StyleSheets/Core/parser.css?family=5&doc=Sampler">

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>

<script src="http://<?php echo SEO_HOST . '/' . SEO_URI_REPORTS; ?>js/SeoApi.js" id="seo-api-init" name-space="SeoApi"></script>

<script>
//window.SeoApi.base.api = "<?php echo 'http://'.SEO_HOST.'/'.SEO_URI_API; ?>";
window.SeoReport = "<?php echo 'http://'.SEO_HOST.'/'.SEO_URI_REPORTS; ?>";
SeoApi.load("<?php echo 'http://'.SEO_HOST.'/'.SEO_URI_API; ?>",'base');
SeoApi.load('render');
//SeoApi.load('head').depends('render').addMethod('getTitle','#reuslts3').exec('willsmelser.com');
//SeoApi.load('body').addMethod('checkH1','#reuslts2').exec('willsmelser.com');
SeoApi.load('server').addMethod('getDomainReport','#reuslts').exec('www.inedo.com');

/*
SeoApi.waitOn('head',function(){
	console.log('head loaded');
	SeoApi.head.addMethod('getTitle','#reuslts3');
	SeoApi.head.exec('willsmelser.com');
});
*/
/*
$(document).ready(function(){
	
	SeoApi.extend('body','base',{
		apiController : 'body',
		render_checkForFrames : function(data, $target){
			$target.html((data.data)?'TRUE':'FALSE');
		}
	});

	window.SeoApi.waitOn('base',function(){
		console.log('base loaded');
	});
	
	window.SeoApi.waitOn('body',function(){
		console.log('body loaded');
		//SeoApi.body.addMethod('checkLinkTags','#reuslts');
		SeoApi.body.addMethod('checkForFrames',$('#reuslts2'));
		SeoApi.body.exec('willsmelser.com');
	});
	
	
	SeoApi.waitOn('head',function(){
		console.log('head loaded');
		
		SeoApi.head.addMethod('getTitle','#reuslts3');
		SeoApi.head.exec('willsmelser.com');
	});
	
	
});*/
</script>

</head>
<body>
<h1>Sample Google Output</h1>
<p id="reuslts3">Loading...</p>
<p id="reuslts">Loading...</p>
<p id="reuslts2">Loading...</p>

<script src="http://<?php echo SEO_HOST . '/' . SEO_URI_REPORTS; ?>js/jquery-ui-1.10.3.custom.min.js"></script>

</body>