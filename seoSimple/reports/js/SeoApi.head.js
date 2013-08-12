(function(){
	var namespace = document.getElementById('seo-api-init').getAttribute('name-space');
	
	if(typeof window[namespace] === "undefined") window[namespace] = {};
	
	window[namespace].head = {
		apiController : 'head',
		dependencies: ['render'],
		
		init:function(){
			window[namespace].load('render');
			this.render = window[namespace].render;
		},
		
		render_all : function(data,$target){
			this.render_getTitle(data.getTitle.data,$target);
			this.render_getMetaDesc(data.getMetaDesc.data, $target);
			this.render_getMetaKeywords(data.getMetaKeywords.data, $target);
			this.render_getFavicon(data.getFavicon.data, $target);
			this.render_getFaviconNoTag(data.getFaviconNoTag.data, $target);
			this.render_getDoctype(data.getDoctype.data, $target);
			this.render_getEncoding(data.getEncoding.data, $target);
			this.render_getLang(data.getLang.data, $target);
		},
		
		render_getTitle : function(data,$target){
			$target.append(
				this.render.newLi('Title',this.getText(data.text))
			);
		},
		
		render_getMetaDesc : function(data,$target){
			console.log(data);
			$target.append(
				this.render.newLi('Meta Description',this.getText(data))
			);
		},
		
		render_getMetaKeywords : function(data,$target){
			$target.append(
				this.render.newLi('Meta Keywords',this.getText(data))
			);
		},
		
		render_getFavicon : function(data,$target){
			$target.append(
				this.render.newLi('Favicon',this.getText(data))
			);
		},
		
		render_getFaviconNoTag : function(data, $target){
			$target.append(
				this.render.newLi('Favicon (No Tag)',this.getText(data))
			);
		},
		
		render_getDoctype : function(data, $target){
			$target.append(
				this.render.newLi('Document Type',this.getText(data))
			);
		},
		
		render_getEncoding : function(data, $target){
			$target.append(
				this.render.newLi('Encoding',this.getText(data))
			);
		},
		
		render_getLang : function(data, $target){
			$target.append(
				this.render.newLi('Language',this.getText(data))
			);
		},
		
		getText : function(str){
			return (str === null) ? "None" : str;
		}
		/*
		render_getTitle : function(data, $target){
			console.log(data);
		}*/
	};
})();