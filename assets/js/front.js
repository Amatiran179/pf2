// Front-end bundle entry point.
import '../css/front.css';
import { $ } from './utils/dom.js';
import './cta/cta-core.js';
import './cta/cta-floating.js';
import './cta/cta-exit-intent.js';

document.addEventListener('DOMContentLoaded', () => {
  const body = $('body');

  if (body) {
    body.classList.add('pf2-js-ready');
  }

  // CTA modules are initialised via side-effect imports (see above).
});
