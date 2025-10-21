// Exit-intent detection to surface modal CTAs opportunistically.
import { openCtaModal, getFirstCtaModalId, hasCtaModal } from './cta-core.js';

const SESSION_KEY_PREFIX = 'pf2_cta_exit_intent';
const DEBOUNCE_DELAY = 3000;

let debounceTimer = null;

const sessionKey = (id) => `${SESSION_KEY_PREFIX}:${id}`;

const hasModalForExitIntent = () => {
  const modalId = getFirstCtaModalId();
  return modalId ? hasCtaModal(modalId) : false;
};

const hasShownModal = (id) => {
  try {
    return window.sessionStorage?.getItem(sessionKey(id)) === '1';
  } catch (error) {
    return false;
  }
};

const markModalShown = (id) => {
  try {
    window.sessionStorage?.setItem(sessionKey(id), '1');
  } catch (error) {
    // Ignore sessionStorage failures.
  }
};

const showExitIntentModal = () => {
  const modalId = getFirstCtaModalId();

  if (!modalId || !hasCtaModal(modalId)) {
    return;
  }

  if (hasShownModal(modalId)) {
    return;
  }

  markModalShown(modalId);
  openCtaModal(modalId);
  document.removeEventListener('mouseleave', handleMouseLeave);
};

const scheduleModal = () => {
  window.clearTimeout(debounceTimer);
  debounceTimer = window.setTimeout(showExitIntentModal, DEBOUNCE_DELAY);
};

const handleMouseLeave = (event) => {
  if (event.clientY > 0) {
    return;
  }

  scheduleModal();
};

const initExitIntent = () => {
  if (!hasModalForExitIntent()) {
    return;
  }

  document.addEventListener('mouseleave', handleMouseLeave);
};

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initExitIntent, { once: true });
} else {
  initExitIntent();
}
