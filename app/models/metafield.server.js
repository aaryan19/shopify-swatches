export async function getMeta(productGid, graphql) {
    const response = await graphql(
      `
        query ($id: ID!) {
          product(id: $id) {
            title
          }
        }
      `,
      {
        variables: {
          id: 'gid://shopify/Product/9273872777511',
        },
      }
    );
  
    const { data } = await response.json();
    return data;
  }
  

  export async function postMetaTest(value, graphql) {
    // 1. Get the Shop GID
    const shopIdResponse = await graphql(`
      {
        shop {
          id
        }
      }
    `);
    const { data: shopData } = await shopIdResponse.json();
    const shopGID = shopData?.shop?.id;
  
    // 2. Use it in the mutation
    const metafieldResponse = await graphql(
      `
        mutation metafieldsSet($metafields: [MetafieldsSetInput!]!) {
          metafieldsSet(metafields: $metafields) {
            metafields {
              id
              key
              namespace
              value
              type
            }
            userErrors {
              field
              message
            }
          }
        }
      `,
      {
        variables: {
          metafields: [
            {
              ownerId: shopGID,
              namespace: "custom",
              key: "custom_data",
              value: value,
              type: "single_line_text_field",
            },
          ],
        },
      }
    );
  
    const { data } = await metafieldResponse.json();
    return data;
  }
  
  export async function postMeta(payload, graphql) {
    const shopIdResponse = await graphql(`
      {
        shop {
          id
        }
      }
    `);
  
    const { data: shopData } = await shopIdResponse.json();
    const shopGID = shopData?.shop?.id;
  
    const { key, namespace, value, type = 'json' } = payload;
  
    const metafieldResponse = await graphql(
      `
        mutation metafieldsSet($metafields: [MetafieldsSetInput!]!) {
          metafieldsSet(metafields: $metafields) {
            metafields {
              id
            }
            userErrors {
              message
            }
          }
        }
      `,
      {
        variables: {
          metafields: [
            {
              ownerId: shopGID,
              namespace,
              key,
              value: typeof value === 'string' ? value : JSON.stringify(value),
              type,
            },
          ],
        },
      }
    );
  
    return metafieldResponse.json();
  }

  export async function getMetafields(graphql) {
    const query = `
      {
        shop {
          metafields(first: 100, namespace: "delivery_settings") {
            edges {
              node {
                id
                namespace
                key
                value
                type
              }
            }
          }
        }
      }
    `;
  
    const response = await graphql(query);
    const json = await response.json();
  
    return json.data.shop.metafields.edges.map(edge => edge.node);
  }
  