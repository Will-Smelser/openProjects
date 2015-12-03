#HashRouter()
    This is a very simple utility for making java @Path annotation type of routing with javascript.  You can bind
    functions to the routes.

##Example
```javascript
//create the router
var router = new HashRouter();

//add some paths
router.addPath("/blog/{id:[\\d]+}",function(data){console.log("blog id requested: "+data.id)});
router.addPath("/user/{name}/{action:[a-z]+}",function(data){console.log(data);});

//add a path with no callback function
router.addPath("/user/{name}/documents/{docId:[\\d]+}");

//evaluate some routes
router.evaluate("#/blog/1234"); //console prints "blog id requested: 1234"
router.evaluate("#/user/JohnDoe@gmail.com/settings"); //console prints "Object {name : 'JohnDoe@gmail', action : 'settings'}"
router.evaluate("#/blog/nothing"); //nothing will happen.

//start the "onhashchange" listener
router.start();

//trigger the routes callback
document.hash="#/blog/1234"; //console prints "blog id requested: 1234"

//evalute with a return object
var obj = router.evaluate("#/user/JohnDoe/documents/1234");
console.log(obj); //console prints "Object {name : 'JohnDoe', docId : 1234}"

```