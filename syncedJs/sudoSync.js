var sync = function(scope){
	return {
		scope : scope,
		run: function(){},
		after : function(){},
		args : [],
		
		callback : function(){
			this.after.apply(this.scope, Array.prototype.slice.call(arguments));
		},
		
		sync : function(/*callbackReference, func, args..., scope, anonymouseFunction */){
			//handle the contructor arguments
			this.after = arguments[arguments.length-1];
			
			var func = arguments[0];			
			this.args = Array.prototype.slice.call(arguments).slice(1, arguments.length-1);
			
			console.log("step 2");
			func.apply(this.scope, this.args);
			
			return; 
		},
		
		getCallback : function(){
			var scope = this;
			return function(){scope.callback.call(scope, Array.prototype.slice.call(arguments));}
		}
	};
};