(function($){

    if(typeof window.Forms === 'undefined'){
        if(console) console.log("Forms does not exist.  forms-core.js must be loaded first.");
        return;
    }

    /**
     * Simplifies adding to an object.  If an object key already exists, change it to an array
     * and add the value to the array.
     */
    var _add = function(obj, key, value){
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

    /*
    //add a type filter
    window.Forms.prototype.filters[0].push(function(name,type){
        var result = {};
        console.log("CALLED");
        _add(result,name,type.toJSON());

        return result;
    });
    */

})(jQuery);