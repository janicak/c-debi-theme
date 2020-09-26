import React from 'react'
import ReactDOM from 'react-dom'
import App from './App'

const root = document.getElementById('react-root')
const data = window.alt_search.data;

ReactDOM.render(<App data={data} />, root);