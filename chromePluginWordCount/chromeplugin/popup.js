

document.addEventListener('DOMContentLoaded', function () {
	var bg = chrome.extension.getBackgroundPage();
	var data = bg.getData();
	
	$info = $('#info');
	
	for(var x in data){
		temp = "";
		for(var y in data[x].context)
			temp += y + ": "+data[x].context[y].count+", ";
		
		temp = temp.substring(0,temp.length-2);
		$info.append("<tr><td>"+data[x].word+"</td><td>"+data[x].count+"</td><td>"+temp);
	}
});