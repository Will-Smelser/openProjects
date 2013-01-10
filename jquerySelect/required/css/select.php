<?php 
header("content-type: text/css; charset=utf-8");

define("SCOPE",$_GET['scope']);

?>
/* style the wrapper */
div<?php echo SCOPE; ?>{
	position:relative;
	z-index:1;
}

div<?php echo SCOPE; ?> > span {
	
}

/* override jquery */
div<?php echo SCOPE; ?> .ui-widget select
{
	font-size: inherit;
	border:none;
	border-spacing:0px;
	margin:0px;
	padding:0px;
}

/* style the input */
div<?php echo SCOPE; ?> span > input
{
	border:none;
	font-size:inherit;
	background-color:transparent;
	margin:0px;
	padding:0 0 0 2px;
	cursor:pointer;
}
/* style the select */
div<?php echo SCOPE; ?>	> select
{
		position:absolute;
		display:block;
		top:0px;
		left:0px;
		
		background-color:transparent;
		border-top:none;
		
		margin:0px;
		padding:0px;	
	}
div<?php echo SCOPE; ?>
	span.ui-widget-content{
		background:transparent;
	}
div<?php echo SCOPE; ?> span
	.ui-spinner-button {
		height:100%;
		width:20px;
		position:absolute;
		z-index:1;
	}
div<?php echo SCOPE; ?> span a.ui-spinner-button{
	cursor:pointer;
}
div<?php echo SCOPE; ?> span
	.ui-icon {
		left:2px;
	}