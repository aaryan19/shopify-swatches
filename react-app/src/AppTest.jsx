import React, { useEffect, useState } from 'react';

function App() {
  const [products, setProducts] = useState([]);
  const [tagFilter, setTagFilter] = useState('');

  useEffect(() => {
    const productScript = document.getElementById('product-data');
    if (productScript) {
      try {
        const data = JSON.parse(productScript.textContent);
        if (Array.isArray(data) && data.length > 0) {
          setProducts(data);
        } else {
          console.warn("No products found in the JSON data.");
        }
      } catch (e) {
        console.error("Failed to parse product JSON", e);
      }
    } else {
      console.warn("Product data script not found.");
    }
  }, []);

  const filteredProducts = tagFilter.trim()
    ? products.filter((product) =>
        product.tags.some((tag) =>
          tag.toLowerCase().includes(tagFilter.trim().toLowerCase())
        )
      )
    : products;

  if (!filteredProducts.length) return <p>No products found.</p>;

  return (
    <div>
      <input
        type="text"
        placeholder="Enter tag to filter (e.g. 'bulky')"
        value={tagFilter}
        onChange={(e) => setTagFilter(e.target.value)}
        style={{
          padding: '0.5rem',
          marginBottom: '1rem',
          width: '100%',
          maxWidth: '300px'
        }}
      />

      {filteredProducts.map((product) => (
        <div className="product" key={product.id} style={{ marginBottom: '2rem' }}>
          <h3>{product.title}</h3>
          <p><strong>Price:</strong> {product.price}</p>
          <p><strong>Description:</strong> {truncate(stripHtml(product.body_html), 100)}</p>

          {product.image?.src && (
            <img
              src={product.image.src}
              alt={product.title}
              style={{ width: '200px', height: 'auto' }}
            />
          )}

          <div>
            <strong>Variants:</strong>
            <ul>
              {product.variants.map((variant) => (
                <li key={variant.id}>
                  {variant.title} - {variant.price} {variant.available ? 'In stock' : 'Sold out'}
                </li>
              ))}
            </ul>
          </div>

          <p><strong>Tags:</strong> {product.tags.join(', ')}</p>
          <p><strong>Type:</strong> {product.product_type}</p>
          <p><strong>Vendor:</strong> {product.vendor}</p>
        </div>
      ))}
    </div>
  );
}

function truncate(text, maxLength) {
  return text.length > maxLength ? text.slice(0, maxLength) + '…' : text;
}

function stripHtml(html) {
  const tmp = document.createElement("div");
  tmp.innerHTML = html;
  return tmp.textContent || tmp.innerText || "";
}

export default App;
