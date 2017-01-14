import React from 'react';
import ReactDOM from 'react-dom';

var states = ["AL","AR"];

class Address extends React.Component {
  constructor(props) {
    super();
    this.state = props._store_.getState()[props.index];
  }

  onChange(evt){
    var obj = {};
    obj[evt.target.name] = evt.target.value;
    this.setState(obj);
    this.props._store_.dispatch({type:"UPDATE", index: this.props.index, state:this.state});
  }

  render() {
    return <div>

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
                    option({states})
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