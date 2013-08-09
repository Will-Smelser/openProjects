(function(){
	var temp = document.location.href.split('?');
	namespace = (temp.length > 1) ? temp[1] : 'SeoApi';
	
	if(typeof window[namespace] == "undefined") window[namespace] = {};
	
	window[namespace].body = {
		apiController : 'body',
			
		init:function(){
			window[namespace].load('render');
			this.render = window[namespace].render;
		},
		
		render_all : function(data, $target){
			
		},
		
		render_checkH1 : function(data,$target){
			this.makeList(data,$target,'&lt;H1&gt;');
		},
		render_checkH2 : function(data,$target){},
		render_checkH3 : function(data,$target){},
		render_checkH4 : function(data,$target){},
		render_getKeyWords : function(data,$target){},
		render_getPhrases : function(data,$target){},
		render_getTopPhrases : function(data,$target){},
		render_getPhrasesSpecific : function(data,$target){},
		render_checkInlineCSS : function(data,$target){},
		render_checkInlineCSS : function(data,$target){},
		render_checkLinkTags : function(data,$target){},
		render_checkInlineStyle : function(data,$target){},
		render_getExternalAnchors : function(data,$target){},
		render_checkImages : function(data,$target){},
		
		makeList : function(data, $target, label){
			var render = this.render;
			var $li = render.newLi(label,data.length);
			$li.wrapInner(document.createElement('a'));
			$li.append(this.addHXcontent(data));
			$li.click(function(){$(this).find('ul').slideToggle();});
			$target.append($li);
		},
		addHXcontent : function(obj){
			var $ul = $(document.createElement('ul'));
			for(var x in obj){
				
				var div = document.createElement('div');
				var txt = obj[x].text;
				div.innerHTML = txt;

				if(typeof div.textContent === "string")
					txt = div.textContent;
				else if(typeof div.innerText === "string")
					txt = div.innerText;

				$ul.append($(document.createElement('li')).html(txt));
			}
			return $ul;
		}
		
		/*,
		render_getTitle : function(data, $target){
			console.log(data);
		}*/
	};
})();