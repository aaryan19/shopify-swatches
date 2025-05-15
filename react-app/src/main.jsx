import React from 'react';
import ReactDOM from 'react-dom/client';
import App from './App';

document.addEventListener('DOMContentLoaded', () => {
  const rootElements = [
    document.getElementById('react-root'),
    document.getElementById('location-banner-root')
  ].filter(Boolean); // Remove nulls

  rootElements.forEach((rootElement) => {
    const view = rootElement.dataset.view || 'default';
    const pageType = rootElement.dataset.pageType || null;

    console.log('🔧 Mounting React app with view:', view, 'on', rootElement.id);

    ReactDOM.createRoot(rootElement).render(
      <App view={view} pageType={pageType} />
    );
  });

  if (rootElements.length === 0) {
    console.warn('⚠️ No valid root elements found to mount the React app.');
  }
});
