/* global wp, pf2EditorPanels */
(function () {
  if (typeof wp === 'undefined' || !wp.editPost || !wp.plugins) {
    return;
  }

  const { registerPlugin } = wp.plugins;
  const { PluginDocumentSettingPanel } = wp.editPost;
  const { TextControl, TextareaControl } = wp.components;
  const { Fragment, useCallback } = wp.element;
  const { useSelect, useDispatch } = wp.data;
  const { __ } = wp.i18n;

  const config = window.pf2EditorPanels || {};
  const postType = config.postType || '';

  const defaults = config.defaults || {};
  const options = config.options || {};
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
      })
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
      })
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
