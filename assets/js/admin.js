// WordPress admin bundle entry point.
import '../css/admin.css';

document.addEventListener('DOMContentLoaded', () => {
  if (document.body) {
    document.body.classList.add('pf2-admin-js-ready');
  }

  // Admin dashboard widgets will be initialized in future batches.
});
