(function(){
	var namespace = document.getElementById('seo-api-init').getAttribute('name-space');
	
	if(typeof window[namespace] === "undefined") window[namespace] = {};
	
	window[namespace].server = {
		//ensure render gets loaded
		init:function(){
			window[namespace].load('render');
		},
		dependencies: ['render'],
		apiController : 'server',
		
		/**
		 * RENDERINGS
		 */
		render_all : function(data, $target){
			var scope = this;
			for(var x in scope){
				if(x !== "render_all" && x.indexOf('render') === 0){
					scope[x](data,$target);
				}
			}
		},
		
		
		render_getWhois : function(data,$target){
			var render = window[namespace].render;
			for(var x in data){
				$target.append(render.newLi(x.replace('_',' '),data[x]));
			}
		},
		
		render_getHeaderResponseLine:function(data,$target){
			var render = window[namespace].render;
			$target.append(render.newLi('HTTP Response Code',data));
		},
		
		render_getLoadTime:function(data,$target){
			var render = window[namespace].render;
			$target.append(render.newLi('Load Time',data+' sec.'));
		},
		
		render_getServer:function(data,$target){
			var render = window[namespace].render;
			$target.append(render.newLi('Server Info',data));
		},
		
		render_isGzip:function(data,$target){
			var render = window[namespace].render;
			$target.append(render.newLi('Gzip Compression',(data?"True":"False")));
		},
		
		render_checkRobots : function(data,$target){
			var render = window[namespace].render;
			$target.append(render.newLi('Robots.txt',data));
		},
		
		render_validateW3C : function(data, $target){
			$target.append('The HTML document is '+(data?'<b>VALID</b>':'<b style="color:red">INVALID</b>'));
		},
		
		render_getValidateW3Cerrors : function(data,$target){
			var render = window[namespace].render;
			if(data.length > 0){
				$target.append(render.newTbl(data));
			}else{
				$target.append('No Errors');
			}
		},
		
		render_getValidateW3Cwarnings : function(data,$target){
			var render = window[namespace].render;
			if(data.length > 0){
				$target.append(render.newTbl(data));
			}else{
				$target.append('No Warnings');
			}
		},
		/*
		render_getTitle : function(data, $target){
			console.log(data);
		}*/
	};
})();