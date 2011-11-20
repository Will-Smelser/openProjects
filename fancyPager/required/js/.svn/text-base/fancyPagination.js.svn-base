/**
 * @classDescription This class will make a single instance of a pagination
 * @author Will Smelser
 */
var mioPagination = {
		/** @class */
		info : {
		/** @class */
		defaults : {dateObj:new Date,loadingImg:'required/images/loader.gif',
			//navigation
			/**
			 * Show the First / Last buttons
			 * @property {Boolean}
			 */
			FirstLast : true,
			/**
			 * Show the Previous / Next buttons
			 * @property {Boolean}
			 */ 
			PrevNext  : true,
			prevId : 'mioPrev', nextId : 'mioNext', firstId : 'mioFirst', lastId : 'mioLast', moreId : 'mioMore',lessId:'mioLess',more:'...',less:'...',
			prev:'&lt;',next:'&gt;',first:'&lt;&lt;',last:'&gt;&gt;',
			seperate:'&gt;',maxShow : 5,
			
			//open/close
			open : '<b> - </b>', openClass : 'open', close : '<b> + </b>', closeClass : 'close',
			
			//page parameters that will be applied to all pages added using the add() function
			/**
			 * Default value that pages will be set to.  This will dissallow backwards navigation
			 * if all pages have it.
			 * @property {Boolean} allowBackNav
			 */
			allowBackNav : true,
			/**
			 * Default value that pages will be set to.  This will force sequential page navigation
			 * @property {Boolean}
			 */
			sequential   : false,
			AJAXfail     : '<p class="ajaxFailMsg">Failed to load content.</p>', 
			height       : '400' //if this page is to be loaded by frame, this height will be used
					
		},
		cache : {},
		opacity: '.75', //animation end opacity
		/**
		 * The animation mode to use.  Just create any combination of animate, fade, bounce, or center
		 * @property {String}
		 */
		mode : 'animate-fade-bounce-center'//affect the animations...they all work together to do different things
	},
	debug : false,
	/**
	 * Object Constructor
	 * @param {Object} pageInfo [optional] An array of information to be used for the pagination.  Recommended using mioPagination.page.add method.
	 * @see mioPagination.page.add
	 */
	init : function(pageInfo){
		
		//make sure we atlead have an empty array for the config
		if(typeof pageInfo == 'undefined') pageInfo = [];
		
		//add error handler if debugging
		if(this.debug && typeof console != 'undefined') this.err = new mioErrHandling();
		
		//check for jquery
		if(typeof $ == 'undefined'){
			this.err.addError('Jquery object does not exist!','error'); //log
			return this.err.returnError();
		}
		
		this.pageInfo = pageInfo;
		
		//store reference to this object in lower levels
		var objRef = this;
		this.manipulate.objRef = this;
		this.page.objRef = this;
		this.layout.objRef = this;
		
		//set the animation mode
		if(this.info.mode.indexOf('fancy') >= 0){
			this.info.mode = this.info.mode.replace('fancy','animate-fade-center-bounce');
		}
		
		//add necessary HTML
		this.addDOMelems();
		
		this.err.log('init done.');
		
		//bind recenter event for window resize
		$(window).resize(function(){objRef.manipulate.centerMain()});
		
		//run default setup script
		this.initDefaults();
		
		if(this.pageInfo.length > 0) this.drawPagination();
		
		this.err.returnError();
	},
	initDefaults : function(){
		var objRef = this;
		this.layout.setupRightNav();
		this.layout.bindOpen(function(){objRef.layout._openOrClose('mainHide')}, 'default');
		this.layout.bindClose(function(){objRef.layout._openOrClose('mainShow')}, 'default');
	},
	importPages : function(pages){
		this.pageInfo = pages;
	},
	addDOMelems : function(){
		var header = '<div id="'+this.manipulate.idHeader+'" class="">';
				header += '<div id="'+this.manipulate.idHeaderNav+'"></div>';
				header += '<div id="'+this.manipulate.idHeaderLoading+'"></div>';
				header += '<div id="'+this.manipulate.idHeaderRight+'">'+this.info.defaults['open']+'</div>';
			header += '</div>';
		var frame  = '<iframe id="'+this.manipulate.idFrame+'" class="" src="#">Upgrade Your Browser!</iframe>';
		var main   = '<div id="'+this.manipulate.idMain+'" class=""></div>';
		var footer = '<div id="'+this.manipulate.idFooter+'" class="" ></div>';
		
		var wrapper = '';
		wrapper += '<div id="'+this.manipulate.idWrapper+'" class=""><div id="'+this.manipulate.idInner+'">';
		wrapper += header + '<div id="'+this.manipulate.idMainWrap+'">' + main + frame + '</div>' + footer + '</div></div>';
		
		$('body').append(wrapper);
		
		this.err.log('DOM elements loaded.');
	},
	/**
	 * If previous, next, first, last buttons are being used this will be called to update the pagination on each page click.
	 * This has not been tested in large page ques.
	 * @return void
	 */
	setPaginationDisplay : function(){
		var objRef = this;

		//current pages index
		var index  = (typeof this.info.cache.currentIndex == 'undefined') ? 0 : this.info.cache.currentIndex;
		
		//max number to display
		var max = Math.min(this.info.defaults.maxShow,this.pageInfo.length);
		this.info.defaults.maxShow = max;

		var goal = Math.floor(this.info.defaults.maxShow/2);
		
		//the left hand portion is constraint
		if(index-goal <= 0){
			var after = this.info.defaults.maxShow-index-1;

		//the right hand portion is constraint
		}else if(this.pageInfo.length - goal <= index){
			var after = this.pageInfo.length - index-1;
			
		//ideal scenraio, equal amounts on each side
		}else{
			var after = Math.ceil((this.info.defaults.maxShow-1)/2);
		}
		
		var before = max-after-1;
			
		var start  = Math.max(index-before,0);
		var end    = Math.min(this.pageInfo.length-1,index+after);
		
		(start == 0) ? $('#'+this.info.defaults.lessId).addClass('hidden') : $('#'+this.info.defaults.lessId).removeClass('hidden');
		(end   == this.pageInfo.length-1) ? $('#'+this.info.defaults.moreId).addClass('hidden') : $('#'+this.info.defaults.moreId).removeClass('hidden');
		
		
		var $LIs = $('#'+this.manipulate.idHeader+' LI');		
		var sliceStart = (this.info.defaults.FirstLast) ? 2 : 1; //start at 1 since we have the '...' by default
		sliceStart += (this.info.defaults.PrevNext) ? 1 : 0;
		
		var i=0;
		$LIs.slice(sliceStart,-sliceStart).each(function(index,el){
			//reset
			$(el).removeClass('hidden');
			
			//hide seperator
			if($(el).hasClass('seperate') && i<=start && i != 0){
				$(el).addClass('hidden');
			}
			//hide/show pages
			var $a = $(el).children().first(); 
			if(!$a.hasClass('mioSkip') && $a.hasClass('page')){
				var page = objRef.pageInfo[i];
				(i < start || i > end) ? $(el).addClass('hidden') : $(el).removeClass('hidden');
				i++;
			}
			//hide show trailing seperator
			if($(el).hasClass('seperate') && i > end){
				$(el).addClass('hidden');
			}
		});
	},
	/**
	 * Draw the pagination...should be called after pages have been added.  Altering events does not impact this method.
	 * @return void
	 */
	drawPagination : function(){		
		var objRef = this;
		var FirstLast = this.info.defaults.FirstLast;
		var PrevNext = this.info.defaults.PrevNext;
		
		this.orderPages();
		
		var splitter = '';
		var html = '<ul>';
		html += (FirstLast) ? '<li id="'+this.info.defaults.firstId+'"><a class="mioSkip" href="#" onclick="return false;">'+this.info['defaults']['first']+'</a></li>' : '';
		html += (PrevNext)  ? '<li id="'+this.info.defaults.prevId+'"><a class="mioSkip" href="#" onclick="return false;">'+this.info['defaults']['prev']+'</a></li>' : '';
		html += (this.pageInfo.length > this.info.defaults.maxShow) ? '<li id="'+this.info.defaults.lessId+'">'+this.info.defaults.less+'</li>' : '';
		for(var x in this.pageInfo){
			var id = this.manipulate.idPaginateLinksPrefix+this.pageInfo[x]['name'];
			this.pageInfo[x]['domAid'] = id;
			
			html += splitter+'<li><a id="'+id+'" class="page">'+this.pageInfo[x].dispName+'</a></li>';
			
			splitter = '<li class="seperate">'+this.pageInfo[x].seperate+'</li>';
		}
		html += (this.pageInfo.length > this.info.defaults.maxShow) ? '<li id="'+this.info.defaults.moreId+'">'+this.info.defaults.more+'</li>' : '';
		html += (PrevNext)  ? '<li id="'+this.info.defaults.nextId+'"><a class="mioSkip" href="#" onclick="return false;">'+this.info['defaults']['next']+'</a></li>' : '';
		html += (FirstLast) ? '<li id="'+this.info.defaults.lastId+'"><a class="mioSkip" href="#" onclick="return false;">'+this.info['defaults']['last']+'</a></li>' : '';
		html += '</ul>';
		
		$('#'+this.manipulate.idHeaderNav).html(html);
		
		objRef._addDOMref();
		
		this.info.cache.paginateHeight = $('#'+this.manipulate.idHeader).outerHeight();
		
		this.page.bindDefaultEvents();
		this.page._bindAll();
		
		if(PrevNext){this.page._bindPrevNext();}
		if(FirstLast){this.page._bindFirstLast();}
		
		this.manipulate.centerMain();
		this.setPaginationDisplay();
	},
	_addDOMref : function(){
		for(var x in this.pageInfo){
			this.pageInfo[x]['domLIref'] = $('#'+this.pageInfo[x]['domAid']).parent();
		}
	},
	/** this holds page information and que
	 * @property */
	pageInfo : [],
	/** @property this holds the right minimize and open button events */
	layout : {
		/** @property reference to mioPagination */
		objRef : {},
		rightNav : {
			openEvents : [],
			closeEvents : []
		},
		setupRightNav : function(){
			var objRef = this;
			this.rightNav['open'] = this.objRef.info.defaults['open'];
			this.rightNav['close']= this.objRef.info.defaults['close'];
			this.rightNav.openClass = this.objRef.info.defaults['openClass'];
			this.rightNav.closeClass = this.objRef.info.defaults['closeClass'];
			
			$('#'+this.objRef.manipulate.idHeaderRight).click(function(){objRef.toggle();});
		},
		//the default open/close function
		_openOrClose : function(funcName){
			var ref = this.objRef.manipulate;
			var id = (this.objRef.info.cache.currentPageRef.type == 'iframe') ? ref.idFrame : ref.idMain ;
			ref[funcName]($('#'+id),null);
		},
		fireOpen:function(){
			var $el = $('#'+this.objRef.manipulate.idHeaderRight);
			$el.html(this.rightNav.close);
			$el.addClass(this.rightNav.closeClass);
			var events = this.rightNav.openEvents;
			for(var x in events){
				events[x]();
			}
		},
		fireClose:function(){
			var $el = $('#'+this.objRef.manipulate.idHeaderRight);
			$el.html(this.rightNav.open);
			$el.addClass(this.rightNav.openClass);
			var events = this.rightNav.closeEvents;
			for(var x in events){
				events[x]();
			}
		},
		bindOpen:function(func, name){
			this.rightNav.openEvents[name] = func;
		},
		unbindOpen:function(funcName){
			this.rightNav.openEvents = this.rightNav.openEvents.splice(funcName,1);
		},
		bindClose:function(func, name){
			this.rightNav.closeEvents[name] = func;
		},
		unbindClose:function(funcName){
			this.rightNav.closeEvents = this.rightNav.closeEvents.splice(funcName,1);
		},
		setRightNavAction : function(func){
			$('#'+this.objRef.manipulate.idHeaderRight).click(func);
		},
		toggle : function(){
			var $el = $('#'+this.objRef.manipulate.idHeaderRight);
			$el.removeClass();

			//open/close
			var ref = this.objRef.manipulate;
			var id = (this.objRef.info.cache.currentPageRef.type == 'iframe') ? ref.idFrame : ref.idMain ;
			var state = ($('#'+id).css('display') == 'block' || $('#'+id).css('display') == 'inline') ? true : false;
			(!state) ? this.fireClose($el) : this.fireOpen($el);
		}
	},
	
	/**
	 * Handles animations and holds IDs of DOM elements used by system
	 * @class manipulate This holds the animation methods
	 */
	manipulate : {
		objRef : {},
		idFrame : 'mioIframe', idWrapper : 'mioWrapper', idInner:'mioMainInner', idHeader : 'mioMainPaginationWrapper', idMainWrap : 'mioBodyWrapper',
		idHeaderNav : 'mioPagination', idHeaderRight : 'mioPaginationRight', idHeaderLoading : 'mioAjaxLoading', idHeaderLast : 'mioNavLast', idHeaderFirst : 'mioNavFirst',
		idMain : 'mioMainWrapper', idFooter : 'mioMainFooterWrapper',
		idPaginateLinksPrefix : 'mioPage_', //use a single "_" at the end
		animSpeed : 200,
		/**
		 * Show or hide the main content box
		 * @public
		 * @function
		 * @param {Object} $target JQuery reference to either the iframe or the div box to be hid
		 * @param {Funciton} callback [optional] Function to be called after the open event occurrs
		 */
		mainHide : function($target,callback){

			//bail if simple animation
			if(this.objRef.info.mode == 'simple'){ return;}
			
			//if center animation
			if(this.objRef.info.mode.indexOf('center') >= 0 && this.objRef.info.mode.indexOf('animate') >=0){
				var ht = (this.objRef.info.cache.paginateHeight)/2;
				var speed = this.animSpeed;
				$('#'+this.idWrapper).animate({marginTop : -ht},speed);
			}
			
			if(this.objRef.info.mode.indexOf('animate') >= 0){
				$target.slideUp(speed);
			}else{
				$target.hide();
			}
			
			if(typeof callback =='function'){
				setTimeout(callback,100);
			}
			
		},
		/**
		 * Show the main content box
		 * @param {Object} $target Jquery object reference of the iframe or div box DOM element
		 * @param {Function} callback [optional] Function to be called at the end
		 */
		mainShow : function($target, callback){
			var objRef = this.objRef;
			if(this.objRef.info.mode == 'simple'){ return;}
			
			var speed = this.animSpeed;
			var slid  = false;
			var $main  = $target;//$('#'+this.idMain);
			
			//if center animation
			if(this.objRef.info.mode.indexOf('center') >= 0 && this.objRef.info.mode.indexOf('animate') >=0){
				var ht = (this.objRef.info.cache.footHeight+this.objRef.info.cache.paginateHeight+this.objRef.info.cache.mainHeight)/2;
				if(this.objRef.info.mode.indexOf('bounce') >= 0){
					slid = true;
					$('#'+this.idWrapper).animate({marginTop : -ht},speed,function(){$main.slideDown(speed);});
				}else{
					$('#'+this.idWrapper).animate({marginTop : -ht},speed);
				}
			}
			
			//show box
			if(this.objRef.info.mode.indexOf('animate') >= 0){
				if(!slid){ $main.slideDown(speed);}
			}else if(this.objRef.info.mode.indexOf('animate') < 0){
				$main.show();
			}
			
			//use the fade-in animation
			if(this.objRef.info.mode.indexOf('fade') >= 0){
				$main.css({opacity:0});
				$main.animate({opacity:objRef.info.opacity},speed*2);
			}

			if(typeof callback =='function'){
				setTimeout(callback,100);
			}
		},
		/**
		 * Will center the entire pagination if the "center" animation is set
		 * @return void
		 */
		centerMain : function(){
			if(this.objRef.info.mode.indexOf('center') < 0){return;}
			
			var ht = $('#'+this.idInner).outerHeight();
			var offset = Math.floor(ht/2);
			
			$('#'+this.idWrapper).css({'margin-top':'-'+offset+'px'});
		},
		resetMainCSS : function(){
			$('#'+this.idWrapper).css({});
		}
	},
	/**
	 * Main Object for working with pages and binding events.  Main interface
	 * @class Object Holds majority of methods needed...used to add events, remove events, add pages, etc....
	 */
	page : {
		/**
		 * References mioPagination namespace
		 */
		objRef : {},
		/**
		 * A list of events to be fired by _fireClick event.  Feel free to add your own into here, but you will need 
		 * to tweak the _fireClick method to include your custom events
		 * <br/><br/><b> beforeClick,onClick,afterClick,afterLoaded,firstClick,notFirstClick,navBackwards,notSequential</b>
		 * @property {Array}
		 */
		_eventParams : ['beforeClick','onClick','afterClick','afterLoaded','firstClick','notFirstClick','navBackwards','notSequential'],
		/**
		 * Adds pages to the pagination array
		 * Run orderPages() after this if you add after drawing the pagination
		 * @see mioPagination.orderPages()
		 * @see mioPagination.drawPagination()
		 * @param {Integer} weight Where you would like the page to show up in the page que
		 * @param {String} dispName The name to be displayed in page navigation
		 * @param {String} name A unique name used to access information about the page through built in methods and system calls
		 * @param {Array} eventParams An array of events associated with the page...see this._eventParams for list of events
		 * @see mioPagination.page._eventParams
		 * @param {String} file The file to be loaded on the page click<br/> -file.file string Path to file to be loaded, either http, or relative and absolute paths<br/> -file.type string The way to load the file, either text or iframe
		 * @param {Object} params json Optional paramters which most are defined in info.defaults
		 * 	<br/> -params.clicked bool whether the page has been assumed to be clicked once already
		 * 	<br/> -params.seperate html string of html for the page seperator(default is ">") before the page
		 * 	<br/> -params.allowBackNav bool wether this page can be navigated to again (allows only 1 onclick to run)
		 * 	<br/> -params.sequential bool wether this page must be in sequence...all pages prior to this page must be clicked to view this page
		 * 	<br/> -params.ajaxFailMsg html string of html to be displayed in content area on an AJAX failure (404 or timeout)
		 * 	@see mioPagination.page._getDefaultParams()
		 */
		add : function(weight,dispName,name,eventParams,file,params){
			if(typeof params == 'undefined'){params = {};}
			params = this._getDefaultParams(params);
			
			//make sure we have atleast empty values for all event params
			for(var x in this._eventParams){
				if(typeof eventParams[this._eventParams[x]] == 'undefined') eventParams[this._eventParams[x]] = [];
			}
			
			//bind a function to the event params to make easier calling of functions
			eventParams.mioEach = this._mioEach;
			
			this.objRef.pageInfo.push(
					{
					'weight':weight,
					'name':name,
					'dispName':dispName,
					'eventParams':eventParams,
					'seperate':params['seperate'],
					'clicked':params['clicked'],
					'allowBackNav':params['allowBackNav'],
					'sequential':params['sequential'],
					'ajaxFailMsg':params['AJAXfail'],
					'file':file.location,
					'type':file.type,
					'height' : params['height']
					}
				);
		},
		_getDefaultParams : function(params){
			params['clicked']      = (typeof params['clicked'] == 'boolean')      ? params['clicked']         : false;
			params['seperate']     = (typeof params['seperate'] == 'undefined')   ? 
					this.objRef.info.defaults.seperate : params['seperate'];
			params['allowBackNav'] = (typeof params['allowBackNav'] == 'boolean') ? 
					params['allowBackNav']    : this.objRef.info.defaults.allowBackNav;
			params['sequential']   = (typeof params['sequential'] == 'boolean')   ? 
					params['sequential']      : this.objRef.info.defaults.squential;
			params['AJAXfail']     = (typeof params['AJAXfail'] == 'undefined')   ? 
					this.objRef.info.defaults.AJAXfail : params['AJAXfail'];
			params['height']       = (typeof params['height'] == 'undefined')     ? this.objRef.info.defaults.height : params['height'];
			
			return params;
		},
		/**
		 * Set the current page being displayed to this page
		 * Actually fires the onclick event for the page.  So same effect as a user clicking on the page
		 * @param {String} pageName The name of the page given in the page array
		 * @return void 
		 */
		setPage : function(pageName){
			//var el = this.getElByPage(pageName);
			var el = this.getPage(pageName);
			this._fireClick(el);
		},
		/**
		 * Go to the first page in the page array...Fires the onclick event for going to the first page. 
		 * This will be affected by page specific setting such as "sequential" and "allowBackNav" events
		 * @see mioPagination.page.goLastPage
		 * @return void
		 */
		goFirstPage : function(){
			//var el = this.objRef.pageInfo[0]['domLIref'];
			el = this.objRef.pageInfo[0];
			this._fireClick(el);
		},
		/**
 		 * Go to the last page in the page array...Fires the onclick event for going to the first page. 
		 * This will be affected by page specific setting such as "sequential" event
		 * @see mioPagination.page.goFirstPage()
		 * @return void
		 */
		goLastPage : function(){
			var last = this.objRef.pageInfo.length-1;
			var el = this.objRef.pageInfo[last];//['domLIref'];
			this._fireClick(el);
		},
		_bindFirstLast : function(){
			var objRef = this.objRef;
			$('#'+objRef.info.defaults.firstId).click(function(){objRef.page.goFirstPage();});
			$('#'+objRef.info.defaults.lastId).click(function(){objRef.page.goLastPage();});
		},
		_bindPrevNext : function(){
			var objRef = this.objRef;
			$('#'+objRef.info.defaults.prevId).click(function(){objRef.page.goPrevPage();});
			$('#'+objRef.info.defaults.nextId).click(function(){objRef.page.goNextPage();});
		},
		/**
		 * This will fire the onclick event for going to next page.  Will be affected by "sequential" event.
		 * @see mioPagination.page.goPrevPage()
		 * @return void
		 */
		goNextPage : function(){
			var objRef = this.objRef;
			var pindex = this._getPageIndex(objRef.info.cache.currentPage)*1+1;
			pindex = (pindex >= objRef.pageInfo.length) ? objRef.pageInfo.length-1 : pindex; 
			var el = objRef.pageInfo[pindex];//['domLIref'];
			this._fireClick(el);
		},
		/**
		 * This will fire the onclick event for going to previous page.  Will be affected by "sequential" and "allowBackNav" events.
		 * @see mioPagination.page.goNextPage()
		 * @return void
		 */
		goPrevPage : function(){
			var objRef = this.objRef;
			var pindex = this._getPageIndex(objRef.info.cache.currentPage)-1;
			pindex = (pindex <= 0) ? 0 : pindex;
			var el = (this.objRef.pageInfo[pindex]['name'] == 'mioFirst') ? this.objRef.pageInfo[pindex+1]/*['domLIref']*/ : this.objRef.pageInfo[pindex]/*['domLIref']*/;
			this._fireClick(el);			
		},
		_getPageIndex : function(pageName){
			var pInfo = this.objRef.pageInfo;
			for(var x in pInfo){if(pInfo[x]['name'] == pageName){return x;}}
			this.objRef.err.addError('Failed to locate page: '+pageName,'warn');
			this.objRef.err.returnError();
		},
		_bindAll : function(){
			for(var x in this.objRef.pageInfo){
				var el = this.getElByPage(this.objRef.pageInfo[x]['name']);
				this._bindClick(el);
			}
		},
		_bindClick : function(el,page){
			if(typeof page != 'undefined'){
				var ref = this;
				$(el).unbind();
				$(el).click(function(){ref._fireClick(page);});
				return;
			}
			if(el.length == 0){
				this.err.addError('Failed to add an element onClick event.','warn');
				return;
			}
			var page = this.getPageByEl(el);
			var ref = this;
			$(el).unbind();
			$(el).click(function(){ref._fireClick(page);});
		},
		_fireClick : function(page){
			//if we are at the current page, dont run events
			if(page.name == this.objRef.info.cache.currentPage){return;}
			
			//handle first click
			var event  = (!page['clicked']) ? 'firstClick' : 'notFirstClick';
			page.eventParams.mioEach(event);
			
			//first click and backwards navigation
			if(page['clicked']){
				page.eventParams.mioEach('navBackwards');
				if(!page['allowBackNav']){return;}
			}			
			
			//sequential events
			for(var x in this.objRef.pageInfo){
				if(this.objRef.pageInfo[x].name == page.name) break; //found the key of the current page
			}
			var bad = false;
			if(x > 0){
				//find this pages position
				for(var i=x-1; i>=0; i--){
					if(this.objRef.pageInfo[i]['clicked'] === false){bad = true;break;}
				}
			}
			
			//if this must be performed in sequence
			if(bad){
				page.eventParams.mioEach('notSequential');
				if(page['sequential']){ return;}
			}	
			
			//standard events
			page['clicked'] = true;
			page.eventParams.mioEach('beforeClick');
			page.eventParams.mioEach('onClick');
			page.eventParams.mioEach('afterClick');
			
			this.objRef.setPaginationDisplay();
			
			return;
			
		},
		_mioEach : function(eventType){
			for(var x in this[eventType]){this[eventType][x]();}
		},
		_fireAfterLoad : function(pageName,delay){
			var page = this.getPage(pageName);
			page.eventParams.mioEach('afterLoaded');
		},
		bindDefaultEvents : function(){
			var objRef = this.objRef;
			$('#'+this.objRef.manipulate.idHeader+' LI A').each(function(index,el){
				if(!$(el).hasClass('mioSkip')){
				
					var page   = objRef.page.getPageByEl(el);
					var objMan = objRef.manipulate;
					var objInf = objRef.info;
					var objPag = objRef.page;
					
					var func = function(){
						
						(page.type == 'iframe') ? objMan.mainHide($('#'+objMan.idFrame),null) : objMan.mainHide($('#'+objMan.idMain),null);
						
						$('#'+objMan.idMain).html('');
						$('#'+objMan.idMain).css('display','none');
						$('#'+objMan.idFrame).css('display','none');
						
						var func = (page.type == 'iframe') ? function(){objPag._loadByFrame(page);} : function(){objPag._loadByAjax(page);};
						
						objPag._wait4ContentEmpty($('#'+objMan.idMain),func,objInf.defaults.dateObj.getTime());
						
						//show wich one was selected
						$('#'+objMan.idHeader+' LI').each(function(){$(this).removeClass('selected');});
						$(el).parent().toggleClass('selected');
						objRef.info.cache.currentPage  = objRef.page.getPageByEl(el)['name'];
						objRef.info.cache.currentIndex = objRef.page._getPageIndex(objRef.info.cache.currentPage)*1;
						objRef.info.cache.currentPageRef=page;
						
					};
					objPag.bindByEl(el,func,'default','onClick');
				}
			});
		},
		_setLoadingImg : function($el){
			//set the ajax loading gif
			var img = '<img src="'+this.objRef.info.defaults.loadingImg+'"/>';
			$el.html(img);
		},
		_loadByFrame : function(page){
			var objMan = this.objRef.manipulate;
			var objInf = this.objRef.info;
			var objPag = this.objRef.page;
			
			var $el = $('#'+this.objRef.manipulate.idHeaderLoading);
			this._setLoadingImg($el);
			
			$('#'+objMan.idFrame).attr('src',page.file);
			$('#'+objMan.idFrame).css('height',page.height+'px');
			
			objInf.cache.mainHeight = page.height;
			objInf.cache.footHeight = $('#'+objMan.idFooter).outerHeight();
			objMan.mainShow($('#'+objMan.idFrame),null);
			objPag._fireAfterLoad(page.name,100);
			$el.html('');
			
		},
		_loadByAjax : function(page){
			var objMan = this.objRef.manipulate;
			var objInf = this.objRef.info;
			var objPag = this.objRef.page;
			
			var $el = $('#'+this.objRef.manipulate.idHeaderLoading);
			
			this._setLoadingImg($el);
			
			var func = function(response,status,xhr){
				
				if(status == 'error' || response == ''){$(this).html(page.ajaxFailMsg);}
				
				var func = function(){
					objInf.cache.mainHeight = $('#'+objMan.idMain).outerHeight();
					objInf.cache.footHeight = $('#'+objMan.idFooter).outerHeight();
					objMan.mainShow($('#'+objMan.idMain),null);
					objPag._fireAfterLoad(page.name,0);
					$el.html('');
				}
				objPag._wait4ContentLoaded(this,func,objInf.defaults.dateObj.getTime());
			};
			
			$('#'+objMan.idMain).load(page.file,func);
			
		},
		_maxWait4Content : 200,
		_wait4ContentLoaded : function(el,postAction,started){
			var objRef = this.objRef;
			var diff = objRef.info.defaults.dateObj.getTime() - started;
			
			if($(el).height() == 0 && diff < this._maxWait4Content ){
				setTimeout(function(){objRef.page._wait4ContentLoaded(el, postAction, started);},10);
			}else{
				postAction();
			}
		},
		_wait4ContentEmpty : function(el,postAction,started){
			var objRef = this.objRef;
			var diff = objRef.info.defaults.dateObj.getTime() - started;
			if($(el).height() > 0 && diff < this._maxWait4Content ){
				setTimeout(function(){objRef.page._wait4ContentEmpty(el,postAction,started);},20);
			}else{
				postAction();
			}
		},
		fireEvent : function(pageName,eventType,funcName){
			var page = this.getPage(pageName);
			page.eventParams[eventType][funcName]();
		},
		/**
		 * Bind an event to all pages in current page que
		 * @param {Function} func The funciton to bind to the given event
		 * @param {String} funcName A unique string identifier for the event (unique to the specific event)
		 * @param {String} eventType The event you want to bind this to
		 * @see mioPagination.page._eventParams
		 */
		bind2AllPages : function(func, funcName, eventType){
			for(var x in this.objRef.pageInfo){
				this._bind(this.objRef.pageInfo[x],func,funcName,eventType);
			}
		},
		/**
		 * Bind an event by a page name
		 * @param {String} pageName The unique page name
		 * @param {Funciton} func The function to be evaluated
		 * @param {string} funcName The unique name for this function (unqiue to the eventType for this page)
		 * @param {String} eventType The event type to be fired.  See mioPagination.page._eventParams
		 * @see mioPagination.page._eventParams
		 */
		bindByName : function(pageName,func,funcName,eventType){
			var page = this.getPage(pageName);
			this._bind(page, func,funcName, eventType);
		},
		/**
		 * Bind an event by DOM element
		 * @param {Object} el The JQuery object reference to a DOM element (example: var el = $('#myID'); )
		 * @param {Funciton} func The function to be evaluated at the event
		 * @param {string} funcName The unique name for this function (unqiue to the eventType for this page)
		 * @param {String} eventType The event type to be fired.  See mioPagination.page._eventParams
		 * @see mioPagination.page._eventParams
		 * @see mioPagination.page.getPageByEl
		 */
		bindByEl : function(el,func,funcName,eventType){
			var page = this.getPageByEl(el);
			this._bind(page, func, funcName, eventType);
		},
		/**
		 * Unbind an event by page name
		 * @param {String} pageName The unique page name
		 * @param {String} funcName The unique funciton name (relative to the eventType and pageName)
		 * @param {String} eventType The event type
		 * @see mioPagination.page._eventParams
		 */
		unbindByName : function(pageName,funcName,eventType){
			var page = this.getPage(pageName);
			this._unbind(page, funcName, eventType);
		},
		/**
		 * Unbind an event by a DOM element
		 * @param {Object} el JQuery object reference to the LI or A tag in the DOM
		 * @param {String} funcName The unique function name
		 * @param {String} eventType The event type that the funciton exists under
		 */
		unbindByEl : function(el,funcName,eventType){
			var page = this.getPageByEl(el);
			this._unbind(page, funcName, eventType);
		},
		/**
		 * Get the html JQuery element from the DOM by a given page name.
		 * @param {String} pageName The unique page name in the page array
		 * @return {Object} JQuery object reference to the element in the DOM
		 * @see mioPagination.page.add
		 */
		getElByPage : function(pageName){
			var page = this.getPage(pageName);
			if(typeof page['domLIref'] != 'undefined'){
				return page['domLIref'];
			}
			
			var $el = $('#'+this._getId(pageName));
			if($el.length == 0) this.objRef.err.addError('Did not find element in getElByPage().  pageName:'+pageName,'error');
			
			return $el;
			
		},
		/**
		 * Get a reference object to the page array with all the information in it.  Really more for internal use.  This object does include
		 * a reference to the DOM element associated with this page (Only after the mioPagination.page.bindDefaultEvents has been run).
		 * @param {String} pageName The unique page name
		 * @see mioPagination.page.add
		 * @return {Object} A reference to the given page as it is stored by the system
		 */
		getPage : function(pageName){
			var pInfo = this.objRef.pageInfo;
			for(var x in pInfo){
				if(pInfo[x]['name'] == pageName) return pInfo[x];
			}
			this.objRef.err.addError('Failed to locate page: "'+pageName+'" in page.getPage()','warn');
			return false;
		},
		/**
		 * Get the page object data based on a given element.  Will look at either the "li" or "A" tag
		 * @param {Object} el JQuery reference to the DOM element
		 * @return {Object} A reference to the page as used by the system
		 */
		getPageByEl : function(el){
			var tag = $(el).attr('tagName');			
			var name = (tag.toLowerCase() == 'a') ? this._parseName($(el)) : this._parseName($(el).children().first());
			return this.getPage(name);
		},
		_getId : function(pageName){
			return this.objRef.manipulate.idPaginateLinksPrefix+pageName;
		},
		_parseName : function(el){
			var parts = $(el).attr('id').split('_');
			parts.shift();
			return parts.concat();
		},
		_bind : function(page,func,funcName,type){
			if(typeof page['eventParams'][type] == 'undefined') type= 'onClick';
			page['eventParams'][type][funcName] = func;
		},
		_unbind : function(page,funcName,type){
			if(typeof page['eventParams'][type] == 'undefined'){
				this.objRef.err.addError('Invalid type sent in unbindByName(): '+type,'warn');
				return false;
			}
			page['eventParams'][type] = page['eventParams'][type].splice(funcName,1);
		},
		_unbindAll : function(pageName){
			var page = this.getPage(pageName);
			for(var x in page['eventParams']){
				if(typeof page['eventParams'][x] == 'object'){
					page['eventParams'][x] = [];
				}
			}
		}
	},
	/**
	 * Order the pages based on the weight given...no priority given if weights are the same.  Identical weights will be order based on
	 * their location in the array prior to ordering
	 * @public
	 * @return void
	 */
	orderPages  : function(){
		this.pageInfo = this._orderObject(this.pageInfo,[],'weight');
	},
	_orderObject : function(origArr,newArr,fieldName){
		if(typeof origArr == 'undefined') return [];

		var tmp = origArr.pop();
		
		//just make sure we did enter something
		var entered =  false;
		
		//cycle the new Array looking for where to insert the new value
		for(var x in newArr){
			var prev = (x > 0) ? newArr[x-1][fieldName] : 0;
			
			//the value is between last value and current value....so enter befor this value
			if(tmp[fieldName] <= newArr[x][fieldName] && tmp[fieldName] >= prev){
				//now we need to splice this into the array
				newArr.splice(x,0,tmp);
				entered = true;
				break;
			}
		}
		
		//make sure we dont loose any values
		if(!entered){
			this.err.log('failed to enter an item.  Force entering.');
			newArr.push(tmp);
		}
		
		if(origArr.length > 0){
			this._orderObject(origArr,newArr,fieldName);
		}

		return newArr;
	},
	err : {
		log : function(){}, returnError:function(){}, addError:function(){}
	}
};