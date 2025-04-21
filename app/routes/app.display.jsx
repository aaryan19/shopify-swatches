import { json } from "@remix-run/node";
import { useLoaderData } from "@remix-run/react";
import {
  Page,
  Layout,
  Card,
  Text,
  Spinner,
} from "@shopify/polaris";
import { getMetafields } from "../models/metafield.server";
import { authenticate } from "../shopify.server";

// --- Loader (runs on server before component renders)
export async function loader({ request }) {
  const { admin } = await authenticate.admin(request);
  const allMetafields = await getMetafields(admin.graphql);

  const filtered = allMetafields

  return json({ metafields: filtered });
}

// --- Component
export default function CustomOptionsDisplay() {
  const { metafields } = useLoaderData();

  return (
    <Page title="Existing Delivery Settings">
      <Layout>
        {metafields.length === 0 ? (
          <Layout.Section>
            <Card sectioned>
              <Text>No metafields found for custom_options.</Text>
            </Card>
          </Layout.Section>
        ) : (
          metafields.map((field, index) => {
            let parsedValue;
            try {
              parsedValue = JSON.parse(field.value);
            } catch (e) {
              parsedValue = { error: "Invalid JSON" };
            }

            const [name, details] = Object.entries(parsedValue)[0] || [];

            return (
              <Layout.Section key={index}>
                <Card title={`Option: ${name || "Unnamed"}`} sectioned>
                  {details && typeof details === "object" ? (
                    <>
                      <Text><strong>Start Time:</strong> {details.Start_time}</Text>
                      <Text><strong>Cutoff Time:</strong> {details.Cutoff_time}</Text>
                      <Text><strong>Cutoff Day:</strong> {details.Cutoff_day}</Text>
                      <Text><strong>Zipcode:</strong> {details.Zipcode}</Text>
                      <Text><strong>Available Days:</strong> {details.day}</Text>
                      <Text><strong>Flower Type:</strong> {details.Flower_type}</Text>
                      <Text><strong>Message Conditions:</strong></Text>
                      {details.Message_condition &&
                        Object.entries(details.Message_condition).map(
                          ([condName, condVal], i) => (
                            <div key={i} style={{ marginLeft: "1rem", marginBottom: "0.5rem" }}>
                              <Text>- {condName}: {JSON.stringify(condVal)}</Text>
                            </div>
                          )
                        )}
                    </>
                  ) : (
                    <Text>Unable to parse metafield content.</Text>
                  )}
                </Card>
              </Layout.Section>
            );
          })
        )}
      </Layout>
    </Page>
  );
}
