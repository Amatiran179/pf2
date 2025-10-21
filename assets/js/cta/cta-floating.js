// Floating CTA behaviour controller (visibility + persistence).
import { $$ } from '../utils/dom.js';

const DISMISS_KEY_PREFIX = 'pf2_cta_floating_dismissed';
const VISIBLE_CLASS = 'is-visible';
const SCROLL_THRESHOLD = 200;

const containers = [];

const storageKey = (key) => `${DISMISS_KEY_PREFIX}:${key}`;
const removeScrollListenerIfInactive = () => {
  if (!containers.length) {
    return;
  }

  const hasActive = containers.some((entry) => !entry.dismissed);

  if (!hasActive) {
    window.removeEventListener('scroll', handleScroll);
  }
};

const isDismissed = (key) => {
  try {
    return window.localStorage?.getItem(storageKey(key)) === '1';
  } catch (error) {
    return false;
  }
};

const markDismissed = (key) => {
  try {
    window.localStorage?.setItem(storageKey(key), '1');
  } catch (error) {
    // Ignore storage failures.
  }
};

const toggleVisibility = (element, isVisible) => {
  if (isVisible) {
    element.removeAttribute('hidden');
    element.setAttribute('aria-hidden', 'false');
    element.classList.add(VISIBLE_CLASS);
  } else {
    element.setAttribute('aria-hidden', 'true');
    element.classList.remove(VISIBLE_CLASS);
    element.setAttribute('hidden', 'hidden');
  }
};

const handleScroll = () => {
  const shouldShow = window.scrollY > SCROLL_THRESHOLD;

  containers.forEach((entry) => {
    if (entry.dismissed) {
      return;
    }

    toggleVisibility(entry.element, shouldShow);
  });
};

const initFloatingCta = () => {
  const nodes = $$('[data-pf2-cta-floating]');

  if (!nodes.length) {
    return;
  }

  nodes.forEach((element) => {
    const key = element.dataset.pf2CtaFloatingKey || 'default';
    const entry = {
      element,
      key,
      dismissed: isDismissed(key),
    };

    containers.push(entry);

    if (entry.dismissed) {
      toggleVisibility(element, false);
    } else {
      toggleVisibility(element, false);
    }

    const dismiss = element.querySelector('[data-pf2-cta-floating-dismiss]');

    if (dismiss) {
      dismiss.addEventListener('click', (event) => {
        event.preventDefault();
        entry.dismissed = true;
        markDismissed(key);
        toggleVisibility(element, false);
        removeScrollListenerIfInactive();
      });
    }
  });

  window.addEventListener('scroll', handleScroll, { passive: true });
  handleScroll();
  removeScrollListenerIfInactive();
};

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initFloatingCta, { once: true });
} else {
  initFloatingCta();
}
