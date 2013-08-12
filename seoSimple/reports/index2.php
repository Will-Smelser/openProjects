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

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script src="http://<?php echo SEO_HOST . '/' . SEO_URI_REPORTS; ?>js/SeoApi.js" id="seo-api-init" name-space="SeoApi"></script>

<script>
var url = "<?php echo isset($_GET['url']) ? urlencode($_GET['url']):''; ?>";
var api = "<?php echo 'http://'.SEO_HOST.'/'.SEO_URI_API; ?>";

window.SeoReport = "<?php echo 'http://'.SEO_HOST.'/'.SEO_URI_REPORTS; ?>";
SeoApi.load(api,'base');
SeoApi.load('render');


SeoApi.load('google').depends('render')
	.addMethod('getPageRank','#google-pr')
	.addMethod('getBacklinks','#google-backlinks')
	.exec(url);
	
SeoApi.load('body').depends('render')
	.addMethod('checkH1','#body-header-tags')
	.addMethod('checkH2','#body-header-tags')
	.addMethod('checkH3','#body-header-tags')
	.addMethod('checkH4','#body-header-tags')
	.addMethod('getKeyWords','#body-keywords')
	.addMethod('getPhrases','#body-keywords2')
	.addMethod('checkLinkTags','#body-inline-style')
	.addMethod('checkInlineCSS','#body-inline-style')
	.addMethod('checkInlineStyle','#body-inline-style')
	.addMethod('getInternalAnchor','#body-anchors')
	.addMethod('getExternalAnchors','#body-anchors')
	.addMethod('checkForFrames','#body-bad-stuff')
	.addMethod('checkForIframes','#body-bad-stuff')
	.addMethod('checkForFlash','#body-bad-stuff')
	.addMethod('checkImages','#body-images')
	.exec(url);
	
SeoApi.load('head').depends('render')
	.addMethod('all',"#head-info")
	.exec(url);
	
SeoApi.load('server').depends('render')
	.addMethod('getWhois','#server-whois')
	.addMethod('getHeaderResponseLine','#server-general-info')
	.addMethod('getLoadTime','#server-general-info')
	.addMethod('isGzip','#server-general-info')
	.addMethod('getServer','#server-general-info')
	.addMethod('validateW3C','#w3c-general')
	.addMethod('getValidateW3Cerrors','#w3c-error')
	.addMethod('getValidateW3Cwarnings','#w3c-warning')
	.exec(url);

SeoApi.load('moz').depends('render')
	.addMethod('getMozLinks','#moz-link')
	.addMethod('getMozJustDiscovered','#moz-disc')
	.exec(url);

SeoApi.load('semrush').depends('render')
	.addMethod('getDomainReport','#semrush-domain')
	.addMethod('getKeyWordsReport','#semrush-keywords')
	.exec(url);

SeoApi.load('social').depends('render')
	.addMethod('all','#social')
	.exec(url);
</script>


<link rel="stylesheet" type="text/css" href="http://www.w3.org/StyleSheets/Core/parser.css?family=5&doc=Sampler" />
<link rel="stylesheet" type="text/css" href="http://<?php echo SEO_HOST . '/' . SEO_URI_REPORTS; ?>css/custom-theme/jquery-ui-1.10.3.custom.css" />
<link rel="stylesheet" type="text/css" href="http://<?php echo SEO_HOST . '/' . SEO_URI_REPORTS; ?>css/report_basic.css" />

<script src="http://<?php echo SEO_HOST . '/' . SEO_URI_REPORTS; ?>js/jquery-ui-1.10.3.custom.min.js"></script>

</head>

<body>
<div id="all-content">
<h1>SEO Report <span class="by-author">by Will Smelser</span></h1>
<form id="form-run-report" method="GET" action="index2.php">
	<label for="url">URL <input name="url" type="text" id="url" /></label>
	<input id="run-report" type="submit" value="Run Report" />
</form>

<?php if(isset($_GET['url'])){ ?>

<div style="float:right" id="save-edit-wrap">
	<button id="save" >Save</button>
	<button id="edit" >Edit</button>
</div>

<h2 id="report-title">Report - <?php echo $_GET['url']; ?></h2>

<!-- api/server -->
<h3>Server Information <a class='addComment'>add comment</a></h3>
	<!-- 
		api/server/
			getHeaderResponseLine, getHeaderField, getServer, getServer, isGzip, getLoadTime, getWhois
	-->
	<h4>General Info</h4>
	<p id="server-general-info" class="loading-text"><img src="http://<?php echo SEO_HOST . '/' . SEO_URI_REPORTS; ?>images/loading.gif" />&nbsp;Loading...</p>
	
	<h4>Domain Information</h4>
	<p id="server-whois" class="loading-text"><img src="http://<?php echo SEO_HOST . '/' . SEO_URI_REPORTS; ?>images/loading.gif" />&nbsp;Loading...</p>
	
<!-- api/head -->
<h3>HTML Head Information <a class='addComment'>add comment</a></h3>
	
<p id="head-info" class="loading-text"><img src="http://<?php echo SEO_HOST . '/' . SEO_URI_REPORTS; ?>images/loading.gif" />&nbsp;Loading...</p>

<!-- api/body -->
<h3>HTML Body Information <a class='addComment'>add comment</a></h3>
	
	<!-- checkH1, checkH2, checkH3, checkH4 -->
	<h4>Header Tags</h4>
	<p id="body-header-tags" class="loading-text"><img src="http://<?php echo SEO_HOST . '/' . SEO_URI_REPORTS; ?>images/loading.gif" />&nbsp;Loading...</p>
	
	<h4>Keywords</h4>
	<p id="body-keywords" class="loading-text"><img src="http://<?php echo SEO_HOST . '/' . SEO_URI_REPORTS; ?>images/loading.gif" />&nbsp;Loading...</p>
	
	<h4>Inline Styles</h4>
	<p id="body-inline-style" class="loading-text"><img src="http://<?php echo SEO_HOST . '/' . SEO_URI_REPORTS; ?>images/loading.gif" />&nbsp;Loading...</p>
	
	<h4>Link Data</h4>
	<p id="body-anchors" class="loading-text"><img src="http://<?php echo SEO_HOST . '/' . SEO_URI_REPORTS; ?>images/loading.gif" />&nbsp;Loading...</p>
	
	<h4>Frames / Object Tags</h4>
	<p id="body-bad-stuff" class="loading-text"><img src="http://<?php echo SEO_HOST . '/' . SEO_URI_REPORTS; ?>images/loading.gif" />&nbsp;Loading...</p>
	
	<h4>Image Analysis</h4>
	<p id="body-images" class="loading-text"><img src="http://<?php echo SEO_HOST . '/' . SEO_URI_REPORTS; ?>images/loading.gif" />&nbsp;Loading...</p>

<h3>W3C Validation <a class='addComment'>add comment</a></h3>

	<!-- /api/server/validateW3C -->
	<h4>General</h4>
	<p id="w3c-general" class="loading-text"><img src="http://<?php echo SEO_HOST . '/' . SEO_URI_REPORTS; ?>images/loading.gif" />&nbsp;Loading...</p>
	
	<!-- api/server/getValidateW3Cerrors -->
	<h4>Errors</h4>
	<p id="w3c-error" class="loading-text"><img src="http://<?php echo SEO_HOST . '/' . SEO_URI_REPORTS; ?>images/loading.gif" />&nbsp;Loading...</p>

	<!-- /api/server/getValidateW3Cwarnings -->
	<h4>Warnings</h4>
	<p id="w3c-warning" class="loading-text"><img src="http://<?php echo SEO_HOST . '/' . SEO_URI_REPORTS; ?>images/loading.gif" />&nbsp;Loading...</p>
	
<h3>Social Stats <a class='addComment'>add comment</a></h3>
	
	<p id="social" class="loading-text"><img src="http://<?php echo SEO_HOST . '/' . SEO_URI_REPORTS; ?>images/loading.gif" />&nbsp;Loading...</p>
	
<h3>Google Stats <a class='addComment'>add comment</a></h3>
	
	<h4>Page Rank: <b id="google-pr" class="loading-text"><img src="http://<?php echo SEO_HOST . '/' . SEO_URI_REPORTS; ?>images/loading.gif" />&nbsp;Loading...</b></h4>

	<h4>Back Links</h4>
	<p id="google-backlinks" class="loading-text"><img src="http://<?php echo SEO_HOST . '/' . SEO_URI_REPORTS; ?>images/loading.gif" />&nbsp;Loading...</p>
	
<h3>SEO Moz Stats <a class='addComment'>add comment</a></h3>
	
	<h4>Moz General Information</h4>
	<p id="moz-link" class="loading-text"><img src="http://<?php echo SEO_HOST . '/' . SEO_URI_REPORTS; ?>images/loading.gif" />&nbsp;Loading...</p>
	
	<h4>Moz Just Discovered Backlinks</h4>
	<p id="moz-disc" class="loading-text"><img src="http://<?php echo SEO_HOST . '/' . SEO_URI_REPORTS; ?>images/loading.gif" />&nbsp;Loading...</p>
	
	
<h3>SEMrush Stats <a class='addComment'>add comment</a></h3>
	
	<h4>Domain Data</h4>
	<p id="semrush-domain" class="loading-text"><img src="http://<?php echo SEO_HOST . '/' . SEO_URI_REPORTS; ?>images/loading.gif" />&nbsp;Loading...</p>
	
	<h4>Domain Keyword Data</h4>
	<p id="semrush-keywords" class="loading-text"><img src="http://<?php echo SEO_HOST . '/' . SEO_URI_REPORTS; ?>images/loading.gif" />&nbsp;Loading...</p>

<h3>Keywords (Extended) <a class='addComment'>add comment</a></h3>
	<h4>Contains phrases using listed key words</h4>
	<p id="body-keywords2" class="loading-text"><img src="http://<?php echo SEO_HOST . '/' . SEO_URI_REPORTS; ?>images/loading.gif" />&nbsp;Loading...</p>


<div id="popup" title="Information">
	<div id="popup-content"></div>
</div>

<?php 
//get the filename, we want this to save as
$filename = str_replace('/','-',preg_replace('@https?://@i','',$_GET['url'])) . '.html';
?>
<form id="save-form" action="http://<?php echo SEO_HOST . '/' . SEO_URI_REPORTS; ?>dosave/<?php echo $filename; ?>" method="POST" target="_blank" style="display:none">
	<textarea name="data" id="save-form-data"></textarea>
</form>

<script>


$(document).ready(function(){
	$('#run-report').button();
	
	$('.addComment').click(function(){
		editOn();
		
		var $parent = $(this).parent();
		
		var $el = $parent.next();

		//already have a comment
		if($el.hasClass('comment')){
			$el.remove();
			$(this).html('add comment').removeClass('removeComment');
			return;
		}

		//create the comment element
		var $div = $(document.createElement('div')).addClass('comment');
		var $txt = $(document.createElement('textarea'));
		var $h4 = $(document.createElement('h4')).html('Comments:');

		$div.append($h4).append($txt);
		$parent.after($div);

		$(this).html('remove comment').addClass('removeComment');
	});

	
	$('#report-title:first').click(function(){
		$('#form-run-report').toggleClass('hide');
		$('#save-edit-wrap').toggleClass('hide');
	});


	var editing = !(document.location.href.indexOf('save') > 0);
	var editOff = function(){
		$('textarea:not(#save-form-data)').each(function(){
			var $p = $(document.createElement('p')).html($(this).val()).attr('class','recommendation');
			$(this).before($p).detach();
		});
		editing = false;
	};

	var editOn = function(){
		$('.recommendation').each(function(){
			var $txt = $(document.createElement('textarea')).val($(this).html());
			$(this).before($txt).detach();
		});
		editing = true;
	};
		
	
	$('#save').button({icons:{primary:"ui-icon-disk"}}).click(function(evt){
		editOff();
		
		var content = '<html><head>' + $('head').html() + '</head><body><div id="all-content">' + $('#all-content').html() + '</div></body></html>';
		$('#save-form textarea:first').val(content).parent().submit();
		
	});

	$('#edit').button({icons:{primary:"ui-icon-pencil"}}).click(function(){
		(editing) ? editOff() : editOn();
	});
	
});

//do a serp query
function serpQuery(q){
	var $pop = $('#popup');
	var $child = $pop.children('#popup-content');

	$child.html('<img src="http://<?php echo SEO_HOST . '/' . SEO_URI_REPORTS; ?>images/loading.gif" />&nbsp;Loading...');
	
	$.getJSON(api+"google/getSerps/"+encodeURIComponent(q)+"?request="+url,function(data){
		
		$child.html("");
		for(var x in data.data){
			
			var $div = $(document.createElement('div'));
			var $h = $(document.createElement('h4')).html((x*1+1)+'.&nbsp;&nbsp;'+data.data[x].title);
			var $p = $(document.createElement('p')).html(data.data[x].htmlSnippet);
			var $a = $(document.createElement('a')).html(data.data[x].displayLink).attr('href',data.data[x].link).attr('target','_blank');

			$div.append($h).append($p).append($a);
			
			if(data.data[x].mime == null){
				var $a = $(document.createElement('a'));
				var url = '?url='+encodeURIComponent(data.data[x].link);
				$a.attr('href',url).html('Create Report').attr('target','_blank');
				$div.append(document.createElement('br'));
				$div.append($a);
				$a.button({icons:{primary:'ui-icon-document'}});
			}else{
				var $div2 = $(document.createElement('div')).html('No report available.');
				$div.append($div2);
			}
			
			
			$child.append($div);
		}
		
		
	});

	$('#popup').dialog({
		modal: true,
		height: 400,
		width: 700,
		title: "Top Google Results - " + q
	});
}

</script>

</div>

</body>

<?php }else{ echo '</body>';} ?>

</html>