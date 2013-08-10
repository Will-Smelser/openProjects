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
			this.makeList(data,$target,true,'&lt;H1&gt;');
		},
		render_checkH2 : function(data,$target){
			this.makeList(data,$target,true,'&lt;H2&gt;');
		},
		render_checkH3 : function(data,$target){
			this.makeList(data,$target,true,'&lt;H3&gt;');
		},
		render_checkH4 : function(data,$target){
			this.makeList(data,$target,true,'&lt;H4&gt;');
		},
		render_getKeyWords : function(data,$target){
			var render = this.render;
			for(var i=0; i<5; i++)
				$target.append(render.newLi(data[i].words[0],data[i].count));
		},
		render_getPhrases : function(data,$target){
			var render = this.render;
			
			for(var i=0; i<5 && $i < data.length; i++){
				var $li = render.newLi('Key Word',i);
				var $ul = render.newEl('ul');
				
				for(var j=0; j<data[i].length; j++){
					$ul.append(render.newEl('li').html(data[i][j]));
				}
				$li.append($ul);
				$target.append($li);
			}
		},
		render_getTopPhrases : function(data,$target){},
		render_getPhrasesSpecific : function(data,$target){},
		render_checkInlineCSS : function(data,$target){},
		render_checkInlineCSS : function(data,$target){},
		render_checkLinkTags : function(data,$target){},
		render_checkInlineStyle : function(data,$target){},
		render_getExternalAnchors : function(data,$target){},
		render_checkImages : function(data,$target){},
		
		makeList : function(data, $target, show, label){
			var render = this.render;
			var $li = render.newLi(label,data.length);
			$li.wrapInner(document.createElement('a'));
			if(show)
				$li.append(this.addHXcontent(data));
			else
				$li.append(this.addHXcontent(data).hide());
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