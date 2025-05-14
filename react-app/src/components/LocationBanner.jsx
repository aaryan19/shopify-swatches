import React, { useContext, useState } from 'react';
import { LocationContext } from '../context/LocationContext';

const LocationBanner = () => {
  const { location, setLocation } = useContext(LocationContext);
  const [editing, setEditing] = useState(false);
  const [inputValue, setInputValue] = useState(location);

  const handleSubmit = (e) => {
    e.preventDefault();
    setLocation(inputValue);
    setEditing(false);
  };

  const handleChangeClick = () => {
    setInputValue(location);
    setEditing(true);
  };

  return (
    <div className="location-banner">
      {editing ? (
        <form onSubmit={handleSubmit} className="location-form">
          <input
            type="text"
            value={inputValue}
            onChange={(e) => setInputValue(e.target.value)}
            autoFocus
            className="location-input"
            placeholder="Enter your location"
          />
          <button type="submit" className="save-btn">Save</button>
        </form>
      ) : (
        <div className="location-display">
          <span className="location-text">
            Delivering to: <strong>{location || 'Not Set'}</strong>
          </span>
          <button onClick={handleChangeClick} className="change-btn">
            Change
          </button>
        </div>
      )}
    </div>
  );
};

export default LocationBanner;
