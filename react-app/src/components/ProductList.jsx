import React from 'react';
import ProductCard from './ProductCard';

function ProductList({ products }) {
  if (!products || products.length === 0) {
    return (
      <div className="no-products-found">
        <p>No products found with the specified tag.</p>
      </div>
    );
  }

  return (
    <div className="product-list">
      <h3>Products ({products.length})</h3>
      <div className="product-grid">
        {products.map(product => (
          <ProductCard key={product.id} product={product} />
        ))}
      </div>
    </div>
  );
}

export default ProductList;