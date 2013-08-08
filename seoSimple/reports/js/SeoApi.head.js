(function(){
	var temp = document.location.href.split('?');
	namespace = (temp.length > 1) ? temp[1] : 'SeoApi';
	
	if(typeof window[namespace] == "undefined") window[namespace] = {};
	
	window[namespace].head = {
		apiController : 'head'/*,
		render_getTitle : function(data, $target){
			console.log(data);
		}*/
	};
})();