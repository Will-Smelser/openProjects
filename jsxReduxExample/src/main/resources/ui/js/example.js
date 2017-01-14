import Hello from './jsx/hello.jsx'; //es6 import!
import World from './jsx/world.jsx';
import { createStore } from 'redux'

//verify an ES6 converted to ES5 with Babel
const PI = 3.141593;

document.getElementById('content').innerHTML = require("./content.js") + "<br/>The ES6 constant was hopefully rewritten to es5.  PI="+PI;

import React from 'react';
import ReactDOM from 'react-dom';
import Address from './jsx/forms/address.jsx';


let addr = [{line1:"Example Line 1", line2:"", city:"", state:"", zip:""}];
function address(state=addr, action){
    switch(action.type){
        case 'UPDATE':
            state[action.index] = action.state;
            break;
    }
    return state;
}

window.addressBookStore = createStore(address);

ReactDOM.render(<Address _store_={addressBookStore} index={0} />, document.getElementById('address'));