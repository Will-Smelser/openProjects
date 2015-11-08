## Forms
    Forms was created to help extract data from HTML &lt;form&gt; elements in a standardized manner allowing for easy 
    extraction and filling of forms.

    This is a normal jquery plugin.
    
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