import React from 'react';
import ReactDOM from 'react-dom';

//make an array of state options
let states = ["AL","AK","AZ","AR","CA","CO","CT","DE","DC","FL","GA","HI","ID","IL","IN","IA","KS","KY","LA","ME","MD","MA","MI","MN","MS","MO","MT","NE","NV","NH","NJ","NM","NY","NC","ND","OH","OK","OR","PA","RI","SC","SD","TN","TX","UT","VT","VA","WA","WV","WI","WY"];
function stateOptions(){
    return states.map(function(st){return <option key={st} value={st}>{st}</option>});
}

class Address extends React.Component {
  constructor(props) {
    super();
    this.state = props._store_.getState().addresses[props.index];
  }

  onChange(evt){
    var obj = {};
    obj[evt.target.name] = evt.target.value;
    this.setState(obj);
    this.props._store_.dispatch({type:"UPDATE", index: this.props.index, state:this.state});
  }

  render() {
    var show = this.state._show ? 'block' : 'none';

    return <div style={{display:show}}>

        <div className="row">
            <div className="form-group col-sm-12">
                <label htmlFor="Name">Name</label>
                <input type="text" name="line1" className="form-control" placeholder="Enter Name" value={this.state.name} onChange={this.onChange.bind(this)} />
            </div>
        </div>

        <div className="row">
            <div className="form-group col-sm-12">
                <label htmlFor="line1">Address Line 1</label>
                <input type="text" name="line1" className="form-control" placeholder="Enter Street Address" value={this.state.line1} onChange={this.onChange.bind(this)} />
            </div>
        </div>

        <div className="row">
            <div className="form-group col-sm-12">
                <label htmlFor="line2">Address Line 2</label>
                <input type="text" name="line2" className="form-control" placeholder="(Optional)" value={this.state.line2} onChange={(e)=>this.onChange(e)} />
            </div>
        </div>

        <div className="row">
            <div className="form-group col-sm-4">
                <label htmlFor="city">City</label>
                <input type="text" name="city" className="form-control" placeholder="Enter Street Address" value={this.state.city} onChange={(e)=>this.onChange(e)} />
            </div>
            <div className="form-group col-sm-4">
                <label htmlFor="state">State</label>
                <select type="text" name="state" className="form-control" placeholder="Enter Street Address" value={this.state.state} onChange={(e)=>this.onChange(e)}>
                    {stateOptions()}
                </select>
            </div>
            <div className="form-group col-sm-4">
                <label htmlFor="zip">Zip</label>
                <input type="text" name="zip" className="form-control" placeholder="Enter Street Address" value={this.state.zip} onChange={(e)=>this.onChange(e)} />
            </div>
        </div>
    </div>
  }
}

//make this available externally
module.exports = Address;