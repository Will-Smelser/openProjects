var SeoApiBase = {
	url : '',
	api : 'should be overwritten by global',
	methodAll : false,
	methods : [],
	targetMap : {},
	failObj : (function(){return $("<div class='seo-fail'><span>Failed</span><div class='reason'></div></div>");})(),
	buildRequest : function(url){
		this.checkApi();
		var method = (this.methodAll) ? 'all' : this.methods.join('|');
		return this.api+this.apiController+'/'+method+'?request='+url;
	},
	checkApi : function(){
		if(typeof window.SeoApi == "undefined"){
			console.log('Missing global window.SeoApi');
			return false;
		}
		this.api = window.SeoApi;
		return true;
	},
	/**
	 * add a method for request
	 * @param method The API method to call
	 * @param target Either dom object or function to callback
	 */
	addMethod : function(method, target, params){
		this.methods.push(method);
		this.targetMap[method] = target;
		if(method === "all")
			this.methodAll = true;
	},
	setMethodToAll : function(){this.methodAll = true;},
	handleSuccess : function(data){
		//success does not mean we got a good response,
		//just that it wasnt an HTTP error
		//TODO handle error cases
		for(var method in data){
			if(typeof this.targetMap[method] == "function"){
				this.targetMap[method](data[method]);
			}else{
				//render this
				this.handleSuccessTrue(method, data[method],this.targetMap[method]);
			}
		}
	},
	handleSuccessTrue : function(method, data, $target){
		if(typeof this['render_'+method] != "function"){
			console.log('Custom handler render_'+method+' did not exist. Using default...');
			$target.html("");
			this.defaultRender(data.data, $target);
		}else{
			this['render_'+method](data, $target);
		}
	},
	handleError : function(){
		for(var x in this.methods){
			console.log(this.methods[x]);
			this.targetMap[this.methods[x]].html(this.failObj.find('.reason').html('Ajax Request Failure'));
		}
	},
	//need to handle this better
	defaultRender : function(data, $target){
		var rUrl = (typeof window.SeoReport == "undefined") ? "" : "js/";
		rUrl += 'prettyPrint.js';
		$.ajax({
		  url: rUrl,
		  dataType: "script",
		  cache: true,
		  success: function(){
			  $target.html(prettyPrint(data));
		  }
		});
	},
	exec : function(url, callback, errCallback){
		var scope = this;
		this.url = url;
		var req = this.buildRequest(url);
		
		if(typeof callback != "function")
			callback = this.handleSuccess;
		if(typeof errCallback != "function")
			errCallback = this.handleError;
		
		//information
		$.getJSON(req,function(data){
			//we dont get nested responses when
			//only a single method is requested,
			//this will give us same response regardless
			var resp = {};
			if(scope.methods.length === 1){
				resp[scope.methods[0]] = {
						data : data.data
				};
			}else
				resp = data.data;
			
			callback.call(scope, resp);
		})
		.fail(function(){errCallback.call(scope);});
	},
	
	/**
	 * Overwrite all the below with your own handler and controller.
	 */
	
	/**
	 * Which controller the API should use
	 */
	apiController : 'body',
	
	/**
	 * This class is intended to be extended.  So just extend
	 * this class with $.extend and overwrite your api method
	 * call with the following format:<br/>
	 * <code>
	 * $.extend(SeoApiBase, {render_apiMethod:function(data,$target){
	 * 		//do some stuff with data
	 *      //$target.html('Request done');
	 * }});
	 * </code>
	 * 
	 * If you do not set a render for a given api method, then the
	 * default handler will be used.  This handler uses prettyPrint.js
	 * http://james.padolsey.com/javascript/prettyprint-for-javascript/.
	 * 
	 * This is more like a diagnostic.
	 * @param data The api returned json objbect
	 * @param $target The jquery target initially given to this api request.
	 */
	render_apiMethod : function(data, $target){
				
	}
};