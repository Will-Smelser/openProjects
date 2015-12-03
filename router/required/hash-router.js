/**
 * A utility to bind document.location.hash routes to functions.  This is meant to be very similar to the Java @Path annotation.
 * Example:
 * <pre>
 * var router = new HashRouter();
 * router.addPath("/blog/{id:[\\d]+}",function(data){console.log("blog id requested: "+data.id)});
 * router.addPath("/user/{name}/{action:[a-z]+}",function(data){console.log(data);});
 *
 * router.eval("#/blog/1234"); //console prints "blog id requested: 1234"
 * router.eval("#/user/JohnDoe@gmail.com/settings"); //console prints "Object {name : 'JohnDoe@gmail', action : 'settings'}"
 * router.eval("#/blog/nothing"); //nothing will happen.
 * </pre>
 */
var HashRouter = function () {
    //regular expression used for Expression
    var re = /\{([\w\d]+)(\:.+)?\}/;

    var self = this;

    /**
     * Stores all the defined routes
     */
    this.paths = [];

    /**
     * Override this if you would like your own function triggered when a Path cannot be matched.
     * @param path {String} The path that failed to be matched.
     */
    this.noRoute = function (path) {
        if (console) console.log("No match for path: " + path)
    };

    /**
     * Paths are broken up into either String values or an Expression.  Expressions hold
     * a variable name and potentially a regex defining what the name is allowed to match against.
     */
    this.Expression = function (str) {
        var found = str.match(re);

        this.evaluate = function (val) {
            if (found.length > 2 && found[2]) {
                var regexp = new RegExp(found[2].substr(1));
                if (!val || !val.match(regexp)) {
                    return;
                }
            }
            return found[1];
        }
    };

    /**
     * Create a route from a String.
     * @param path {String} Specified the path portion of a URL such as "/some/path/to/something"
     * @param fn {Function=} A function to call when a Path is matched.  Either triggered from
     * calling {@link #evaluate this.evaluate(path)} or when "onhashchange" event is active.
     */
    this.Path = function (path, fn) {
        this.data = [];
        this.fn = fn;

        var parts = path.split('/');

        for (var x in parts) {
            if (parts[x].match(re)) {
                this.data.push(new self.Expression(parts[x]));
            } else {
                this.data.push(parts[x]);
            }
        }
    }

    /**
     * Add a URL path.
     * @param path {String} Specified the path portion of a URL such as "/some/path/to/something"
     * @param fn {Function=} A function to call when a Path is matched.  Either triggered from
     * calling {@link #evaluate this.evaluate(path)} or when "onhashchange" event is active.
     */
    this.addPath = function (path, fn) {
        self.paths.push(new self.Path(path, fn));
    }

    /**
     * Evaluate a path looking for a matching Path in the defined paths.  If a match is found, then an Object with
     * key=>value, where the key is an Expression from the stored Path and value is the correspond path portion from input.
     * @param path {String} Specified the path portion of a URL such as "/some/path/to/something"
     * @return Will return an object with key=>value pairs if no function is defined for a matched hash.
     */
    this.evaluate = function (path) {
        //create a Path object from the given hash
        var _path = new self.Path((path[0] === '#' ? path.substr(1) : path));

        //iterate all the paths
        for (var x in self.paths) {
            var result = {};
            var match = true;

            if(self.paths[x].data.length != _path.data.length){
                continue;
            }

            for (var y in self.paths[x].data) {

                //if this is expression, then extract the name and set the value
                if (self.paths[x].data[y] instanceof self.Expression) {
                    var evalResult = self.paths[x].data[y].evaluate(_path.data[y]);

                    if (evalResult) {
                        result[evalResult] = _path.data[y];
                    } else {
                        match = false;
                        break;
                    }

                } else if (self.paths[x].data[y] !== _path.data[y]) {
                    //the given path does not match the current path
                    match = false;
                    break;
                }
            }

            if (match) {
                if (self.paths[x].fn) {
                    return self.paths[x].fn(result);
                }
                return result;
            }
        }

        if (self.noRoute) self.noRoute(path);
    };

    /**
     * Start listening for onhashchange event.  Calling {@link #evaluate this.evaluate(window.location.hash)} on any change.  This
     * will poll the hash ever 100ms if the browser does not support onhashchange event.
     * @param pollTime {Integer=} If the browser does not support "window.onhashchange" event, then the
     * document.hash will be polled every {@link #start pollTime} ms with a setInterval call. Defaults to 100 ms.
     */
    this.start = function (pollTime) {
        if(typeof pollTime === "undefined") pollTime = 100;
        var location = window.location, oldHash = "";

        //basically copied from https://developer.mozilla.org/en-US/docs/Web/Events/hashchange
        // exit if the browser implements that event
        if ("onhashchange" in window.document.body) {
            window.onhashchange = function () {
                self.evaluate(location.hash);
            }
            self.evaluate(location.hash);
            return;
        }

        // check the location hash on a 100ms interval
        setInterval(function () {
            var newHash = location.hash;

            // if the hash has changed and a handler has been bound...
            if (newHash !== oldHash) {
                oldHash = newHash;
                self.evaluate(newHash);
            }
        }, pollTime);

    };
};