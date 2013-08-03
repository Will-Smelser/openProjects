(function(){
	var temp = document.location.href.split('?');
	namespace = (temp.length > 1) ? temp[1] : 'SeoApi';
	
	var jsLoc = temp[0].split('/');
	jsLoc.pop();
	jsLoc = jsLoc.join('/');
	
	console.log(jsLoc);
	
	console.log('loading: window.'+namespace);
	
	window[namespace] = {
			
		load : function(api, apiObject){
			if(typeof apiObject == "undefined"){
				apiObject = api;
				api = null;
			}
			
			$.ajax({
			  url: jsLoc + '/js/SeoApi.'+apiObject+'.js?'+namespace,
			  dataType: "script",
			  cache: true,
			  success: function(){
				  if(apiObject !== 'base')
					  window[namespace][apiObject] = $.extend({}, window[namespace].base, window[namespace][apiObject]);
				  
				  if(api !== null)
					  window[namespace][apiObject].api = api;
			  },
			  failure: function(){
				  console.log("Failed to load api object ("+apiObject+")");
				  window[namespace][apiObject] = {};
			  }
			});
		},
		extend : function(apiObject, targetObj, object){
			var scope = this;
			if(typeof window[namespace][targetObj] === "undefined"){
				setTimeout(function(){scope.extend(apiObject, targetObj, object);},100);
			}else{
				window[namespace][apiObject] = $.extend({},window[namespace][targetObj], object);
			}
		},
		waitOn : function(apiObject, func){
			var scope = this;
			if(typeof window[namespace][apiObject] === "undefined")
				setTimeout(function(){scope.waitOn(apiObject, func);},100);
			else
				func();
		}
			
	};

})();