(function(){
	
	var el = document.getElementById('seo-api-init');
	var namespace = el.getAttribute('name-space');
	var jsLoc = el.getAttribute('src').split('/');
	jsLoc.pop();
	jsLoc = jsLoc.join('/');
	
	
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
		 * @param callback A callback to be executed after load and merge
		 */
		load : function(api, apiObject, callback){
			
			if(typeof apiObject == "undefined"){
				apiObject = api;
				api = null;
			}
			
			
			$.ajax({
			  url: jsLoc + '/SeoApi.'+apiObject+'.js',
			  dataType: "script",
			  cache: true,
			  success: function(){
				  
				  //TODO fix this!
				  //window[namespace].waitOn('base', function(){
				  window[namespace].ready(function(){
					  if(apiObject !== 'base')
						  window[namespace][apiObject] = $.extend(true, {}, window[namespace].base, window[namespace][apiObject]);

					  if(api !== null)
						  window[namespace][apiObject].api = api;
					  
					  if(typeof window[namespace][apiObject].init === 'function')
						  window[namespace][apiObject].init();
					  
					  if(typeof callback === "function") callback();
					  
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
		
		addMethod : function(method,target){
			var apiObject = this.currentApiObject;
			this.ready(function(){
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
		
		dependencies : ['base'],
		depends : function(apiObject){
			this.dependencies.push(apiObject);
			return this;
		},
		
		/**
		 * Wait till all dependencies are ready
		 * @param callback Callback to fire one all the dependencies are loaded
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
			
			(!ready)? setTimeout(function(){scope.ready(callback);},50) : callback();
			
			return this;
		}
			
	};

})();