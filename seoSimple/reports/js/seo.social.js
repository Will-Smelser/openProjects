//social
var
$.getJSON(api+"social/all?request="+url,function(data){
	var $soc = $('#social');
	var $ul = $(document.createElement('ul'));
	for(var x in data.data){
		$ul.append(createList(x.replace('get',''), data.data[x].data));
	}
	$soc.html($ul);
});