(function(){
	var temp = document.location.href.split('?');
	namespace = (temp.length > 1) ? temp[1] : 'SeoApi';
	
	if(typeof window[namespace] === "undefined") window[namespace] = {};
	
	window[namespace].semrush = {
		apiController : 'semrush',
		dependencies: ['render'],
		
		init:function(){
			window[namespace].load('render');
			this.render = window[namespace].render;
		},
		
		render_all : function(data,$target){
			this.render_getDomainReport(data.getDomainReport.data,$target);
			this.render_getKeyWordsReport(data.getKeyWordsReport.data, $target);
		},
		
		render_getDomainReport : function(data,$target){
			var render = window[namespace].render;
			for(var x in data){
				var temp = data[x];
				$target.append(render.newLi(temp['short'],temp.data));
			}
		},
		
		render_getKeyWordsReport : function(data,$target){
			var render = window[namespace].render;
			
			for(var x in data){
				var $li = render.newEl('li');
				var $ul = render.newEl('ul');
			
				//key word report
				var temp = data[x];
				
				$li.html(temp.Ph.data);
				for(var y in temp)	
					$ul.append(render.newLi(temp[y]['short'],temp[y].data));

				$target.append($li.append($ul));
			}
		}
		
		/*
		render_getTitle : function(data, $target){
			console.log(data);
		}*/
	};
})();