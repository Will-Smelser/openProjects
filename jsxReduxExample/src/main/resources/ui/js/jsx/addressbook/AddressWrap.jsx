import React from 'react';
import ReactDOM from 'react-dom';
import Address from './Address.jsx';


class AddressWrap extends React.Component {
  constructor(props) {
    super();
    this.state = {address: props.address};
  }


  toggleShowAddress(evt){

       console.log(this.addressWrapper);

       //update the show state
       this.state.address._show = !this.state.address._show;

       //I use forceUpdate(), since change state on object itself, a little easier than setState(this.state.addresses)


       if(this.state.address._show){
          this.forceUpdate(()=>{
              $(this.addressWrapper).slideDown();
          });
       }else{
          $(this.addressWrapper).slideUp(()=>{
              this.forceUpdate();
          });
       }

    }

    removeAddress(){
        this.props.removeAddress(this.props.address.id);
    }

    render(){
        console.log("render");
        return <div className="panel panel-default">
           <div className="panel-body">
               <div>
                    <b onClick={(e)=>this.toggleShowAddress(e)}>{this.props.address.name}</b>
                    <span className="pull-right" onClick={()=>{this.removeAddress();}}>remove</span>
               </div>
               <div ref={(div)=>{this.addressWrapper = div;}}>
                  <Address address={this.props.address} updateAddress={this.props.updateAddress} />
               </div>
           </div>
        </div>;
    }
}

module.exports = AddressWrap;