import React from 'react';
import ReactDOM from 'react-dom';
import AddressWrap from './AddressWrap.jsx';


class AddressList extends React.Component {
  constructor(props) {
    super();
  }

  render(){
    //using props here instead of state, since state is not maintained here.  Think about it, the AddressBook should
    //hold all the addresses, why try and duplicate that here.  So its just this Components job to iterate the
    //Address book

    let addrs = [];

    for(var x in this.props.addresses){
        addrs.push(<AddressWrap key={this.props.addresses[x].id} address={this.props.addresses[x]} />);
    };

    return <div>{addrs}</div>;
  }
}

module.exports = AddressList;