<?php 

require_once '../config.php';

if(isset($_GET['data'])){
	$vars = explode('/', $_GET['data']);
	
	if(isset($vars[0]) && $vars[0] === 'save'){
		include 'save.php';
		exit;
	}
}


?><!DOCTYPE html>
<head>

<title>SimpleSEO Report</title>

<link href="http://<?php echo SEO_HOST . SEO_URI_REPORTS; ?>css/custom-theme/jquery-ui-1.10.3.custom.css" rel="stylesheet">

<link rel="stylesheet" type="text/css" href="http://www.w3.org/StyleSheets/Core/parser.css?family=5&doc=Sampler">

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script src="http://<?php echo SEO_HOST . SEO_URI_REPORTS; ?>js/jquery-ui-1.10.3.custom.min.js"></script>


<style>
table{width:90%;}
td{
	vertical-align:top;
	padding:3px;
}
h2{
	margin-top:1em;
}
h3{
	background-color:#EFEFEF;
	padding:3px;
	font-size:2em;
}
.by-author{
	font-style:italic;
	font-size:.5em;
}
.hide{
	display:none;
}
textarea{
	border:solid #EFEFEF 1px;
	width:90%;
	height:25px;
	color:#000;
	font-style:italic;
	font-size:1.5em;
}

.recommendation{
	font-style:italic;
	font-size:1.5em;
}

#popup-content{
	font-size:1em;
}

.li-label{
	display:inline-block;
	width:275px;
	vertical-align: top;
}

</style>

</head>

<body>
<div id="all-content">
<h1>SEO Report <span class="by-author">by Will Smelser</span></h1>
<form id="form-run-report" method="GET" action="index.php">
	<label for="url">URL <input name="url" type="text" id="url" /></label>
	<input type="submit" value="Run Report" />
</form>

<?php if(isset($_GET['url'])){ ?>

<div style="float:right" id="save-edit-wrap"><input type="checkbox" id="save-edit"  /><label for="save-edit">Save / Edit</label></div>

<h2 id="report-title">Report - <?php echo $_GET['url']; ?></h2>

<!-- api/server -->
<h3>Server Information</h3>
	<h4>Rcommendations...</h4>
	<textarea></textarea>
	<!-- 
		api/server/
			getHeaderResponseLine, getHeaderField, getServer, getServer, isGzip, getLoadTime, getWhois
	-->
	<h4>General Info</h4>
	<p id="server-general-info">Loading...</p>
	
	<h4>Domain Information</h4>
	<p id="server-whois">Loading...</p>
	
<!-- api/head -->
<h3>HTML Head Information</h3>
<h4>Rcommendations...</h4>
<textarea></textarea>
	
<p id="head-info">Loading...</p>

<!-- api/body -->
<h3>HTML Body Information</h3>
	<h4>Rcommendations...</h4>
	<textarea></textarea>
	
	<!-- checkH1, checkH2, checkH3, checkH4 -->
	<h4>Header Tags</h4>
	<p id="body-header-tags">Loading...</p>
	
	<h4>Keywords</h4>
	<p id="body-keywords">Loading...</p>
	
	<h4>Inline Styles</h4>
	<p id="body-inline-style">Loading...</p>
	
	<h4>Link Data</h4>
	<p id="body-anchors">Loading...</p>
	
	<h4>Frames / Object Tags</h4>
	<p id="body-bad-stuff">Loading...</p>
	
	<h4>Image Analysis</h4>
	<p id="body-images">Loading...</p>
	
<h3>Social Stats</h3>
	<h4>Rcommendations...</h4>
	<textarea></textarea>
	
	<p id="social">Loading...</p>
	
<h3>Google Stats</h3>
	<h4>Rcommendations...</h4>
	<textarea></textarea>
	
	<h4>Page Rank: <b id="google-pr">Loading...</b></h4>

	<h4>Back Links</h4>
	<p id="google-backlinks">Loading...</p>
	
<h3>SEO Moz Stats</h3>
	<h4>Rcommendations...</h4>
	<textarea>SEO Moz is site which collects various link specific data about sites.</textarea>
	
	<h4>Moz General Information</h4>
	<p id="moz-link">Loading...</p>
	
	<h4>Moz Just Discovered Backlinks</h4>
	<p id="moz-disc">Loading...</p>
	
<h3>SEMrush Stats</h3>
	<h4>Rcommendations...</h4>
	<textarea>SEMrush collects general data about various search terms for competition analysis.  
	This data is domain specific, but gives insight into what your competitors are doing.</textarea>
	
	<h4>Domain Data</h4>
	<p id="semrush-domain">Loading...</p>
	
	<h4>Domain Keyword Data</h4>
	<p id="semrush-keywords">Loading...</p>
	
<h3>W3C Validation</h3>
	<h4>Rcommendations...</h4>
	<textarea></textarea>

	<!-- /api/server/validateW3C -->
	<h4>General</h4>
	<p id="w3c-general">Loading...</p>
	
	<!-- api/server/getValidateW3Cerrors -->
	<h4>Errors</h4>
	<p id="w3c-error">Loading...</p>

	<!-- /api/server/getValidateW3Cwarnings -->
	<h4>Warnings</h4>
	<p id="w3c-warning">Loading...</p>

<h3>Keywords (Extended)</h3>
	<h4>Contains phrases using listed key words</h4>
	<p id="body-keywords2">Loading...</p>


<div id="popup" title="Information">
	<div id="popup-content"></div>
</div>

<?php 
//get the filename, we want this to save as
$filename = str_replace('/','-',preg_replace('@https?://@i','',$_GET['url'])) . '.html';
?>
<form id="save-form" action="http://<?php echo SEO_HOST . '/' . SEO_URI_REPORTS; ?>save/<?php echo $filename; ?>" method="POST" target="_blank" style="display:none">
	<textarea name="data" id="save-form-data"></textarea>
</form>

<script>

var url = "<?php echo isset($_GET['url']) ? urlencode($_GET['url']):''; ?>";
var api = "<?php echo 'http://'.SEO_HOST.'/'.SEO_URI_API; ?>";

$(document).ready(function(){
	$('#report-title:first').click(function(){
		$('#form-run-report').toggleClass('hide');
		$('#save-edit-wrap').toggleClass('hide');
	});

	//this is for the save.php
	if(document.location.href.indexOf('save.php')>0){
		$( "#save-edit" ).prop('checked',true);
		console.log('hello world');
	}else{
		$( "#save-edit" ).prop('checked',false);
	}
	
	//save or edit
	$( "#save-edit" ).button().click(function(evt){
		$btn = $(evt.target);
		
		//save
		if($btn.prop('checked')){
			$('textarea:not(#save-form-data)').each(function(){
				var $p = $(document.createElement('p')).html($(this).val()).attr('class','recommendation');
				$(this).before($p).detach();
			});
			
			if(document.location.href.indexOf('save.php')<0){
				var content = '<html><head>' + $('head').html() + '</head>' + $('#all-content').html() + '</body></html>';
				$('#save-form textarea:first').val(content).parent().submit();
			}
		//edit
		}else{
			$('.recommendation').each(function(){
				var $txt = $(document.createElement('textarea')).val($(this).html());
				$(this).before($txt).detach();
			});
		}

	});
});

//do a serp query
function serpQuery(q){
	var $pop = $('#popup');
	var $child = $pop.children('#popup-content');

	$child.html('Loading...');
	
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

<script>
$(document).ready(function(){
	

	function createList(label, value){
		var $ul = $(document.createElement('li'));
		return $ul.html('<i class="li-label">'+label+'</i> '+value);
	};

	function w3cErrToString(info){
		var $tr = $(document.createElement('tr'));
		for(var x in info){
			var val = (info[x] === null) ? 'NULL' : info[x];
			var $td = $(document.createElement('td')).html(val);
			$tr.append($td);
		}
		return $tr;
	};

	function createTableRow(row){
		var $tr = $(document.createElement('tr'));
		for(var x in row)
			$tr.append($(document.createElement('td')).html(row[x]));

		return $tr;
	}

	

	
	//google information
	$.getJSON(api+"google/getPageRank|getBacklinks?request="+url,function(data){
		
		$('#google-pr').html(data.data.getPageRank.data);
		var $bl = $('#google-backlinks');

		var $ul = $(document.createElement('ul'));
		$ul.append(createList('Total Backlinks',data.data.getBacklinks.data.length));
		for(var x in data.data.getBacklinks.data){
			var title = data.data.getBacklinks.data[x].title;
			var link = data.data.getBacklinks.data[x].link;
			var a = "<a href='"+link+"'>"+title+"</a>";
			$ul.append(createList('Link',a));
		}
		$bl.html($ul);
	});
	
	
	//social
	$.getJSON(api+"social/all?request="+url,function(data){
		var $soc = $('#social');
		var $ul = $(document.createElement('ul'));
		for(var x in data.data){
			$ul.append(createList(x.replace('get',''), data.data[x].data));
		}
		$soc.html($ul);
	});

	//get the body data
	$.getJSON(api+"body/all?request="+url,function(data){
		//check the header data
		var $htags = $('#body-header-tags');

		//header tags
		var $ul = $(document.createElement('ul'));
		$ul.append(createList('&lt;H1&gt;',data.data.checkH1.data.length));
		$ul.append(createList('&lt;H2&gt;',data.data.checkH2.data.length));
		$ul.append(createList('&lt;H3&gt;',data.data.checkH3.data.length));
		$ul.append(createList('&lt;H4&gt;',data.data.checkH4.data.length));
		$htags.html($ul);

		//word count
		var $words = $('#body-keywords');
		var $phrases = $('#body-keywords2');
		
		$ul = $(document.createElement('ul'));
		$pul = $(document.createElement('ul'));
		for(var i=0; i<5; i++){
			var temp = data.data.getKeyWords.data[i];

			var $a = $(document.createElement('a')).attr('onclick','serpQuery(\''+temp.words[0]+'\')').
			attr('class','li-label').html(temp.words[0]);

			var $a2 = $(document.createElement('a')).attr('onclick','serpQuery(\''+temp.words[0]+'\')').
			attr('class','li-label').html(temp.words[0]);
			
			$li = $(document.createElement('li')).append($a2).append(temp.count);
			$pli = $(document.createElement('li')).append($a).append(temp.count);

			//add phrase data
			var phrase = data.data.getPhrases.data[temp.normal][0];
			var $ul2 = $(document.createElement('ul'));
			for(var x in phrase){
				var start = phrase[x].indexOf(temp.normal);
				end = start;
				for(var j=start; j < phrase[x].length && phrase[x].charAt(j) !== ' '; j++)
					end = j;

				end++;

				var front = phrase[x].substr(0,start-1);
				var middle = phrase[x].substr(start, end-start);
				var back = phrase[x].substr(end+1);

				var $a = $(document.createElement('a')).attr('onclick','serpQuery(\''+temp.words[0]+'\')').html(front+' <b>'+middle+'</b> '+back);
				
				$ul2.append($(document.createElement('li')).append($a));

			}
			$pli.append($ul2);
			$pul.append($pli);

			$ul.append($li);
		}
		$words.html($ul);
		$phrases.html($pul);

		//inline css
		var $icss = $('#body-inline-style');
		$ul = $(document.createElement('ul'));
		var ltagcount = 0;
		var ltaghosts = 0;
		for(var host in data.data.checkLinkTags.data)
			ltaghosts++;
			ltagcount += data.data.checkLinkTags.data[host].length;
		
		$ul.append(createList('Total &lt;link&gt; tag count',ltagcount));
		$ul.append(createList('Total &lt;link&gt; tag host count',ltaghosts));
		$ul.append(createList('Inline CSS count',data.data.checkInlineCSS.data.length));
		$ul.append(createList('Inline &lt;style&gt; count',data.data.checkInlineStyle.data.length));
		$icss.html($ul);

		//anchors
		var $anchor = $('#body-anchors');
		$ul = $(document.createElement('ul'));
		$ul.append(createList('Internal &lt;a&gt; tags',data.data.getInternalAnchor.data.length));
		$ul.append(createList('External &lt;a&gt; tags',data.data.getExternalAnchors.data.length));
		$anchor.html($ul);

		//bad stuff like frames
		var $bad = $('#body-bad-stuff');
		$ul = $(document.createElement('ul'));
		$ul.append(createList('Page contains frames?',data.data.checkForFrames.data));
		$ul.append(createList('Page contains iframes?',data.data.checkForIframes.data));
		$ul.append(createList('Page contains flash/objects?',data.data.checkForFlash.data));
		$bad.html($ul);

		//images
		var $img = $('#body-images');

		var $table = $(document.createElement('table'));
		var $tr = $(document.createElement('tr'));
		$tr.html('<th>Result</th><th>Url</th><th>Alt</th><th>Title</th><th>Expected</th><th>Actual</th>');
		$table.append($tr);
		
		for(var x in data.data.checkImages.data){
			var temp = data.data.checkImages.data[x];
			var result;
			switch(temp.result){
			case 0:
				result = 'Bad Size';
				break;
			case 1:
				result = 'Good';
				break;
			default:
				result = 'Failed';
				break;
			}
			var sizeHtml = (temp.result === 1) ? temp.htmlWidth + 'x' + temp.htmlHeight : 'N/A';
			var sizeAct = (temp.result === 1) ? temp.actualWidth + 'x' + temp.actualHeight : 'N/A';  
			var row = [
				result,
				'<div style="text-overflow:clip;max-width:250px;"><a target="_blank" href="'+temp.url+'">'+temp.url.substr(temp.url.lastIndexOf("/") + 1)+'</a></div>',
				temp.alt,temp.title,
				sizeAct,
				sizeHtml
			];
			var $tr = createTableRow(row);
			
			$table.append($tr);
		}
		$img.html($table);
	});

	//get the header information
	$.getJSON(api+"head/all?request="+url,function(data){
		
		var $head = $("#head-info");

		var title = data.data.getTitle.data.text;
		var mdesc = (data.data.getMetaDesc.data === null) ? 'None' : data.data.getMetaDesc.data;
		var mkwrd = (data.data.getMetaKeywords.data === null) ? 'None' : data.data.getMetaKeywords.data;
		var fav = data.data.getFavicon.data;
		if(fav === null)
			fav = (data.data.getFaviconNoTag.data === null) ? 'None' : data.data.getFaviconNoTag.data;
		var doc = data.data.getDoctype.data;
		var enc = (data.data.getEncoding.data == null) ? 'None' : data.data.getEncoding.data;
		var lang= (data.data.getLang.data == null) ? 'None' : data.data.getLang.data;

		$ul = $(document.createElement('ul'));
		$ul.append(createList('Title',title));
		$ul.append(createList('Meta Description', mdesc));
		$ul.append(createList('Meta Keywords',mkwrd));
		$ul.append(createList('Favicon',fav));
		$ul.append(createList('Document Type',doc));
		$ul.append(createList('Content Encoding',enc));
		$ul.append(createList('Language',lang));

		$head.html($ul); 	
		
	});
	
	//get the server data
	$.getJSON(api+"server/all?request="+url,function(data){

		//whois
		var $ul = $(document.createElement('ul'));
		var whois = data.data.getWhois.data;
		for(var x in whois){
			$ul.append(createList(x.replace('_',' '),whois[x]));
		}
		$('#server-whois').html($ul);

		//general
		$ul = $(document.createElement('ul'));
		var response = data.data.getHeaderResponseLine.data;
		var load = data.data.getLoadTime.data;
		var gzip = data.data.isGzip.data;
		var server = data.data.getServer.data;
		$ul.append(createList('HTTP Response Code',response));
		$ul.append(createList('Load Time',load+' sec.'));
		$ul.append(createList('Server Info', server));
		$ul.append(createList('Gzip Compression', gzip));
		$ul.append(createList('Robots.txt',(data.data.checkRobots.data == false) ? false : true));
		$('#server-general-info').html($ul);

		//w3c validation
		$ul = $(document.createElement('ul'));
		var w3cgen = data.data.validateW3C.data;
		var w3cerr = data.data.getValidateW3Cerrors.data;
		var w3cwarn= data.data.getValidateW3Cwarnings.data;
		$('#w3c-general').html('The HTML document is '+(w3cgen?'<b>VALID</b>':'<b style="color:red">INVALID</b>'));

		//w3c errors
		var $w3cerr = $('#w3c-error');
		if(w3cerr.length > 0){
			var $table = $(document.createElement('table'));
			var $tr = $(document.createElement('tr'));
			for(var x in w3cerr[0])
				$tr.append($(document.createElement('th')).html(x));

			$table.append($tr);
			
			for(var x in w3cerr)
				$table.append(w3cErrToString(w3cerr[x]));

			$w3cerr.html($table);
			
		}else{
			$w3cerr.html('No Errors');
		}

		//w3c warning
		var $w3cwarn = $('#w3c-warning');
		if(w3cwarn.length > 0){
			var $table = $(document.createElement('table'));
			var $tr = $(document.createElement('tr'));
			for(var x in w3cwarn[0])
				$tr.append($(document.createElement('th')).html(x));

			$table.append($tr);
			
			for(var x in w3cwarn)
				$table.append(w3cErrToString(w3cwarn[x]));

			$w3cwarn.html($table);
			
		}else{
			$w3warn.html('No Warnings');
		}
		
	});

	//some moz data
	$.getJSON(api+"moz/all?request="+url,function(data){


		
		var $ul = $(document.createElement('ul'));
		$ul.append(createList('Page Authority',data.data.getMozLinks.data.pageAuthority));
		$ul.append(createList('Domain Authority',data.data.getMozLinks.data.domainAuthority));
		$ul.append(createList('Inbound Links',data.data.getMozLinks.data.totalInboundLinks));
		$ul.append(createList('Inboutnd Domains',data.data.getMozLinks.data.linkingRootDomains));
		$('#moz-link').html($ul);

		$ul = $(document.createElement('ul'));
		
		
		for(var x in data.data.getMozJustDiscovered.data){
			
			var $li = $(document.createElement('li'));
			var $ul2 = $(document.createElement('ul'));
			
			var temp = data.data.getMozJustDiscovered.data[x];
			$ul2.append(createList('Link Text',temp.text));
			$ul2.append(createList('Page Authority',temp.pageAuthority));
			$ul2.append(createList('Domain Authority',temp.DomainAuthority));
			$ul2.append(createList('Discovery Time',temp.DiscoveryTime));
			
			$li.append(temp.link);
			$li.append($ul2);
			$ul.append($li);
		}
		$('#moz-disc').html($ul);
		
	});

	//semRUSH data
	$.getJSON(api+"SemRush/all?request="+url,function(data){
		var $ul = $(document.createElement('ul'));
		
		for(var x in data.data.getKeyWordsReport.data){
			var $li = $(document.createElement('li'));
			var $ul2 = $(document.createElement('ul'));
		
			//key word report
			var temp = data.data.getKeyWordsReport.data[x];
			
			$li.html(temp.Ph.data);
			for(var y in temp)	
				$ul2.append(createList(temp[y]['short'],temp[y].data));

			$ul.append($li.append($ul2));
		}
		$('#semrush-keywords').html($ul);
		
			
		$ul = $(document.createElement('ul'));
		for(var x in data.data.getDomainReport.data){
			var temp = data.data.getDomainReport.data[x];
			$ul.append(createList(temp['short'],temp.data));
		}
		$('#semrush-domain').html($ul);
		
	});
});

</script>

<?php }else{echo '</body>';} ?>

</html>