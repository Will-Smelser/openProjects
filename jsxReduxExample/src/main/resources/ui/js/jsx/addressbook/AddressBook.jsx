import React from 'react';
import ReactDOM from 'react-dom';
import AddressList from './AddressList.jsx';

let i = 2;

class AddressBook extends React.Component {
    constructor(props) {
        super();
        this.state = {addresses : props.addresses}
    }

    addAddress(){
        //copy addresses and add new address to top of stack
        var temp = this.state.addresses.slice();
        temp.unshift({_show:true, id:i, name:"Example Address "+(i++), line1:"Example Line 1", line2:"", city:"", state:"", zip:""});

        this.setState({addresses:temp});
    }

    removeAddress(id){
        console.log(this, this.state);
        for(var x in this.state.addresses){
            if(this.state.addresses[x].id === id){
                delete this.state.addresses[x];
            }
        }
        this.forceUpdate();
    }

    //note we have to bind the removeAddress function to "this".
    render(){
        return  <div>
                <div><a onClick={()=>this.addAddress()}>Add</a></div>
                <AddressList addresses={this.state.addresses} removeAddress={this.removeAddress.bind(this)} />
            </div>;
    }
}

//initial state
window.init = {
    addresses: [{_show:true, id:1, name:"Example Address", line1:"Example Line 1", line2:"", city:"", state:"", zip:""}]
};


ReactDOM.render(<AddressBook addresses={init.addresses} />, document.getElementById('address'));