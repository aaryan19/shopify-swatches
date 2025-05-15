import React from 'react';
import ReactDOM from 'react-dom/client';
import App from './App';
import { LocationProvider } from './context/LocationProvider'; // ✅ Corrected import

const mountReactApp = (element) => {
  const view = element.dataset.view || "product";
  const pageType = element.dataset.pageType || null;

  let productData = null;
  const productScript = document.getElementById('product-data');
  if (productScript) {
    try {
      const parsed = JSON.parse(productScript.textContent);
      productData = Array.isArray(parsed) ? parsed : [parsed];
      console.log(`✅ Loaded product data for ${view}`, productData);
    } catch (e) {
      console.error(`❌ Failed to parse product JSON for ${view}`, e);
    }
  }

  ReactDOM.createRoot(element).render(
    <LocationProvider> {/* ✅ Wrap app in provider */}
      <App view={view} pageType={pageType} product={productData} />
    </LocationProvider>
  );
};

document.querySelectorAll('[data-view]').forEach((el) => {
  mountReactApp(el);
});
