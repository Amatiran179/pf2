/* global wp, pf2EditorPanels */
(function () {
  if (typeof wp === 'undefined' || !wp.editPost || !wp.plugins) {
    return;
  }

  const { registerPlugin } = wp.plugins;
  const { PluginDocumentSettingPanel } = wp.editPost;
  const { TextControl, TextareaControl, Button } = wp.components;
  const { Fragment, useMemo, useCallback } = wp.element;
  const { useSelect, useDispatch } = wp.data;
  const { __ } = wp.i18n;

  const config = window.pf2EditorPanels || {};
  const postType = config.postType || '';

  const defaults = config.defaults || {};
  const options = config.options || {};
  const labels = config.i18n || {};

  const supported = ['pf2_product', 'pf2_portfolio'];

  if (!supported.includes(postType)) {
    return;
  }

  const useMetaField = (metaKey) => {
    const value = useSelect(
      (select) => {
        const meta = select('core/editor').getEditedPostAttribute('meta') || {};
        return meta[metaKey];
      },
      [metaKey]
    );

    const { editPost } = useDispatch('core/editor');

    const setValue = useCallback(
      (nextValue) => {
        const valueToStore = typeof nextValue === 'string' ? nextValue : '';
        editPost({ meta: { [metaKey]: valueToStore } });
      },
      [editPost, metaKey]
    );

    return [value, setValue];
  };

  const useNumericMetaField = (metaKey) => {
    const rawValue = useSelect(
      (select) => {
        const meta = select('core/editor').getEditedPostAttribute('meta') || {};
        return meta[metaKey];
      },
      [metaKey]
    );

    const { editPost } = useDispatch('core/editor');

    const setValue = useCallback(
      (nextValue) => {
        if (nextValue === '' || nextValue === null || typeof nextValue === 'undefined') {
          editPost({ meta: { [metaKey]: '' } });
          return;
        }

        editPost({ meta: { [metaKey]: nextValue } });
      },
      [editPost, metaKey]
    );

    return [rawValue, setValue];
  };

  const parseCsvIds = (value) => {
    if (!value) {
      return [];
    }

    return String(value)
      .split(',')
      .map((item) => parseInt(item, 10))
      .filter((id, index, array) => Number.isFinite(id) && id > 0 && array.indexOf(id) === index);
  };

  const GalleryControl = ({ metaKey }) => {
    const [value, setValue] = useMetaField(metaKey);
    const ids = useMemo(() => parseCsvIds(value), [value]);

    const attachments = useSelect(
      (select) =>
        ids.map((id) => {
          const record = select('core').getMedia(id);
          return record || null;
        }),
      [ids]
    );

    const openMediaFrame = useCallback(() => {
      if (!wp.media) {
        return;
      }

      const frame = wp.media({
        title: labels.galleryButton || __('Pilih Gambar Galeri', 'pf2'),
        library: { type: 'image' },
        multiple: true,
      });

      frame.on('open', () => {
        const selection = frame.state().get('selection');
        ids.forEach((id) => {
          const attachment = wp.media.attachment(id);
          if (attachment) {
            attachment.fetch();
            selection.add(attachment);
          }
        });
      });

      frame.on('select', () => {
        const selection = frame.state().get('selection');
        const selectedIds = [];

        selection.each((attachment) => {
          const id = attachment.get('id');
          if (id && selectedIds.indexOf(id) === -1) {
            selectedIds.push(id);
          }
        });

        setValue(selectedIds.join(','));
      });

      frame.open();
    }, [ids, labels.galleryButton, setValue]);

    const removeImage = useCallback(
      (removeId) => {
        const filtered = ids.filter((id) => id !== removeId);
        setValue(filtered.join(','));
      },
      [ids, setValue]
    );

    const clearGallery = useCallback(() => {
      setValue('');
    }, [setValue]);

    const galleryItems = ids.length
      ? attachments.map((attachment, index) => {
          const id = ids[index];
          const thumb =
            attachment &&
            attachment.media_details &&
            attachment.media_details.sizes &&
            attachment.media_details.sizes.thumbnail
              ? attachment.media_details.sizes.thumbnail.source_url
              : attachment && attachment.source_url;

          const alt = attachment && attachment.alt_text ? attachment.alt_text : '';

          return wp.element.createElement(
            'li',
            { key: id, className: 'pf2-editor-gallery__item' },
            thumb
              ? wp.element.createElement('img', {
                  src: thumb,
                  alt,
                })
              : null,
            wp.element.createElement(
              Button,
              {
                isSmall: true,
                variant: 'tertiary',
                onClick: () => removeImage(id),
              },
              labels.removeImage || __('Hapus', 'pf2')
            )
          );
        })
      : null;

    return wp.element.createElement(
      'div',
      { className: 'pf2-editor-gallery' },
      wp.element.createElement(
        Button,
        {
          variant: 'secondary',
          onClick: openMediaFrame,
        },
        labels.galleryButton || __('Pilih Gambar Galeri', 'pf2')
      ),
      ids.length === 0
        ? wp.element.createElement(
            'p',
            { className: 'pf2-editor-gallery__empty' },
            labels.galleryEmpty || __('Belum ada gambar yang dipilih.', 'pf2')
          )
        : wp.element.createElement('ul', { className: 'pf2-editor-gallery__list' }, galleryItems),
      ids.length > 0
        ? wp.element.createElement(
            Button,
            {
              isSmall: true,
              variant: 'link',
              onClick: clearGallery,
            },
            labels.clearGallery || __('Kosongkan galeri', 'pf2')
          )
        : null
    );
  };

  const ProductPanel = () => {
    const productDefaults = defaults.product || {};

    const [sku, setSku] = useMetaField('pf2_sku');
    const [material, setMaterial] = useMetaField('pf2_material');
    const [model, setModel] = useMetaField('pf2_model');
    const [color, setColor] = useMetaField('pf2_color');
    const [size, setSize] = useMetaField('pf2_size');
    const [currency, setCurrency] = useMetaField('pf2_currency');
    const [waNumber, setWaNumber] = useMetaField('pf2_wa');
    const [features, setFeatures] = useMetaField('pf2_features');
    const [price, setPrice] = useNumericMetaField('pf2_price');

    const priceValue = price === undefined || price === null ? '' : String(price);

    return wp.element.createElement(
      PluginDocumentSettingPanel,
      {
        name: 'pf2-product-panel',
        title: __('Pengaturan Produk PF2', 'pf2'),
        className: 'pf2-product-panel',
      },
      wp.element.createElement(TextControl, {
        label: __('SKU Produk', 'pf2'),
        help: __('Kosongkan untuk mengisi otomatis saat disimpan.', 'pf2'),
        value: sku || '',
        onChange: (value) => setSku(typeof value === 'string' ? value : ''),
      }),
      wp.element.createElement(TextControl, {
        label: __('Material', 'pf2'),
        value: material || '',
        placeholder: productDefaults.material || '',
        onChange: (value) => setMaterial(typeof value === 'string' ? value : ''),
      }),
      wp.element.createElement(TextControl, {
        label: __('Model', 'pf2'),
        value: model || '',
        placeholder: productDefaults.model || '',
        onChange: (value) => setModel(typeof value === 'string' ? value : ''),
      }),
      wp.element.createElement(TextControl, {
        label: __('Warna', 'pf2'),
        value: color || '',
        placeholder: productDefaults.color || '',
        onChange: (value) => setColor(typeof value === 'string' ? value : ''),
      }),
      wp.element.createElement(TextControl, {
        label: __('Ukuran', 'pf2'),
        value: size || '',
        placeholder: productDefaults.size || '',
        onChange: (value) => setSize(typeof value === 'string' ? value : ''),
      }),
      wp.element.createElement(TextControl, {
        label: __('Harga', 'pf2'),
        type: 'number',
        value: priceValue,
        onChange: (value) => setPrice(typeof value === 'string' ? value : ''),
      }),
      wp.element.createElement(TextControl, {
        label: __('Mata Uang', 'pf2'),
        value: currency || '',
        placeholder: productDefaults.currency || 'IDR',
        onChange: (value) => setCurrency(typeof value === 'string' ? value : ''),
      }),
      wp.element.createElement(TextControl, {
        label: __('Nomor WhatsApp Khusus', 'pf2'),
        value: waNumber || '',
        placeholder: options.phoneWa || '',
        onChange: (value) => setWaNumber(typeof value === 'string' ? value : ''),
      }),
      wp.element.createElement(TextareaControl, {
        label: __('Fitur atau Catatan', 'pf2'),
        value: features || '',
        onChange: (value) => setFeatures(typeof value === 'string' ? value : ''),
      }),
      wp.element.createElement(GalleryControl, { metaKey: 'pf2_gallery_ids' })
    );
  };

  const PortfolioPanel = () => {
    const [client, setClient] = useMetaField('pf2_client');
    const [location, setLocation] = useMetaField('pf2_location');
    const [productName, setProductName] = useMetaField('pf2_product_name');

    return wp.element.createElement(
      PluginDocumentSettingPanel,
      {
        name: 'pf2-portfolio-panel',
        title: __('Pengaturan Portofolio PF2', 'pf2'),
        className: 'pf2-portfolio-panel',
      },
      wp.element.createElement(TextControl, {
        label: __('Nama Klien', 'pf2'),
        value: client || '',
        onChange: (value) => setClient(typeof value === 'string' ? value : ''),
      }),
      wp.element.createElement(TextControl, {
        label: __('Lokasi atau URL Google Maps', 'pf2'),
        value: location || '',
        onChange: (value) => setLocation(typeof value === 'string' ? value : ''),
      }),
      wp.element.createElement(TextControl, {
        label: __('Produk atau Layanan', 'pf2'),
        value: productName || '',
        onChange: (value) => setProductName(typeof value === 'string' ? value : ''),
      }),
      wp.element.createElement(GalleryControl, { metaKey: 'pf2_gallery_ids' })
    );
  };

  const Panels = () =>
    wp.element.createElement(
      Fragment,
      null,
      postType === 'pf2_product' ? wp.element.createElement(ProductPanel, null) : null,
      postType === 'pf2_portfolio' ? wp.element.createElement(PortfolioPanel, null) : null
    );

  registerPlugin('pf2-editor-panels', {
    render: Panels,
  });
})();
