import React from 'react';
import { useLocation } from '../context/LocationContext';

export default function LocationBanner() {
  const { location, setLocation } = useLocation();

  return (
    <div className="location-banner">
      <span className="icon">📍</span>
      <span>Deliver To: {location || 'Your Area'}.</span>
      <span className="change" onClick={() => setLocation('')}> (change)</span>
    </div>
  );
}
