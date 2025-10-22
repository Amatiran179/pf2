/* global wp, pf2GalleryPanel */
(function () {
  if (typeof window === 'undefined') {
    return;
  }

  const config = window.pf2GalleryPanel || {};
  const postType = config.postType || '';
  const metaKey = config.metaKey || 'pf2_gallery_ids';
  const labels = config.labels || {};
  const allowedPostTypes = ['pf2_product', 'pf2_portfolio'];

  if (!allowedPostTypes.includes(postType)) {
    return;
  }

  const wpGlobal = window.wp || {};
  const i18n = wpGlobal.i18n || {};
  const __ = typeof i18n.__ === 'function' ? i18n.__ : (text) => text;

  const getLabel = (key, fallback, domain = 'pf2') => {
    if (labels[key]) {
      return labels[key];
    }

    return typeof __ === 'function' ? __(fallback, domain) : fallback;
  };

  const uniqueIds = (ids) => {
    const seen = new Set();
    const result = [];

    ids.forEach((value) => {
      const id = parseInt(value, 10);
      if (!Number.isFinite(id) || id <= 0 || seen.has(id)) {
        return;
      }

      seen.add(id);
      result.push(id);
    });

    return result;
  };

  const parseCsv = (value) => {
    if (!value) {
      return [];
    }

    return uniqueIds(String(value).split(','));
  };

  const toCsv = (ids) => (ids.length ? ids.join(',') : '');

  const extractAttachmentMeta = (record) => {
    if (!record) {
      return { id: 0, thumb: '', alt: '' };
    }

    const data = typeof record.toJSON === 'function' ? record.toJSON() : record;
    const id = typeof data.id === 'number' ? data.id : parseInt(data.id, 10) || 0;

    const details = data.media_details || {};
    const sizes = details.sizes || data.sizes || {};

    const thumbCandidate =
      (sizes.thumbnail && (sizes.thumbnail.source_url || sizes.thumbnail.url)) ||
      (sizes.medium && (sizes.medium.source_url || sizes.medium.url)) ||
      data.source_url ||
      data.url ||
      '';

    const altCandidate =
      data.alt ||
      data.alt_text ||
      (data.title && data.title.rendered ? data.title.rendered : data.title) ||
      '';

    return {
      id,
      thumb: thumbCandidate || '',
      alt: altCandidate || '',
    };
  };

  const openMediaFrame = (currentIds, onSelect) => {
    if (!wpGlobal.media || typeof wpGlobal.media !== 'function' || typeof wpGlobal.media.attachment !== 'function') {
      return;
    }

    const frame = wpGlobal.media({
      title: getLabel('modalTitle', 'Pilih gambar untuk galeri PF2'),
      library: { type: 'image' },
      multiple: true,
    });

    frame.on('open', () => {
      const selection = frame.state().get('selection');

      currentIds.forEach((id) => {
        const attachment = wpGlobal.media.attachment(id);

        if (attachment) {
          attachment.fetch();
          selection.add(attachment);
        }
      });
    });

    frame.on('select', () => {
      const selection = frame.state().get('selection');
      const selectedIds = [];
      const records = [];

      selection.each((attachment) => {
        const id = attachment.get('id');
        if (id && selectedIds.indexOf(id) === -1) {
          selectedIds.push(id);
          records.push(attachment);
        }
      });

      if (typeof onSelect === 'function') {
        onSelect(uniqueIds(selectedIds), records);
      }
    });

    frame.open();
  };

  const initGutenbergPanel = () => {
    if (!wpGlobal.editPost || !wpGlobal.plugins || !wpGlobal.element || !wpGlobal.components || !wpGlobal.data) {
      return;
    }

    const { registerPlugin } = wpGlobal.plugins;
    const { PluginDocumentSettingPanel } = wpGlobal.editPost;
    const { Button, Spinner } = wpGlobal.components;
    const { useCallback, useMemo, useRef } = wpGlobal.element;
    const { useSelect, useDispatch } = wpGlobal.data;

    const useMetaField = (key) => {
      const value = useSelect(
        (select) => {
          const meta = select('core/editor').getEditedPostAttribute('meta') || {};
          return meta[key] || '';
        },
        [key]
      );

      const { editPost } = useDispatch('core/editor');

      const setValue = useCallback(
        (nextValue) => {
          if (!nextValue) {
            editPost({ meta: { [key]: '' } });
            return;
          }

          editPost({ meta: { [key]: String(nextValue) } });
        },
        [editPost, key]
      );

      return [value, setValue];
    };

    const GalleryPanel = () => {
      const [value, setValue] = useMetaField(metaKey);
      const ids = useMemo(() => parseCsv(value), [value]);

      const attachments = useSelect(
        (select) => ids.map((id) => select('core').getMedia(id)),
        [ids]
      );

      const updateIds = useCallback(
        (nextIds) => {
          const unique = uniqueIds(nextIds);
          setValue(unique.length ? unique.join(',') : '');
        },
        [setValue]
      );

      const handleOpen = useCallback(() => {
        openMediaFrame(ids, (selectedIds) => {
          updateIds(selectedIds);
        });
      }, [ids, updateIds]);

      const handleClear = useCallback(() => {
        updateIds([]);
      }, [updateIds]);

      const handleRemove = useCallback(
        (removeId) => {
          updateIds(ids.filter((id) => id !== removeId));
        },
        [ids, updateIds]
      );

      const dragSource = useRef(-1);

      const handleDragStart = useCallback((event, index) => {
        dragSource.current = index;
        if (event.dataTransfer) {
          event.dataTransfer.effectAllowed = 'move';
          event.dataTransfer.setData('text/plain', String(index));
        }
        event.currentTarget.classList.add('is-dragging');
      }, []);

      const handleDragEnd = useCallback((event) => {
        event.currentTarget.classList.remove('is-dragging');
        dragSource.current = -1;
      }, []);

      const handleDragOver = useCallback((event) => {
        event.preventDefault();
        if (event.dataTransfer) {
          event.dataTransfer.dropEffect = 'move';
        }
      }, []);

      const handleDrop = useCallback(
        (event, index) => {
          event.preventDefault();

          let fromIndex = dragSource.current;

          if (event.dataTransfer) {
            const payload = event.dataTransfer.getData('text/plain');
            const parsed = parseInt(payload, 10);

            if (Number.isInteger(parsed)) {
              fromIndex = parsed;
            }
          }

          if (!Number.isInteger(fromIndex) || fromIndex < 0 || fromIndex >= ids.length || fromIndex === index) {
            return;
          }

          const next = ids.slice();
          const [moved] = next.splice(fromIndex, 1);
          next.splice(index, 0, moved);
          updateIds(next);
        },
        [ids, updateIds]
      );

      const galleryItems = ids.map((id, index) => {
        const record = attachments[index];
        const meta = extractAttachmentMeta(record);
        const thumb = meta.thumb;
        const alt = meta.alt || getLabel('noPreview', 'Tidak ada pratinjau');

        let preview;

        if (!record) {
          preview = wpGlobal.element.createElement(Spinner, {
            key: `spinner-${id}`,
            className: 'pf2-gallery-preview__spinner',
          });
        } else if (thumb) {
          preview = wpGlobal.element.createElement('img', {
            src: thumb,
            alt,
            className: 'pf2-gallery-preview__thumbnail',
          });
        } else {
          preview = wpGlobal.element.createElement(
            'span',
            { className: 'pf2-gallery-preview__placeholder' },
            getLabel('noPreview', 'Tidak ada pratinjau')
          );
        }

        return wpGlobal.element.createElement(
          'li',
          {
            key: id,
            className: 'pf2-gallery-preview__item',
            draggable: true,
            onDragStart: (event) => handleDragStart(event, index),
            onDragEnd: handleDragEnd,
            onDragOver: handleDragOver,
            onDrop: (event) => handleDrop(event, index),
          },
          wpGlobal.element.createElement('span', { className: 'pf2-gallery-preview__handle', 'aria-hidden': 'true' }, '⇅'),
          preview,
          wpGlobal.element.createElement(
            Button,
            {
              className: 'pf2-gallery-preview__remove',
              isSmall: true,
              variant: 'tertiary',
              onClick: () => handleRemove(id),
            },
            getLabel('removeImage', 'Hapus')
          )
        );
      });

      return wpGlobal.element.createElement(
        PluginDocumentSettingPanel,
        {
          name: 'pf2-gallery-panel',
          title: getLabel('panelTitle', 'PF2 Gallery'),
          className: 'pf2-gallery-panel',
        },
        wpGlobal.element.createElement(
          'div',
          { className: 'pf2-gallery-panel__actions' },
          wpGlobal.element.createElement(
            Button,
            { variant: 'secondary', onClick: handleOpen },
            getLabel('addImages', 'Upload/Select Images')
          ),
          ids.length > 0
            ? wpGlobal.element.createElement(
                Button,
                {
                  className: 'pf2-gallery-panel__clear',
                  isSmall: true,
                  variant: 'link',
                  onClick: handleClear,
                },
                getLabel('clearGallery', 'Kosongkan galeri')
              )
            : null
        ),
        ids.length === 0
          ? wpGlobal.element.createElement(
              'p',
              { className: 'pf2-gallery-panel__empty' },
              getLabel('emptyText', 'Belum ada gambar yang dipilih.')
            )
          : wpGlobal.element.createElement('ul', { className: 'pf2-gallery-preview' }, galleryItems),
        wpGlobal.element.createElement(
          'p',
          { className: 'pf2-gallery-panel__hint' },
          getLabel('dragHint', 'Tarik untuk mengurutkan ulang.')
        )
      );
    };

    registerPlugin('pf2-gallery-panel', { render: GalleryPanel });
  };

  const initClassicMetaboxes = () => {
    const containers = document.querySelectorAll('[data-pf2-gallery-metabox]');

    if (!containers.length) {
      return;
    }

    containers.forEach((container) => {
      const field = container.querySelector('.pf2-gallery-metabox__field');
      const list = container.querySelector('.pf2-gallery-metabox__list');
      const emptyState = container.querySelector('.pf2-gallery-metabox__empty');
      const addButton = container.querySelector('.pf2-gallery-metabox__add');
      const clearButton = container.querySelector('.pf2-gallery-metabox__clear');

      if (!field || !list || !addButton) {
        return;
      }

      let ids = parseCsv(field.value);
      const metadata = new Map();

      const refreshVisibility = () => {
        if (emptyState) {
          emptyState.hidden = ids.length > 0;
        }

        if (list) {
          list.hidden = ids.length === 0;
        }

        if (clearButton) {
          clearButton.disabled = ids.length === 0;
        }

        container.setAttribute('data-gallery-ids', toCsv(ids));
      };

      const render = () => {
        list.innerHTML = '';

        ids.forEach((id, index) => {
          const meta = metadata.get(id) || { thumb: '', alt: '' };
          const item = document.createElement('li');
          item.className = 'pf2-gallery-preview__item';
          item.setAttribute('draggable', 'true');
          item.dataset.id = String(id);
          item.dataset.thumb = meta.thumb || '';
          item.dataset.alt = meta.alt || '';

          const handle = document.createElement('span');
          handle.className = 'pf2-gallery-preview__handle';
          handle.setAttribute('aria-hidden', 'true');
          handle.textContent = '⇅';
          item.appendChild(handle);

          if (meta.thumb) {
            const img = document.createElement('img');
            img.src = meta.thumb;
            img.alt = meta.alt || getLabel('noPreview', 'Tidak ada pratinjau');
            img.className = 'pf2-gallery-preview__thumbnail';
            item.appendChild(img);
          } else {
            const placeholder = document.createElement('span');
            placeholder.className = 'pf2-gallery-preview__placeholder';
            placeholder.textContent = getLabel('noPreview', 'Tidak ada pratinjau');
            item.appendChild(placeholder);
          }

          const remove = document.createElement('button');
          remove.type = 'button';
          remove.className = 'button-link pf2-gallery-preview__remove';
          remove.textContent = getLabel('removeImage', 'Hapus');
          remove.addEventListener('click', (event) => {
            event.preventDefault();
            ids = ids.filter((value) => value !== id);
            field.value = toCsv(ids);
            refreshVisibility();
            render();
          });
          item.appendChild(remove);

          item.addEventListener('dragstart', (event) => {
            if (event.dataTransfer) {
              event.dataTransfer.effectAllowed = 'move';
              event.dataTransfer.setData('text/plain', String(index));
            }
            item.classList.add('is-dragging');
          });

          item.addEventListener('dragend', () => {
            item.classList.remove('is-dragging');
          });

          item.addEventListener('dragover', (event) => {
            event.preventDefault();
            if (event.dataTransfer) {
              event.dataTransfer.dropEffect = 'move';
            }
          });

          item.addEventListener('drop', (event) => {
            event.preventDefault();

            if (!event.dataTransfer) {
              return;
            }

            const from = parseInt(event.dataTransfer.getData('text/plain'), 10);

            if (!Number.isInteger(from) || from === index || from < 0 || from >= ids.length) {
              return;
            }

            const next = ids.slice();
            const [moved] = next.splice(from, 1);
            next.splice(index, 0, moved);
            ids = next;
            field.value = toCsv(ids);
            refreshVisibility();
            render();
          });

          list.appendChild(item);
        });

        refreshVisibility();
      };

      const ensureMetadata = (id) => {
        if (metadata.has(id) || !wpGlobal.media || typeof wpGlobal.media.attachment !== 'function') {
          return;
        }

        const attachment = wpGlobal.media.attachment(id);

        if (!attachment) {
          return;
        }

        const applyMeta = () => {
          const meta = extractAttachmentMeta(attachment);
          if (meta.id) {
            metadata.set(id, { thumb: meta.thumb, alt: meta.alt });
            window.requestAnimationFrame(render);
          }
        };

        const immediate = extractAttachmentMeta(attachment);

        if (immediate.id && (immediate.thumb || immediate.alt)) {
          metadata.set(id, { thumb: immediate.thumb, alt: immediate.alt });
        }

        attachment.fetch();
        attachment.once('change', applyMeta);
      };

      const setIds = (nextIds) => {
        ids = uniqueIds(nextIds);
        field.value = toCsv(ids);
        refreshVisibility();
        render();
        ids.forEach((id) => ensureMetadata(id));
      };

      const initialiseFromDom = () => {
        const existingItems = Array.from(list.querySelectorAll('[data-id]'));

        if (!existingItems.length) {
          return;
        }

        existingItems.forEach((node) => {
          const id = parseInt(node.getAttribute('data-id') || '', 10);
          if (!Number.isFinite(id) || id <= 0) {
            return;
          }

          const thumb = node.getAttribute('data-thumb') || '';
          const alt = node.getAttribute('data-alt') || '';
          metadata.set(id, { thumb, alt });
        });
      };

      initialiseFromDom();
      render();
      ids.forEach((id) => ensureMetadata(id));

      addButton.addEventListener('click', (event) => {
        event.preventDefault();

        openMediaFrame(ids, (selectedIds, records) => {
          if (Array.isArray(records)) {
            records.forEach((record) => {
              const meta = extractAttachmentMeta(record);
              if (meta.id) {
                metadata.set(meta.id, { thumb: meta.thumb, alt: meta.alt });
              }
            });
          }

          setIds(selectedIds);
        });
      });

      if (clearButton) {
        clearButton.addEventListener('click', (event) => {
          event.preventDefault();
          metadata.clear();
          setIds([]);
        });
      }
    });
  };

  if (wpGlobal.editPost && wpGlobal.plugins) {
    initGutenbergPanel();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initClassicMetaboxes);
  } else {
    initClassicMetaboxes();
  }
})();
