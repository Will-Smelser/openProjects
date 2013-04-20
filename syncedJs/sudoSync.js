var sync = function(){
	return {
		scope : window,
		after : function(){},
		args : [],
		
		callback : function(){
			this.after.apply(this.scope, this.args);
		},
		
		sync : function(/*callbackReference, func, args..., scope, anonymouseFunction */){
			
			//handle the contructor arguments
			this.after = arguments[arguments.length-1];
			this.scope = arguments[arguments.length-2];
			var func = arguments[0];			
			this.args = Array.prototype.slice.call(arguments).slice(1, arguments.length-2);
			
			console.log("step 2");
			func.apply(this.scope, this.args);
			
			return; 
		},
		
		getCallback : function(){
			var scope = this;
			return function(){scope.callback.call(scope, Array.prototype.slice.call(arguments));}
		}
	}
}