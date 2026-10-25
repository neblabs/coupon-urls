import React from 'react';
import ReactDOM from 'react-dom';
import Dashboard from './Dashboard';
import { Provider } from 'react-redux'
import store from './data/store';
import DashboardManager from './DashboardManager';

new DashboardManager(store);

ReactDOM.render(
  <React.StrictMode>
    <Provider store={store}>
        <Dashboard />        
    </Provider>
  </React.StrictMode>,
  document.getElementById('coupon_urls')
);