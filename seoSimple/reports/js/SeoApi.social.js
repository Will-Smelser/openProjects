(function(){
	var temp = document.location.href.split('?');
	namespace = (temp.length > 1) ? temp[1] : 'SeoApi';
	
	if(typeof window[namespace] == "undefined") window[namespace] = {};
	
	window[namespace].social = {
		//ensure render gets loaded
		init:function(){window[namespace].load('render');},
		dependencies: ['render'],
		apiController : 'social',
		
		/**
		 * RENDERINGS
		 */
		
		render_all : function(data, $target){
			var render = window[namespace].render;
			$target.append(render.newLi(data));
			for(var x in data.data){
				$ul.append(createList(x.replace('get',''), data.data[x].data));
			}
			$soc.html($ul);
		},
		
		render_googlePlus : function(data, $target){
			
		},
		
		render_getFbLikeCount : function(data, $target){
			
		},
		
		render_getFbShareCount : function(data, $target){
			
		},
		
		render_getFbCommentCount : function(data, $target){
			
		},
		
		render_getTwitterShares : function(data, $target){
			
		},
		
		render_getDeliciousShares : function(data, $target){
			
		},
		
		render_getDiggShares : function(data, $target){
			
		},
		
		render_getLinkedInShares : function(data, $target){
			
		},
		
		render_getPinterestShares : function(data, $target){
			
		},
		
		render_getStumbleUponShares : function(data, $target){
			
		},
		
		render_getVKontakteShares : function(data, $target){
			
		}
		/*
		render_getTitle : function(data, $target){
			console.log(data);
		}*/
	};
})();