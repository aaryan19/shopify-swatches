import {
  Page,
  Layout,
  TextField,
  Button,
  FormLayout,
  Frame,
  Toast,
  Select,
  Checkbox,
  Icon,
  ChoiceList,
  Card,
} from "@shopify/polaris";
import { PlusIcon } from "@shopify/polaris-icons";
import { useActionData, Form as RemixForm } from "@remix-run/react";
import { useState, useCallback, useEffect } from "react";
import { json } from "@remix-run/node";
import { authenticate } from "../shopify.server";
import { postMeta } from "../models/metafield.server";

const timeOptions = [
  "12:00AM", "12:30AM", "1:00AM", "1:30AM", "2:00AM", "2:30AM", "3:00AM",
  "3:30AM", "4:00AM", "4:30AM", "5:00AM", "5:30AM", "6:00AM", "6:30AM", "7:00AM",
  "7:30AM", "8:00AM", "8:30AM", "9:00AM", "9:30AM", "10:00AM", "10:30AM",
  "11:00AM", "11:30AM", "12:00PM", "12:30PM", "1:00PM", "1:30PM", "2:00PM",
  "2:30PM", "3:00PM", "3:30PM", "4:00PM", "4:30PM", "5:00PM", "5:30PM", "6:00PM",
  "6:30PM", "7:00PM", "7:30PM", "8:00PM", "8:30PM", "9:00PM", "9:30PM", "10:00PM",
  "10:30PM", "11:00PM", "11:30PM"
].map((label) => ({ label, value: label }));

const daysOfWeek = [
  "Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"
];

const flowerTypes = ["Flower_bouquet", "Boxed", "Wrapped", "Other"];

export async function action({ request }) {
  const { admin } = await authenticate.admin(request);
  const formData = await request.formData();

  const deliveryName = formData.get("deliveryName");
  const startTime = formData.get("startTime");
  const cutoffTime = formData.get("cutoffTime");
  const cutoffDay = formData.get("cutoffDay");
  const zipcode = formData.get("zipcode");
  const availableDays = formData.getAll("availableDays");
  const flowerType = formData.getAll("flowerType");

  const messageConditions = [];
  const conditionCount = formData.get("conditionCount");

  for (let i = 0; i < conditionCount; i++) {
    const base = `condition[${i}]`;
    messageConditions.push({
      Condition: formData.get(`${base}[name]`),
      Condition_type: formData.get(`${base}[type]`),
      Message: formData.get(`${base}[message]`),
      message_start_time: formData.get(`${base}[startTime]`),
      message_end_time: formData.get(`${base}[endTime]`),
    });
  }

  const finalJSON = {
    [deliveryName]: {
      Start_time: startTime,
      Cutoff_time: cutoffTime,
      Cutoff_day: cutoffDay,
      Zipcode: zipcode,
      day: availableDays.join(","),
      Message_condition: messageConditions.reduce((acc, condition, i) => {
        acc[`Condition_${i + 1}`] = condition;
        return acc;
      }, {}),
      Flower_type: flowerType.join(","),
    },
  };

  try {
    const result = await postMeta(JSON.stringify(finalJSON), admin.graphql);
    return json({ success: true, result });
  } catch (error) {
    return json({ success: false, error: error.message });
  }
}

export default function FormPage() {
  const actionData = useActionData();
  const [toastActive, setToastActive] = useState(false);
  const [errorMessage, setErrorMessage] = useState("");
  const [conditionCount, setConditionCount] = useState(0);
  const [deliveryName, setDeliveryName] = useState("");

  useEffect(() => {
    if (actionData?.success) {
      setToastActive(true);
    }
    if (actionData?.success === false) {
      setErrorMessage(actionData.error);
    }
  }, [actionData]);

  return (
    <Frame>
      <Page title="Delivery Customization Settings">
        <Layout>
          <Layout.Section>
            <RemixForm method="post">
              <FormLayout>
                <Card sectioned>
                  <Layout>
                    <Layout.Section oneThird>
                      <strong>Basic Info</strong>
                    </Layout.Section>
                    <Layout.Section>
                      <TextField
                        name="deliveryName"
                        label="Delivery Option Name"
                        value={deliveryName}
                        onChange={setDeliveryName}
                        autoComplete="off"
                      />
                      <Select name="startTime" label="Start Time" options={timeOptions} />
                      <Select name="cutoffTime" label="Cutoff Time" options={timeOptions} />
                      <TextField name="cutoffDay" label="Cutoff Day (0-6)" type="number" />
                      <TextField name="zipcode" label="Zip Codes (comma separated)" />
                      <ChoiceList
                        allowMultiple
                        name="availableDays"
                        title="Available Days"
                        choices={daysOfWeek.map((day) => ({
                          label: day,
                          value: day,
                        }))}
                      />
                      <ChoiceList
                        allowMultiple
                        name="flowerType"
                        title="Flower Types"
                        choices={flowerTypes.map((type) => ({
                          label: type,
                          value: type,
                        }))}
                      />
                    </Layout.Section>
                  </Layout>
                </Card>

                <Card sectioned title="Message Conditions">
                  {Array.from({ length: conditionCount }).map((_, i) => (
                    <Layout key={i}>
                      <Layout.Section oneThird>
                        <strong>Condition {i + 1}</strong>
                      </Layout.Section>
                      <Layout.Section>
                        <TextField name={`condition[${i}][name]`} label="Condition Name" />
                        <Select
                          name={`condition[${i}][type]`}
                          label="Condition Type"
                          options={[
                            { label: "Message", value: "message" },
                            { label: "Hide", value: "hide" },
                          ]}
                        />
                        <TextField name={`condition[${i}][message]`} label="Message Text" />
                        <Select
                          name={`condition[${i}][startTime]`}
                          label="Start Time"
                          options={timeOptions}
                        />
                        <Select
                          name={`condition[${i}][endTime]`}
                          label="End Time"
                          options={timeOptions}
                        />
                      </Layout.Section>
                    </Layout>
                  ))}
                  <input type="hidden" name="conditionCount" value={conditionCount} />
                  <Button
                    icon={PlusIcon}
                    onClick={() => setConditionCount((c) => c + 1)}
                    plain
                  >
                    Add Condition
                  </Button>
                </Card>

                <Button submit primary>Submit Settings</Button>
              </FormLayout>
            </RemixForm>

            {errorMessage && (
              <div style={{ color: "red", marginTop: "10px" }}>
                Error: {errorMessage}
              </div>
            )}
          </Layout.Section>
        </Layout>

        {toastActive && (
          <Toast
            content="Settings saved!"
            onDismiss={() => setToastActive(false)}
          />
        )}
      </Page>
    </Frame>
  );
}
