import React, { useEffect, useState } from 'react';
import { LocationContext } from './LocationContext';

export const LocationProvider = ({ children }) => {
  const [location, setLocation] = useState('');
  const [hydrated, setHydrated] = useState(false); // 🆕 controls initial render

  useEffect(() => {
    const stored = localStorage.getItem('user-location');
    if (stored) setLocation(stored);
    setHydrated(true); // ✅ Mark as ready
  }, []);

  const updateLocation = (newLocation) => {
    setLocation(newLocation);
    localStorage.setItem('user-location', newLocation);
  };

  if (!hydrated) return null; // ✅ Don't render anything until ready

  return (
    <LocationContext.Provider value={{ location, setLocation: updateLocation }}>
      {children}
    </LocationContext.Provider>
  );
};
