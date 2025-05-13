import React, { useState, useEffect } from 'react';
import { useLocation } from '../context/LocationContext';

export default function LocationPopup() {
  const { location, setLocation } = useLocation();
  const [input, setInput] = useState('');
  const [showPopup, setShowPopup] = useState(!location);

  useEffect(() => {
    if (showPopup) {
      document.body.style.overflow = 'hidden'; // disable scroll
    } else {
      document.body.style.overflow = 'auto'; // re-enable scroll
    }
  }, [showPopup]);

  const handleSubmit = (e) => {
    e.preventDefault();
    if (input.trim()) {
      setLocation(input.trim());
      console.log('Location set to:', input.trim());
      setShowPopup(false);
    }
  };

  if (!showPopup) return null;

  return (
    <div className="popup-overlay">
      <div className="popup-content">
        <form onSubmit={handleSubmit}>
          <label><strong>Enter your delivery suburb or postcode</strong></label>
          <input
            type="text"
            placeholder="Enter suburb or postcode"
            value={input}
            onChange={(e) => setInput(e.target.value)}
          />
          <button type="submit">Submit</button>
        </form>
        <p>
          Some products may be unavailable in certain locations due to seasonality.
          Please enter your delivery postcode or suburb to see what’s available to your intended delivery destination.
        </p>
      </div>
    </div>
  );
}
