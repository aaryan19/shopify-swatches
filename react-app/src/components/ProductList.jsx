import React, { useContext, useEffect } from 'react';
import { LocationContext } from '../context/LocationContext';

const ProductList = ({ products }) => {
  const { location, setLocation } = useContext(LocationContext);

  // Initialize location from localStorage
  useEffect(() => {
    const savedLocation = localStorage.getItem('location');
    if (savedLocation && savedLocation !== location) {
      setLocation(savedLocation);
    }
  }, []);

  // Listen for both storage event and custom locationChanged event
  useEffect(() => {
    const handleStorage = (event) => {
      if (event.key === 'location') {
        const newLocation = event.newValue;
        if (newLocation !== location) {
          setLocation(newLocation);
        }
      }
    };

    const handleCustomChange = (event) => {
      const newLocation = event.detail;
      if (newLocation !== location) {
        setLocation(newLocation);
      }
    };

    window.addEventListener('storage', handleStorage);
    window.addEventListener('locationChanged', handleCustomChange);

    return () => {
      window.removeEventListener('storage', handleStorage);
      window.removeEventListener('locationChanged', handleCustomChange);
    };
  }, [location]);

  // DEBUG
  useEffect(() => {
    console.log('📦 Products:', products);
    console.log('📍 Location:', location);
  }, [products, location]);

  if (!Array.isArray(products)) {
    return <p style={{ color: 'red' }}>Product list is not valid</p>;
  }

  const filteredProducts = !location
    ? products
    : products.filter(product =>
        Array.isArray(product.tags) &&
        product.tags.some(tag =>
          tag.toLowerCase().includes(location.toLowerCase())
        )
      );

  if (filteredProducts.length === 0) {
    return <p>No matching products found for location "{location}".</p>;
  }

  return (
    <div style={{ marginTop: '2em' }}>
      {filteredProducts.map((product) => (
        <div key={product.id} style={{
          border: '1px solid #ccc',
          borderRadius: '8px',
          padding: '1em',
          marginBottom: '1em',
        }}>
          <h3>{product.title}</h3>
          <p><strong>Price:</strong> ${(product.price / 100).toFixed(2)}</p>
          {product.tags?.length > 0 && (
            <p><strong>Tags:</strong> {product.tags.join(', ')}</p>
          )}
          {product.image?.src ? (
            <img
              src={product.image.src}
              alt={product.title}
              style={{ maxWidth: '200px', height: 'auto', marginTop: '1em' }}
            />
          ) : (
            <p style={{ color: 'gray' }}>No image available</p>
          )}
        </div>
      ))}
    </div>
  );
};

export default ProductList;
