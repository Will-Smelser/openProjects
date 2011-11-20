jQuery.fn.miomenu = function(){
	
	return this.children('ul').children().each(function(index,el){
		mioMenu.width = $(el).width();
		mioMenu.height= $(el).find('li a').first().height();
		mioMenu.tabHeight = $(this).outerHeight();
		mioMenu.addNavHtml(el);
		mioMenu.bindHoverRec(el);
		mioMenu.bindHoverTab(el);
	});
}

var mioMenu = {
	width: 0,
	height: 0,
	tabHeight : 0,
	
	fade : false,
	left : true,
	top  : false,
	
	speed : 'slow',
	
	hooks : {'before':[],'after':[]},
	
	addNavHtml : function(el){
		var html = '<div class="lower">&nbsp;</div>';
		$(el).children('a').first().before(html);
	},
	bindHoverTab : function(el){
		var obj = this;
		$(el).hover(function(){
			var tmp = $(el).children('div').first();
			$(el).stop();
			tmp.css({'height':'0px'});
			tmp.animate({'height':obj.tabHeight+'px'},obj.speed);//,function(){obj.clearCss(tmp);});
		});
	},
	//take in an dom elment of LI
	bindHoverRec : function(el){
		
		//scoping
		var obj = this;
				
		//the first UL child
		var ul = $(el).children('ul').first();
		
		if($(ul).children().length > 0){
			//bind the LI with a hover event
			$(el).hover(function(){obj.animShow(ul)});
			
			//bind all child LIs also
			$(ul).children().each(function(index,el){
				obj.bindHoverRec(el);
			});
		}
		
	},
	makeOpt : function(start, el){
		var opt = {};
		if(this.fade){opt['opacity'] = (start) ? '0' : '1';}
		if(this.top ){
			opt['height'] = (start) ? '0px' : this.getUlHeight(el)+'px';
		}
		if(this.left){
			opt['width']  = (start) ? '0px' : this.width+'px';
		}
		return opt;
	},
	animShow : function(el){
		var obj = this;
		
		this.clearCss(el);
		
		var opt = this.makeOpt(true,el);
		$(el).css(opt);
		var opt = this.makeOpt(false,el);
		
		$(el).stop();
		$(el).animate(opt,obj.speed,function(){
			obj.clearCss(el);
			obj.runHook('after');
		});
	},
	clearCss : function(el){
		$(el).removeAttr("style");
	},
	getUlHeight: function(el){
		return $(el).children().length * this.height;
	},
	addHook : function(hook, func){
		this.hooks[hook].push(func);
	},
	runHook : function(hook){
		for(var x in this.hooks.before){
			this.hooks.before[x]();
		}
	}
}