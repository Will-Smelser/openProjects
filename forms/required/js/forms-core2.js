/**
1.  Serialized form elements
2.  Take serialized form elements and fill
*/

(function( $ ){

    /**
     * Iterate the form elements
     * @param $form The form tag to iterate
     * @param cb Callback function which takes a jQuery element
     */
    var formIterator = function($form, ctx, cb){
        var name = $form.attr('name');
        if(document.forms[name]){
            for(var i=0; i<document.forms[name].length; i++){
                var $el = $(document.forms[name].elements[i]);
                cb($el, ctx);
            }
        }
    };

    /**
     *  Supported form elements
     */
    var Types = {
        //text, password, radio, checkbox, html5 types
        Text:function($el, name, value){this.$el=$el;this.name=name;this.value=value;},
        Checkbox:function($el, name, value, checked){this.$el=$el;this.name=name;this.value=value;this.checked=checked;},
        Radio:function($el, name, value, checked){this.$el=$el;this.name=name;this.value=value;this.checked=checked;},
        Select:function($el, name, value){this.$el=$el;this.name=name;this.value=value;},
        SelectMulti:function($el, name, value){this.$el=$el;this.name=name;this.value=value;},
        TextArea:function($el, name, value){this.$el=$el;this.name=name;this.value=value;},
        Button:function($el, name, value){this.$el=$el;this.name=name;this.value=value;},
        DataList:function($el, name, value){this.$el=$el;this.name=name;this.value=value;},
        KeyGen:function($el, name, value){this.$el=$el;this.name=name;this.value=value;},

        //fieldset is actually a little special
        Fieldset:function($el, name){this.$el=$el;this.name=name;}
    };

    //we will extend the above Type objects
    for(var x in Types){
        Types[x].prototype.type = x;
        Types[x].prototype.constructor = x;
        Types[x].prototype.getName = function(){return this.name};
        Types[x].prototype.getValue = function(){return this.value};
        Types[x].prototype.getType = function(){return this.prototype.constructor};
        Types[x].prototype.toJSON = function(includeEl){
            var result = {name : this.name, value : this.value, type : this.type}
            if(typeof this.checked !== 'undefined') result.checked = this.checked; //for radios and checkboxes
            if(typeof this.elements !== 'undefined') result.elements = this.elements; //for fieldset
            if(includeEl) result.$el = this.$el;
            return result;
        };
        Types[x].prototype.updateValue = function(json){this.$el.val(json.value)};
    }

    //specific for checkbox and radios
    Types.Checkbox.prototype.isChecked = function(){return this.checked};
    Types.Radio.prototype.isChecked = function(){return this.checked};
    Types.Checkbox.prototype.updateValue = function(json){this.$el.prop('checked',json.checked)};
    Types.Radio.prototype.updateValue = function(json){this.$el.prop('checked',json.checked)};

    //specifit for fieldset
    Types.Fieldset.prototype.updateValue = function(elements){this.elements=elements};

    /**
     *  Convert a '<input type="x"' into a Type object.  Will default to Type.Text
     *  if not matched to any known types (checkbox, radio).
     */
    var inputToType = function($el){
        var name = $el.attr('name');
        var val = $el.val();
        var type = $el.attr('type') || 'text';

        switch(type.toLowerCase()){
            case 'checkbox':
                return new Types.Checkbox($el,name,val,$el.is(':checked'));
            case 'radio':
                return new Types.Radio($el,name,val,$el.is(':checked'));
            default:
            case 'password':
            case 'text':
                return new Types.Text($el,name,val);
        }
    };

    var elToType = function($el){
        var name = $el.attr('name');
        var val = $el.val();
        var tag = $el.prop('tagName').toLowerCase();
        switch(tag){
            case 'input':
                return inputToType($el);
            case '':
                return new Types.Fieldset($el,name);
            case 'select':
                return new Types.Select($el,name,val);
            case 'selectMulti':
                return new Types.SelectMulti($el,name,val);
            case 'textarea':
                return new Types.TextArea($el,name,val);
            case 'button':
                return new Types.Button($el,name,val);
            case 'datalist':
                return new Types.DataList($el,name,val);
            case 'keygen':
                return new Types.KeyGen($el,name,val);
            case 'output':
                return new Types.Output($el,name,$el.text());
            case 'fieldset':
                return new Types.Fieldset($el, name, val);
            default:
                if(console) console.error('Unsupported type: '+tag+', will be skipped');
        }
    };

    var typeToEl = function(type){
        var name = type.getName
    };

    /**
     * Apply the filters to the given Type object
     * @param name The name of form element
     * @param obj This is a Types JSON object representation
     * @param filters The filters to apply
     * @param method Which filters method to call.  Either 'extract' or 'fill'
     * @param target Only available when this is a fill call.
     */
    var _applyFilters = function(name, obj, filters, method, target){
        for(var x in filters){
            for(var filter in filters[x]){
                if(typeof filters[x][filter][method] === 'function'){
                    obj = filters[x][filter][method](name,obj,target);
                }
            }
        }
        return obj;
    }

    /**
     * Iterate a form elements and mark nodes as visited as you visit them.  This is for extracting values.
     */
    var _itr = function(formEls, ctx, filters, includeEl){
        for(var i=0; i < formEls.length; i++){
            var $el = $(formEls[i]);

            if($el.attr('data-forms-visited') === 'true') continue;

            var type = elToType($el);
            var name = type.getName();

            $el.attr('data-forms-visited',true);

            //special case <fieldset>
            if(type instanceof Types.Fieldset){
                var elements = {};
                _itr(formEls[i].elements, elements, filters, includeEl);
                type.updateValue(elements);
                var temp = _applyFilters(name,type.toJSON(includeEl), filters, 'extract');
                if(typeof temp !== 'undefined'){
                    if(typeof ctx[name] === 'undefined'){
                        ctx[name] = [];
                    }
                    ctx[name].push(temp);
                }

            //normal case, store object on result with the given name
            } else if(typeof ctx[name] === 'undefined'){
                var temp = _applyFilters(name,type.toJSON(includeEl), filters, 'extract');
                if(typeof temp !== 'undefined'){
                    ctx[name] = temp;
                }

            //we already have duplicate names, just add to the array
            }else if($.isArray(ctx[name])){
                var temp = _applyFilters(name,type.toJSON(includeEl), filters, 'extract');
                if(typeof temp !== 'undefined'){
                    ctx[name].push(temp);
                }

            //have an object, need to convert to array of objects.  Means duplicate name in the form
            }else{
                var temp2 = _applyFilters(name,type.toJSON(includeEl), filters, 'extract');

                if(typeof temp2 !== 'undefined'){
                    var temp = ctx[name];
                    ctx[name] = [];
                    ctx[name].push(temp);
                    ctx[name].push(temp2);
                }
            }
        }
    }

    /**
     * Make all elments as not visitied.
     */
    var _itrUnvisit = function(formEls){
        for(var i=0; i < formEls.length; i++){
            var $el = $(formEls[i]);
            $el.attr('data-forms-visited',false);
        }
    }

    function Forms($form){
        this.form = $form;

        var TYPE_FILTER = 0;
        var NAME_FILTER = 1;

        this.filters = {};
        this.filters[TYPE_FILTER] = [];
        this.filters[NAME_FILTER] = [];

        var self = this;

        var _extract = function(includeEl){
            if(typeof includeEl === 'undefined') includeEl = false;

            var output = {};

            var formName = $form.attr('name');

            if(!formName) return output;

            _itr(document.forms[formName].elements, output, self.filters, includeEl);
            _itrUnvisit(document.forms[formName].elements); //need to unmark elements as visited

            return output;
        };


        /**
         * Fill a form from the JSON output and schema,  should really do an extract and iterate that along with the json
         */
        var _fill = function(name, json, schema){
            //console.log(json,schema);
            //var name = $form.attr('name');

            for(var x in schema){
                if(typeof json[x] !== 'undefined'){
                    if(schema[x].type === 'Fieldset'){
                        //_applyFilters(x,json[x],self.filters,'fill',type)
                        _fill(name, json[x].elements, schema[x].elements);
                    }else if($.isArray(schema[x])){
                        for(var y in schema[x]){
                            var sname = schema[x][y].name;
                            var temp1 = {};
                            var temp2 = {};
                            temp1[sname] = schema[x][y];
                            temp2[sname] = json[x][y];
                            _fill(name, temp2, temp1);
                        }
                    }else{
                        console.log(x,schema[x]);
                        var type = elToType(schema[x].$el);
                        type.updateValue(_applyFilters(x,json[x],self.filters,'fill',schema[x].type));
                    }
                }else{
                    if(console) console.log("Had to skip: "+json[x])
                }
            }
        };


        return {
            addTypeFilter : function(type1,method,fn){
                var obj = {};
                obj[method] = function(name,type2,target){
                    if(type1 === type2.type || (type1 instanceof RegExp && type1.test(type2.type))){
                        return fn(name,type2,target)
                    }
                    return type2;
                };
                self.filters[TYPE_FILTER].push(obj);
            },
            addNameFilter : function(name,method,fn){
                var obj = {};
                obj[method] = function(name,type,target){
                    if(type.name === name || (name instanceof RegExp && name.test(type2.type))){
                        return fn(name,type,target);
                    }
                    return type;
                }
                self.filters[NAME_FILTER].push(obj);
            },
            extract : _extract,
            fill : _fill,
            getFilters : function(){
                return self.filters;
            }
        }

    };


    jQuery.fn.forms = function(options,nameOrJson,method,fn){

        //for holding results of extract
        var result = [];

        //all the elements from jquery selector, expecting "form" tags
        this.each(function(index,el){
            var $el = $(el);
            var form = $el.data("forms");
            if(!form){
                form = new Forms($el);
                $el.data("forms", form);
            }

            if(options === "extract"){
                result.push(form.extract());
            } else if(options === "fill"){
                form.fill($el.attr('name'),nameOrJson,form.extract(true));
            } else if(options === "addTypeFilter"){
                if(method === 'fill' || method === 'extract'){
                    form.addTypeFilter(nameOrJson,method,fn);
                }else{
                    if(console) console.log("expected 'fill' or 'extract', but got "+method);
                }

            } else if(options === "addNameFilter"){
                if(method === 'fill' || method === 'extract'){
                    form.addNameFilter(nameOrJson,method,fn);
                }else{
                    if(console) console.log("expected 'fill' or 'extract', but got "+method);
                }
            } else if(options === "getForm"){
                nameOrJson(form);
            }
        });

        if(options === "extract"){
            if(result.length === 1){
                return result[0];
            }
            return result;
        }
        return this;
    }
})(jQuery );