import React from 'react';
import ReactDOM from 'react-dom';

class World extends React.Component {
  render() {
    return <h1>World</h1>
  }
}

module.exports = World;

ReactDOM.render(<World/>, document.getElementById('world'));