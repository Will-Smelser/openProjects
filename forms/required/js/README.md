## Classes
<dl>
<dt><a href="#TypeBase">TypeBase</a></dt>
<dd></dd>
<dt><a href="#Forms">Forms</a></dt>
<dd></dd>
</dl>
## Objects
<dl>
<dt><a href="#jQuery">jQuery</a> : <code>object</code></dt>
<dd><p><a href="http://api.jquery.com/">http://api.jquery.com/</a></p>
</dd>
<dt><a href="#Types">Types</a> : <code>object</code></dt>
<dd><p>Supported form elements</p>
</dd>
</dl>
## Functions
<dl>
<dt><a href="#$.fn.forms">$.fn.forms(options)</a> ⇒ <code><a href="#jQuery">jQuery</a></code></dt>
<dd><p>The forms namespace extends jQuery.fn to be a jQuery plugin.</p>
</dd>
</dl>
<a name="TypeBase"></a>
## TypeBase
**Kind**: global class  

* [TypeBase](#TypeBase)
  * [new TypeBase()](#new_TypeBase_new)
  * [.getName()](#TypeBase+getName) ⇒ <code>String</code>
  * [.getValue()](#TypeBase+getValue) ⇒ <code>Object</code>
  * [.getType()](#TypeBase+getType) ⇒ <code>String</code>
  * [.equals(type)](#TypeBase+equals) ⇒ <code>boolean</code>
  * [.equals()](#TypeBase+equals)
  * [.updateValue(json)](#TypeBase+updateValue)
  * [.clone()](#TypeBase+clone)

<a name="new_TypeBase_new"></a>
### new TypeBase()
This is the base class that all Types objects extend.

<a name="TypeBase+getName"></a>
### typeBase.getName() ⇒ <code>String</code>
Returns the elements name attribute.

**Kind**: instance method of <code>[TypeBase](#TypeBase)</code>  
**Returns**: <code>String</code> - The form elements name attribute value.  
<a name="TypeBase+getValue"></a>
### typeBase.getValue() ⇒ <code>Object</code>
**Kind**: instance method of <code>[TypeBase](#TypeBase)</code>  
**Returns**: <code>Object</code> - The form elements value attribute value.  
<a name="TypeBase+getType"></a>
### typeBase.getType() ⇒ <code>String</code>
Gets the type of the object.

**Kind**: instance method of <code>[TypeBase](#TypeBase)</code>  
**Returns**: <code>String</code> - The string name of the Types object.  
<a name="TypeBase+equals"></a>
### typeBase.equals(type) ⇒ <code>boolean</code>
**Kind**: instance method of <code>[TypeBase](#TypeBase)</code>  
**Returns**: <code>boolean</code> - True if they are equivalent, false otherwise.  

| Param | Type | Description |
| --- | --- | --- |
| type | <code>[Types](#Types)</code> | A Types object. |

<a name="TypeBase+equals"></a>
### typeBase.equals()
Get the serializable JSON representation of the Types object.

**Kind**: instance method of <code>[TypeBase](#TypeBase)</code>  
<a name="TypeBase+updateValue"></a>
### typeBase.updateValue(json)
Update the inner representation of Types object from a given JSON represetation.  See [TypeBase#toJSON](TypeBase#toJSON).

**Kind**: instance method of <code>[TypeBase](#TypeBase)</code>  

| Param | Type | Description |
| --- | --- | --- |
| json | <code>Object</code> | A JSON representation of a Types object.  See [TypeBase#toJSON](TypeBase#toJSON). |

<a name="TypeBase+clone"></a>
### typeBase.clone()
Make a copy of the Types object.  It really returns a "default" value of a the current Type.  The "value"will not be copied.

**Kind**: instance method of <code>[TypeBase](#TypeBase)</code>  
<a name="Forms"></a>
## Forms
**Kind**: global class  

* [Forms](#Forms)
  * [new Forms($form, options)](#new_Forms_new)
  * [.Types](#Forms+Types)
  * [.filters](#Forms+filters)
  * [.addFillFilter(fn)](#Forms+addFillFilter)
  * [.addExtractFilter(fn)](#Forms+addExtractFilter)
  * [.addNameFillFilter(name, fn)](#Forms+addNameFillFilter)
  * [.addNameExtractFilter(name, fn)](#Forms+addNameExtractFilter)
  * [.addTypeFillFilter(name, fn)](#Forms+addTypeFillFilter)
  * [.addTypeExtractFilter(name, fn)](#Forms+addTypeExtractFilter)
  * [._filter(name, get, fn)](#Forms+_filter)
  * [.fill(json)](#Forms+fill)
  * [.getFilters()](#Forms+getFilters)
  * [.extract()](#Forms+extract)
  * [.getSchema()](#Forms+getSchema)

<a name="new_Forms_new"></a>
### new Forms($form, options)
Wrapper for methods for working on a form element.


| Param | Type | Description |
| --- | --- | --- |
| $form | <code>[jQuery](#jQuery)</code> | The jquery "&lt;form&gt;" element. |
| options | <code>Object</code> | Options configuration for form processing. |

<a name="Forms+Types"></a>
### forms.Types
Let user's create [Types](#Types).

**Kind**: instance property of <code>[Forms](#Forms)</code>  
<a name="Forms+filters"></a>
### forms.filters
All the filters that will be applied.  Depending on the options passed in at construction, you willbe given some default filters.

**Kind**: instance property of <code>[Forms](#Forms)</code>  
<a name="Forms+addFillFilter"></a>
### forms.addFillFilter(fn)
Add a filter which happens at fill time.  You will be given a properly filledType object to work with.  Meaning, this adds to the end of the fill process.

**Kind**: instance method of <code>[Forms](#Forms)</code>  

| Param | Type | Description |
| --- | --- | --- |
| fn | <code>function</code> | The function to be called during the filter process.  The signature is fn(Type, JSON).  Where the Type is a Types object that represents the form elment. And JSON is the incoming representation of the form element.  You should modify the the JSON and return it.  The returned element should be in the same format as Types.Type.toJSON() output. |

<a name="Forms+addExtractFilter"></a>
### forms.addExtractFilter(fn)
Add a filter that happens at the extract time.  This adds to filter at index 1.  Meaning it is the secondfilter called.  The first filter is always calling Types.Type.toJSON() so you will be working on theJSON representation of the form element.

**Kind**: instance method of <code>[Forms](#Forms)</code>  

| Param | Type | Description |
| --- | --- | --- |
| fn | <code>function</code> | The function to be called during the filter process.  The signature is fn(Type, JSON).  Where the Type is a Types object that represents the form elment. And JSON is the incoming representation of the form element.  You should modify the the JSON and return it.  The returned element should be in the same format as Types.Type.toJSON() output. |

<a name="Forms+addNameFillFilter"></a>
### forms.addNameFillFilter(name, fn)
Add a filter that will only filter on the form element's "name" attribute.

**Kind**: instance method of <code>[Forms](#Forms)</code>  

| Param | Type | Description |
| --- | --- | --- |
| name | <code>String</code> &#124; <code>RegExp</code> | Will check the elements name. |
| fn | <code>function</code> | The function to apply if the name matches.  See [#addFillFilter](#addFillFilter). |

<a name="Forms+addNameExtractFilter"></a>
### forms.addNameExtractFilter(name, fn)
Add a filter that will only filter on the form element's "name" attribute.

**Kind**: instance method of <code>[Forms](#Forms)</code>  

| Param | Type | Description |
| --- | --- | --- |
| name | <code>String</code> &#124; <code>RegExp</code> | Will check the elements name. |
| fn | <code>function</code> | The function to apply if the name matches.  See [#addFillFilter](#addFillFilter). |

<a name="Forms+addTypeFillFilter"></a>
### forms.addTypeFillFilter(name, fn)
Add a filter that will only filter on the form element's tag name.

**Kind**: instance method of <code>[Forms](#Forms)</code>  

| Param | Type | Description |
| --- | --- | --- |
| name | <code>String</code> &#124; <code>RegExp</code> | Will check the elements tag name. |
| fn | <code>function</code> | The function to apply if the name matches.  See [#addFillFilter](#addFillFilter). |

<a name="Forms+addTypeExtractFilter"></a>
### forms.addTypeExtractFilter(name, fn)
Add a filter that will only filter on the form element's tag name.

**Kind**: instance method of <code>[Forms](#Forms)</code>  

| Param | Type | Description |
| --- | --- | --- |
| name | <code>String</code> &#124; <code>RegExp</code> | Will check the elements tag name. |
| fn | <code>function</code> | The function to apply if the name matches.  See [#addFillFilter](#addFillFilter). |

<a name="Forms+_filter"></a>
### forms._filter(name, get, fn)
Used by filter functions to avoid code duplication.

**Kind**: instance method of <code>[Forms](#Forms)</code>  

| Param | Type | Description |
| --- | --- | --- |
| name | <code>String</code> | The String or RegExp to be used for comparison. |
| get | <code>function</code> | A function that returns the value from a Types.Type to be compared to {@param name} |
| fn | <code>function</code> | A function to apply if a comparison is true. |

<a name="Forms+fill"></a>
### forms.fill(json)
Fill the form given the json.  This will perform filters on the given json.

**Kind**: instance method of <code>[Forms](#Forms)</code>  

| Param | Type | Description |
| --- | --- | --- |
| json | <code>Object</code> | A JSON object created from an extract call. |

<a name="Forms+getFilters"></a>
### forms.getFilters()
Get the this#filters object.

**Kind**: instance method of <code>[Forms](#Forms)</code>  
<a name="Forms+extract"></a>
### forms.extract()
Extract the current form element data into a JSON object which can be serialized.  The filters will be appliedduring the extract process.

**Kind**: instance method of <code>[Forms](#Forms)</code>  
<a name="Forms+getSchema"></a>
### forms.getSchema()
Get a JSON object comprised of name and Types.Type that represent this form.

**Kind**: instance method of <code>[Forms](#Forms)</code>  
<a name="jQuery"></a>
## jQuery : <code>object</code>
[http://api.jquery.com/](http://api.jquery.com/)

**Kind**: global namespace  
<a name="Types"></a>
## Types : <code>object</code>
Supported form elements

**Kind**: global namespace  

* [Types](#Types) : <code>object</code>
  * [.Text](#Types.Text) ⇐ <code>[TypeBase](#TypeBase)</code>
    * [new Text($el, name, value)](#new_Types.Text_new)
    * [.getName()](#TypeBase+getName) ⇒ <code>String</code>
    * [.getValue()](#TypeBase+getValue) ⇒ <code>Object</code>
    * [.getType()](#TypeBase+getType) ⇒ <code>String</code>
    * [.equals(type)](#TypeBase+equals) ⇒ <code>boolean</code>
    * [.updateValue(json)](#TypeBase+updateValue)
    * [.clone()](#TypeBase+clone)
  * [.Checkbox](#Types.Checkbox) ⇐ <code>[TypeBase](#TypeBase)</code>
    * [new Checkbox($el, name, value, checked)](#new_Types.Checkbox_new)
    * [.getName()](#TypeBase+getName) ⇒ <code>String</code>
    * [.getValue()](#TypeBase+getValue) ⇒ <code>Object</code>
    * [.getType()](#TypeBase+getType) ⇒ <code>String</code>
    * [.equals(type)](#TypeBase+equals) ⇒ <code>boolean</code>
    * [.updateValue(json)](#TypeBase+updateValue)
    * [.clone()](#TypeBase+clone)
  * [.Radio](#Types.Radio) ⇐ <code>[TypeBase](#TypeBase)</code>
    * [new Radio($el, name, value, checked)](#new_Types.Radio_new)
    * [.getName()](#TypeBase+getName) ⇒ <code>String</code>
    * [.getValue()](#TypeBase+getValue) ⇒ <code>Object</code>
    * [.getType()](#TypeBase+getType) ⇒ <code>String</code>
    * [.equals(type)](#TypeBase+equals) ⇒ <code>boolean</code>
    * [.updateValue(json)](#TypeBase+updateValue)
    * [.clone()](#TypeBase+clone)
  * [.Select](#Types.Select) ⇐ <code>[TypeBase](#TypeBase)</code>
    * [new Select($el, name, value)](#new_Types.Select_new)
    * [.getName()](#TypeBase+getName) ⇒ <code>String</code>
    * [.getValue()](#TypeBase+getValue) ⇒ <code>Object</code>
    * [.getType()](#TypeBase+getType) ⇒ <code>String</code>
    * [.equals(type)](#TypeBase+equals) ⇒ <code>boolean</code>
    * [.updateValue(json)](#TypeBase+updateValue)
    * [.clone()](#TypeBase+clone)
  * [.SelectMulti](#Types.SelectMulti) ⇐ <code>[TypeBase](#TypeBase)</code>
    * [new SelectMulti($el, name, value)](#new_Types.SelectMulti_new)
    * [.getName()](#TypeBase+getName) ⇒ <code>String</code>
    * [.getValue()](#TypeBase+getValue) ⇒ <code>Object</code>
    * [.getType()](#TypeBase+getType) ⇒ <code>String</code>
    * [.equals(type)](#TypeBase+equals) ⇒ <code>boolean</code>
    * [.updateValue(json)](#TypeBase+updateValue)
    * [.clone()](#TypeBase+clone)
  * [.TextArea](#Types.TextArea) ⇐ <code>[TypeBase](#TypeBase)</code>
    * [new TextArea($el, name, value)](#new_Types.TextArea_new)
    * [.getName()](#TypeBase+getName) ⇒ <code>String</code>
    * [.getValue()](#TypeBase+getValue) ⇒ <code>Object</code>
    * [.getType()](#TypeBase+getType) ⇒ <code>String</code>
    * [.equals(type)](#TypeBase+equals) ⇒ <code>boolean</code>
    * [.updateValue(json)](#TypeBase+updateValue)
    * [.clone()](#TypeBase+clone)
  * [.Button](#Types.Button) ⇐ <code>[TypeBase](#TypeBase)</code>
    * [new Button($el, name, value)](#new_Types.Button_new)
    * [.getName()](#TypeBase+getName) ⇒ <code>String</code>
    * [.getValue()](#TypeBase+getValue) ⇒ <code>Object</code>
    * [.getType()](#TypeBase+getType) ⇒ <code>String</code>
    * [.equals(type)](#TypeBase+equals) ⇒ <code>boolean</code>
    * [.updateValue(json)](#TypeBase+updateValue)
    * [.clone()](#TypeBase+clone)
  * [.DataList](#Types.DataList) ⇐ <code>[TypeBase](#TypeBase)</code>
    * [new DataList($el, name, value)](#new_Types.DataList_new)
    * [.getName()](#TypeBase+getName) ⇒ <code>String</code>
    * [.getValue()](#TypeBase+getValue) ⇒ <code>Object</code>
    * [.getType()](#TypeBase+getType) ⇒ <code>String</code>
    * [.equals(type)](#TypeBase+equals) ⇒ <code>boolean</code>
    * [.updateValue(json)](#TypeBase+updateValue)
    * [.clone()](#TypeBase+clone)
  * [.KeyGen](#Types.KeyGen)
  * [.Fieldset](#Types.Fieldset) ⇐ <code>[TypeBase](#TypeBase)</code>
    * [new Fieldset($el, name)](#new_Types.Fieldset_new)
    * [.getName()](#TypeBase+getName) ⇒ <code>String</code>
    * [.getValue()](#TypeBase+getValue) ⇒ <code>Object</code>
    * [.getType()](#TypeBase+getType) ⇒ <code>String</code>
    * [.equals(type)](#TypeBase+equals) ⇒ <code>boolean</code>
    * [.updateValue(json)](#TypeBase+updateValue)
    * [.clone()](#TypeBase+clone)

<a name="Types.Text"></a>
### Types.Text ⇐ <code>[TypeBase](#TypeBase)</code>
**Kind**: static class of <code>[Types](#Types)</code>  
**Extends:** <code>[TypeBase](#TypeBase)</code>  

* [.Text](#Types.Text) ⇐ <code>[TypeBase](#TypeBase)</code>
  * [new Text($el, name, value)](#new_Types.Text_new)
  * [.getName()](#TypeBase+getName) ⇒ <code>String</code>
  * [.getValue()](#TypeBase+getValue) ⇒ <code>Object</code>
  * [.getType()](#TypeBase+getType) ⇒ <code>String</code>
  * [.equals(type)](#TypeBase+equals) ⇒ <code>boolean</code>
  * [.updateValue(json)](#TypeBase+updateValue)
  * [.clone()](#TypeBase+clone)

<a name="new_Types.Text_new"></a>
#### new Text($el, name, value)
Represents an HTML &lt;input type="text"&gt; element.


| Param | Type | Description |
| --- | --- | --- |
| $el | <code>[jQuery](#jQuery)</code> | A jquery element, should be an HTML input element. |
| name | <code>String</code> | The name attribute of $el. |
| value | <code>Object</code> | The value attribute of $el. |

<a name="TypeBase+getName"></a>
#### text.getName() ⇒ <code>String</code>
Returns the elements name attribute.

**Kind**: instance method of <code>[Text](#Types.Text)</code>  
**Returns**: <code>String</code> - The form elements name attribute value.  
<a name="TypeBase+getValue"></a>
#### text.getValue() ⇒ <code>Object</code>
**Kind**: instance method of <code>[Text](#Types.Text)</code>  
**Returns**: <code>Object</code> - The form elements value attribute value.  
<a name="TypeBase+getType"></a>
#### text.getType() ⇒ <code>String</code>
Gets the type of the object.

**Kind**: instance method of <code>[Text](#Types.Text)</code>  
**Returns**: <code>String</code> - The string name of the Types object.  
<a name="TypeBase+equals"></a>
#### text.equals(type) ⇒ <code>boolean</code>
**Kind**: instance method of <code>[Text](#Types.Text)</code>  
**Overrides:** <code>[equals](#TypeBase+equals)</code>  
**Returns**: <code>boolean</code> - True if they are equivalent, false otherwise.  

| Param | Type | Description |
| --- | --- | --- |
| type | <code>[Types](#Types)</code> | A Types object. |

<a name="TypeBase+updateValue"></a>
#### text.updateValue(json)
Update the inner representation of Types object from a given JSON represetation.  See [TypeBase#toJSON](TypeBase#toJSON).

**Kind**: instance method of <code>[Text](#Types.Text)</code>  

| Param | Type | Description |
| --- | --- | --- |
| json | <code>Object</code> | A JSON representation of a Types object.  See [TypeBase#toJSON](TypeBase#toJSON). |

<a name="TypeBase+clone"></a>
#### text.clone()
Make a copy of the Types object.  It really returns a "default" value of a the current Type.  The "value"will not be copied.

**Kind**: instance method of <code>[Text](#Types.Text)</code>  
<a name="Types.Checkbox"></a>
### Types.Checkbox ⇐ <code>[TypeBase](#TypeBase)</code>
**Kind**: static class of <code>[Types](#Types)</code>  
**Extends:** <code>[TypeBase](#TypeBase)</code>  

* [.Checkbox](#Types.Checkbox) ⇐ <code>[TypeBase](#TypeBase)</code>
  * [new Checkbox($el, name, value, checked)](#new_Types.Checkbox_new)
  * [.getName()](#TypeBase+getName) ⇒ <code>String</code>
  * [.getValue()](#TypeBase+getValue) ⇒ <code>Object</code>
  * [.getType()](#TypeBase+getType) ⇒ <code>String</code>
  * [.equals(type)](#TypeBase+equals) ⇒ <code>boolean</code>
  * [.updateValue(json)](#TypeBase+updateValue)
  * [.clone()](#TypeBase+clone)

<a name="new_Types.Checkbox_new"></a>
#### new Checkbox($el, name, value, checked)
Represents an HTML checkbox representation


| Param | Type | Description |
| --- | --- | --- |
| $el | <code>[jQuery](#jQuery)</code> | A jquery element, should be an HTML &lt;input type="checkbox"&gt; element. |
| name | <code>String</code> | The name attribute of $el. |
| value | <code>Object</code> | The value attribute of $el. |
| checked | <code>boolean</code> | Boolean representing whether $el is checked or not. |

<a name="TypeBase+getName"></a>
#### checkbox.getName() ⇒ <code>String</code>
Returns the elements name attribute.

**Kind**: instance method of <code>[Checkbox](#Types.Checkbox)</code>  
**Returns**: <code>String</code> - The form elements name attribute value.  
<a name="TypeBase+getValue"></a>
#### checkbox.getValue() ⇒ <code>Object</code>
**Kind**: instance method of <code>[Checkbox](#Types.Checkbox)</code>  
**Returns**: <code>Object</code> - The form elements value attribute value.  
<a name="TypeBase+getType"></a>
#### checkbox.getType() ⇒ <code>String</code>
Gets the type of the object.

**Kind**: instance method of <code>[Checkbox](#Types.Checkbox)</code>  
**Returns**: <code>String</code> - The string name of the Types object.  
<a name="TypeBase+equals"></a>
#### checkbox.equals(type) ⇒ <code>boolean</code>
**Kind**: instance method of <code>[Checkbox](#Types.Checkbox)</code>  
**Overrides:** <code>[equals](#TypeBase+equals)</code>  
**Returns**: <code>boolean</code> - True if they are equivalent, false otherwise.  

| Param | Type | Description |
| --- | --- | --- |
| type | <code>[Types](#Types)</code> | A Types object. |

<a name="TypeBase+updateValue"></a>
#### checkbox.updateValue(json)
Update the inner representation of Types object from a given JSON represetation.  See [TypeBase#toJSON](TypeBase#toJSON).

**Kind**: instance method of <code>[Checkbox](#Types.Checkbox)</code>  

| Param | Type | Description |
| --- | --- | --- |
| json | <code>Object</code> | A JSON representation of a Types object.  See [TypeBase#toJSON](TypeBase#toJSON). |

<a name="TypeBase+clone"></a>
#### checkbox.clone()
Make a copy of the Types object.  It really returns a "default" value of a the current Type.  The "value"will not be copied.

**Kind**: instance method of <code>[Checkbox](#Types.Checkbox)</code>  
<a name="Types.Radio"></a>
### Types.Radio ⇐ <code>[TypeBase](#TypeBase)</code>
**Kind**: static class of <code>[Types](#Types)</code>  
**Extends:** <code>[TypeBase](#TypeBase)</code>  

* [.Radio](#Types.Radio) ⇐ <code>[TypeBase](#TypeBase)</code>
  * [new Radio($el, name, value, checked)](#new_Types.Radio_new)
  * [.getName()](#TypeBase+getName) ⇒ <code>String</code>
  * [.getValue()](#TypeBase+getValue) ⇒ <code>Object</code>
  * [.getType()](#TypeBase+getType) ⇒ <code>String</code>
  * [.equals(type)](#TypeBase+equals) ⇒ <code>boolean</code>
  * [.updateValue(json)](#TypeBase+updateValue)
  * [.clone()](#TypeBase+clone)

<a name="new_Types.Radio_new"></a>
#### new Radio($el, name, value, checked)
Represents an HTML radio representation


| Param | Type | Description |
| --- | --- | --- |
| $el | <code>[jQuery](#jQuery)</code> | A jquery element, should be an HTML &lt;input type="radio"&gt; element. |
| name | <code>String</code> | The name attribute of $el. |
| value | <code>Object</code> | The value attribute of $el. |
| checked | <code>boolean</code> | Boolean representing whether $el is checked or not. |

<a name="TypeBase+getName"></a>
#### radio.getName() ⇒ <code>String</code>
Returns the elements name attribute.

**Kind**: instance method of <code>[Radio](#Types.Radio)</code>  
**Returns**: <code>String</code> - The form elements name attribute value.  
<a name="TypeBase+getValue"></a>
#### radio.getValue() ⇒ <code>Object</code>
**Kind**: instance method of <code>[Radio](#Types.Radio)</code>  
**Returns**: <code>Object</code> - The form elements value attribute value.  
<a name="TypeBase+getType"></a>
#### radio.getType() ⇒ <code>String</code>
Gets the type of the object.

**Kind**: instance method of <code>[Radio](#Types.Radio)</code>  
**Returns**: <code>String</code> - The string name of the Types object.  
<a name="TypeBase+equals"></a>
#### radio.equals(type) ⇒ <code>boolean</code>
**Kind**: instance method of <code>[Radio](#Types.Radio)</code>  
**Overrides:** <code>[equals](#TypeBase+equals)</code>  
**Returns**: <code>boolean</code> - True if they are equivalent, false otherwise.  

| Param | Type | Description |
| --- | --- | --- |
| type | <code>[Types](#Types)</code> | A Types object. |

<a name="TypeBase+updateValue"></a>
#### radio.updateValue(json)
Update the inner representation of Types object from a given JSON represetation.  See [TypeBase#toJSON](TypeBase#toJSON).

**Kind**: instance method of <code>[Radio](#Types.Radio)</code>  

| Param | Type | Description |
| --- | --- | --- |
| json | <code>Object</code> | A JSON representation of a Types object.  See [TypeBase#toJSON](TypeBase#toJSON). |

<a name="TypeBase+clone"></a>
#### radio.clone()
Make a copy of the Types object.  It really returns a "default" value of a the current Type.  The "value"will not be copied.

**Kind**: instance method of <code>[Radio](#Types.Radio)</code>  
<a name="Types.Select"></a>
### Types.Select ⇐ <code>[TypeBase](#TypeBase)</code>
**Kind**: static class of <code>[Types](#Types)</code>  
**Extends:** <code>[TypeBase](#TypeBase)</code>  

* [.Select](#Types.Select) ⇐ <code>[TypeBase](#TypeBase)</code>
  * [new Select($el, name, value)](#new_Types.Select_new)
  * [.getName()](#TypeBase+getName) ⇒ <code>String</code>
  * [.getValue()](#TypeBase+getValue) ⇒ <code>Object</code>
  * [.getType()](#TypeBase+getType) ⇒ <code>String</code>
  * [.equals(type)](#TypeBase+equals) ⇒ <code>boolean</code>
  * [.updateValue(json)](#TypeBase+updateValue)
  * [.clone()](#TypeBase+clone)

<a name="new_Types.Select_new"></a>
#### new Select($el, name, value)
Represents an HTML &lt;select&gt; element.


| Param | Type | Description |
| --- | --- | --- |
| $el | <code>[jQuery](#jQuery)</code> | A jquery element, should be an HTML select element. |
| name | <code>String</code> | The name attribute of $el. |
| value | <code>Object</code> | The value attribute of $el. |

<a name="TypeBase+getName"></a>
#### select.getName() ⇒ <code>String</code>
Returns the elements name attribute.

**Kind**: instance method of <code>[Select](#Types.Select)</code>  
**Returns**: <code>String</code> - The form elements name attribute value.  
<a name="TypeBase+getValue"></a>
#### select.getValue() ⇒ <code>Object</code>
**Kind**: instance method of <code>[Select](#Types.Select)</code>  
**Returns**: <code>Object</code> - The form elements value attribute value.  
<a name="TypeBase+getType"></a>
#### select.getType() ⇒ <code>String</code>
Gets the type of the object.

**Kind**: instance method of <code>[Select](#Types.Select)</code>  
**Returns**: <code>String</code> - The string name of the Types object.  
<a name="TypeBase+equals"></a>
#### select.equals(type) ⇒ <code>boolean</code>
**Kind**: instance method of <code>[Select](#Types.Select)</code>  
**Overrides:** <code>[equals](#TypeBase+equals)</code>  
**Returns**: <code>boolean</code> - True if they are equivalent, false otherwise.  

| Param | Type | Description |
| --- | --- | --- |
| type | <code>[Types](#Types)</code> | A Types object. |

<a name="TypeBase+updateValue"></a>
#### select.updateValue(json)
Update the inner representation of Types object from a given JSON represetation.  See [TypeBase#toJSON](TypeBase#toJSON).

**Kind**: instance method of <code>[Select](#Types.Select)</code>  

| Param | Type | Description |
| --- | --- | --- |
| json | <code>Object</code> | A JSON representation of a Types object.  See [TypeBase#toJSON](TypeBase#toJSON). |

<a name="TypeBase+clone"></a>
#### select.clone()
Make a copy of the Types object.  It really returns a "default" value of a the current Type.  The "value"will not be copied.

**Kind**: instance method of <code>[Select](#Types.Select)</code>  
<a name="Types.SelectMulti"></a>
### Types.SelectMulti ⇐ <code>[TypeBase](#TypeBase)</code>
**Kind**: static class of <code>[Types](#Types)</code>  
**Extends:** <code>[TypeBase](#TypeBase)</code>  

* [.SelectMulti](#Types.SelectMulti) ⇐ <code>[TypeBase](#TypeBase)</code>
  * [new SelectMulti($el, name, value)](#new_Types.SelectMulti_new)
  * [.getName()](#TypeBase+getName) ⇒ <code>String</code>
  * [.getValue()](#TypeBase+getValue) ⇒ <code>Object</code>
  * [.getType()](#TypeBase+getType) ⇒ <code>String</code>
  * [.equals(type)](#TypeBase+equals) ⇒ <code>boolean</code>
  * [.updateValue(json)](#TypeBase+updateValue)
  * [.clone()](#TypeBase+clone)

<a name="new_Types.SelectMulti_new"></a>
#### new SelectMulti($el, name, value)
Represents an HTML &lt;select multiple&gt; element.


| Param | Type | Description |
| --- | --- | --- |
| $el | <code>[jQuery](#jQuery)</code> | A jquery element, should be an HTML &lt;select multiple&gt; element. |
| name | <code>String</code> | The name attribute of $el. |
| value | <code>Object</code> | The value attribute of $el. |

<a name="TypeBase+getName"></a>
#### selectMulti.getName() ⇒ <code>String</code>
Returns the elements name attribute.

**Kind**: instance method of <code>[SelectMulti](#Types.SelectMulti)</code>  
**Returns**: <code>String</code> - The form elements name attribute value.  
<a name="TypeBase+getValue"></a>
#### selectMulti.getValue() ⇒ <code>Object</code>
**Kind**: instance method of <code>[SelectMulti](#Types.SelectMulti)</code>  
**Returns**: <code>Object</code> - The form elements value attribute value.  
<a name="TypeBase+getType"></a>
#### selectMulti.getType() ⇒ <code>String</code>
Gets the type of the object.

**Kind**: instance method of <code>[SelectMulti](#Types.SelectMulti)</code>  
**Returns**: <code>String</code> - The string name of the Types object.  
<a name="TypeBase+equals"></a>
#### selectMulti.equals(type) ⇒ <code>boolean</code>
**Kind**: instance method of <code>[SelectMulti](#Types.SelectMulti)</code>  
**Overrides:** <code>[equals](#TypeBase+equals)</code>  
**Returns**: <code>boolean</code> - True if they are equivalent, false otherwise.  

| Param | Type | Description |
| --- | --- | --- |
| type | <code>[Types](#Types)</code> | A Types object. |

<a name="TypeBase+updateValue"></a>
#### selectMulti.updateValue(json)
Update the inner representation of Types object from a given JSON represetation.  See [TypeBase#toJSON](TypeBase#toJSON).

**Kind**: instance method of <code>[SelectMulti](#Types.SelectMulti)</code>  

| Param | Type | Description |
| --- | --- | --- |
| json | <code>Object</code> | A JSON representation of a Types object.  See [TypeBase#toJSON](TypeBase#toJSON). |

<a name="TypeBase+clone"></a>
#### selectMulti.clone()
Make a copy of the Types object.  It really returns a "default" value of a the current Type.  The "value"will not be copied.

**Kind**: instance method of <code>[SelectMulti](#Types.SelectMulti)</code>  
<a name="Types.TextArea"></a>
### Types.TextArea ⇐ <code>[TypeBase](#TypeBase)</code>
**Kind**: static class of <code>[Types](#Types)</code>  
**Extends:** <code>[TypeBase](#TypeBase)</code>  

* [.TextArea](#Types.TextArea) ⇐ <code>[TypeBase](#TypeBase)</code>
  * [new TextArea($el, name, value)](#new_Types.TextArea_new)
  * [.getName()](#TypeBase+getName) ⇒ <code>String</code>
  * [.getValue()](#TypeBase+getValue) ⇒ <code>Object</code>
  * [.getType()](#TypeBase+getType) ⇒ <code>String</code>
  * [.equals(type)](#TypeBase+equals) ⇒ <code>boolean</code>
  * [.updateValue(json)](#TypeBase+updateValue)
  * [.clone()](#TypeBase+clone)

<a name="new_Types.TextArea_new"></a>
#### new TextArea($el, name, value)
Represents an HTML &lt;textarea&gt; element.


| Param | Type | Description |
| --- | --- | --- |
| $el | <code>[jQuery](#jQuery)</code> | A jquery element, should be an HTML &lt;textarea&gt; element. |
| name | <code>String</code> | The name attribute of $el. |
| value | <code>Object</code> | The value attribute of $el. |

<a name="TypeBase+getName"></a>
#### textArea.getName() ⇒ <code>String</code>
Returns the elements name attribute.

**Kind**: instance method of <code>[TextArea](#Types.TextArea)</code>  
**Returns**: <code>String</code> - The form elements name attribute value.  
<a name="TypeBase+getValue"></a>
#### textArea.getValue() ⇒ <code>Object</code>
**Kind**: instance method of <code>[TextArea](#Types.TextArea)</code>  
**Returns**: <code>Object</code> - The form elements value attribute value.  
<a name="TypeBase+getType"></a>
#### textArea.getType() ⇒ <code>String</code>
Gets the type of the object.

**Kind**: instance method of <code>[TextArea](#Types.TextArea)</code>  
**Returns**: <code>String</code> - The string name of the Types object.  
<a name="TypeBase+equals"></a>
#### textArea.equals(type) ⇒ <code>boolean</code>
**Kind**: instance method of <code>[TextArea](#Types.TextArea)</code>  
**Overrides:** <code>[equals](#TypeBase+equals)</code>  
**Returns**: <code>boolean</code> - True if they are equivalent, false otherwise.  

| Param | Type | Description |
| --- | --- | --- |
| type | <code>[Types](#Types)</code> | A Types object. |

<a name="TypeBase+updateValue"></a>
#### textArea.updateValue(json)
Update the inner representation of Types object from a given JSON represetation.  See [TypeBase#toJSON](TypeBase#toJSON).

**Kind**: instance method of <code>[TextArea](#Types.TextArea)</code>  

| Param | Type | Description |
| --- | --- | --- |
| json | <code>Object</code> | A JSON representation of a Types object.  See [TypeBase#toJSON](TypeBase#toJSON). |

<a name="TypeBase+clone"></a>
#### textArea.clone()
Make a copy of the Types object.  It really returns a "default" value of a the current Type.  The "value"will not be copied.

**Kind**: instance method of <code>[TextArea](#Types.TextArea)</code>  
<a name="Types.Button"></a>
### Types.Button ⇐ <code>[TypeBase](#TypeBase)</code>
**Kind**: static class of <code>[Types](#Types)</code>  
**Extends:** <code>[TypeBase](#TypeBase)</code>  

* [.Button](#Types.Button) ⇐ <code>[TypeBase](#TypeBase)</code>
  * [new Button($el, name, value)](#new_Types.Button_new)
  * [.getName()](#TypeBase+getName) ⇒ <code>String</code>
  * [.getValue()](#TypeBase+getValue) ⇒ <code>Object</code>
  * [.getType()](#TypeBase+getType) ⇒ <code>String</code>
  * [.equals(type)](#TypeBase+equals) ⇒ <code>boolean</code>
  * [.updateValue(json)](#TypeBase+updateValue)
  * [.clone()](#TypeBase+clone)

<a name="new_Types.Button_new"></a>
#### new Button($el, name, value)
Represents an HTML &lt;button&gt; element or an &lt;input type="button"&gt; element.


| Param | Type | Description |
| --- | --- | --- |
| $el | <code>[jQuery](#jQuery)</code> | A jquery element, should be an HTML button or input of type &lt;button&gt; element. |
| name | <code>String</code> | The name attribute of $el. |
| value | <code>Object</code> | The value attribute of $el. |

<a name="TypeBase+getName"></a>
#### button.getName() ⇒ <code>String</code>
Returns the elements name attribute.

**Kind**: instance method of <code>[Button](#Types.Button)</code>  
**Returns**: <code>String</code> - The form elements name attribute value.  
<a name="TypeBase+getValue"></a>
#### button.getValue() ⇒ <code>Object</code>
**Kind**: instance method of <code>[Button](#Types.Button)</code>  
**Returns**: <code>Object</code> - The form elements value attribute value.  
<a name="TypeBase+getType"></a>
#### button.getType() ⇒ <code>String</code>
Gets the type of the object.

**Kind**: instance method of <code>[Button](#Types.Button)</code>  
**Returns**: <code>String</code> - The string name of the Types object.  
<a name="TypeBase+equals"></a>
#### button.equals(type) ⇒ <code>boolean</code>
**Kind**: instance method of <code>[Button](#Types.Button)</code>  
**Overrides:** <code>[equals](#TypeBase+equals)</code>  
**Returns**: <code>boolean</code> - True if they are equivalent, false otherwise.  

| Param | Type | Description |
| --- | --- | --- |
| type | <code>[Types](#Types)</code> | A Types object. |

<a name="TypeBase+updateValue"></a>
#### button.updateValue(json)
Update the inner representation of Types object from a given JSON represetation.  See [TypeBase#toJSON](TypeBase#toJSON).

**Kind**: instance method of <code>[Button](#Types.Button)</code>  

| Param | Type | Description |
| --- | --- | --- |
| json | <code>Object</code> | A JSON representation of a Types object.  See [TypeBase#toJSON](TypeBase#toJSON). |

<a name="TypeBase+clone"></a>
#### button.clone()
Make a copy of the Types object.  It really returns a "default" value of a the current Type.  The "value"will not be copied.

**Kind**: instance method of <code>[Button](#Types.Button)</code>  
<a name="Types.DataList"></a>
### Types.DataList ⇐ <code>[TypeBase](#TypeBase)</code>
**Kind**: static class of <code>[Types](#Types)</code>  
**Extends:** <code>[TypeBase](#TypeBase)</code>  

* [.DataList](#Types.DataList) ⇐ <code>[TypeBase](#TypeBase)</code>
  * [new DataList($el, name, value)](#new_Types.DataList_new)
  * [.getName()](#TypeBase+getName) ⇒ <code>String</code>
  * [.getValue()](#TypeBase+getValue) ⇒ <code>Object</code>
  * [.getType()](#TypeBase+getType) ⇒ <code>String</code>
  * [.equals(type)](#TypeBase+equals) ⇒ <code>boolean</code>
  * [.updateValue(json)](#TypeBase+updateValue)
  * [.clone()](#TypeBase+clone)

<a name="new_Types.DataList_new"></a>
#### new DataList($el, name, value)

| Param | Type | Description |
| --- | --- | --- |
| $el | <code>[jQuery](#jQuery)</code> | A jquery element, should be an HTML &lt;datalist&gt; element. |
| name | <code>String</code> | The name attribute of $el. |
| value | <code>Object</code> | The value attribute of $el. |

<a name="TypeBase+getName"></a>
#### dataList.getName() ⇒ <code>String</code>
Returns the elements name attribute.

**Kind**: instance method of <code>[DataList](#Types.DataList)</code>  
**Returns**: <code>String</code> - The form elements name attribute value.  
<a name="TypeBase+getValue"></a>
#### dataList.getValue() ⇒ <code>Object</code>
**Kind**: instance method of <code>[DataList](#Types.DataList)</code>  
**Returns**: <code>Object</code> - The form elements value attribute value.  
<a name="TypeBase+getType"></a>
#### dataList.getType() ⇒ <code>String</code>
Gets the type of the object.

**Kind**: instance method of <code>[DataList](#Types.DataList)</code>  
**Returns**: <code>String</code> - The string name of the Types object.  
<a name="TypeBase+equals"></a>
#### dataList.equals(type) ⇒ <code>boolean</code>
**Kind**: instance method of <code>[DataList](#Types.DataList)</code>  
**Overrides:** <code>[equals](#TypeBase+equals)</code>  
**Returns**: <code>boolean</code> - True if they are equivalent, false otherwise.  

| Param | Type | Description |
| --- | --- | --- |
| type | <code>[Types](#Types)</code> | A Types object. |

<a name="TypeBase+updateValue"></a>
#### dataList.updateValue(json)
Update the inner representation of Types object from a given JSON represetation.  See [TypeBase#toJSON](TypeBase#toJSON).

**Kind**: instance method of <code>[DataList](#Types.DataList)</code>  

| Param | Type | Description |
| --- | --- | --- |
| json | <code>Object</code> | A JSON representation of a Types object.  See [TypeBase#toJSON](TypeBase#toJSON). |

<a name="TypeBase+clone"></a>
#### dataList.clone()
Make a copy of the Types object.  It really returns a "default" value of a the current Type.  The "value"will not be copied.

**Kind**: instance method of <code>[DataList](#Types.DataList)</code>  
<a name="Types.KeyGen"></a>
### Types.KeyGen
**Kind**: static class of <code>[Types](#Types)</code>  
<a name="Types.Fieldset"></a>
### Types.Fieldset ⇐ <code>[TypeBase](#TypeBase)</code>
**Kind**: static class of <code>[Types](#Types)</code>  
**Extends:** <code>[TypeBase](#TypeBase)</code>  

* [.Fieldset](#Types.Fieldset) ⇐ <code>[TypeBase](#TypeBase)</code>
  * [new Fieldset($el, name)](#new_Types.Fieldset_new)
  * [.getName()](#TypeBase+getName) ⇒ <code>String</code>
  * [.getValue()](#TypeBase+getValue) ⇒ <code>Object</code>
  * [.getType()](#TypeBase+getType) ⇒ <code>String</code>
  * [.equals(type)](#TypeBase+equals) ⇒ <code>boolean</code>
  * [.updateValue(json)](#TypeBase+updateValue)
  * [.clone()](#TypeBase+clone)

<a name="new_Types.Fieldset_new"></a>
#### new Fieldset($el, name)
A special type that allows for grouping.  Used for organizational purposes.


| Param | Type | Description |
| --- | --- | --- |
| $el | <code>[jQuery](#jQuery)</code> | A jquery element, shoudl be an HTML &lt;fieldset&gt; element. |
| name | <code>string</code> | The name attribute of $el. |

<a name="TypeBase+getName"></a>
#### fieldset.getName() ⇒ <code>String</code>
Returns the elements name attribute.

**Kind**: instance method of <code>[Fieldset](#Types.Fieldset)</code>  
**Returns**: <code>String</code> - The form elements name attribute value.  
<a name="TypeBase+getValue"></a>
#### fieldset.getValue() ⇒ <code>Object</code>
**Kind**: instance method of <code>[Fieldset](#Types.Fieldset)</code>  
**Returns**: <code>Object</code> - The form elements value attribute value.  
<a name="TypeBase+getType"></a>
#### fieldset.getType() ⇒ <code>String</code>
Gets the type of the object.

**Kind**: instance method of <code>[Fieldset](#Types.Fieldset)</code>  
**Returns**: <code>String</code> - The string name of the Types object.  
<a name="TypeBase+equals"></a>
#### fieldset.equals(type) ⇒ <code>boolean</code>
**Kind**: instance method of <code>[Fieldset](#Types.Fieldset)</code>  
**Overrides:** <code>[equals](#TypeBase+equals)</code>  
**Returns**: <code>boolean</code> - True if they are equivalent, false otherwise.  

| Param | Type | Description |
| --- | --- | --- |
| type | <code>[Types](#Types)</code> | A Types object. |

<a name="TypeBase+updateValue"></a>
#### fieldset.updateValue(json)
Update the inner representation of Types object from a given JSON represetation.  See [TypeBase#toJSON](TypeBase#toJSON).

**Kind**: instance method of <code>[Fieldset](#Types.Fieldset)</code>  

| Param | Type | Description |
| --- | --- | --- |
| json | <code>Object</code> | A JSON representation of a Types object.  See [TypeBase#toJSON](TypeBase#toJSON). |

<a name="TypeBase+clone"></a>
#### fieldset.clone()
Make a copy of the Types object.  It really returns a "default" value of a the current Type.  The "value"will not be copied.

**Kind**: instance method of <code>[Fieldset](#Types.Fieldset)</code>  
<a name="$.fn.forms"></a>
## $.fn.forms(options) ⇒ <code>[jQuery](#jQuery)</code>
The forms namespace extends jQuery.fn to be a jQuery plugin.

**Kind**: global function  
**Returns**: <code>[jQuery](#jQuery)</code> - The matched jQuery element.  

| Param | Type | Description |
| --- | --- | --- |
| options | <code>Object</code> &#124; <code>function</code> | If Object, then the options are applied to [Forms](#Forms).  If function, then the function is called being passed the [Forms](#Forms) object as a first parameter to the function. |

