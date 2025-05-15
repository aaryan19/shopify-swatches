import React, { useContext } from 'react';
import { LocationContext } from './context/LocationContext';
import LocationPopup from './components/LocationPopup';
import LocationBanner from './components/LocationBanner';

function App({ view, product, pageType }) {
  const { location } = useContext(LocationContext);

  console.log("📦 view:", view);
  console.log("🧭 pageType:", pageType);
  console.log("📍 Location from context:", location);

  // Always render popup and banner so they can share context
  return (
    <div>
      <LocationPopup />
      <LocationBanner />
    </div>
  );
}

export default App;
