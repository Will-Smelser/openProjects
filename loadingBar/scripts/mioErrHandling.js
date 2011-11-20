//requires using firebug with "console" abilities
var mioErrHandling = function(){
	var myDate = new Date();
	
	return {
		date : myDate,
		errors : [], warn:[], debug:[], info:[], log:[],
			
		//1=debug, 2=info, 3=warn, 0=error 
		addError : function(msg,level){
			switch(level){
				default:
				case '0':
				case 'error':
					this.errors.push({'errType':'error','msg':msg,'time':this.getTime(),'unix':this.date.getTime()});
					break;
				case '1':
				case 'warn':
					this.warn.push({'errType':'warn','msg':msg,'time':this.getTime(),'unix':this.date.getTime()});
					break;
					break;
				case '2':
				case 'debug':
					this.debug.push({'errType':'debug','msg':msg,'time':this.getTime(),'unix':this.date.getTime()});
					break;
				case '3':
				case 'info':
					this.info.push({'errType':'info','msg':msg,'time':this.getTime(),'unix':this.date.getTime()});
					break;
				case '4':
				case 'log':
					this.log.push({'errType':'log','msg':msg,'time':this.getTime(),'unix':this.date.getTime()});
					break;
			}
		},
		getTime : function(){
			var time = '';
			return this.date.getMinutes() + ':'+this.date.getSeconds()+':'+this.date.getMilliseconds();
		},
		resetLog : function(errvar){
			errvar = [];
		},
		execLogErrors : function(errvar){
			for(var x in errvar){
				console[errvar[x].errType]('['+errvar[x].time+'] '+errvar[x].msg);				
			}
		},
		/**
		 * Just print errors to the console log, but do not clear the error log que
		 */
		printError : function(){
			/*
			this.execLogErrors(this.errors);
			this.execLogErrors(this.warn);
			this.execLogErrors(this.debug);
			this.execLogErrors(this.info);
			this.execLogErrors(this.log);*/
			 
			this.execLogErrors(this._orderAllErrorsByTime());
		},
		/**
		 * Print errors to console, clear the error log que, return boolean if error count greater than 0
		 */
		returnError : function(){
			this.execLogErrors(this._orderAllErrorsByTime());
			this._resetAllLogs();
			
			return (this.errors.length > 0) ? true : false;
		},
		log : function(msg){
			console.log(msg);
		},
		_resetAllLogs : function(){
			this.resetLog(this.errors);
			this.resetLog(this.warn);
			this.resetLog(this.debug);
			this.resetLog(this.info);
			this.resetLog(this.log);
		},
		_joinErrors : function(arr){
			var tmp = [];
			for(var x in arr){
				for(var y in arr[x]){
					tmp.push(arr[x][y]);
				}
			}
			return tmp;
		},
		_orderAllErrorsByTime : function(){
			var tmp = this._joinErrors([this.errors,this.warn,this.debug,this.info,this.log]);
			return this._orderErrorsByTime(tmp,[]);
		},
		_orderErrorsByTime : function(errorArrObj,newErrArr){
			
			if(errorArrObj.length == 0){
				//return a copy of the array
				newErrArr = errorArrObj.slice(0);
				return newErrArr;
			}
			
			//if this is the first time through
			if(newErrArr.length == 0){
				var tmp = errorArrObj.pop();
				newErrArr.push(tmp);
				return this._orderErrorsByTime(errorArrObj,newErrArr);
			}
			
			//get the value to compare
			var tmp = errorArrObj.pop();
			
			//just make sure we did enter something
			var entered =  false;
			
			//cycle the new Array looking for where to insert the new value
			for(var x in newErrArr){
				//handle getting the previous value...default to 0 if it doesnt exist and trap the error
				var prev = 0;
				if(x > 0) prev = newErrArr[x-1].unix;
				
				//the value is between last value and current value....so enter befor this value
				//alert(tmp.unix +' < '+ newErrArr[x].unix +' && ' + tmp.unix + ' >= ' +prev);
				if(tmp.unix <= newErrArr[x].unix && tmp.unix >= prev){
					//now we need to splice this into the array
					newErrArr.splice(x,0,tmp);
					entered = true;
					break;
				}
			}
			
			//make sure we dont loose any errors
			if(!entered){
				newErrArr.push(tmp);
				newErrArr.push({'errType':'warn','msg':'Previous error was not inserted properly...timeline may be incorrect','time':tmp.time,'unix':tmp.unix});
			}
			
			if(errorArrObj.length > 0){
				this._orderErrorsByTime(errorArrObj,newErrArr);
			}

			return newErrArr;
		}
	}
}