import SimpleLightbox from 'simplelightbox';
import 'simplelightbox/dist/simple-lightbox.css';

let lightboxInstance = null;

const selector = '.pf2-gallery a.pf2-gl';

const initLightbox = () => {
  const links = document.querySelectorAll(selector);

  if (!links.length) {
    if (lightboxInstance) {
      lightboxInstance.destroy();
      lightboxInstance = null;
    }
    return;
  }

  if (lightboxInstance) {
    lightboxInstance.refresh();
    return;
  }

  lightboxInstance = new SimpleLightbox(selector, {
    captions: true,
    captionSelector: 'self',
    captionType: 'attr',
    caption: 'alt',
    close: true,
    history: false,
    scrollZoom: false,
  });
};

document.addEventListener('DOMContentLoaded', initLightbox);
document.addEventListener('pf2GalleryInitialized', initLightbox);
