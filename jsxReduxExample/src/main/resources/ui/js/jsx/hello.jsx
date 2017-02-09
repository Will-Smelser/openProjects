import React from 'react';
import ReactDOM from 'react-dom';

class Hello extends React.Component {
  render() {
    return <h4>Hello</h4>
  }
}

module.exports = Hello;

ReactDOM.render(<Hello/>, document.getElementById('hello'));