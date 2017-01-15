import Hello from './jsx/hello.jsx'; //es6 import!
import World from './jsx/world.jsx';


//verify an ES6 converted to ES5 with Babel
const PI = 3.141593;

document.getElementById('content').innerHTML = require("./content.js") + "<br/>The ES6 constant was hopefully rewritten to es5.  PI="+PI;

import React from 'react';
import ReactDOM from 'react-dom';
import AddressBook from './jsx/addressbook/AddressBook.jsx';




