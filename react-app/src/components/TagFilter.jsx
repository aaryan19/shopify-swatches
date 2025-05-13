import React, { useState } from 'react';

function TagFilter({ tagFilter, setTagFilter, availableTags }) {
  const [suggestions, setSuggestions] = useState([]);
  const [showSuggestions, setShowSuggestions] = useState(false);

  const handleInputChange = (e) => {
    const value = e.target.value;
    setTagFilter(value);
    
    if (value.trim() !== '') {
      const filteredSuggestions = availableTags.filter(tag =>
        tag.toLowerCase().includes(value.toLowerCase())
      );
      setSuggestions(filteredSuggestions);
      setShowSuggestions(true);
    } else {
      setSuggestions([]);
      setShowSuggestions(false);
    }
  };

  const handleSuggestionClick = (suggestion) => {
    setTagFilter(suggestion);
    setSuggestions([]);
    setShowSuggestions(false);
  };

  return (
    <div className="tag-filter">
      <h3>Filter Products by Tag</h3>
      <div className="tag-input-container">
        <input
          type="text"
          value={tagFilter}
          onChange={handleInputChange}
          placeholder="Enter a tag to filter products..."
          className="tag-input"
          onFocus={() => tagFilter && setSuggestions(availableTags.filter(tag => 
            tag.toLowerCase().includes(tagFilter.toLowerCase())))}
          onBlur={() => setTimeout(() => setShowSuggestions(false), 200)}
        />
        
        {showSuggestions && suggestions.length > 0 && (
          <ul className="suggestions-list">
            {suggestions.map((suggestion, index) => (
              <li 
                key={index} 
                onClick={() => handleSuggestionClick(suggestion)}
                className="suggestion-item"
              >
                {suggestion}
              </li>
            ))}
          </ul>
        )}
      </div>
      
      {tagFilter && (
        <button 
          className="clear-filter" 
          onClick={() => setTagFilter('')}
        >
          Clear Filter
        </button>
      )}
    </div>
  );
}

export default TagFilter;