import React from 'react';

function ProductCard({ product }) {
  const formatPrice = (price) => {
    // Check if price is already formatted
    if (typeof price === 'string' && price.includes('$')) {
      return price;
    }
    
    // Simple price formatter
    return new Intl.NumberFormat('en-US', {
      style: 'currency',
      currency: 'USD'
    }).format(price / 100); // Shopify prices are in cents
  };

  return (
    <div className="product-card">
      {product.featured_image && (
        <div className="product-image">
          <img 
            src={product.featured_image} 
            alt={product.title} 
            onError={(e) => {
              e.target.onerror = null;
              e.target.src = "https://cdn.shopify.com/s/files/1/0533/2089/files/placeholder-images-image_large.png";
            }}
          />
        </div>
      )}
      
      <div className="product-info">
        <h4 className="product-title">{product.title}</h4>
        
        {product.vendor && (
          <p className="product-vendor">{product.vendor}</p>
        )}
        
        {product.variants && product.variants.length > 0 && (
          <p className="product-price">
            {formatPrice(product.variants[0].price)}
          </p>
        )}
        
        {product.tags && product.tags.length > 0 && (
          <div className="product-tags">
            {product.tags.map((tag, index) => (
              <span key={index} className="product-tag">
                {tag}
              </span>
            ))}
          </div>
        )}
      </div>
    </div>
  );
}

export default ProductCard;