
window.data = {};

chrome.extension.onRequest.addListener(function(results, sender, sendResponse){
	window.data = results;
	console.log(results);
});


function getData(){
	console.log('getting data');
	return window.data;
}

//chrome.extension.sendRequest();