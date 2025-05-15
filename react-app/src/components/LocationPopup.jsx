import React, { useState, useEffect, useContext } from 'react';
import { LocationContext } from '../context/LocationContext';

const LocationPopup = () => {
  const { location, setLocation } = useContext(LocationContext);
  const [input, setInput] = useState(location || '');
  const [showPopup, setShowPopup] = useState(false);

  // Show popup if no location is set
  useEffect(() => {
    if (!location) {
      setShowPopup(true);
    } else {
      setShowPopup(false);
    }
  }, [location]);

  // Disable scrolling while popup is open
  useEffect(() => {
    document.body.style.overflow = showPopup ? 'hidden' : 'auto';
    return () => {
      document.body.style.overflow = 'auto';
    };
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
          <label htmlFor="location-input">
            <strong>Enter your delivery suburb or postcode</strong>
          </label>
          <input
            id="location-input"
            type="text"
            placeholder="Enter suburb or postcode"
            value={input}
            onChange={(e) => setInput(e.target.value)}
            autoFocus
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
};

export default LocationPopup;
