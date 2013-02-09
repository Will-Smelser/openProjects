<!DOCTYPE html>
<html>
<head>
	<title>.uiselect Menu</title>

	<link rel="stylesheet" href="required/css/reset.css" type="text/css" media="screen" />
	<link rel="stylesheet" href="required/css/select.php?scope=.ui-select" type="text/css" media="screen" />
		
	<link rel="stylesheet" href="http://www.w3.org/StyleSheets/Core/Swiss" type="text/css">
	<link rel="stylesheet" href="http://code.jquery.com/ui/1.9.2/themes/base/jquery-ui.css" />
    <script src="http://code.jquery.com/jquery-1.8.3.js"></script>
    <script src="http://code.jquery.com/ui/1.9.2/jquery-ui.js"></script>
    <script src="required/js/jquery.uiselect.js"></script>
	
	<script>
	$(document).ready(function(){
		$('.style select').uiselect();

		$('#onchange').change(function(){alert($(this).val());});
		$('#onclick').click(function(){alert('click');});

		
	});
	</script>
	
	<style>
	comment{
		border:dotted #ccc 1px;
		background-color:#efefef;
		display:block;
		padding:10px;
	}
	h2{
		display:block;
		border-bottom:dotted #ccc 1px;
	}
	</style>
	
</head>

<body>
<?php include 'body.htm'; ?>
</body>
</html>