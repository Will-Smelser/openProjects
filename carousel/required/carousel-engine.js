"use strict";

var CarouselEngine = function(options,$target){
    //allow a css selector or jquery for target
    if(typeof $target === 'string'){
        $target = $($target);
    }

    //default options
    var _opts = {
        showElements:2, //how many to be displayed at one time
        startElement:4,
        animateDuration:1,
        animateDurJqueryAdjust:250, //since opening is CSS3 and close is jQuery animate, we need an adjustment
        classNameWrapper:'carousel-wrapper',
        classNameActive:'carousel-active',
        classNameShow:'carousel-show',
        classNameHide:'carousel-hide',
        classNameLeft:'carousel-left',
        classNameRight: 'carousel-right',
        debug:true
    };
    $.extend(_opts,options);

    var styleId = 'style-carousel-'+(new Date().getTime());

    //used for animations
    $('<style id="'+styleId+'">'+
        '.'+_opts.classNameWrapper+'{max-width:0px;transition: max-width '+_opts.animateDuration+'s ease-out; height:0px;vertical-align:top;display:inline-block;overflow:hidden;} '+
        'div.'+_opts.classNameShow+'{max-width:1200px;transition: max-width '+_opts.animateDuration+'s  ease-in; height:auto;} '+
        '.'+_opts.classNameRight+'{overflow:hidden;float:left;} '+
        '.'+_opts.classNameLeft+'{overflow:hidden;float:right;} '+
        '.'+_opts.classNameHide+'{width:0px;max-width:0px;height:0px;} </style>').appendTo('head');

    var $wrapper = $('<div class="'+_opts.classNameWrapper+'" ><div></div></div>');

    $target.children().wrap($wrapper);

    var els = $target.children();

    var log = function(str){
        if(console && _opts.debug){ console.log(str); }
    }

    /**
     * Calculate how many before and how many after should dbe shown.
     * @param idx {int} The "active" index.
     * @param showCnt {int} The number of elements to show.
     * @param els {array} The elements array that will be in the slide show.
     */
    var getBeforeAndAfter = function(idx,showCnt,els){
        //ideal before and after
        var before = Math.floor((showCnt-1)/2.0);
        var after = Math.ceil((showCnt-1)/2.0);

        //handle edge cases, shifting before and after as needed
        while(idx - before < 0){
            before--;
            after++;
        }
        while(idx + after > els.length-1){
            before++;
            after--;
        }
        return [before,after];
    };

    /**
     * Initializing elements.
     * @param idx {int} The index to initialize things to.
     * @param els {array} The elements array that will be in the slide show.
     * @param showCnt {int} The number of elements to show in the slider.
     */
    var showElems = function(idx,els,showCnt){
        if(idx < 0 || idx >= els.length){
            log("Index out of bounds: "+idx+", min:0, max:"+(els.length-1));
            idx = els.length-1;
            log("Changing index to "+idx);
        }

        //sanity check
        if(showCnt > els.length){
            log("Request to show more elements than available, only showing: "+els.length);
            showCnt = els.length;
        }

        var temp = getBeforeAndAfter(idx,showCnt,els);
        var before = temp[0];
        var after = temp[1];

        //show before, and current
        for(var i=0; i <= before; i++){
            $(els[idx - i]).addClass(_opts.classNameShow);
        }

        //show after
        for(var i=1; i<=after; i++){
            $(els[idx + i]).addClass(_opts.classNameShow)
        }

        $(els[idx]).addClass(_opts.classNameActive);

    }

    $target.append(els);
    showElems(_opts.startElement,els,_opts.showElements);

    this.elements = function(){
        return $target.children();
    }

    /**
     * The current "active" element.
     * @return {jQuery} A jQuery element.
     */
    this.currentEl = function(){
        return $target.find('.'+_opts.classNameActive);
    }

    /**
     * Get the index of the current "active" element.
     * @return {jQuery} A jQuery element.
     */
    this.currentIndex = function(){
        return this.currentEl().index();
    }

    /**
     * Check whether this is a previous slide to show.  This mostly applies to a carousel is displaying more than
     * a single slide visible.  See examples for why this is used.
     * @return {boolean}
     */
    this.isPrevNotShown = function(){
        var ideal = Math.floor((_opts.showElements-1)/2.0); //ideal
        var actual = getBeforeAndAfter(this.currentIndex()-1,_opts.showElements,this.elements())[0];
        return (actual === ideal) ? true : false;
    };

    /**
     * Check whether there is next slide to show.  This mostly applies to a carousel is displaying more than
     * a single slide.  If true, then calling next() would trigger a new slide to show.  Returns false if calling
     * next() would merely change which slide is active.  Meaning which slide wrapper has the _opts.classNameActive
     * set.
     * @returns {boolean}
     */
    this.isNextNotShown = function(){
        var ideal = Math.floor((_opts.showElements-1)/2.0); //ideal
        var actual = getBeforeAndAfter(this.currentIndex()-1,_opts.showElements,this.elements())[1];
        return (actual === ideal) ? true : false;
    };

    /**
     * Check if an element is in view.
     * @return {boolean} True if the index is currently within view.
     */
    this.isIndexShown = function(index){
        var visibleSlideBounds = this.visibleSlideBounds();
        return (index >= visibleSlideBounds[0] && index <= visibleSlideBounds[1]);
    }

    /**
     * Animate the closing of a slide. Uses jquery animation to close.
     * @param $target {jQuery} The target element to open.
     * @param dirClass {string} The direction class to apply while opening.
     * @return {jQuery.promise} A jquery promise that will resolve when navigation is complete.
     */
    var animateClose = function($target, dirClass, cb){
        var $inner = $target.children().first().removeAttr('class').addClass(dirClass);

        $target.animate({width:'0px'},_opts.animateDuration*_opts.animateDurJqueryAdjust,'linear',function(){
            $(this).addClass(_opts.classNameHide).removeClass(_opts.classNameShow);
            $(this).removeAttr('style');
            $inner.removeAttr('class');
            if(cb) cb();
        });
    };

    /**
     * Animate the opening of a slide.  Uses css 3 animation hack with max-width.
     * @param $target {jQuery} The target element to open.
     * @param dirClass {string} The direction class to apply while opening.
     * @return {jQuery.promise} A jquery promise that will resolve when navigation is complete.
     */
    var animateOpen = function($target, dirClass){
        $target.children().first().removeAttr('style').addClass(dirClass);

        //no way to animate to origional width with jQuery (simple way that is)
        $target.addClass(_opts.classNameShow).removeClass(_opts.classNameHide);
    }

    /**
     * Navigate forward or backwards 1 slide.
     * @param direction {-1|1} -1 for backwards, 1 for forwards
     * @param cb {null|function} A callback to fire when navigation is complete.
     * @param scope {Carousel} A reference to this object.
     * @return {jQuery.promise} A jquery promise that will resolve when navigation is complete.
     */
    var navigate = function(direction, cb, scope){
        var $defered = $.Deferred();
        var children = scope.elements();
        var $cur = scope.currentEl();
        var idx = $cur.index()+direction;

        if(idx < 0){
            log("Cannot go to negative index");
            return $defered.resolve().promise();
        }else if(idx >= children.length){
            log("Cannot navigate to index greater than length.");
            return $defered.resolve().promise();
        }

        var temp = getBeforeAndAfter(idx,_opts.showElements,children);
        var before = temp[0];
        var after = temp[1];

        //hide the trailing element
        if(after+1 < children.length){
            if(direction > 0){
                animateOpen($(children[idx+after]),_opts.classNameRight);
            }else{
                animateClose($(children[idx+after+1]),_opts.classNameRight,function(){
                    $defered.resolve();
                });
            }
        }

        //animate the showing new element
        if(before-1 < idx){
            if(direction > 0){
                animateClose($(children[idx-before-1]),_opts.classNameLeft,function(){
                    $defered.resolve();
                });
            }else{
                animateOpen($(children[idx-before]),_opts.classNameLeft);
            }
        }

        $cur.removeClass(_opts.classNameActive);
        $(children[idx]).addClass(_opts.classNameActive);

        //call the user's callback when animation completes
        if(typeof cb === 'function'){
            $defered.done(function(){cb();});
        }

        return $defered.promise();
    };

    /**
     * Move to previous slide.
     * @param {function=} An optional callback function.
     * @return {jQuery.promise} A jquery promise that will resolve when navigation is complete.
     */
    this.prev = function(cb){
        return navigate(-1,cb,this);
    }

    /**
     * Move to next slide.
     * @param {function=} An optional callback function.
     * @return {jQuery.promise} A jquery promise that will resolve when navigation is complete.
     */
    this.next = function(cb){
        return navigate(1,cb,this);
    }

    /**
     * Jump to a specific index.
     * @param {int} index The index to jump to
     * @param {function=} cb An optional callback function.
     * @return {jQuery.promise} A jquery promise that will resolve when navigation is complete.
     */
    this.gotTo = function(index,cb){
        var curIndex = this.currentIndex();

        var direction = (index > curIndex) ? 1 : -1;


        var lastPromise;
        while(index !== curIndex && curIndex >= 0 && curIndex < this.elements().length){
            lastPromise = navigate(direction,null,this);
            curIndex = curIndex+direction;
        }

        if(typeof cb === 'function'){
            lastPromise.done(function(){cb();});
        }

        return lastPromise;
    };

    /**
     * Wrap an element with needed wrappers.
     */
    var wrapElement = function(el){
        var $newEl = $wrapper.clone();
        $newEl.children().first().append(el);
        return $newEl;
    };

    /**
     * Get the upper and lower bound based on active slide and number of slides which should be shown.
     */
    this.visibleSlideBounds = function(){
        var curIndex = this.currentIndex();
        var upper = curIndex;
        var lower = curIndex;
        var children = this.elements();

        while(upper < children.length && $(children[upper]).hasClass(_opts.classNameShow)){
            upper++;
        }

        while(lower >= 0 && $(children[lower]).hasClass(_opts.classNameShow)){
            lower--;
        }

        //found the limite +/- 1.  Need to adjust.
        return [++lower,--upper];
    };

    /**
     * User's should call {@link #slideAdd()}.  This adds before the current index, resulting in the
     * element at the current index being pushed back 1.  To add to the end, use {@link #slideAdd()}.
     * @param index {int} Index to add new element before.
     * @param $el {string|DOM Element|jQuery Element} The element to be inserted.
     * @param elements {array} All elements current in carousel.
     * @return {jQuery.promise} A jquery promise that will resolve when animations/insert are complete.
     */
    this._addBefore = function(index, $el, elements){
        var $deferred = $.Deferred();
        var visibleSlideBounds = this.visibleSlideBounds();
        var curIndex = this.currentIndex();
        var temp = getBeforeAndAfter(curIndex,_opts.showElements,elements);
        var before = temp[0];
        var after = temp[1];
        var curLower = curIndex - before;
        var curUpper = curIndex + after;


        //check if this index is in view, if not just add it
        if(index <= visibleSlideBounds[0] || index > visibleSlideBounds[1]){
            $(elements[index]).before($el);
            return $deferred.resolve().promise();
        }

        //hide the last element
        var $elToHide = $(elements[visibleSlideBounds[1]]);

        //insert and animate things
        $(elements[index]).before($el);
        animateClose($elToHide,_opts.classNameRight,function(){$deferred.resolve();});
        animateOpen($el,_opts.classNameRight);

        //may have to choose a new active slide, might as well choose the one inserted
        if($elToHide.hasClass(_opts.classNameActive)){
            $elToHide.removeClass(_opts.classNameActive);
            $el.addClass(_opts.classNameActive);
        }

        return $deferred.promise();
    };

    /**
     * Inserts a slide before the given index.  Example:
     * 1.  Index of 0 would put it at the front
     * 2.  Index of 1 would put it as the second element
     *
     * To add to the end, simply give a number greater than or equal to the current number of slides.
     * @param el {string|jQuery Element|DOM Element} The content to insert over current content.
     * @param index {int} The slide to replace
     * @return {jQuery.promise} A jquery promise that will resolve when animations/insert are complete.
     */
    this.slideAdd = function(el,index){
        var children = this.elements();
        if(index > children.length) log("Index greater han current length, adding to end");
        if(index < 0){
            log("Negative index given, adding to beginning.");
            index = 0;
        }

        var $newEl = wrapElement(el);

        //appending to the end
        if(index >= children.length){
            //todo: what if showSlides > 1, but we only have 1 loaded.  We should show this one as well.
            index = children.length;
            $(children[index-1]).after($newEl);
            return $.Deferred().resolve().promise();
        }

        return this._addBefore(index,$newEl,children);
    };

    /**
     * Overwrites the contents of a slide
     * @param el {string|jQuery Element|DOM Element} The content to insert over current content.
     * @param index {int} The slide to replace
     * @returns {}
     */
    this.slideReplace = function(el,index){
        var children = this.elements();
        if(index < 0 || index >= children.length){
            return log("replaceSlide("+index+"): index out of bounds, ignoring...");
        }
        $(children[index]).children().first().children().first().empty().append(el);
    };

    this.slideRemove = function(index){
        var $deferred = $.Deferred();
        var children = this.elements();
        var visibleSlideBounds = this.visibleSlideBounds();
        var curIndex = this.currentIndex();
        var temp = getBeforeAndAfter(curIndex,_opts.showElements,children);
        var before = temp[0];
        var after = temp[1];
        var curLower = curIndex - before;
        var curUpper = curIndex + after;

        if(!children[index]){
            log("Cannot remove index ("+index+"), it does not exist");
            return $deferred.resolve().promise();
        }

        var _$target = $(children[index]);

        //check if this index is in view, if not just remove it
        //or if we do not have enough elements to show, just remove it.
        if(index < visibleSlideBounds[0] || index > visibleSlideBounds[1]){
            if(children.length < _opts.showElements){
                animateClose(_$target,_opts.classNameRight,function(){
                    _$target.remove();
                    $deferred.resolve();
                });
            }else{
                _$target.remove();
                $deferred.resolve();
            }

            return $deferred.promise();
        }

        //more complicated situation now, have to figure out which element we want to show
        var $elToShow;

        //try and show new end element
        if(children[visibleSlideBounds[1]+1]){
            $elToShow = $(children[visibleSlideBounds[1]+1]);
        }else{
            $elToShow = $(children[visibleSlideBounds[0]-1]);
        }

        //may have to choose a new active slide, might as well choose the one inserted
        if($target.hasClass(_opts.classNameActive)){
            $elToShow.addClass(_opts.classNameActive);
        }

        //animate things
        animateOpen($elToShow,_opts.classNameRight);
        animateClose(_$target,_opts.classNameRight,function(){
            _$target.remove();
            $deferred.resolve();
        });

        return $deferred.promise();
    }

    /**
     * Clean up and restore the the target.  Basically unwrapping the slides and removing some objects created for
     * style and state.
     */
    this.destroy = function(){

        //find the origional elements and unwrap them
        var unwrapped = [];
        this.elements().each(function(i){
            var orig = $(this).children().first().children().first().detach();
            unwrapped.push(orig);
        });

        $target.empty().append(unwrapped);
        $('#'+styleId).remove();
    }
};

(function($){
    var setup = function($target,columns){
        for(var i=0; i < columns; i++){
            var $el = $(document.createElement('div'));
            $el.addClass("carousel-col-"+i);
            $target.append($el);
        }
    };

    var _settings = {
        columns : 5
    };

    var Carousel = function($target,settings){
        $.extend(_settings,settings);

        setup($target,_settings.columns);

        var index = _settings.columns/2 + 1;

        this.set = function(index,data){

        }
    };
})(jQuery);