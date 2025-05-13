import React from 'react';
import ReactDOM from 'react-dom/client';
import App from './App';
import './index.css';

document.querySelectorAll('[data-role]').forEach((el) => {
  const role = el.dataset.role;
  const root = ReactDOM.createRoot(el);
  root.render(<App role={role} />);
});
