

document.addEventListener('DOMContentLoaded', function () {
	chrome.tabs.getSelected(null, function(tab){
		console.log('sending content page a request');
		
		chrome.tabs.sendRequest(tab.id,{hello:'world'}, function(data){
			chrome.extension.getBackgroundPage().console.log(data);
			
			var $info = $('#info');
			
			for(var x in data){
				temp = "";
				for(var y in data[x].context)
					temp += y + ": "+data[x].context[y].count+", ";
				
				var css = (x%2 == 0) ? "even" : "";
				
				//create the link
				var variations = data[x].variations.join(", ");
				
				temp = temp.substring(0,temp.length-2);
				$info.append("<tr class='"+css+"'><td>"+x+"<td>"+data[x].word+"</td><td>"+data[x].count+"</td><td>"+temp);
				
				if(x > 25) break;
			}
		});
	});
});