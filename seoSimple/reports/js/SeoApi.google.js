(function(){
	var namespace = document.getElementById('seo-api-init').getAttribute('name-space');
	
	if(typeof window[namespace] === "undefined") window[namespace] = {};
	
	window[namespace].google = {
		//ensure render gets loaded
		init:function(){window[namespace].load('render');},
		dependencies: ['render'],
		apiController : 'google',
		
		/**
		 * RENDERINGS
		 */
		
		render_getPageRank : function(data, $target){
			$target.html(data);
		},
		render_getBacklinks : function(data, $target){
			var render = window[namespace].render;

			var $ul = render.newEl('ul');
			
			$ul.append(render.newLi('Total Domains',data.domainTotals));
			$ul.append(render.newLi('Total Backlinks',data.backlinks.length));
			$ul.append(render.newLi('Totals by Domain','<div style="display:inline-block;max-width:600px;">'+data.domainComposite+'</div>'));
			for(var x in data.backlinks){
				var title = data.backlinks[x].title;
				var link = data.backlinks[x].link;
				var a = "<a href='"+link+"'>"+title+"</a>";
				$ul.append(render.newLi('Link',a));
			}
			$target.html($ul);
		}
		/*
		render_getTitle : function(data, $target){
			console.log(data);
		}*/
	};
})();