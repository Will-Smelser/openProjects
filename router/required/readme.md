## Members
<dl>
<dt><a href="#paths">paths</a></dt>
<dd><p>Stores all the defined routes</p>
</dd>
</dl>
## Functions
<dl>
<dt><a href="#HashRouter">HashRouter()</a></dt>
<dd><p>A utility to bind document.location.hash routes to functions.  This is meant to be very similar to the Java @Path annotation.
Example:</p>
<pre>
var router = new HashRouter();
router.addPath("/blog/{id:[\\d]+}",function(data){console.log("blog id requested: "+data.id)});
router.addPath("/user/{name}/{action:[a-z]+}",function(data){console.log(data);});

router.evaluate("#/blog/1234"); //console prints "blog id requested: 1234"
router.evaluate("#/user/JohnDoe@gmail.com/settings"); //console prints "Object {name : 'JohnDoe@gmail', action : 'settings'}"
router.evaluate("#/blog/nothing"); //nothing will happen.
</pre></dd>
<dt><a href="#Expression">Expression()</a></dt>
<dd><p>Paths are broken up into either String values or an Expression.  Expressions hold
a variable name and potentially a regex defining what the name is allowed to match against.</p>
</dd>
<dt><a href="#Path">Path(path, [fn])</a></dt>
<dd><p>Create a route from a String.</p>
</dd>
<dt><a href="#noRoute">noRoute(path)</a></dt>
<dd><p>Override this if you would like your own function triggered when a Path cannot be matched.</p>
</dd>
<dt><a href="#addPath">addPath(path, [fn])</a></dt>
<dd><p>Add a URL path.</p>
</dd>
<dt><a href="#evaluate">evaluate(path)</a> ⇒</dt>
<dd><p>Evaluate a path looking for a matching Path in the defined paths.  If a match is found, then an Object with
key=&gt;value, where the key is an Expression from the stored Path and value is the correspond path portion from input.</p>
</dd>
<dt><a href="#start">start([pollTime])</a></dt>
<dd><p>Start listening for onhashchange event.  Calling <a href="#evaluate">this.evaluate(window.location.hash)</a> on any change.  This
will poll the hash ever 100ms if the browser does not support onhashchange event.</p>
</dd>
</dl>
<a name="paths"></a>
## paths
Stores all the defined routes

**Kind**: global variable  
<a name="HashRouter"></a>
## HashRouter()
A utility to bind document.location.hash routes to functions.  This is meant to be very similar to the Java @Path annotation.Example:<pre>var router = new HashRouter();router.addPath("/blog/{id:[\\d]+}",function(data){console.log("blog id requested: "+data.id)});router.addPath("/user/{name}/{action:[a-z]+}",function(data){console.log(data);});router.evaluate("#/blog/1234"); //console prints "blog id requested: 1234"router.evaluate("#/user/JohnDoe@gmail.com/settings"); //console prints "Object {name : 'JohnDoe@gmail', action : 'settings'}"router.evaluate("#/blog/nothing"); //nothing will happen.</pre>

**Kind**: global function  
<a name="Expression"></a>
## Expression()
Paths are broken up into either String values or an Expression.  Expressions holda variable name and potentially a regex defining what the name is allowed to match against.

**Kind**: global function  
<a name="Path"></a>
## Path(path, [fn])
Create a route from a String.

**Kind**: global function  

| Param | Type | Description |
| --- | --- | --- |
| path | <code>String</code> | Specified the path portion of a URL such as "/some/path/to/something" |
| [fn] | <code>function</code> | A function to call when a Path is matched.  Either triggered from calling [this.evaluate(path)](#evaluate) or when "onhashchange" event is active. |

<a name="noRoute"></a>
## noRoute(path)
Override this if you would like your own function triggered when a Path cannot be matched.

**Kind**: global function  

| Param | Type | Description |
| --- | --- | --- |
| path | <code>String</code> | The path that failed to be matched. |

<a name="addPath"></a>
## addPath(path, [fn])
Add a URL path.

**Kind**: global function  

| Param | Type | Description |
| --- | --- | --- |
| path | <code>String</code> | Specified the path portion of a URL such as "/some/path/to/something" |
| [fn] | <code>function</code> | A function to call when a Path is matched.  Either triggered from calling [this.evaluate(path)](#evaluate) or when "onhashchange" event is active. |

<a name="evaluate"></a>
## evaluate(path) ⇒
Evaluate a path looking for a matching Path in the defined paths.  If a match is found, then an Object withkey=>value, where the key is an Expression from the stored Path and value is the correspond path portion from input.

**Kind**: global function  
**Returns**: Will return an object with key=>value pairs if no function is defined for a matched hash.  

| Param | Type | Description |
| --- | --- | --- |
| path | <code>String</code> | Specified the path portion of a URL such as "/some/path/to/something" |

<a name="start"></a>
## start([pollTime])
Start listening for onhashchange event.  Calling [this.evaluate(window.location.hash)](#evaluate) on any change.  Thiswill poll the hash ever 100ms if the browser does not support onhashchange event.

**Kind**: global function  

| Param | Type | Description |
| --- | --- | --- |
| [pollTime] | <code>Integer</code> | If the browser does not support "window.onhashchange" event, then the document.hash will be polled every [pollTime](#start) ms with a setInterval call. Defaults to 100 ms. |

