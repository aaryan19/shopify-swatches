import React, { createContext, useState, useContext, useEffect } from 'react';

const LocationContext = createContext();

export function LocationProvider({ children }) {
  const [location, setLocation] = useState(() => {
    if (typeof window !== 'undefined') {
      return localStorage.getItem('userLocation') || '';
    }
    return '';
  });

  useEffect(() => {
    if (location) {
      localStorage.setItem('userLocation', location);
    }
  }, [location]);

  return (
    <LocationContext.Provider value={{ location, setLocation }}>
      {children}
    </LocationContext.Provider>
  );
}

export function useLocation() {
  return useContext(LocationContext);
}
