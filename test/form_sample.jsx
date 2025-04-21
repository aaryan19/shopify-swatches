// import {
//   Page,
//   Layout,
//   TextField,
//   Button,
//   FormLayout,
//   Frame,
//   Toast,
// } from "@shopify/polaris";
// import { useActionData, Form as RemixForm } from "@remix-run/react";
// import { useState, useCallback, useEffect } from "react";
// import { authenticate } from "../shopify.server";
// import { postMeta } from "../models/metafield.server"; // Path may need adjustment based on your project structure

// import { json } from "@remix-run/node";

// export async function loader() {
//   return null;
// }

// export async function action({ request, params }) {
//   const { admin } = await authenticate.admin(request);
//   const formData = await request.formData();
//   const value = formData.get("value"); // This is the product GID from the form

//   try {
//     const data = await postMeta(value, admin.graphql);

//     if (!data?.product?.title) {
//       return json({ success: false, error: "Product not found." });
//     }

//     return json({ success: true, product: data.product });
//   } catch (error) {
//     return json({ success: false, error: error.message || "Unknown error" });
//   }
// }



// // The client component
// export default function FormPage() {
//   const actionData = useActionData();
//   const [value, setValue] = useState("");
//   const [toastActive, setToastActive] = useState(false);
//   const [errorMessage, setErrorMessage] = useState("");
//   const [productTitle, setProductTitle] = useState("");

//   const handleChange = useCallback((newValue) => setValue(newValue), []);

//   useEffect(() => {
//     if (actionData?.success) {
//       setToastActive(true);
//       setProductTitle(actionData.product?.title || "");
//     }
  
//     if (actionData?.success === false) {
//       setErrorMessage(actionData.error);
//     }
//   }, [actionData]);

//   return (
//     <Frame>
//       <Page title="Delivery Customization Settings">
//         <Layout>
//           <Layout.Section>
//             <RemixForm method="post">
//               <FormLayout>
//                 <TextField
//                   name="value"
//                   label="Product GID (e.g. gid://shopify/Product/9273872777511)"
//                   value={value}
//                   onChange={handleChange}
//                   autoComplete="off"
//                 />
//                 <Button submit primary>
//                   Fetch Product
//                 </Button>
//               </FormLayout>
//             </RemixForm>

//             {productTitle && (
//               <div style={{ marginTop: "10px" }}>
//                 <strong>Product Title:</strong> {productTitle}
//               </div>
//             )}

//             {errorMessage && (
//               <div style={{ color: "red", marginTop: "10px" }}>
//                 Error: {errorMessage}
//               </div>
//             )}
//           </Layout.Section>
//         </Layout>

//         {toastActive && (
//           <Toast
//             content="Fetched successfully!"
//             onDismiss={() => setToastActive(false)}
//           />
//         )}
//       </Page>
//     </Frame>
//   );
// }
