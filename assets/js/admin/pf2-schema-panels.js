/* global wp, pf2SchemaPanels */
(function () {
  if (typeof wp === 'undefined') {
    return;
  }

  const { registerPlugin } = wp.plugins || {};
  const { PluginDocumentSettingPanel } = wp.editPost || {};
  const {
    TabPanel,
    ToggleControl,
    TextControl,
    TextareaControl,
    Button,
    SelectControl,
    PanelBody,
    PanelRow,
  } = wp.components || {};
  const element = wp.element || {};
  const { Fragment, useCallback } = element;
  const i18n = wp.i18n || {};
  const translate = typeof i18n.__ === 'function' ? i18n.__ : (text) => text;
  const format = typeof i18n.sprintf === 'function' ? i18n.sprintf : (...parts) => parts.join(' ');
  const { useEntityProp } = wp.coreData || {};
  const blockEditor = wp.blockEditor || wp.editor || {};
  const { MediaUpload, MediaUploadCheck } = blockEditor;
  const { useSelect } = wp.data || {};

  if (!registerPlugin || !PluginDocumentSettingPanel || !TabPanel || !ToggleControl || !useEntityProp) {
    return;
  }

  const config = window.pf2SchemaPanels || {};
  const postType = config.postType || '';
  const supported = ['post', 'page', 'pf2_product', 'pf2_portfolio'];

  if (!supported.includes(postType)) {
    return;
  }

  const labels = config.i18n || {};
  const serviceAreaTypes = Array.isArray(config.serviceAreaTypes) ? config.serviceAreaTypes : [];

  const sanitizeText = (value) => {
    if (typeof value !== 'string') {
      return '';
    }

    return value.trim();
  };

  const sanitizeMultiline = (value) => {
    if (typeof value !== 'string') {
      return '';
    }

    return value.replace(/\r/g, '').trim();
  };

  const normalizeFaqItems = (items) => {
    if (!Array.isArray(items)) {
      return [];
    }

    return items
      .map((item) => {
        const question = sanitizeText(item && typeof item.question === 'string' ? item.question : '');
        const answer = sanitizeMultiline(item && typeof item.answer === 'string' ? item.answer : '');
        return { question, answer };
      })
      .filter((item) => item.question !== '' && item.answer !== '');
  };

  const normalizeHowToSteps = (items) => {
    if (!Array.isArray(items)) {
      return [];
    }

    return items
      .map((item) => {
        const name = sanitizeText(item && typeof item.name === 'string' ? item.name : '');
        const text = sanitizeMultiline(item && typeof item.text === 'string' ? item.text : '');
        const rawImage = item ? item.image_id ?? item.image : undefined;
        const image = parseInt(rawImage, 10);
        const step = { name, text };

        if (Number.isFinite(image) && image > 0) {
          step.image_id = image;
        }

        return step;
      })
      .filter((item) => item.name !== '' || item.text !== '' || item.image_id);
  };

  const normalizeStringArray = (values) => {
    if (!Array.isArray(values)) {
      return [];
    }

    const seen = [];

    values.forEach((value) => {
      if (typeof value !== 'string') {
        return;
      }

      const text = value.trim();
      if (text !== '' && !seen.includes(text)) {
        seen.push(text);
      }
    });

    return seen;
  };

  const sanitizeCsvIds = (value) => {
    let source = value;

    if (Array.isArray(source)) {
      source = source.join(',');
    }

    if (typeof source !== 'string') {
      return '';
    }

    const ids = [];
    source
      .split(',')
      .map((part) => part.trim())
      .forEach((part) => {
        const number = parseInt(part, 10);
        if (Number.isFinite(number) && number > 0 && !ids.includes(number)) {
          ids.push(number);
        }
      });

    return ids.join(',');
  };

  const SchemaExtrasPanel = () => {
    const [meta, setMeta] = useEntityProp('postType', postType, 'meta');
    const safeMeta = meta || {};

    const setMetaValue = useCallback(
      (key, value) => {
        setMeta({
          ...safeMeta,
          [key]: value,
        });
      },
      [safeMeta, setMeta]
    );

    const getMetaValue = useCallback(
      (key, fallback) => {
        if (Object.prototype.hasOwnProperty.call(safeMeta, key)) {
          return safeMeta[key];
        }

        return fallback;
      },
      [safeMeta]
    );

    const mediaAvailable = !!MediaUpload && !!MediaUploadCheck && typeof useSelect === 'function';

    const MediaPicker = ({ value, onChange, selectText, replaceText, clearText, description }) => {
      const parsedValue = parseInt(value, 10) || 0;

      if (!mediaAvailable) {
        return (
          <TextControl
            label={description}
            type="number"
            value={parsedValue > 0 ? parsedValue : ''}
            onChange={(next) => {
              const number = parseInt(next, 10);
              onChange(Number.isFinite(number) && number > 0 ? number : 0);
            }}
          />
        );
      }

      const media = useSelect(
        (select) => {
          if (!parsedValue) {
            return null;
          }

          const core = select('core');
          if (!core || typeof core.getMedia !== 'function') {
            return null;
          }

          return core.getMedia(parsedValue);
        },
        [parsedValue]
      );

      const currentLabel = parsedValue
        ? (media && media.title && (media.title.rendered || media.title.raw || media.title)) || format(translate('ID: %d', 'pf2'), parsedValue)
        : labels.noImage || translate('Belum ada gambar.', 'pf2');

      return (
        <MediaUploadCheck>
          <MediaUpload
            allowedTypes={['image']}
            value={parsedValue}
            onSelect={(item) => onChange(item && item.id ? item.id : 0)}
            render={({ open }) => (
              <div className="pf2-schema-panel__media">
                <Button variant="secondary" onClick={open}>
                  {parsedValue ? replaceText : selectText}
                </Button>
                {parsedValue ? (
                  <Button variant="tertiary" onClick={() => onChange(0)}>
                    {clearText}
                  </Button>
                ) : null}
                <p className="description">{currentLabel}</p>
              </div>
            )}
          />
        </MediaUploadCheck>
      );
    };

    const renderFaqTab = () => {
      const enabled = !!getMetaValue('pf2_schema_faq_enabled', false);
      const items = normalizeFaqItems(getMetaValue('pf2_schema_faq_items', []));

      const updateItems = (nextItems) => {
        setMetaValue('pf2_schema_faq_items', normalizeFaqItems(nextItems));
      };

      const changeItem = (index, field, value) => {
        const next = items.slice();
        const item = { ...next[index] };

        if (value === undefined) {
          delete item[field];
        } else {
          item[field] = value;
        }

        next[index] = item;
        updateItems(next);
      };

      const addItem = () => {
        updateItems([...items, { question: '', answer: '' }]);
      };

      const removeItem = (index) => {
        const next = items.slice();
        next.splice(index, 1);
        updateItems(next);
      };

      return (
        <Fragment>
          <ToggleControl
            label={labels.tabFaq || translate('FAQ', 'pf2')}
            checked={enabled}
            onChange={(nextValue) => setMetaValue('pf2_schema_faq_enabled', !!nextValue)}
          />
          {items.length === 0 ? <p>{labels.emptyFaq || translate('Belum ada FAQ.', 'pf2')}</p> : null}
          {items.map((item, index) => (
            <div className="pf2-schema-panel__group" key={`faq-${index}`}>
              <TextControl
                label={translate('Pertanyaan', 'pf2')}
                value={item.question}
                onChange={(value) => changeItem(index, 'question', sanitizeText(value))}
              />
              <TextareaControl
                label={translate('Jawaban', 'pf2')}
                value={item.answer}
                onChange={(value) => changeItem(index, 'answer', sanitizeMultiline(value))}
              />
              <Button variant="tertiary" onClick={() => removeItem(index)}>
                {labels.removeItem || translate('Hapus', 'pf2')}
              </Button>
            </div>
          ))}
          <Button variant="secondary" onClick={addItem}>
            {labels.addItem || translate('Tambah', 'pf2')}
          </Button>
        </Fragment>
      );
    };

    const renderHowToTab = () => {
      const enabled = !!getMetaValue('pf2_schema_howto_enabled', false);
      const title = sanitizeText(getMetaValue('pf2_schema_howto_name', ''));
      const steps = normalizeHowToSteps(getMetaValue('pf2_schema_howto_steps', []));

      const updateSteps = (nextSteps) => {
        setMetaValue('pf2_schema_howto_steps', normalizeHowToSteps(nextSteps));
      };

      const changeStep = (index, field, value) => {
        const next = steps.slice();
        const step = { ...next[index] };

        if (value === undefined) {
          delete step[field];
        } else {
          step[field] = value;
        }

        next[index] = step;
        updateSteps(next);
      };

      const addStep = () => {
        updateSteps([...steps, { name: '', text: '' }]);
      };

      const removeStep = (index) => {
        const next = steps.slice();
        next.splice(index, 1);
        updateSteps(next);
      };

      return (
        <Fragment>
          <ToggleControl
            label={labels.tabHowTo || translate('HowTo', 'pf2')}
            checked={enabled}
            onChange={(nextValue) => setMetaValue('pf2_schema_howto_enabled', !!nextValue)}
          />
          <TextControl
            label={translate('Judul HowTo', 'pf2')}
            value={title}
            onChange={(value) => setMetaValue('pf2_schema_howto_name', sanitizeText(value))}
          />
          {steps.length === 0 ? <p>{labels.emptySteps || translate('Belum ada langkah HowTo.', 'pf2')}</p> : null}
          {steps.map((step, index) => (
            <div className="pf2-schema-panel__group" key={`step-${index}`}>
              <TextControl
                label={translate('Nama Langkah', 'pf2')}
                value={step.name}
                onChange={(value) => changeStep(index, 'name', sanitizeText(value))}
              />
              <TextareaControl
                label={translate('Deskripsi', 'pf2')}
                value={step.text}
                onChange={(value) => changeStep(index, 'text', sanitizeMultiline(value))}
              />
              <MediaPicker
                value={step.image_id || 0}
                onChange={(nextValue) => changeStep(index, 'image_id', nextValue > 0 ? nextValue : undefined)}
                selectText={labels.selectImage || translate('Pilih gambar', 'pf2')}
                replaceText={labels.replaceImage || translate('Ganti gambar', 'pf2')}
                clearText={labels.clearImage || translate('Hapus gambar', 'pf2')}
                description={translate('ID Gambar', 'pf2')}
              />
              <Button variant="tertiary" onClick={() => removeStep(index)}>
                {labels.removeItem || translate('Hapus', 'pf2')}
              </Button>
            </div>
          ))}
          <Button variant="secondary" onClick={addStep}>
            {labels.addItem || translate('Tambah', 'pf2')}
          </Button>
        </Fragment>
      );
    };

    const renderVideoTab = () => {
      const enabled = !!getMetaValue('pf2_schema_video_enabled', false);
      const url = sanitizeText(getMetaValue('pf2_schema_video_url', ''));
      const name = sanitizeText(getMetaValue('pf2_schema_video_name', ''));
      const description = sanitizeMultiline(getMetaValue('pf2_schema_video_description', ''));
      const thumbnailId = parseInt(getMetaValue('pf2_schema_video_thumbnail_id', 0), 10) || 0;
      const uploadDate = sanitizeText(getMetaValue('pf2_schema_video_upload_date', ''));

      return (
        <Fragment>
          <ToggleControl
            label={labels.tabVideo || translate('Video', 'pf2')}
            checked={enabled}
            onChange={(nextValue) => setMetaValue('pf2_schema_video_enabled', !!nextValue)}
          />
          <TextControl
            label={translate('URL Video', 'pf2')}
            value={url}
            onChange={(value) => setMetaValue('pf2_schema_video_url', sanitizeText(value))}
          />
          <TextControl
            label={translate('Judul Video', 'pf2')}
            value={name}
            onChange={(value) => setMetaValue('pf2_schema_video_name', sanitizeText(value))}
          />
          <TextareaControl
            label={translate('Deskripsi Video', 'pf2')}
            value={description}
            onChange={(value) => setMetaValue('pf2_schema_video_description', sanitizeMultiline(value))}
          />
          <MediaPicker
            value={thumbnailId}
            onChange={(nextValue) => setMetaValue('pf2_schema_video_thumbnail_id', nextValue > 0 ? nextValue : 0)}
            selectText={labels.selectThumbnail || translate('Pilih thumbnail', 'pf2')}
            replaceText={labels.replaceThumbnail || translate('Ganti thumbnail', 'pf2')}
            clearText={labels.clearThumbnail || translate('Hapus thumbnail', 'pf2')}
            description={translate('ID Thumbnail', 'pf2')}
          />
          <TextControl
            label={translate('Tanggal Upload (ISO8601)', 'pf2')}
            value={uploadDate}
            onChange={(value) => setMetaValue('pf2_schema_video_upload_date', sanitizeText(value))}
          />
        </Fragment>
      );
    };

    const renderServiceAreaTab = () => {
      const enabled = !!getMetaValue('pf2_schema_servicearea_enabled', false);
      const type = sanitizeText(getMetaValue('pf2_schema_servicearea_type', ''));
      const values = normalizeStringArray(getMetaValue('pf2_schema_servicearea_values', []));
      const postalRaw = getMetaValue('pf2_schema_servicearea_postal', {});
      const geoRaw = getMetaValue('pf2_schema_servicearea_geo', {});

      const postal = {
        streetAddress: sanitizeText(postalRaw && postalRaw.streetAddress ? postalRaw.streetAddress : ''),
        addressLocality: sanitizeText(postalRaw && postalRaw.addressLocality ? postalRaw.addressLocality : ''),
        addressRegion: sanitizeText(postalRaw && postalRaw.addressRegion ? postalRaw.addressRegion : ''),
        postalCode: sanitizeText(postalRaw && postalRaw.postalCode ? postalRaw.postalCode : ''),
        addressCountry: sanitizeText(postalRaw && postalRaw.addressCountry ? postalRaw.addressCountry : ''),
      };

      const geo = {
        circle: sanitizeText(geoRaw && geoRaw.circle ? geoRaw.circle : ''),
        polygon: sanitizeMultiline(geoRaw && geoRaw.polygon ? geoRaw.polygon : ''),
      };

      return (
        <Fragment>
          <ToggleControl
            label={labels.tabServiceArea || translate('Service Area', 'pf2')}
            checked={enabled}
            onChange={(nextValue) => setMetaValue('pf2_schema_servicearea_enabled', !!nextValue)}
          />
          <SelectControl
            label={translate('Tipe Area', 'pf2')}
            value={type}
            options={serviceAreaTypes}
            onChange={(value) => setMetaValue('pf2_schema_servicearea_type', sanitizeText(value))}
          />
          <TextareaControl
            label={labels.serviceAreaValues || translate('Daftar area (pisahkan baris).', 'pf2')}
            help={translate('Gunakan satu baris per area untuk City/Country/Region.', 'pf2')}
            value={values.join('\n')}
            onChange={(value) => {
              const parts = sanitizeMultiline(value)
                .split(/\n+/)
                .map((line) => line.trim())
                .filter((line) => line !== '');
              setMetaValue('pf2_schema_servicearea_values', normalizeStringArray(parts));
            }}
          />
          <PanelBody title={translate('PostalAddress', 'pf2')} initialOpen={false}>
            <PanelRow>
              <TextControl
                label={translate('Alamat Jalan', 'pf2')}
                value={postal.streetAddress}
                onChange={(value) =>
                  setMetaValue('pf2_schema_servicearea_postal', {
                    ...postal,
                    streetAddress: sanitizeText(value),
                  })
                }
              />
            </PanelRow>
            <PanelRow>
              <TextControl
                label={translate('Kota', 'pf2')}
                value={postal.addressLocality}
                onChange={(value) =>
                  setMetaValue('pf2_schema_servicearea_postal', {
                    ...postal,
                    addressLocality: sanitizeText(value),
                  })
                }
              />
            </PanelRow>
            <PanelRow>
              <TextControl
                label={translate('Provinsi/Region', 'pf2')}
                value={postal.addressRegion}
                onChange={(value) =>
                  setMetaValue('pf2_schema_servicearea_postal', {
                    ...postal,
                    addressRegion: sanitizeText(value),
                  })
                }
              />
            </PanelRow>
            <PanelRow>
              <TextControl
                label={translate('Kode Pos', 'pf2')}
                value={postal.postalCode}
                onChange={(value) =>
                  setMetaValue('pf2_schema_servicearea_postal', {
                    ...postal,
                    postalCode: sanitizeText(value),
                  })
                }
              />
            </PanelRow>
            <PanelRow>
              <TextControl
                label={translate('Negara', 'pf2')}
                value={postal.addressCountry}
                onChange={(value) =>
                  setMetaValue('pf2_schema_servicearea_postal', {
                    ...postal,
                    addressCountry: sanitizeText(value),
                  })
                }
              />
            </PanelRow>
          </PanelBody>
          <PanelBody title={translate('GeoShape', 'pf2')} initialOpen={false}>
            <PanelRow>
              <TextControl
                label={translate('Lingkaran', 'pf2')}
                help={translate('Format: latitude,longitude radius', 'pf2')}
                value={geo.circle}
                onChange={(value) =>
                  setMetaValue('pf2_schema_servicearea_geo', {
                    ...geo,
                    circle: sanitizeText(value),
                  })
                }
              />
            </PanelRow>
            <PanelRow>
              <TextareaControl
                label={translate('Polygon', 'pf2')}
                help={translate('Daftar koordinat dipisahkan spasi.', 'pf2')}
                value={geo.polygon}
                onChange={(value) =>
                  setMetaValue('pf2_schema_servicearea_geo', {
                    ...geo,
                    polygon: sanitizeMultiline(value),
                  })
                }
              />
            </PanelRow>
          </PanelBody>
        </Fragment>
      );
    };

    const renderTouristTab = () => {
      const enabled = !!getMetaValue('pf2_schema_touristattraction_enabled', false);
      const name = sanitizeText(getMetaValue('pf2_schema_touristattraction_name', ''));
      const description = sanitizeMultiline(getMetaValue('pf2_schema_touristattraction_description', ''));
      const imageCsv = sanitizeCsvIds(getMetaValue('pf2_schema_touristattraction_image_ids', ''));
      const geoRaw = getMetaValue('pf2_schema_touristattraction_geo', {});
      const latitude = sanitizeText(geoRaw && geoRaw.latitude ? geoRaw.latitude : '');
      const longitude = sanitizeText(geoRaw && geoRaw.longitude ? geoRaw.longitude : '');

      return (
        <Fragment>
          <ToggleControl
            label={labels.tabTourist || translate('Tourist Attraction', 'pf2')}
            checked={enabled}
            onChange={(nextValue) => setMetaValue('pf2_schema_touristattraction_enabled', !!nextValue)}
          />
          <TextControl
            label={translate('Nama', 'pf2')}
            value={name}
            onChange={(value) => setMetaValue('pf2_schema_touristattraction_name', sanitizeText(value))}
          />
          <TextareaControl
            label={translate('Deskripsi', 'pf2')}
            value={description}
            onChange={(value) => setMetaValue('pf2_schema_touristattraction_description', sanitizeMultiline(value))}
          />
          <TextControl
            label={translate('ID Gambar (pisahkan koma)', 'pf2')}
            value={imageCsv}
            onChange={(value) => setMetaValue('pf2_schema_touristattraction_image_ids', sanitizeCsvIds(value))}
          />
          <PanelBody title={translate('Koordinat', 'pf2')} initialOpen={false}>
            <PanelRow>
              <TextControl
                label={translate('Latitude', 'pf2')}
                value={latitude}
                onChange={(value) =>
                  setMetaValue('pf2_schema_touristattraction_geo', {
                    longitude,
                    latitude: sanitizeText(value),
                  })
                }
              />
            </PanelRow>
            <PanelRow>
              <TextControl
                label={translate('Longitude', 'pf2')}
                value={longitude}
                onChange={(value) =>
                  setMetaValue('pf2_schema_touristattraction_geo', {
                    latitude,
                    longitude: sanitizeText(value),
                  })
                }
              />
            </PanelRow>
          </PanelBody>
        </Fragment>
      );
    };

    return (
      <PluginDocumentSettingPanel
        name="pf2-schema-extras"
        title={labels.panelTitle || translate('PF2 Schema', 'pf2')}
        initialOpen={false}
      >
        <TabPanel
          className="pf2-schema-panel"
          activeClass="is-active"
          tabs={[
            { name: 'faq', title: labels.tabFaq || translate('FAQ', 'pf2') },
            { name: 'howto', title: labels.tabHowTo || translate('HowTo', 'pf2') },
            { name: 'video', title: labels.tabVideo || translate('Video', 'pf2') },
            { name: 'servicearea', title: labels.tabServiceArea || translate('Service Area', 'pf2') },
            { name: 'tourist', title: labels.tabTourist || translate('Tourist Attraction', 'pf2') },
          ]}
        >
          {(tab) => {
            switch (tab.name) {
              case 'faq':
                return renderFaqTab();
              case 'howto':
                return renderHowToTab();
              case 'video':
                return renderVideoTab();
              case 'servicearea':
                return renderServiceAreaTab();
              case 'tourist':
                return renderTouristTab();
              default:
                return null;
            }
          }}
        </TabPanel>
      </PluginDocumentSettingPanel>
    );
  };

  registerPlugin('pf2-schema-panels', {
    render: SchemaExtrasPanel,
    icon: null,
  });
})();
