// Front-end bundle entry point.
import '../css/front.css';
import { $ } from './utils/dom.js';

document.addEventListener('DOMContentLoaded', () => {
  const body = $('body');

  if (body) {
    body.classList.add('pf2-js-ready');
  }

  // CTA and gallery initializers will be registered in future batches.
});
