    (function( $ ){
        var regex = /^(?:input|select|textarea|keygen)/i;
        function Forms(config){
            var debug = (config && config.debug) ? config.debug : false ;
            var result = {
                _itrForm : function($form, cb){
                    var name = $form.attr('name');
                    if(document.forms[name]){
                        for(var i=0; i<document.forms[name].length; i++){
                            var $el = $(document.forms[name].elements[i]);
                            cb($el);
                        }
                    }
                },
                _fill : function($form, json){
                    var self = this;
                    this._itrForm($form,function($el){
                        if(!$el.attr('name')) return; //skip elements without a name
                        if($el.hasClass('forms-ignore')) return; //skip elements
                    
                        //run the filters
                        var val = self._getValue(json, $el.attr('name'));
                        for(var x in self.fill.handlers.filters){
                            val = self.fill.handlers.filters[x].call(self.fill.handlers, $el, val, json);
                        }
                        
                        //fill the actual value
                        if(typeof val !== 'undefined')
                            self.getHandler($el,self.fill.handlers).call(self.fill.handlers, $el, val);
                    });
                },
                _extract : function($form){
                    var self = this;
                    var result = {};
                    
                    this._itrForm($form,function($el){
                        if(!$el.attr('name')) return; //skip elements without a name
                        if($el.hasClass('forms-ignore')) return; //skip elements
                        
                        var val = self.getHandler($el,self.extract.handlers).call(self.extract.handlers, $el);
                        
                        for(var x in self.extract.handlers.filters){
                            val = self.extract.handlers.filters[x].call(self.extract.handlers, $el, val);
                        }
                        
                        self._setValue(result,$el.attr('name'),val);
                    });
                    return result;
                },
                //user defined filters and handlers
                fill : {
                    handlers : {actions:{},filters:[]}
                },
                extract : {
                    handlers : {actions:{},filters:[]}
                },
                getHandler : function($el,scope){
                    var name = $el.prop('tagName').toLowerCase();
                    var handler;
                    switch(name){
                        case 'select':
                            if($el.prop("multiple")){
                                handler = scope['select-multi']; break;    
                            }else{
                                handler = scope['select']; break;
                            }
                        case 'textarea':
                            handler = scope.textarea; break;
                        case 'input':
                            switch($el.prop('type').toLowerCase()){
                                case 'radio':
                                    handler = scope['input-radio']; break;
                                case 'checkbox':
                                    handler = scope['input-checkbox']; break;
                                default:
                                    handler = scope['input-text']; break;
                            }
                            break;
                        default:
                            handler = scope['_none'];
                    }
                    return handler;
                },
                /**
                 * Explode a name attribute with [] into an array of element names
                 * Example: hello[next][world][] returns [hello,next,world,""]
                 */
                _buildPath : function(name){
                    var path = [];
                    var bracket = name.indexOf("[");
                    if(bracket > 0){
                        var index= name.substring(0,bracket);
                        var subs = name.substring(bracket).split("][");
                        
                        if(index >= 0) path.push(index);
                        
                        for(var x in subs){
                            var _name = subs[x].replace("[","").replace("]","");
                            path.push(_name);
                        }
                    }else{
                        path.push(name);
                    }
                    return path;
                },
                _getValue : function(target, name){
                    var _target = target;
                    var path = this._buildPath(name);
                    
                    for(var x in path){
                        _target = _target[path[x]];
                    }
                    return _target;
                },
                //handle arrays names and such
                _setValue : function(target,name,val){
                    //get the initial name
                    var bracket = name.indexOf("[");
                    if(bracket > 0){
                        var index= name.substring(0,bracket);
                        var subs = name.substring(bracket).split("][");
                        //build the object
                        var _target = target;
                        for(var x in subs){
                            var _name = subs[x].replace("[","").replace("]","");
                            //just empty brackets []
                            var obj;
                            if(_name === ""){
                                _name = name.replace("[]","");
                                obj = [];
                            }else{
                                obj = {};
                            }
                            if(!_target[_name] || typeof _target[_name] !== "object") _target[_name] = obj;
                            _target = _target[_name];
                        }
                        //do the save
                        _target.push(val);
                    }else{
                        //radio is special case, where value will be null
                        if(val != null){
                            target[name] = val;
                        }
                    }
                },
                //default handlers, copied into in/out .handlers
                _handlers : {
                    actions : {},
                    filters : [
                            //action filter
                            function($el,val,json){
                                if($el.attr('data-forms-action')){
                                    
                                    var actions = $el.attr('data-forms-action').split(";");

                                    for(var x in actions){
                                        var parts = actions[x].split("-");
                                        var func = parts.shift();
                                        var name = parts.join("-");
                                    
                                        //this should be scope of "handlers"
                                        //call a user defined method if it exists
                                        if(this.actions[func]){
                                            this.actions[func]($el,val,name);
                                            
                                        //try to call the jQuery method
                                        }else if($(name)[func]){
                                            $(name)[func](val);
                                        }
                                    }
                                }
                                return val;
                            }
                    ],
                    _none : function(){return null;},
                    _text : function($el,val){
                        if(typeof val !== 'undefined'){
                            return $el.val(val)
                        }else{
                            return $el.val();
                        }
                    },
                    _check : function($el,val){
                        //in
                        if(typeof val !== 'undefined'){
                            $el.prop('checked',(val && (val === true || val == 1 || val === "true")));
                        //out
                        }else{
                            return $el.is(':checked');
                        }
                    },
                    _radio : function($el,val){
                        //in
                        if(typeof val !== 'undefined'){
                            if(val === $el.val()) $el.prop('checked',true);
                        //out
                        }else{
                            return ($el.is(':checked')) ? $el.val() : null;
                        }
                    },
                    _selectMulti :function($el,val){
                        //in
                        if(typeof val !== 'undefined'){
                            if(val != null && typeof val === 'object'){
                                for(var x in val){
                                    $el.children("option[value='"+val+"']").prop("checked",val[x]);
                                }
                            }else{
                                $el.children('option').each(function(){
                                    $(this).prop('checked',($(this).val() === String(val)));
                                });
                            }
                        //out
                        }else{
                            var out = {};
                            $el.children('option').each(function(){
                                out[$(this).val()] = $(this).prop("checked");
                            });
                            return out;
                        }
                    },
                    'input-hidden' : function($el,json,result,saveFunc){ return this._text($el,json,result,saveFunc)},
                    'input-text' : function($el,json,result,saveFunc){ return this._text($el,json,result,saveFunc)},
                    'textarea' : function($el,json,result,saveFunc){ return this._text($el,json,result,saveFunc)},
                    'input-checkbox' : function($el,json,result,saveFunc){ return this._check($el,json,result,saveFunc)},
                    'input-radio' : function($el,json,result,saveFunc){ return this._radio($el,json,result,saveFunc)},
                    'select' : function($el,val){
                        //in
                        if(typeof val !== 'undefined'){
                            $el.children('option[value="'+val+'"]').prop('selected',true);
                        //out
                        }else{
                            return $el.children('option:selected').val();
                        }
                    },
                    'select-multi' : function($el,json,result,saveFunc){ return this._selectMulti($el,json,result,saveFunc)}
                }
            };
            //add pointers to _handlers in user defined handlers for in/out
            for(var x in result._handlers){
                if(x !== 'filters' && x !== 'actions'){
                    result.fill.handlers[x] = result._handlers[x];
                    result.extract.handlers[x] = result._handlers[x];
                }
            }
            for(var x in result._handlers.filters){
                result.fill.handlers.filters.push(result._handlers.filters[x]);
                result.extract.handlers.filters.push(result._handlers.filters[x]);
            }
            return result;
        }
        jQuery.fn.forms = function(options,nameOrJson,fn){
            
            //for holding results of extract
            var result = {};
            
            //all the elements from jquery selector, expecting "form" tags
            this.each(function(index,el){
                var form = $(el).data("forms");
                if(!form){
                    form = new Forms();
                    $(el).data("forms", form);
                }
                
                if(options === "extract"){
                    $.extend(result,form._extract($(el)));
                } else if(options === "fill"){
                    form._fill($(el), nameOrJson);
                } else if(options === "addFillFilter"){
                    form.fill.handlers.filters.push(function($el,val,json){
                        if($el.attr('name') === nameOrJson){
                            return fn($el,val,json);
                        }
                        return val;
                    });
                } else if(options === "addExtractFilter"){
                    form.extract.handlers.filters.push(function($el,val,json){
                        if($el.attr('name') === nameOrJson){
                            return fn($el,val,json);
                        }
                        return val;
                    });
                } else if(options === "addExtractAction"){
                    form.extract.handlers.actions[nameOrJson] = fn;
                } else if(options === "addFillAction"){
                    form.fill.handlers.actions[nameOrJson] = fn;
                }
            });
            
            if(options === "extract"){
                return result;
            }
            return this;
        }
    })(jQuery );