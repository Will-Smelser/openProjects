(function(){
	var temp = document.location.href.split('?');
	namespace = (temp.length > 1) ? temp[1] : 'SeoApi';
	
	var jsLoc = temp[0].split('/');
	jsLoc.pop();
	jsLoc = jsLoc.join('/');
	
	var apiObject = null;
	
	
	window[namespace] = {
		currentApiObject:null,
			
		/**
		 * Can be overwritten, called after
		 * the object has finished loading
		 */
		init:function(){},
		
		/**
		 * Takes 1 or 2 parameters
		 * apiObject is null, then api variable is copied 
		 * to apiObject and api is set to null.
		 * @param api  The url of seo api
		 * @param apiObject The api controller to load
		 */
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
				  
				  //TODO fix this!
				  window[namespace].waitOn('base', function(){
					  if(apiObject !== 'base')
						  window[namespace][apiObject] = $.extend(true, {}, window[namespace].base, window[namespace][apiObject]);

					  if(api !== null)
						  window[namespace][apiObject].api = api;
					  
					  if(typeof window[namespace][apiObject].init === 'function')
						  window[namespace][apiObject].init();
					  
				  });
				  
			  },
			  failure: function(){
				  console.log("Failed to load api object ("+apiObject+")");
				  window[namespace][apiObject] = {};
			  }
			});
			
			var self = $.extend(true,{},this);
			self.currentApiObject = apiObject;
			
			//has to wait on itself to load
			self.depends(apiObject);
			
			return self;
		},
		/**
		 * 
		 * @param apiObject
		 * @param targetObj Should be "base"
		 * @param object
		 */
		extend : function(apiObject, targetObj, object){
			var scope = this;
			if(typeof window[namespace][targetObj] === "undefined"){
				setTimeout(function(){scope.extend(apiObject, targetObj, object);},50);
			}else{
				if(typeof window[namespace][apiObject] === "undefined")
					window[namespace][apiObject] = {};
				
				$.extend(true,window[namespace][apiObject],window[namespace][targetObj],object);				
			}
		},
		/**
		 * Wait on a api object to complete loading
		 * @param apiObject The api object/controller to wait on.  
		 * For example: base, body, head, google, etc... 
		 * @param func The callback function to execute one it has loaded
		 */
		waitOn : function(apiObject, func){
			var scope = this;
			if(typeof window[namespace][apiObject] === "undefined")
				setTimeout(function(){scope.waitOn(apiObject, func);},50);
			else
				func();
		},
		
		addMethod : function(method,target){
			var apiObject = this.currentApiObject;
			this.waitOn(this.currentApiObject,function(){
				window[namespace][apiObject].addMethod(method,target);
			});
			
			return this;
		},
		
		exec : function(url, callback, errCallback){
			var scope = this;
				
			this.ready(function(){
				window[namespace][scope.currentApiObject].exec(url, callback, errCallback);
			});
			
			return this;
		},
		
		dependencies : [],
		depends : function(apiObject){
			this.dependencies.push(apiObject);
			return this;
		},
		
		/**
		 * Wait till all dependencies are ready
		 * @param callback
		 * @returns self
		 */
		ready : function(callback){
			var scope = this;
			var ready = true;
			for(dep in this.dependencies){
				if(typeof window[namespace][this.dependencies[dep]] === "undefined"){
					ready = false;
					break;
				}
			}
			
			if(!ready) 
				setTimeout(function(){scope.ready(callback);},50);
			else 
				callback();
			
			return this;
		}
			
	};

})();