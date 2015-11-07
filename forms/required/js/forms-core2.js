/**
1.  Serialized form elements
2.  Take serialized form elements and fill
*/

(function( $ ){

    /**
     * Simplifies adding to an object.  If an object key already exists, change value to an array
     * and add the value to the array.
     */
    var _add = function(obj, key, value){
        if(typeof value === 'undefined' || value === null) return;

        if(typeof obj[key] === 'undefined'){
            obj[key] = value;
        }else if($.isArray(obj[key])){
            obj[key].push(value);
        }else{
            var temp = obj[key];
            obj[key] = [];
            obj[key].push(temp);
            obj[key].push(value);
        }
    };

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

    //we will extend the above Type objects with their prototypes.  Just trying to save some repeated code.
    for(var x in Types){
        Types[x].prototype.type = x;
        Types[x].prototype.constructor = x;
        Types[x].prototype.getName = function(){return this.name};
        Types[x].prototype.getValue = function(){return this.value};
        Types[x].prototype.getType = function(){return this.prototype.constructor};
        Types[x].prototype.equals = function(type){return (type && this.type === type.type && this.name === type.name)};
        Types[x].prototype.toJSON = function(){
            var result = {name : this.name, value : this.value, type : this.type}
            if(typeof this.checked !== 'undefined') result.checked = this.checked; //for radios and checkboxes
            if(typeof this.elements !== 'undefined') result.elements = this.elements; //for fieldset
            return result;
        };
        Types[x].prototype.updateValue = function(json){this.$el.val(json.value)};
        Types[x].prototype.clone = function(){
            var clone = jQuery.extend(true, {}, this);
            clone.value = null;
            return clone;
        };
    }

    //specific for checkbox and radios
    Types.Checkbox.prototype.equals = function(type){return (type && this.type === type.type && this.value === type.value && this.name === type.name)};
    Types.Radio.prototype.equals = function(type){return (type && this.type === type.type && this.value === type.value && this.name === type.name)};

    Types.Checkbox.prototype.isChecked = function(){return this.checked};
    Types.Radio.prototype.isChecked = function(){return this.checked};

    Types.Checkbox.prototype.updateValue = function(json){this.$el.prop('checked',json.checked)};
    Types.Radio.prototype.updateValue = function(json){this.$el.prop('checked',json.checked)};

    Types.Checkbox.prototype.clone = function(){
        var clone = jQuery.extend(true, {}, this);
        clone.checked = false;
        return clone;
    };
    Types.Radio.prototype.clone = function(){
        var clone = jQuery.extend(true, {}, this);
        clone.checked = false;
        return clone;
    };

    //specific for fieldset
    Types.Fieldset.prototype.updateValue = function(elements){this.elements=elements};
    Types.Fieldset.prototype.toJSON = function(){
        var result = {};
        for(var x in this.elements){
            if($.isArray(this.elements[x])){
                for(var y in this.elements[x]){
                    _add(result, this.elements[x][y].name, this.elements[x][y].toJSON());
                }
            }else{
                _add(result, this.elements[x].name, this.elements[x].toJSON());
            }
        }
        return result;
    };

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

    /**
     * Create a Type object form an HTML Dom element.
     */
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

    /**
     * Apply the filters to the given Type object
     * @param type A Type object for current element.
     * @param obj This is a Types JSON object representation
     * @param filters The filters to apply
     */
    var _applyFilters = function(type, obj, filters){
        for(var filter in filters){
            if(typeof filters[filter] === 'function'){
                obj = filters[filter](type, obj);
            }
        }

        return obj;
    }

    /**
     * Iterate the form elements creating a schema, which is an Object comprised of Type objects.
     */
    var _schema = function(formEls, ctx){
        for(var i=0; i < formEls.length; i++){
            var $el = $(formEls[i]);

            if($el.attr('data-forms-visited') === 'true') continue;

            var type = elToType($el);
            var name = type.getName();

            $el.attr('data-forms-visited',true);

            //special case <fieldset>
            if(type instanceof Types.Fieldset){
                var elements = {};
                _schema(formEls[i].elements, elements);
                type.updateValue(elements);

                if(typeof ctx[name] === 'undefined'){
                    ctx[name] = [];
                }
                ctx[name].push(type);

            //normal case, store object on result with the given name
            } else if(typeof ctx[name] === 'undefined'){
                ctx[name] = type;

            //we already have duplicate names, just add to the array
            }else if($.isArray(ctx[name])){
                ctx[name].push(type);

            //have an object, need to convert to array of objects.  Means duplicate name in the form
            }else{
                var temp = ctx[name];
                ctx[name] = [];
                ctx[name].push(temp);
                ctx[name].push(type);
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
    };

    /**
     * Convert schema into its JSON form.  Basically just recursively calls toJSON() on
     * elements in the Schema.
     */
    var _extractJsonFilter = function(schema){
        var result = {};

        for(var x in schema){
            if($.isArray(schema[x])){
                for(var y in schema[x]){
                    _add(result, x, schema[x][y].toJSON());
                }
            }else{
                _add(result, x, schema[x].toJSON());
            }
        }
        return result;
    };

    /**
     * Wrapper for methods for working on a form element.
     * @param $form The jquery "<form>" element.
     * @param options Options configuration for form processing.
     */
    Forms = function($form, options){
        var self = this;

        this.form = $form;

        var settings = {
            filterBase : true,
            filterSlim : true,
            filters : {
                extract:[],
                fill:[]
            }
        };

        if(options){
            $.extend(settings, options);
        }


        //add the slim filter.  This gives a minimal form representation.
        if(settings.filterSlim){
            //extract filter for slim
            settings.filters.extract.unshift(function(type, obj){
                if(type.type === 'Radio' || type.type === 'Checkbox'){
                    if(type.checked){
                        return type.value;
                    }
                }else if(type.type === 'Fieldset'){
                    return type.elements;
                }else{
                    return type.value;
                }
            });

            //the slim fill filter.  Use the Type and json to rebuild the representation.
            settings.filters.fill.unshift(function(type, obj){

                var json = type.toJSON();
                switch(type.type){
                    case 'Checkbox':
                    case 'Radio':
                        json.checked = (type.value === obj);
                        json.value = obj;
                        break;
                    case 'Fieldset':
                        //console.log('fieldset',name,value,target);
                        break;
                    default:
                        json.value = obj;
                }
                //console.log(name,value,target);
                return json;
            });
        };


        //this is the minimum filter for converting Type object to a serializable JSON representation.
        if(settings.filterBase || settings.filterSlim){
            //the most basic extract function
            settings.filters.extract.unshift(function(type, obj){return type.toJSON();});
        }

        /**
         * Fill a form from the JSON output and schema,  should really do an extract and iterate that along with the json
         */
        var _fill = function(json, schema, filters){

            for(var x in schema){
                if(typeof json !== 'undefined' && typeof json[x] !== 'undefined'){
                    if(schema[x].type === 'Fieldset'){
                        _fill(json[x], schema[x].elements, filters);

                    }else if($.isArray(schema[x])){
                        //maybe a checkbox or multiselect, but only 1 element given
                        if(!$.isArray(json[x])){
                            for(var y in schema[x]){
                                var temp = _applyFilters(schema[x][y], json[x], filters);
                                schema[x][y].updateValue(temp);
                            }
                        }else{

                            //possible we have fewer entries in JSON than we have in
                            //the schema, may need to skip schema entries
                            var skips = 0;

                            for(var y in schema[x]){

                                var temp = _applyFilters(schema[x][y], json[x][y-skips], filters);



                                //possible we are missing elements in json, that schema has.  So skip.
                                if(!schema[x][y].equals(temp) && schema[x][y].type !== 'Fieldset'){
                                    schema[x][y].updateValue(schema[x][y].clone());
                                    skips++;
                                    continue;
                                }

                                var sname = schema[x][y].name;
                                var temp1 = {};
                                var temp2 = {};
                                temp1[sname] = schema[x][y];
                                temp2[sname] = json[x][y-skips];
                                _fill(temp2, temp1, filters);
                            }
                        }
                    }else{
                        schema[x].updateValue(_applyFilters(schema[x], json[x], filters));
                    }
                }else{
                    if(console) console.log("Element was undefined, skipped.",json,x)
                }
            }
        };

        var _extract = function(schema, result, filters){
            for(var x in schema){
                if(schema[x].type === 'Fieldset'){
                    var result2 = {};
                    _extract(schema[x].elements, result2, filters);
                    _add(result,schema[x].name,result2);


                }else if($.isArray(schema[x])){
                    for(var y in schema[x]){
                        _extract([schema[x][y]], result, filters);
                    }
                }else{
                    _add(result,schema[x].name,_applyFilters(schema[x], schema[x], filters));
                }
            }
            return result;
        };

        return {
            filters : settings.filters,

            /**
             * Add a filter which happens at fill time.  You will be given a properly filled
             * Type object to work with.  Meaning, this adds to the end of the fill process.
             * @param fn The function to be called during the filter process.  The signature is
             * fn(Type, JSON).  Where the Type is a Types object that represents the form elment.
             * And JSON is the incoming representation of the form element.  You should modify the the JSON and
             * return it.  The returned element should be in the same format as Types.Type.toJSON() output.
             */
            addFillFilter : function(fn){filters.fill.push(fn);},

            /**
             * Add a filter that happens at the extract time.  This adds to filter at index 1.  Meaning it is the second
             * filter called.  The first filter is always calling Types.Type.toJSON() so you will be working on the
             * JSON representation of the form element.
             *
             * @param fn The function to be called during the filter process.  The signature is
             * fn(Type, JSON).  Where the Type is a Types object that represents the form elment.
             * And JSON is the incoming representation of the form element.  You should modify the the JSON and
             * return it.  The returned element should be in the same format as Types.Type.toJSON() output.
             */
            addExtractFilter : function(fn){filters.extract.splice(1,0,fn);},

            /**
             * Add a filter that will only filter on the form element's "name" attribute.
             * @param name Sting or Regex.  Will check the elements name.
             * @param fn The function to apply if the name matches.  See {@link #addFillFilter}.
             */
            addNameFillFilter : function(name, fn){
                this.addFillFilter(this._filter(name, function(type){return type.name}, fn));
            },

            /**
             * Add a filter that will only filter on the form element's "name" attribute.
             * @param name Sting or Regex.  Will check the elements name.
             * @param fn The function to apply if the name matches.  See {@link #addFillFilter}.
             */
            addNameExtractFilter : function(name, fn){
                this.addExtractFilter(this._filter(name,function(type){return type.name},fn));
            },

            /**
             * Add a filter that will only filter on the form element's tag name.
             * @param name Sting or Regex.  Will check the elements tag name.
             * @param fn The function to apply if the name matches.  See {@link #addFillFilter}.
             */
            addTypeFillFilter : function(typeName, fn){
                this.addFillFilter(this._filter(typeName, function(type){return type.$el.tagName}, fn));
            },

            /**
             * Add a filter that will only filter on the form element's tag name.
             * @param name Sting or Regex.  Will check the elements tag name.
             * @param fn The function to apply if the name matches.  See {@link #addFillFilter}.
             */
            addTypeExtractFilter : function(typeName, fn){
                this.addExtractFilter(this._filter(typeName, function(type){return type.$el.tagName}, fn));
            },

            /**
             * Used by filter functions to avoid code duplication.
             * @param name The String or RegExp to be used for comparison.
             * @param get A function that returns the value from a Types.Type to be compared to {@param name}
             * @param fn A function to apply if a comparison is true.
             */
            _filter : function(name, get, fn){
                return
                    function(type, json){
                       if(name instanceof RegExp){
                           if(name.test(get(type)){
                               return fn(type,json);
                           }
                       }else if(name === get(type)){
                           return fn(type,json);
                       }
                       return json;
                   };
            },

            /**
             * Fill the form given the json.  This will perform filters on the given json.
             * @param json A JSON object created from an extract call.
             */
            fill : function(json){
                _fill(json, this.getSchema(), this.filters.fill);
            },

            /**
             * Returns the filters used by this Form object.
             */
            getFilters : function(){
                return self.filters;
            },

            /**
             * Extract the current form element data into a JSON object which can be serialized.  The filters will be applied
             * during the extract process.
             */
            extract : function(){
                var result = {};
                var schema = this.getSchema();
                window.temp = this.getSchema();

                _extract(schema, result, this.filters.extract);
                return result;
            },

            /**
             * Get a JSON object comprised of name and Types.Type that represent this form.
             */
            getSchema : function(){
                var output = {};

                var formName = $form.attr('name');

                if(!formName) return output;

                _schema(document.forms[formName].elements, output);
                _itrUnvisit(document.forms[formName].elements); //need to unmark elements as visited

                return output;
            }
        };
    };



    $.fn.forms = function(options){

        //all the elements from jquery selector, expecting "form" tags
        this.each(function(index,el){
            var $el = $(el);
            var form = $el.data("forms");
            if(!form){
                form = new Forms($el, options);
                $el.data("forms", form);
            }

            if(typeof options === 'function'){
                options(form);
            }
        });

        return this;
    }


})(jQuery );