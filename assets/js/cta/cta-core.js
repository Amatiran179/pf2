// CTA core interactions: tracking, modal orchestration, and data binding.
import { $, $$ } from '../utils/dom.js';

const CLICK_EVENT = 'pf2:cta_click';
const STORAGE_KEY = 'pf2_cta_events';
const focusableSelector = 'a[href], button:not([disabled]), textarea, input, select, [tabindex]:not([tabindex="-1"])';

let activeModalId = null;
let lastFocusedElement = null;

const escapeSelector = (value) => {
  if (!value) {
    return '';
  }

  if (window.CSS && typeof window.CSS.escape === 'function') {
    return window.CSS.escape(value);
  }

  return value.replace(/([#.;?+*~\':"!^$\[\]()=>|\/\\])/g, '\\$1');
};

const getModalElement = (id) => {
  if (!id) {
    return null;
  }

  return document.querySelector(`[data-pf2-cta-modal="${escapeSelector(id)}"]`);
};

const getModalDialog = (modal) => (modal ? modal.querySelector('[data-pf2-cta-modal-dialog]') : null);

const readStoredEvents = () => {
  try {
    const stored = window.localStorage ? window.localStorage.getItem(STORAGE_KEY) : null;

    if (!stored) {
      return [];
    }

    const parsed = JSON.parse(stored);
    return Array.isArray(parsed) ? parsed : [];
  } catch (error) {
    return [];
  }
};

const writeStoredEvents = (events) => {
  try {
    if (window.localStorage) {
      window.localStorage.setItem(STORAGE_KEY, JSON.stringify(events));
    }
  } catch (error) {
    // Storage can fail (Safari private mode, etc.). Ignore silently.
  }
};

const persistEvent = (detail) => {
  const events = readStoredEvents();
  events.push(detail);

  if (events.length > 50) {
    events.splice(0, events.length - 50);
  }

  writeStoredEvents(events);
};

const buildWhatsAppUrl = (phone, text) => {
  const sanitizedPhone = (phone || '').replace(/[^0-9]/g, '');
  const encodedMessage = text ? `?text=${encodeURIComponent(text)}` : '';
  return `https://wa.me/${sanitizedPhone}${encodedMessage}`;
};

const parseContext = (value) => {
  if (!value) {
    return null;
  }

  try {
    const parsed = JSON.parse(value);
    return parsed && typeof parsed === 'object' ? parsed : null;
  } catch (error) {
    return null;
  }
};

const dispatchClickEvent = (detail) => {
  window.dispatchEvent(new CustomEvent(CLICK_EVENT, { detail }));

  if (window?.wp?.hooks?.doAction) {
    window.wp.hooks.doAction('pf2_cta_clicked', detail);
  }
};

const openCtaModal = (id) => {
  if (!id) {
    return;
  }

  const modal = getModalElement(id);

  if (!modal) {
    return;
  }

  activeModalId = id;
  lastFocusedElement = document.activeElement instanceof HTMLElement ? document.activeElement : null;

  modal.removeAttribute('hidden');
  modal.setAttribute('aria-hidden', 'false');
  modal.classList.add('is-open');

  const dialog = getModalDialog(modal);
  if (dialog) {
    dialog.focus({ preventScroll: true });
  }

  document.addEventListener('keydown', handleModalKeydown);
};

const closeCtaModal = (id = activeModalId) => {
  if (!id) {
    return;
  }

  const modal = getModalElement(id);

  if (!modal) {
    return;
  }

  modal.setAttribute('aria-hidden', 'true');
  modal.classList.remove('is-open');
  modal.setAttribute('hidden', 'hidden');

  if (activeModalId === id) {
    activeModalId = null;
  }

  if (!activeModalId) {
    document.removeEventListener('keydown', handleModalKeydown);
  }

  if (lastFocusedElement && typeof lastFocusedElement.focus === 'function') {
    lastFocusedElement.focus({ preventScroll: true });
  }

  lastFocusedElement = null;
};

const handleModalKeydown = (event) => {
  if (!activeModalId) {
    return;
  }

  if (event.key === 'Escape') {
    event.preventDefault();
    closeCtaModal(activeModalId);
    return;
  }

  if (event.key !== 'Tab') {
    return;
  }

  const modal = getModalElement(activeModalId);

  if (!modal) {
    return;
  }

  const focusable = $$(focusableSelector, modal);

  if (!focusable.length) {
    event.preventDefault();
    return;
  }

  const first = focusable[0];
  const last = focusable[focusable.length - 1];
  const isShift = event.shiftKey;
  const active = document.activeElement;

  if (!isShift && active === last) {
    event.preventDefault();
    first.focus();
  } else if (isShift && active === first) {
    event.preventDefault();
    last.focus();
  }
};

const handleCtaClick = (event) => {
  const target = event.target.closest('[data-pf2-cta]');

  if (!target) {
    return;
  }

  const { pf2Cta: type = 'inline', pf2CtaPhone: phone = '', pf2CtaText, pf2CtaPost, pf2CtaContext } = target.dataset;
  const context = parseContext(pf2CtaContext);
  const linkText = pf2CtaText || target.textContent.trim();
  const url = buildWhatsAppUrl(phone, linkText);
  const timestamp = new Date().toISOString();
  const postId = pf2CtaPost ? parseInt(pf2CtaPost, 10) : null;

  const detail = {
    type,
    phone,
    text: linkText,
    url,
    timestamp,
    postId: Number.isInteger(postId) ? postId : null,
    context,
  };

  persistEvent(detail);
  dispatchClickEvent(detail);

  if (target.tagName === 'A') {
    target.setAttribute('href', url);
  }

  event.preventDefault();
  const newWindow = window.open(url, '_blank', 'noopener');

  if (!newWindow) {
    window.location.href = url;
  }
};

const handleModalOpen = (event) => {
  const trigger = event.target.closest('[data-pf2-cta-open]');

  if (!trigger) {
    return;
  }

  event.preventDefault();
  openCtaModal(trigger.dataset.pf2CtaOpen);
};

const handleModalClose = (event) => {
  const closer = event.target.closest('[data-pf2-cta-modal-close]');

  if (!closer) {
    return;
  }

  event.preventDefault();
  const modal = closer.closest('[data-pf2-cta-modal]');
  const id = modal ? modal.dataset.pf2CtaModal : null;
  closeCtaModal(id || undefined);
};

const init = () => {
  document.addEventListener('click', handleCtaClick);
  document.addEventListener('click', handleModalOpen);
  document.addEventListener('click', handleModalClose);
};

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', init, { once: true });
} else {
  init();
}

const hasCtaModal = (id) => Boolean(getModalElement(id));
const getFirstCtaModalId = () => {
  const modal = $('[data-pf2-cta-modal]');
  return modal ? modal.dataset.pf2CtaModal : null;
};

export { openCtaModal, closeCtaModal, hasCtaModal, getFirstCtaModalId };
