<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>

<link rel="stylesheet" type="text/css" href="required/css/base4.css" />

<script type="text/javascript" src="http://code.jquery.com/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="scripts/mioErrHandling.js"></script>
<script type="text/javascript" src="required/js/fancyPagination-min.js"></script>

<script type="text/javascript">
$(document).ready(function(){
	mioPagination.info.mode = 'animate-fade';
	mioPagination.init();
	mioPagination.page.add(10, 'test', 'page1', {}, {location:'testpages/test1.html',type:'text'},{height:450});
	mioPagination.page.add(15, 'test2', 'page2', {},{location:'scripts/loadPage.php?url=http://www.google.com',type:'text'});//'testpages/test2.html');
	mioPagination.page.add(20, 'test3', 'page3', {},{location:'testpages/test3.html',type:'text'},{sequential:true});
	mioPagination.page.add(25, 'test4', 'page4', {},{location:'testpages/test3.html',type:'text'});
	mioPagination.page.add(25, 'test4-5', 'page4-5', {},{location:'http://www.google.com',type:'iframe'},{height:450});
	mioPagination.page.add(30, 'test5', 'page5', {},{location:'testpages/test3.html',type:'text'});
	mioPagination.page.add(35, 'test6', 'page6', {},{location:'testpages/test3.html',type:'text'});
	mioPagination.page.add(40, 'test7', 'page7', {},{location:'testpages/test3.html',type:'text'});
	mioPagination.page.add(50, 'test8', 'page8', {},{location:'testpages/test3.html',type:'text'});
	mioPagination.page.add(5, 'test9', 'page9', {},{location:'testpages/test3.html',type:'text'});
	
	mioPagination.page.bindByName('page2', function(){alert('page 2 loaded');},'loaded', 'afterLoaded');
	//mioPagination.page.bindByName('page2', function(){alert('page 2 first click');}, 'firstClick');
	mioPagination.page.bindByName('page3', function(){alert('You must view page 2 first');},'page3alert', 'notSequential');
	//mioPagination.page.bind2AllPages(function(){alert('you cannot navigate backwards')}, 'noback', 'navBackwards');

	//mioPagination.page.unbindByName('page2','loaded','afterLoaded');


	mioPagination.drawPagination();	
	mioPagination.page.setPage('page1');
});
</script>

</head>

<body>

</body>
</html>