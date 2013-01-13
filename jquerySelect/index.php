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

		$('.style select').uiselect('refresh');
		
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
<h2>Index</h2>
<p>
<ul>
	<li><a href="#main">General User</a></li>
	<li><a href="#events">Events</a></li>
	<li><a href="#dpendencies">Dependencies</a></li>
	<li><a href="#api">API Documentation</a>
		<ul>
			<li><a href="#api-methods">.uiselect Methods</a></li>
		</ul>
	</li>
</ul>
</p>
<h2 id="main">Base Example</h2>
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
<h2 id="events">JS Event Binding</h2>
<p>The following events will automatically trigger the origional select box's events
<ul>
	<li>onchange</li>
	<li>onclick</li>
</ul>
<p>Triggers origional select <b>onchange</b> using jquery.</p>
<p class="style">
<select id="onchange"><option value="0">onchange</option><option value="1">more</option></select>
</p>
<p>Triggers origional select <b>onclick</b> using jquery.</p>
<p class="style">
<select id="onclick"><option>onclick</option><option>more</option></select>
</p>

<h2 id="dependencies">Dependencies</h2>
<p>This uses the following jquery-ui libraries:
<ul>
	<li><a href="http://api.jqueryui.com/autocomplete" target="_blank">Autocomplete</a></li>
</ul>
</p>

<h2 id="api">Api Documentation</h2>
<p>Normally I use jsdoc for documentation, but for this it didn't really make sense.</p>
<h4>Constructor .uiselect(&lt;optional&gt; <i>String</i> className)</h4>
<p><i>String</i> <b>className</b> - This will set the class name of the ui select wrapper.</p>
<p><b>NOTE:</b> You cannot use a class name which is a uiselect action string.</p>
<comment><b>Example</b><br/>
<span style="color:grey">//Creating uiselect elements.</span><br/>
$('select').uiselect();<br/>or<br/>
$('select').uiselect('my-style');
</comment>

<h4 id="api-methods">General Methods .uiselect(<i>String</i> action)</h4>
<p><i>String</i> <b>action</b> - The action that uiselect should perform on the selected elements.  This 
is hooked to the origional select element.</p>
<h5><i>Method</i> .uiselect("refresh")</h5>
<p>Will rebuild the uiselect element based on changes to the origional select box.  Triggers the autocomplete widget method 
<a href="http://api.jqueryui.com/autocomplete/#method-destroy">destroy</a>.</p>
<comment><b>Example</b><br/>
<span style="color:grey">//Refresh the first select element, ignoring remaining select elements.</span><br/>
$('select').uiselect();<br/>
$('select:first').uiselect('refresh');
</comment>
</body>
</html>