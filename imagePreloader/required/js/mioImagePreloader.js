/**
 * @classDescription Simple helper for managing image preloading.
 * @param {String} id [optional] The id for image preloader hidden wrapper.
 * @author Will Smelser
 */
var mioImagePreloader = function(id)
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
			imageObj.loaded = true;
		},
			
		/**
		 * Hashes url to give a smaller 32 bit positive hash.
		 * @param {String} str The url to hash
		 * @return {String} Hash of the url
		 */
		_hashCode : function(str){
			var hash = 0;
			if (str.length == 0) return hash;
			for (i = 0; i < str.length; i++) {
				char = str.charCodeAt(i);
				hash = ((hash<<5)-hash)+char;
				hash = hash & hash; // Convert to 32bit integer
			}
			return hash << 1; //I want positive numbers
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
		 */
		preload : function(url, callback, scope){
			var hash = this._hashCode(url);
			if(this.isLoaded(hash)) throw "Image already loaded ["+url+"]";
			
			if(typeof scope === "undefined") scope = window;
			
			//create image object
			var $img = $(document.createElement('img'))
				.attr('src',url)
				.attr('id',hash)
				.load(function(obj, hash){
					return function(){
						obj._images[hash].callback.call(obj._images[hash].scope, obj._images[hash]);
						obj._images[hash].func.call(obj, obj._images[hash]);
					}
				}(this, hash));
			
			console.log(hash);
			this._images[hash] = new this._Image(url, callback, scope, false, $img, this); 
			this._$loadNode.append(this._images[hash].$img);
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
			var hash = this._hashCode(url);
			if(!this.isLoaded(hash)) throw "Image did not exist ["+url+"]";
			
			var image = this._images[hash].$img.remove();
			this._images.splice(hash,1);
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
		 * @param {Function} func The function that will be referenced by name.
		 */
		extendImage : function(name, func){
			//todo add prototype stuff
			this._Image.prototype[name] = func;
		}
	}
	preloadObj.id = (typeof id === "undefined") ? preloadObj._loadingNodeId : id;
	preloadObj.createLoadingNode();
	return preloadObj;
};