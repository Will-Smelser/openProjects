import React from 'react';
import ReactDOM from 'react-dom';
import Address from './Address.jsx';


class AddressList extends React.Component {
  constructor(props) {
    super();
    var addrCount = props._store_.getState().addresses.length;
    this.state = {addresses: props._store_.getState().addresses};
  }

  toggleShowAddress(id){
    var addrs = this.state.addresses.slice();
    for(var x in addrs){
        if(addrs[x].id === id){
            addrs[x]._show = !addrs[x]._show;
        }
    }

    this.setState(addrs);
  }

  render(){
    console.log("render()");

    //have to make these vars available to the map scope?
    let _store_ = this.props._store_;

    let addresses = [];

    let temp = this.props._store_.getState().addresses;
    for(var x in temp){
        addresses.push(
            <div className="panel panel-default" key={temp[x].id}>
                <div className="panel-body">
                    <div onClick={()=>this.toggleShowAddress(temp[x].id)}><b>{temp[x].name}</b></div>
                    <Address index={x} _store_={_store_} />
                </div>
            </div>
        );
    };

    return <div>{addresses}</div>;
  }
}

module.exports = AddressList;