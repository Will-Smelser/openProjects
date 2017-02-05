import React from 'react';
import ReactDOM from 'react-dom';
import Address from './Address.jsx';


class AddressWrap extends React.Component {
  constructor(props) {
    super();
    this.state = {address: props.address};
  }


  toggleShowAddress(evt){

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

    render(){
        console.log("AddressWrap.render()");

        return <div className="panel panel-default">
           <div className="panel-body">
               <div onClick={(e)=>this.toggleShowAddress(e)}><b>{this.state.address.name}</b></div>
               <div ref={(div)=>{this.addressWrapper = div;}}>
                  <Address address={this.state.address} />
               </div>
           </div>
        </div>;
    }
}

module.exports = AddressWrap;