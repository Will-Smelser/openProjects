/**
 * @classDescription Simple helper for managing image preloading.
 * @param {String} id [optional] The id for image preloader hidden wrapper.
 * @author Will Smelser
 */
var Preload = function(id)
{
	/** @class Return object */
	preloadObj = {
		/**
		 * DOM id to the preload node which holds the image elements
		 * @property {String}
		 */
		_loadingNodeId : 'mioPreloadNode',
		
		/**
		 * @classDescription Image object which holds information about the image which is preloading.
		 * This object can be prototyped with extendImage.
		 * @param {String} url The url of image
		 * @param {Function} callback The users callback function
		 * @param {Object} scope The scope to call callback function in
		 * @param {Boolean} loaded Whether the image has been loaded
		 * @param {Jquery Object} $img The reference to the jquery image object in preloading node.
		 * @param {Object} objRef This object's reference
		 * @see extendImage()
		 */
		_Image : function(url, callback, scope, loaded, $img, objRef){
			this.url = url;
			this.callback = callback;
			this.scope = scope;
			this.loaded = loaded;
			this.$img = $img;
			this.func = objRef._imageLoadComplete;
			this.loadFailed = true;
			this.timeout = 5000;//5 seconds
			this.start = new Date().getTime();
			this.finish = 0;
		},
		
		/**
		 * An array of _Image objects
		 * @property 
		 */
		_images : [],
		
		/**
		 * Creates the loading node.
		 * @see _loadingNodeId
		 */
		createLoadingNode : function(id){
			this._$loadNode = $(document.createElement('div'))
				.attr('id',id)
				.css('display','none')
				.appendTo($('body'));
		},
		
		/**
		 * Called when the image finishes loading.
		 * @param {Object} imageObj An _Image object
		 * @see _Image
		 */
		_imageLoadComplete : function(imageObj){
			//we may have already called this.
			if(imageObj.loaded) return;
			
			imageObj.finish = (imageObj.finish === 0) ? new Date().getTime() : imageObj.finish;
			imageObj.loaded = true;
			imageObj.loadFailed = ((imageObj.finish-imageObj.start) > imageObj.timeout);
			
			if(imageObj.loadFailed) console.log("Image failed to load", imageObj);
			
			//users callback function
			if(typeof imageObj.callback === "function")
				imageObj.callback.call(imageObj.scope, imageObj);
		},
			
		/**
		 * Hashes url to give a smaller 32 bit positive hash.
		 * @param {String} str The url to hash
		 * @return {String} Hash of the url
		 */
		_hashCode : function(str){
			str = str + "";
			var hash = 0;
			if (str.length == 0) return hash;
			var len = str.length;
			for (var i = 0; i < len, i<100; i=i+1) {
				if(i>=len) break;
				char = str.charCodeAt(i);
				
				hash = ((hash<<5)-hash)+char;
				hash = hash & hash; // Convert to 32bit integer
			}

			return hash >>> 1; //I want positive numbers
		},
		
		/**
		 * Check if the image is already preloading.
		 * @param {String} url The url to check if _Image object exists for.
		 */
		isLoaded : function(url){
			var hash = this._hashCode(url);
			return (typeof this._images[hash] !== "undefined");
		},
		
		/**
		 * Check if an image is finished loading.
		 * @param {String} url The url to check if _Image object has finished loading image for.
		 */
		isLoadedComplete: function(url){
			var hash = this._hashCode(url);
			return (this.isLoaded(url) && this._images[hash].loaded);
		},
		
		/**
		 * Preload an image.
		 * @param {String} url The url of the image to load.  Absolute or relative.
		 * @param {Function} callback A function to be called when load is complete.
		 * @param {Object} scope [optional] The scope to call the callback in. Defaults to window.
		 * @param {Integer} timeout [optional] The timeout for loading image.  Defaults to _Image.timeout.  
		 * 		Can override this using prototype or ImageExtend
		 * @return {_Image} The created image object
		 */
		preload : function(url, callback, scope, timeout){
			var hash = this._hashCode(url);
			if(this.isLoaded(hash)) return;
			
			if(typeof scope === "undefined") scope = window;
			
			
			//create image object
			var $img = $(document.createElement('img'))
				.attr('src',url)
				.attr('id',hash);
			this._images[hash] = new this._Image(url, callback, scope, false, $img, this);
			
			
			//bind the onload...pass vars using closure ;)
			this._images[hash].$img.load(function(obj, hash){
					//only executes on successful load
					return function(){obj._images[hash].func.call(obj, obj._images[hash]);};
				}(this, hash));
			
			//we always want to trigger our functions, but jquery only
			//callsback on success, so have to keep poll at the image timeout
			if(typeof timeout !== "undefined") this._images[hash].timeout = timeout;
			this.waitOnImage(url, this._imageLoadComplete, this, this._images[hash].timeout, 2);
			
			this._$loadNode.append(this._images[hash].$img);
			
			return this._images[hash];
		},
		
		/**
		 * Bind specific callback to wait on image
		 * @param {String} url The url of image to wait on load for
		 * @param {Function} callback Callback function to call when loaded
		 * @param {Object} scope The scope to call callback in.  Default to window.
		 * @param {Integer} perdiod Time in milliseconds between recalling wait
		 * @param {Integer} maxTries max attempts to call this function
		 * @param {Integer} tryCount defaults to 0.  Used for cancelling this.
		 */
		waitOnImage : function(url, callback, scope, period, maxTries, tryCount){
			if(typeof period === "undefined") period = 50;
			if(typeof maxTries === "undefined") maxTries = 100;
			if(typeof scope === "undefined") scope = window;
			if(typeof tryCount == "undefined") tryCount = 0;
						
			var hash = this._hashCode(url);
			var imgObj = this._images[hash];
			
			if(typeof imgObj === "undefined"){
				this.preload(url, null, null);
			} else if(imgObj.loaded || tryCount > maxTries){
				callback.call(scope, imgObj);
				return;
			}
			
			//use closures to call self
			setTimeout(
				function(url, callback, scope, period, maxTries, tryCount, _this){
					return function(){
						_this.waitOnImage(url, callback, scope, period, maxTries, tryCount);
					};
				}(url, callback, scope, period, maxTries, ++tryCount, this)
			,period);
			
		},
		
		/**
		 * Get the image object by url.
		 * @param {String} url The url of image to lookup.
		 * @return {Object} _Image
		 * @requires Requires the image is preloaded.
		 */
		getImage : function(url){
			var hash = this._hashCode(url);
			return this._images[hash];
		},
		
		/**
		 * Remove an object from _images array and from the image loading node.
		 * @param {String} url The url of _Image object to remove.
		 */
		removeImage : function(url){
			if(!this.isLoaded(url) || !this.isLoadedComplete(url)) return;//throw "Image did not exist ["+url+"]";
			var hash = this._hashCode(url);
			this._images.splice(hash,1);
			this._images[hash].$img.remove();
		},
		
		/**
		 * Empty the _images array and empty the image loading node.
		 */
		clearCache : function(){
			this._images = [];
			this._$loadNode.empty();
		},
		
		/**
		 * Access the prototype to _Image to add your own attributes.
		 * @param {String} name Name of the reference in prototype.
		 * @param {Object} obj The function that will be referenced by name.
		 */
		extendImage : function(name, func){
			//todo add prototype stuff
			this._Image.prototype[name] = func;
		}
	};
	preloadObj.id = (typeof id === "undefined") ? preloadObj._loadingNodeId : id;
	preloadObj.createLoadingNode(preloadObj.id);
	return preloadObj;
};