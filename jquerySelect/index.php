<!DOCTYPE html>
<html>
<head>
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

		$('#onchange').change(function(){alert('change');});
		$('#onclick').click(function(){alert('click');});
		
	});
	</script>
	
</head>

<body>
<h2>Here is the select menu</h2>
<table>
<tr><th>From JS<th>Default
<tr>
	<td class="style">
		<select>
			<option>Option 1</option>
			<option>Option 2</option>
		</select>

	<!-- Default select box -->
	<td>
		<select class="ui-widget" onchange="alert('changed')">
			<option>Option 1</option>
			<option>Option 2</option>
		</select>
</table>
<h2>JS event binding</h2>
<p>The following events will automatically trigger the origional select box's events
<ul>
	<li>onchange</li>
	<li>onclick</li>
</ul>
<p>Triggers origional select <b>onchange</b> using jquery.</p>
<p class="style">
<select id="onchange"><option>onchange</option><option>more</option></select>
</p>
<p>Triggers origional select <b>onclick</b> using jquery.</p>
<p class="style">
<select id="onclick"><option>onclick</option><option>more</option></select>
</p>

<h2>Dependencies</h2>
<p>This uses the following jquery-ui libraries:
<ul>
	<li><a href="http://api.jqueryui.com/autocomplete" target="_blank">Autocomplete</a></li>
</ul>
</p>
</body>
</html>