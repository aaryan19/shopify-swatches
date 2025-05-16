import React from 'react';
import { LocationProvider } from './context/LocationProvider';
import LocationPopup from './components/LocationPopup';
import LocationBanner from './components/LocationBanner';
import ProductList from './components/ProductList';

function App({ view }) {
  // Read product data from <script id="product-data">
  let productData = [];
  const script = document.getElementById('product-data');
  if (script) {
    try {
      productData = JSON.parse(script.textContent);
      console.log('✅ Parsed product data:', productData);
    } catch (error) {
      console.error('❌ Failed to parse product data:', error);
    }
  } else {
    console.warn('⚠️ No script tag with id="product-data" found.');
  }

  console.log('📍 View:', view);

  return (
    <LocationProvider>
      <LocationPopup />
      <LocationBanner/>

      {view === 'product' && <ProductList products={productData} />}
    </LocationProvider>
  );

}

export default App;
