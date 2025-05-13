import React from 'react';
import { useLocation } from '../context/LocationContext';

export default function ProductRenderer({ type }) {
  const { location } = useLocation();

  return (
    <div>
      <h3>Rendering products for "{type}"</h3>
      <p>Location: {location || 'No location set'}</p>
      {/* Add logic to show/hide products based on location */}
    </div>
  );
}
