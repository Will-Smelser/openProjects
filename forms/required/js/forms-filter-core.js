(function(){
    if(typeof window.FormSettings === 'undefined'){
        window.FormSettings = {
            filters : {
                extract : [],
                fill : []
            }
        };
    }

    window.FormSettings.filters.extract.push(function(type, obj){
        return type.toJSON();
    });
})();