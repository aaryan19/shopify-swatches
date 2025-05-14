import React from 'react';
import ReactDOM from 'react-dom/client';
import App from './App';
import { LocationProvider } from './context/LocationContext';

ReactDOM.createRoot(document.getElementById('location-popup-root')).render(
  <LocationProvider>
    <App />
  </LocationProvider>
);
