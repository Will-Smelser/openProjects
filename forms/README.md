## Forms
    Forms was created to help extract data from HTML <form> elements in a standardized manner allowing for easy 
    extraction and filling of forms.

    This is a normal jquery plugin.
    
## Dependencies
1. Requires jQuery.  However, it would not be hard to remove the dependency.  If you want a standalone, let me know.
2. [forms-core2.js jsDoc](./required/js/)
    
## Notes
* Construction
--* You can only call jQuery.fn.forms once per element.  The first call creates the Forms object.
* Filters
--* Do not modify the "name" attribute of the returned JSON.  This name must align with the form elements name attribute.
--* During Fill filters, Filter may be called multiple times, but only applied once.  This happens when evaluating radio/checkboxes.  This is because the name has to be evaluated looking for matching value attribute.

## Options
```javascript
//default settings, these get merged with user settings
var settings = {
    filterBase : true,
    filterSlim : true,
    filters : {
        extract:[],
        fill:[]
    }
};

//usage
$('form').forms(settings);
```
    
### Examples
#### Extract Form Data
```javascript
$('form').forms(
    function(form){
        //print the form data to the console as a JSON object
        console.log(forms.extract());
    }
);
```

#### Custom Filter (filtered by name)
```html
<form name="myform">
    <input type="text" name="email" />
</form>
```
```javascript
var global_valid_form = true;
$('form').first().forms(
    function(form){
        form.addNameExtractFilter("email",function(Type, json){
            console.log(json.value);
            if(!json.value || json.value.length < 3){
                global_valid_form = false;
                alert("Please enter a valid email");
            }
            //you should return the value, this will be passed to next filter if there is one.
            return json;
        });
    }
);
```

#### Custom Filter (filtered by regex)
```javascript
var global_valid_form = true;
$('form').first().forms(
    function(form){
        //match all elements with attribute "name" that starts with email
        form.addNameExtractFilter(/^email.*/,function(Type, json){
            console.log(json.value);
            if(!json.value || json.value.length < 3){
                global_valid_form = false;
                alert("Please enter a valid email");
            }
            //you should return the value, this will be passed to next filter if there is one.
            return json;
        });
    }
);
```

#### Custom Filters for Conversion
    Imagine you want to save the form currency value in USD, but your are displaying for EURO.
    
```html
<form name="myform">
    Product X costs: <input type="text" name="cost" disabled/> Euros
</form>
```
```javascript
var euro2us = 1.07;
$('form').first().forms(
    function(form){
        form.addNameExtractFilter("cost",function(Type, json){
            json.value = euro2us * json.value;
            return json;
        });
        form.addNameFillFilter("cost",function(Type, json){
            json.value = json.value / euro2us;
            return json;
        });
    }
);
```