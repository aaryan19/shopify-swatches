import React, { useContext, useEffect } from 'react';
import { LocationContext } from '../context/LocationContext';

const ProductList = ({ products }) => {
  const { location } = useContext(LocationContext);

  useEffect(() => {
    console.log('✅ ProductList received products:', products);
    console.log('📍 Current location filter:', location);
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
        <div
          key={product.id}
          style={{
            border: '1px solid #ccc',
            borderRadius: '8px',
            padding: '1em',
            marginBottom: '1em',
          }}
        >
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
