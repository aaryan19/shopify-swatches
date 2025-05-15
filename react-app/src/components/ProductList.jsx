import React, { useContext, useEffect, useState } from 'react';
import { LocationContext } from '../context/LocationContext'; // ✅ Correct import

const ProductList = () => {
  const { location } = useContext(LocationContext);
  const [allProducts, setAllProducts] = useState([]);
  const [filteredProducts, setFilteredProducts] = useState([]);

  useEffect(() => {
    const productDataScript = document.getElementById('product-data');
    if (productDataScript) {
      const products = JSON.parse(productDataScript.textContent);
      setAllProducts(products);
    }
  }, []);

  useEffect(() => {
    if (!location) {
      setFilteredProducts([]);
      return;
    }

    const normalizedLocation = location.trim().toLowerCase();

    const filtered = allProducts.filter(product =>
      product.tags.some(tag => tag.toLowerCase().includes(normalizedLocation))
    );

    setFilteredProducts(filtered);
  }, [location, allProducts]);

  if (!location) {
    return <p style={{ color: 'gray' }}>Please enter your location to view products.</p>;
  }

  if (filteredProducts.length === 0) {
    return <p style={{ color: 'red' }}>No products available for location: {location}</p>;
  }

  return (
    <div className="product-list">
      {filteredProducts.map(product => (
        <div key={product.id} className="product-card">
          <img src={product.image.src} alt={product.title} />
          <h3>{product.title}</h3>
          <p>Price: ${product.price / 100}</p>
          <p dangerouslySetInnerHTML={{ __html: product.body_html }} />
        </div>
      ))}
    </div>
  );
};

export default ProductList;
