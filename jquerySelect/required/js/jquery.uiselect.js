/**
 * Use this however you feel.  If you have improvement, please
 * let me know.
 * 
 * @example $('.select-wrapper).uiselect();
 * @requires jquery ui javascript and css
 * 
 * @author Will Smelser (willsmelser@gmail.com)
 */
(function( $ ){

/**
 * @param className <string> Optional class name applied to select wrapper. 
 */
jQuery.fn.uiselect = function(className){
	className = (typeof className == 'undefined') ? 'ui-select' : className;
	return this.each(function(index,el){
		//create the dom elements needed
		var $div = $(document.createElement('div')).addClass(className);
		var $span = $(document.createElement('span')).addClass('ui-spinner ui-widget ui-widget-content ui-corner-all');
		var $input= $(document.createElement('input'));
		var $a    = $(document.createElement('a')).addClass('ui-spinner-button ui-spinner-down ui-state-default ui-corner-right');
		var $aspan= $(document.createElement('span')).addClass('ui-button-text');
		var $asspan=$(document.createElement('span')).addClass('ui-icon ui-icon-triangle-1-s');
		$a.append($aspan.append($asspan));
		$(this).wrap($div);
		$(this).before($span.append($input).append($a));
		
		
		$(this).each(function(){
			var data = [];
			var $select = $(this).hide();

			//gather data
			$select.children().each(function(){
				data.push({"label":$(this).html(),"value":$(this).attr('val')});
			});

			var $input = $select.siblings().find('input').val(data[0].label)
				.autocomplete({source: data, delay: 0, minLength: 0});

			var show = function(){$input.autocomplete("search","");$input.focus();$select.trigger('click');};

			//the drop down button
			$select.siblings().find('a.ui-spinner-button').click(show);

			//remove the ability to focus on input
			$input.click(show).on("autocompleteselect",
				function(evt,ui){
					if(ui.item.value != $select.val()){
						$select.val(ui.item.value);
						$select.trigger('change');
					}
				});
			
			var first = true;
			
			var key = function(evt){
				$(this).val($select.val());
				
				//enter or esc
				if(evt.keyCode == 13 || evt.keyCode == 27){
					first = true;
					return;
				}

				//up or down
				if((evt.keyCode == 38 || evt.keyCode == 40)){
					if(first) first = false;
					else return;
				}
				$input.autocomplete("search","");
			};
			$input.keydown(key).keyup(key).keypress(key);
		});
		
	});
};
})( jQuery );