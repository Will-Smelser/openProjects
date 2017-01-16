import React from 'react';
import ReactDOM from 'react-dom';
import { createStore } from 'redux'
import AddressList from './AddressList.jsx';

function AddressBook(props){
    return <div><AddressList _store_={props._store_} /></div>;
}

//initial state
let init = {
    addresses: [{_show:false, id:1, name:"Example Address", line1:"Example Line 1", line2:"", city:"", state:"", zip:""}]
};

//handle address updates
function address(state=init, action){
    switch(action.type){
        case 'UPDATE':
            state[action.index] = action.state;
            break;
    }
    return state;
}

//create our store
window.addressBookStore = createStore(address);

ReactDOM.render(<AddressBook _store_={addressBookStore} />, document.getElementById('address'));