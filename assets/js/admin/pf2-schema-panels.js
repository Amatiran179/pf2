/* global wp, pf2SchemaPanels */
(function () {
  if (typeof wp === 'undefined') {
    return;
  }

  const { registerPlugin } = wp.plugins || {};
  const { PluginDocumentSettingPanel } = wp.editPost || {};
  const { TabPanel, ToggleControl, TextControl, TextareaControl, Button, SelectControl, PanelBody, PanelRow } =
    wp.components || {};
  const { Fragment, useCallback } = wp.element || {};
  const { __ } = wp.i18n || {};
  const { useEntityProp } = wp.coreData || {};

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
        const image = parseInt(item && item.image ? item.image : 0, 10);

        const step = { name, text };

        if (Number.isFinite(image) && image > 0) {
          step.image = image;
        }

        return step;
      })
      .filter((item) => item.name !== '' || item.text !== '' || item.image);
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

  const normalizeIntArray = (values) => {
    if (!Array.isArray(values)) {
      return [];
    }

    const seen = [];

    values.forEach((value) => {
      const number = parseInt(value, 10);
      if (Number.isFinite(number) && number > 0 && !seen.includes(number)) {
        seen.push(number);
      }
    });

    return seen;
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

    const renderFaqTab = () => {
      const enabled = !!getMetaValue('pf2_schema_faq_enabled', false);
      const items = normalizeFaqItems(getMetaValue('pf2_schema_faq_items', []));

      const updateItems = (nextItems) => {
        setMetaValue('pf2_schema_faq_items', normalizeFaqItems(nextItems));
      };

      const changeItem = (index, field, value) => {
        const next = items.slice();
        const item = { ...next[index], [field]: value };
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
            label={labels.tabFaq || __('FAQ', 'pf2')}
            checked={enabled}
            onChange={(nextValue) => setMetaValue('pf2_schema_faq_enabled', !!nextValue)}
          />
          {items.length === 0 ? <p>{labels.emptyFaq || __('Belum ada FAQ.', 'pf2')}</p> : null}
          {items.map((item, index) => (
            <div className="pf2-schema-panel__group" key={`faq-${index}`}>
              <TextControl
                label={__('Pertanyaan', 'pf2')}
                value={item.question}
                onChange={(value) => changeItem(index, 'question', sanitizeText(value))}
              />
              <TextareaControl
                label={__('Jawaban', 'pf2')}
                value={item.answer}
                onChange={(value) => changeItem(index, 'answer', sanitizeMultiline(value))}
              />
              <Button variant="tertiary" onClick={() => removeItem(index)}>
                {labels.removeItem || __('Hapus', 'pf2')}
              </Button>
            </div>
          ))}
          <Button variant="secondary" onClick={addItem}>
            {labels.addItem || __('Tambah', 'pf2')}
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
        const step = { ...next[index], [field]: value };
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
            label={labels.tabHowTo || __('HowTo', 'pf2')}
            checked={enabled}
            onChange={(nextValue) => setMetaValue('pf2_schema_howto_enabled', !!nextValue)}
          />
          <TextControl
            label={__('Judul HowTo', 'pf2')}
            value={title}
            onChange={(value) => setMetaValue('pf2_schema_howto_name', sanitizeText(value))}
          />
          {steps.length === 0 ? <p>{labels.emptySteps || __('Belum ada langkah HowTo.', 'pf2')}</p> : null}
          {steps.map((step, index) => (
            <div className="pf2-schema-panel__group" key={`step-${index}`}>
              <TextControl
                label={__('Nama Langkah', 'pf2')}
                value={step.name}
                onChange={(value) => changeStep(index, 'name', sanitizeText(value))}
              />
              <TextareaControl
                label={__('Deskripsi', 'pf2')}
                value={step.text}
                onChange={(value) => changeStep(index, 'text', sanitizeMultiline(value))}
              />
              <TextControl
                label={__('ID Gambar (opsional)', 'pf2')}
                type="number"
                value={step.image || ''}
                onChange={(value) => {
                  const number = parseInt(value, 10);
                  changeStep(index, 'image', Number.isFinite(number) && number > 0 ? number : undefined);
                }}
              />
              <Button variant="tertiary" onClick={() => removeStep(index)}>
                {labels.removeItem || __('Hapus', 'pf2')}
              </Button>
            </div>
          ))}
          <Button variant="secondary" onClick={addStep}>
            {labels.addItem || __('Tambah', 'pf2')}
          </Button>
        </Fragment>
      );
    };

    const renderVideoTab = () => {
      const enabled = !!getMetaValue('pf2_schema_video_enabled', false);
      const url = sanitizeText(getMetaValue('pf2_schema_video_url', ''));
      const name = sanitizeText(getMetaValue('pf2_schema_video_name', ''));
      const description = sanitizeMultiline(getMetaValue('pf2_schema_video_description', ''));
      const thumbnail = sanitizeText(getMetaValue('pf2_schema_video_thumbnail', ''));
      const uploadDate = sanitizeText(getMetaValue('pf2_schema_video_upload_date', ''));

      return (
        <Fragment>
          <ToggleControl
            label={labels.tabVideo || __('Video', 'pf2')}
            checked={enabled}
            onChange={(nextValue) => setMetaValue('pf2_schema_video_enabled', !!nextValue)}
          />
          <TextControl
            label={__('URL Video', 'pf2')}
            value={url}
            onChange={(value) => setMetaValue('pf2_schema_video_url', sanitizeText(value))}
          />
          <TextControl
            label={__('Judul Video', 'pf2')}
            value={name}
            onChange={(value) => setMetaValue('pf2_schema_video_name', sanitizeText(value))}
          />
          <TextareaControl
            label={__('Deskripsi Video', 'pf2')}
            value={description}
            onChange={(value) => setMetaValue('pf2_schema_video_description', sanitizeMultiline(value))}
          />
          <TextControl
            label={__('Thumbnail URL', 'pf2')}
            value={thumbnail}
            onChange={(value) => setMetaValue('pf2_schema_video_thumbnail', sanitizeText(value))}
          />
          <TextControl
            label={__('Tanggal Upload (ISO8601)', 'pf2')}
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
            label={labels.tabServiceArea || __('Service Area', 'pf2')}
            checked={enabled}
            onChange={(nextValue) => setMetaValue('pf2_schema_servicearea_enabled', !!nextValue)}
          />
          <SelectControl
            label={__('Tipe Area', 'pf2')}
            value={type}
            options={serviceAreaTypes}
            onChange={(value) => setMetaValue('pf2_schema_servicearea_type', sanitizeText(value))}
          />
          <TextareaControl
            label={labels.serviceAreaValues || __('Daftar area (pisahkan baris).', 'pf2')}
            help={__('Gunakan satu baris per area untuk City/Country/Region.', 'pf2')}
            value={values.join('\n')}
            onChange={(value) => {
              const parts = sanitizeMultiline(value)
                .split(/\n+/)
                .map((line) => line.trim())
                .filter((line) => line !== '');
              setMetaValue('pf2_schema_servicearea_values', normalizeStringArray(parts));
            }}
          />
          <PanelBody title={__('PostalAddress', 'pf2')} initialOpen={false}>
            <PanelRow>
              <TextControl
                label={__('Alamat Jalan', 'pf2')}
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
                label={__('Kota', 'pf2')}
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
                label={__('Provinsi/Region', 'pf2')}
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
                label={__('Kode Pos', 'pf2')}
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
                label={__('Negara', 'pf2')}
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
          <PanelBody title={__('GeoShape', 'pf2')} initialOpen={false}>
            <PanelRow>
              <TextControl
                label={__('Lingkaran', 'pf2')}
                help={__('Format: latitude,longitude radius', 'pf2')}
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
                label={__('Polygon', 'pf2')}
                help={__('Daftar koordinat dipisahkan spasi.', 'pf2')}
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
      const images = normalizeIntArray(getMetaValue('pf2_schema_touristattraction_images', []));
      const geoRaw = getMetaValue('pf2_schema_touristattraction_geo', {});
      const latitude = sanitizeText(geoRaw && geoRaw.latitude ? geoRaw.latitude : '');
      const longitude = sanitizeText(geoRaw && geoRaw.longitude ? geoRaw.longitude : '');

      return (
        <Fragment>
          <ToggleControl
            label={labels.tabTourist || __('Tourist Attraction', 'pf2')}
            checked={enabled}
            onChange={(nextValue) => setMetaValue('pf2_schema_touristattraction_enabled', !!nextValue)}
          />
          <TextControl
            label={__('Nama', 'pf2')}
            value={name}
            onChange={(value) => setMetaValue('pf2_schema_touristattraction_name', sanitizeText(value))}
          />
          <TextareaControl
            label={__('Deskripsi', 'pf2')}
            value={description}
            onChange={(value) => setMetaValue('pf2_schema_touristattraction_description', sanitizeMultiline(value))}
          />
          <TextControl
            label={__('ID Gambar (pisahkan koma)', 'pf2')}
            value={images.join(',')}
            onChange={(value) => {
              const parts = sanitizeText(value)
                .split(',')
                .map((part) => part.trim())
                .filter((part) => part !== '');
              setMetaValue('pf2_schema_touristattraction_images', normalizeIntArray(parts));
            }}
          />
          <PanelBody title={__('Koordinat', 'pf2')} initialOpen={false}>
            <PanelRow>
              <TextControl
                label={__('Latitude', 'pf2')}
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
                label={__('Longitude', 'pf2')}
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
        title={labels.panelTitle || __('Schema Extras', 'pf2')}
        initialOpen={false}
      >
        <TabPanel
          className="pf2-schema-panel"
          activeClass="is-active"
          tabs={[
            { name: 'faq', title: labels.tabFaq || __('FAQ', 'pf2') },
            { name: 'howto', title: labels.tabHowTo || __('HowTo', 'pf2') },
            { name: 'video', title: labels.tabVideo || __('Video', 'pf2') },
            { name: 'servicearea', title: labels.tabServiceArea || __('Service Area', 'pf2') },
            { name: 'tourist', title: labels.tabTourist || __('Tourist Attraction', 'pf2') },
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
