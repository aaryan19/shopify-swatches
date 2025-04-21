import {
    Page,
    Layout,
    LegacyCard,
    TextField,
    FormLayout,
    Text,
    TextContainer,
    Button,
    Frame,
    Toast,
    LegacyStack,
    Tag,
    Listbox,
    EmptySearchResult,
    Combobox,
    AutoSelection,
    Select,
    DropZone,
    Thumbnail,
  } from '@shopify/polaris';
  import { useState, useCallback, useMemo, useEffect } from 'react';
  import * as RemixReact from "@remix-run/react";
  
  import { json } from '@remix-run/node';
  import { authenticate } from '../shopify.server';
  import { postMeta } from '../models/metafield.server';
  
  export async function loader() {
    return null;
  }
  
  export async function action({ request }) {
    const { admin } = await authenticate.admin(request);
    const formData = await request.formData();
    const type = formData.get('type');
    const key = formData.get('key');
    const namespace = formData.get('namespace');
  
    const allowedValues = formData.getAll('allowedValues[]');
    const payload = {
      key,
      namespace,
      type,
      allowedValues,
    };
  
    try {
      await postMeta(payload, admin.graphql);
      return json({ success: true });
    } catch (error) {
      return json({ success: false, error: error.message });
    }
  }
  
  export default function MetafieldForm() {
    const { Form: RemixForm, useActionData } = RemixReact;
    const actionData = useActionData();
    const [namespace] = useState('swatches');
    const [key, setKey] = useState('');
    const [type, setType] = useState('');
    const [tags, setTags] = useState([]);
    const [value, setValue] = useState('');
    const [suggestion, setSuggestion] = useState('');
    const [toastActive, setToastActive] = useState(false);
    const [errorMessage, setErrorMessage] = useState('');
    const [files, setFiles] = useState([]);
  
    useEffect(() => {
      if (actionData?.success) {
        setToastActive(true);
        setKey('');
        setType('');
        setTags([]);
        setFiles([]);
      }
      if (actionData?.success === false) {
        setErrorMessage(actionData.error);
      }
    }, [actionData]);
  
    const handleActiveOptionChange = useCallback(
      (activeOption) => {
        const isExisting = tags.includes(activeOption);
        if (!isExisting && activeOption !== value) {
          setSuggestion(activeOption);
        } else {
          setSuggestion('');
        }
      },
      [value, tags],
    );
  
    const updateSelection = useCallback(
      (selected) => {
        const next = new Set([...tags]);
        if (next.has(selected)) {
          next.delete(selected);
        } else {
          next.add(selected);
        }
        setTags([...next]);
        setValue('');
        setSuggestion('');
      },
      [tags],
    );
  
    const removeTag = useCallback((tag) => () => updateSelection(tag), [updateSelection]);
  
    const getAllTags = useCallback(() => {
      return [...new Set([...tags])];
    }, [tags]);
  
    const formatOptionText = useCallback(
      (option) => {
        const matchIndex = option.toLowerCase().indexOf(value.toLowerCase());
        if (matchIndex === -1) return option;
  
        const start = option.slice(0, matchIndex);
        const highlight = option.slice(matchIndex, matchIndex + value.length);
        const end = option.slice(matchIndex + value.length);
  
        return (
          <p>
            {start}
            <Text fontWeight="bold" as="span">
              {highlight}
            </Text>
            {end}
          </p>
        );
      },
      [value],
    );
  
    const escapeRegEx = useCallback((v) => v.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'), []);
  
    const options = useMemo(() => {
      const filter = new RegExp(escapeRegEx(value), 'i');
      return value ? getAllTags().filter((tag) => tag.match(filter)) : getAllTags();
    }, [value, getAllTags, escapeRegEx]);
  
    const optionMarkup = options.map((option) => (
      <Listbox.Option key={option} value={option} selected={tags.includes(option)}>
        <Listbox.TextOption selected={tags.includes(option)}>
          {formatOptionText(option)}
        </Listbox.TextOption>
      </Listbox.Option>
    ));
  
    const listboxMarkup = (
      <Listbox onSelect={updateSelection} onActiveOptionChange={handleActiveOptionChange}>
        {optionMarkup}
      </Listbox>
    );
  
    const tagMarkup = tags.length > 0 && (
      <LegacyStack spacing="extraTight">
        {tags.map((tag) => (
          <Tag key={tag} onRemove={removeTag(tag)}>
            {tag}
          </Tag>
        ))}
      </LegacyStack>
    );
  
    const fileUpload = (
      <DropZone onDrop={setFiles} allowMultiple={type === 'multi_image'}>
        {files.length > 0 ? (
          <LegacyStack>
            {files.map((file, index) => (
              <Thumbnail key={index} size="small" alt={file.name} source={window.URL.createObjectURL(file)} />
            ))}
          </LegacyStack>
        ) : (
          <DropZone.FileUpload actionTitle="Upload Image(s)" />
        )}
      </DropZone>
    );
  
    return (
      <Frame>
        <Page fullWidth>
          <Layout>
            <Layout.Section variant="oneThird">
              <div style={{ marginTop: 'var(--p-space-500)' }}>
                <TextContainer>
                  <Text variant="headingMd" as="h2">
                    Create New Metafield
                  </Text>
                  <Text tone="subdued" as="p">
                    Define namespace, key, type and allowed values for your metafield.
                  </Text>
                </TextContainer>
              </div>
            </Layout.Section>
            <Layout.Section>
              <LegacyCard sectioned>
                <RemixForm method="post" encType="multipart/form-data">
                  <FormLayout>
                    <TextField label="Namespace" name="namespace" value={namespace} disabled autoComplete="off" required />
                    <TextField label="Key" name="key" value={key} onChange={setKey} autoComplete="off" required />
                    <Select
                      label="Type"
                      name="type"
                      options={[
                        { label: 'Single Line Text', value: 'text_single_line' },
                        { label: 'Multi-Line Text', value: 'text_multi_line' },
                        { label: 'Image', value: 'image' },
                        { label: 'Multi-Image', value: 'multi_image' },
                        { label: 'Boolean', value: 'boolean' },
                      ]}
                      value={type}
                      onChange={setType}
                      required
                    />
  
                    {['text_single_line', 'text_multi_line', 'boolean'].includes(type) && (
                      <>
                        <Text>Allowed Values</Text>
                        <div style={{ height: '200px' }}>
                          <Combobox
                            allowMultiple
                            activator={
                              <Combobox.TextField
                                autoComplete="off"
                                label=""
                                labelHidden
                                value={value}
                                suggestion={suggestion}
                                placeholder="Enter value"
                                verticalContent={tagMarkup}
                                onChange={setValue}
                              />
                            }
                          >
                            {listboxMarkup}
                          </Combobox>
                        </div>
                        {tags.map((tag) => (
                          <input key={tag} type="hidden" name="allowedValues[]" value={tag} />
                        ))}
                      </>
                    )}
  
                    {['image', 'multi_image'].includes(type) && (
                      <>
                        <Text>Upload Image{type === 'multi_image' ? 's' : ''}</Text>
                        {fileUpload}
                      </>
                    )}
  
                    <Button submit primary>
                      Create Metafield
                    </Button>
                  </FormLayout>
                </RemixForm>
                {errorMessage && (
                  <Text tone="critical">Error: {errorMessage}</Text>
                )}
              </LegacyCard>
            </Layout.Section>
          </Layout>
          {toastActive && (
            <Toast content="Metafield created successfully!" onDismiss={() => setToastActive(false)} />
          )}
        </Page>
      </Frame>
    );
  }