(function(){
	var namespace = document.getElementById('seo-api-init').getAttribute('name-space');
	
	if(typeof window[namespace] === "undefined") window[namespace] = {};
	
	window[namespace].moz = {
		apiController : 'moz',
		
		init:function(){
			window[namespace].load('render');
			this.render = window[namespace].render;
		},
		
		render_all : function(data,$target){
			this.render_getMozLinks(data.getMozLinks.data,$target);
			this.render_getMozJustDiscovered(data.getMozJustDiscovered.data, $target);
		},
		
		render_getMozLinks : function(data,$target){
			var render = window[namespace].render;
			$target.append(render.newLi('Page Authority',data.pageAuthority));
			$target.append(render.newLi('Domain Authority',data.domainAuthority));
			$target.append(render.newLi('Inbound Links',data.totalInboundLinks));
			$target.append(render.newLi('Inbound Domains',data.linkingRootDomains));
		},
		
		render_getMozJustDiscovered:function(data,$target){
			var render = window[namespace].render;
			for(var x in data){
				
				var $li = render.newEl('li');
				var $ul = render.newEl('ul');
				
				$ul.append(render.newLi('Link Text', data[x].text));
				$ul.append(render.newLi('Page Authority', data[x].pageAuthority));
				$ul.append(render.newLi('Domain Authority',data[x].DomainAuthority));
				$ul.append(render.newLi('Discovery Time',data[x].DiscoveryTime));

				var $a = render.newEl('a').attr('target','_blank').attr('href',data[x].link).html(data[x].link);
				
				$li.append($a);
				$li.append($ul);
				$target.append($li);
			}
		}
		
		/*
		render_getTitle : function(data, $target){
			console.log(data);
		}*/
	};
})();