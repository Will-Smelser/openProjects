/**
 * Returns a "loadingbar" object
 * @function
 * @param barId string The unique id in the DOM of the loading bar
 * @param wrapId string The unique id in the DOM of the loading bar wrapper
 * @param animation string ['normal','centered','bounce'] The animation type
 * @return object The loading bar object and methods
 */
var loadingbar = function(barId,wrapId,animation){
	/**
	 * Just a temporary object created to make jsDoc work...this object is returned in this function
	 * @class The object holding methods for loadingbar object
	 * @param object Just a wrapper for this object to be returned 
	 */
	var obj = {
		/**
		 * The interval passed to setInterval for the resize() function call
		 * @type Integer
		 * @param interval Integer The time in milliseconds between resize() calls
		 */
		interval:25,  //the intervall timeout in milliseconds
		
		/**
		 * The minimum number of cycles to run the loader cycles
		 * @type Integer
		 * @param minCycle Integer The minimum number of cycles to run the loading bar
		 */
		minCycle:1,
		
		/**
		 * The current cycle number
		 * @type Integer
		 */
		cycle:0,
		
		/**
		 * The lower limit bound to the width of the loading bar in pixels
		 * @type Integer
		 */
		wL:5,
		
		/**
		 * aL string ['left','center','right'] Align the loading bar during 1st cycle and every odd cycle there after.
		 * @type String
		 * @see aR
		 */
		aL:'left', 
		
		/**
		 * aR string ['left','center','right'] Align the loading bar during 2nd cycle and every even cycle there after.
		 * @type String
		 * @see aL
		 */
		aR:'right',
		
		/**
		 * hooks Array This holds the function you want to be run after the animation has stopped
		 * @type Array
		 * @private
		 * @see addStopHook()
		 */
		hooks:[],
		
		/**
		 * Resize the loading bar animation
		 * This is called via a setInterval
		 * @return void 
		 */
		sizer : function(){
			var x = this.loadBar.style.width.replace('px','')*1;
			if(x>=this.wU || x<=this.wL){
				this.s=-1*this.s;
				this.cycle++; //number of times this has been called
			}
			this.loadWrapper.style.textAlign = ( (this.cycle%4)<2 ) ? this.aL : this.aR;
			this.loadBar.style.backgroundPosition = ( (this.cycle%4)<2 ) ? this.aL : this.aR;
			this.loadBar.style.width= x + (this.s) * 5+'px';
		},
		/**
		 * Stop the sizer animation.  Will not stop it, till the resizer has occurred this.minCycle # of times
		 * @return void
		 * @see cycle
		 * @see minCycle
		 * @see sizer()
		 */
		stop : function(){
			var ref = this;
			if(this.cycle>this.minCycle){
				clearInterval(this.bI);
				this.bI=null;
				for(var x in this.hooks){
					this.hooks[x]();
				}
			}else{
				setTimeout(function(){ref.stop();},100);
			}
		},
		/**
		 * Use this to add a function to be called once the loader has stopped running
		 * @function
		 * @param func This is a reference to a function you want to be called after the animation has been stopped.
		 * @example myloader.addStopHook(function(){alert('Hello World');})
		 * @return void
		 */
		addStopHook : function(func){
			if(typeof func == 'function'){
				this.hooks.push(func);
			}
		},
		/**
		 * Initialize the object and create the setInterval
		 * @return void
		 */
		run : function(){
			if(typeof this.bI == 'number') return;
			var ref = this;
			
			//setup the objects
			this.loadBar=document.getElementById(barId);
			this.loadWrapper=document.getElementById(wrapId);
			this.wU = this.loadWrapper.style.width.replace('px','')-5;
			
			//initialize some values
			this.s = -1;
			
			//set the alignments based on animation type
			if(typeof animation == 'undefined') animation = 'normal';
			switch(animation){
			case 'normal':
				this.aR = 'left';
				this.aL = 'left';
				break;
			case 'bounce':
				this.aR = 'right';
				this.aL = 'left';
				break;
			case 'center':
				this.aR = 'center';
				this.aL = 'center';
				break;
			}
			this.loadWrapper.style.textAlign = this.aL;
			
			//start running the sizing code
			this.bI = setInterval(function(){ref.sizer()},this.interval);
		}
	};
	return obj;
};
