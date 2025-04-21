import { authenticate } from '~/shopify.server';

export const action = async ({ request }) => {
  const { admin } = await authenticate.admin(request);
  const { value } = await request.json();

  try {
    const appInstallationQuery = await admin.graphql(`
      {
        currentAppInstallation {
          id
        }
      }
    `);

    const appInstallationId = appInstallationQuery.data.currentAppInstallation.id;

    const mutation = await admin.graphql(`
      mutation metafieldsSet {
        metafieldsSet(metafields: [
          {
            namespace: "delivery_customization",
            key: "note",
            type: "single_line_text_field",
            value: "${value}",
            ownerId: "${appInstallationId}"
          }
        ]) {
          metafields {
            id
          }
          userErrors {
            field
            message
          }
        }
      }
    `);

    return new Response(JSON.stringify({ success: true, result: mutation.data }), {
      status: 200,
    });
  } catch (error) {
    console.error('Metafield save error:', error);
    return new Response(JSON.stringify({ error: error.message }), { status: 500 });
  }
};
