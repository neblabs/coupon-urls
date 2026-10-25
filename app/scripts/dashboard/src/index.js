import React from 'react';
import ReactDOM from 'react-dom';
import Dashboard from './Dashboard';
import { Provider } from 'react-redux'
import store from './data/store';
import DashboardManager from './DashboardManager';
import { DashboardManagerContext } from './contexts';

const dashboardManager = new DashboardManager(store);

ReactDOM.render(
  <React.StrictMode>
    <Provider store={store}>
      <DashboardManagerContext.Provider value={dashboardManager}>
        <Dashboard />
      </DashboardManagerContext.Provider>        
    </Provider>
  </React.StrictMode>,
  document.getElementById('coupon_urls')
);
