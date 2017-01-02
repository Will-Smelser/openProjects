import Hello from './jsx/hello.jsx'; //es6 import!

const PI = 3.141593;

document.getElementById('content').innerHTML = require("./content.js") + "<br/>The ES6 constant was hopefully rewritten to es5.  PI="+PI;

require("./jsx/world.jsx"); //normal webpack import