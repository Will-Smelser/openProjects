(function(){
	var namespace = document.getElementById('seo-api-init').getAttribute('name-space');
	
	if(typeof window[namespace] === "undefined") window[namespace] = {};
	
	window[namespace].body = {
		apiController : 'body',
		dependencies: ['render'],
			
		init:function(){
			window[namespace].load('render');
			this.render = window[namespace].render;
		},
		
		render_all : function(data, $target){
			var scope = this;
			for(var x in scope){
				if(x !== "render_all" && x.indexOf('render') === 0){
					scope[x](data,$target);
				}
			}
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
			
			for(var i in data){
				var $li = render.newLi('Normalized Key Word', i);
				var $ul = render.newEl('ul');
				
				for(var j=0; j<data[i].length; j++){
					$ul.append(render.newEl('li').html(data[i][j]));
				}
				
				$li.append($ul);
				$target.append($li);
			}
		},
		render_getTopPhrases : function(data,$target){
			var render = this.render;
			for(var i in data){
				console.log(i);
				var $li = render.newLi('Normalized Phrase '+
						'('+data[i].actual.length+')',data[i].normal);
				var $ul = render.newEl('ul');
				
				for(var j in data[i].actual){
					$ul.append(render.newEl('li').html(data[i].actual[j]));
				}
				
				$li.append($ul);
				$target.append($li);
			}
		},
		render_getPhrasesSpecific : function(data,$target){
			//TODO: add ability to send parameters with api method
		},
		render_checkInlineCSS : function(data,$target){
			var render = this.render;
			$target.append(render.newLi('Inline CSS count',data.length));
		},
		render_checkInlineStyle : function(data,$target){
			var render = this.render;
			$target.append(render.newLi('Inline &lt;style&gt; count',data.length));
		},
		render_checkLinkTags : function(data,$target){
			var render = this.render;
			
			var ltagcount = 0;
			var ltaghosts = 0;
			for(var host in data){
				ltaghosts++;
				ltagcount += data[host].length;
			}
			
			$target.append(render.newLi('Total &lt;link&gt; tag count',ltagcount));
			$target.append(render.newLi('Total &lt;link&gt; tag host count',ltaghosts));
			
			
		},
		render_getExternalAnchors : function(data,$target){
			var render = this.render;
			$target.append(render.newLi('External &lt;a&gt; tags',data.length));
		},
		render_getInternalAnchor : function(data,$target){
			var render = this.render;
			$target.append(render.newLi('Internal &lt;a&gt; tags',data.length));
		},
		render_checkForFrames: function(data,$target){
			var render = this.render;
			$target.append(render.newLi('Page contains frames?',(data?"True":"False")));
		},
		render_checkForIframes:function(data,$target){
			var render = this.render;
			$target.append(render.newLi('Page contains iframes?',(data?"True":"False")));
		},
		render_checkForFlash:function(data,$target){
			var render = this.render;
			$target.append(render.newLi('Page contains flash/objects?',(data?"True":"False")));
		},
		
		render_checkImages : function(data,$target){
			var render = this.render;
			
			//cycle over results an build better representation
			var myresult = [];
			for(var x in data){
				
				var temp = data[x];
				var result;
				//this is the key=>value data
				switch(data[x].result){
				case 0:
					result = 'Bad Size';
					break;
				case 1:
					result = 'Good';
					break;
				default:
					result = 'Failed';
					break;
				}
					
				var sizeHtml = (temp.result === 1) ? temp.htmlWidth + 'x' + temp.htmlHeight : 'N/A';
				var sizeAct = (temp.result === 1) ? temp.actualWidth + 'x' + temp.actualHeight : 'N/A';
				
				var short = (temp.url.length > 30) ? '...'+temp.url.substr((temp.url.length-30),30) : temp.url;
				var link = render.newEl('a').attr('href',temp.url).attr('target','_blank').html(short);
				myresult.push({
				     'Result':result,
				     'Html Size':sizeHtml,
				     'Actual Size':sizeAct,
				     'Url':link,
				     'Alt':temp.alt,
				     'Title':temp.title
				});
			}
			
			$target.append(render.newTbl(myresult));
			
			
		},
		
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