import React, { useState, useEffect, useContext } from 'react';
import { LocationContext } from '../context/LocationContext';

export default function LocationPopup() {
  const { location, setLocation } = useContext(LocationContext); // ✅ Correct usage
  const [input, setInput] = useState('');
  const [showPopup, setShowPopup] = useState(!location); // Show popup if no location

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
