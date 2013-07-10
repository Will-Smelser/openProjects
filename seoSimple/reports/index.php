<!DOCTYPE html>
<head>

<title>SimpleSEO Report</title>

<link href="css/custom-theme/jquery-ui-1.10.3.custom.css" rel="stylesheet">

<link rel="STYLESHEET" type="text/css" href="http://www.w3.org/StyleSheets/Core/parser.css?family=5&doc=Sampler">

<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>


</head>

<body>
<h1>Simple SEO Report</h1>
<form method="GET" action="index.php">
	<label for="url">URL <input name="url" type="text" id="url" /></label>
	<input type="submit" value="Run Report" />
</form>

<?php if(isset($_GET['url'])){ ?>

<h2>Report - <?php echo $_GET['url']; ?></h2>

<!-- api/server -->
<h3>Server Information</h3>
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
<p id="head-info">Loading...</p>

<!-- api/body -->
<h3>HTML Body Information</h3>
	<!-- checkH1, checkH2, checkH3, checkH4 -->
	<h4>Header Tags</h4>
	<p id="body-header-tags">Loading...</p>
	
	<h4>Keywords</h4>
	<p id="body-keywords">Loading...</p>

<h3>W3C Validation</h3>
	<!-- /api/server/validateW3C -->
	<h4>General</h4>
	<p id="w3c-general">Loading...</p>
	
	<!-- api/server/getValidateW3Cerrors -->
	<h4>Errors</h4>
	<p id="w3c-error">Loading...</p>

	<!-- /api/server/getValidateW3Cwarnings -->
	<h4>Warnings</h4>
	<p id="w3c-warning">Loading...</p>

<?php } ?>
</body>

<script>
$(document).ready(function(){
	var url = "<?php echo urlencode($_GET['url']); ?>";
	var api = '/seoSimple/api/';

	function createList(label, value){
		var $ul = $(document.createElement('li'));
		return $ul.html('<i style="display:inline-block;width:250px">'+label+'</i> '+value);
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
		$ul = $(document.createElement('ul'));
		for(var i=0; i<5; i++){
			var temp = data.data.getKeyWords.data[i];
			$ul.append(createList(temp.words[0], temp.count));
		}
		$words.html($ul);
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
		var enc = data.data.getEncoding.data;
		var lang= data.data.getLang.data;

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
			console.log(x);
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
});

</script>

</html>