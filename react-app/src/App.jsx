import React from 'react';
import LocationPopup from './components/LocationPopup';
import LocationBanner from './components/LocationBanner';
import ProductRenderer from './components/ProductRenderer';
import { LocationProvider } from './context/LocationContext';

export default function App({ role }) {
  return (
    <LocationProvider>
      {role === 'popup' && <LocationPopup />}
      {role === 'banner' && <LocationBanner />}
      {['home-products', 'collection-products', 'related-products'].includes(role) && (
        <ProductRenderer type={role} />
      )}
    </LocationProvider>
  );
}
