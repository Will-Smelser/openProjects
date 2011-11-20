jQuery.fn.iframeresize = function(){
	return this.each(function(index,el){
		var setHeight = function(){
			//set some vars
			var el = this;
			var $target = $(el).contents().find('body').first();
			
			//set page properties of iframe
			var prop = 'padding:0px;margin:0px';
			$target.attr('style',prop);
			$(el).contents().find('html').attr('style',prop);
			
			//get the height
			var ht = $target.height(); //should work
			if(ht == 0){ ht = $target[0].scrollHeight; } //just incase
			if(ht == 0){ ht = $target[0].clientHeight; } //just just incase ;)
			
			//add height for scrollbars
			var add = ($target.width() > $(el).width()) ? 20 : 0;

			//set the height
			$(el).height(ht+add);
		};
		if($(this).attr('tagName').toLowerCase() == 'iframe'){
			$(this).bind('iframeresize',setHeight);
			$(this).load(function(){$(el).trigger('iframeresize');});
		}
	});
};