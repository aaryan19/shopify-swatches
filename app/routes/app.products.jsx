// import { useEffect, useState } from 'react';
// import { Card, Page, ResourceList, ResourceItem, Button, Select } from '@shopify/polaris';
// import { useAuthenticatedFetch } from '../hooks';
// import { postMeta } from '../utils/postMeta';

// export default function ProductManager() {
//   const [products, setProducts] = useState([]);
//   const [selectedValues, setSelectedValues] = useState({});
//   const fetch = useAuthenticatedFetch();

//   useEffect(() => {
//     const fetchProducts = async () => {
//       const query = `{
//         products(first: 10) {
//           edges {
//             node {
//               id
//               title
//               variants(first: 5) {
//                 edges {
//                   node {
//                     id
//                     title
//                   }
//                 }
//               }
//             }
//           }
//         }
//       }`;

//       const response = await fetch('/admin/api/graphql.json', {
//         method: 'POST',
//         headers: { 'Content-Type': 'application/json' },
//         body: JSON.stringify({ query }),
//       });
//       const result = await response.json();
//       setProducts(result.data.products.edges);
//     };

//     fetchProducts();
//   }, []);

//   const handleChange = (id, value) => {
//     setSelectedValues(prev => ({ ...prev, [id]: value }));
//   };

//   const handleAssign = async (ownerId) => {
//     const value = selectedValues[ownerId];
//     if (value) {
//       await postMeta(JSON.stringify({ value }), fetch, ownerId);
//       alert('Metafield assigned.');
//     }
//   };

//   return (
//     <Page title="Assign Metafields to Products or Variants">
//       <Card>
//         <ResourceList
//           items={products}
//           renderItem={({ node }) => (
//             <ResourceItem id={node.id} accessibilityLabel={node.title}>
//               <h3>{node.title}</h3>
//               <Select
//                 label="Assign Value to Product"
//                 options={[{ label: 'Select value', value: '' }, 'Option A', 'Option B', 'Option C'].map(opt => typeof opt === 'string' ? { label: opt, value: opt } : opt)}
//                 onChange={(value) => handleChange(node.id, value)}
//                 value={selectedValues[node.id] || ''}
//               />
//               <Button onClick={() => handleAssign(node.id)} primary>Assign to Product</Button>
//               {node.variants.edges.map(({ node: variant }) => (
//                 <div key={variant.id} style={{ marginTop: '10px' }}>
//                   <strong>{variant.title}</strong>
//                   <Select
//                     label="Assign Value to Variant"
//                     options={[{ label: 'Select value', value: '' }, 'Option A', 'Option B', 'Option C'].map(opt => typeof opt === 'string' ? { label: opt, value: opt } : opt)}
//                     onChange={(value) => handleChange(variant.id, value)}
//                     value={selectedValues[variant.id] || ''}
//                   />
//                   <Button onClick={() => handleAssign(variant.id)}>Assign to Variant</Button>
//                 </div>
//               ))}
//             </ResourceItem>
//           )}
//         />
//       </Card>
//     </Page>
//   );
// }