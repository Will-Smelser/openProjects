import React from 'react';
import ReactDOM from 'react-dom';
import { createStore } from 'redux'
import Address from './Address.jsx';

function AddressBook(props){
    var names = [];
    var addresses = [];

    $.each(props._store_.getState(),function(index, addr){
        names.push(<li key="address-name-{index}">Address {index}</li>);
        addresses.push(<div role="tabpanel" className="tab-pane active" id="address-{index}" key="address-{index}"><Address index={index++} _store_={props._store_} /></div>);
    });

    return <div>
        <div>
            <ul className="nav nav-tabs" role="tablist">
                {names}
            </ul>
        </div>
        <div className="tab-content">
            {addresses}
        </div>
    </div>;
}

//initial state
let init = [{line1:"Example Line 1", line2:"", city:"", state:"", zip:""}];

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