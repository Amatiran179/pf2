import Swiper from 'swiper';
import { Navigation, Pagination, Keyboard, A11y } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';

const initGallery = () => {
  const galleries = document.querySelectorAll('[data-pf2-gallery]');

  if (!galleries.length) {
    return;
  }

  const visibilityObserver = 'IntersectionObserver' in window
    ? new IntersectionObserver((entries, observer) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add('is-visible');
            observer.unobserve(entry.target);
          }
        });
      }, { rootMargin: '80px 0px' })
    : null;

  galleries.forEach((gallery) => {
    if (gallery.dataset.pf2GalleryInit === '1') {
      return;
    }

    const swiperEl = gallery.querySelector('.swiper');

    if (!swiperEl) {
      return;
    }

    const slides = swiperEl.querySelectorAll('.swiper-slide');
    const paginationEl = swiperEl.querySelector('.swiper-pagination');
    const nextEl = swiperEl.querySelector('.swiper-button-next');
    const prevEl = swiperEl.querySelector('.swiper-button-prev');

    const swiperModules = [Keyboard, A11y];
    const swiperOptions = {
      modules: swiperModules,
      loop: slides.length > 1,
      speed: 450,
      centeredSlides: false,
      slidesPerView: 1,
      spaceBetween: 8,
      keyboard: {
        enabled: true,
      },
      a11y: {
        enabled: true,
      },
    };

    if (paginationEl) {
      swiperModules.push(Pagination);
      swiperOptions.pagination = {
        el: paginationEl,
        clickable: true,
      };
    }

    if (nextEl && prevEl) {
      swiperModules.push(Navigation);
      swiperOptions.navigation = {
        nextEl,
        prevEl,
      };
    }

    const swiperInstance = new Swiper(swiperEl, swiperOptions);

    gallery.dataset.pf2GalleryInit = '1';

    if (visibilityObserver) {
      swiperEl.querySelectorAll('img[loading="lazy"]').forEach((img) => {
        visibilityObserver.observe(img);
      });
    } else {
      swiperEl.querySelectorAll('img[loading="lazy"]').forEach((img) => {
        img.classList.add('is-visible');
      });
    }

    swiperInstance.on('afterInit', () => {
      gallery.classList.add('pf2-gallery--ready');
    });

    document.dispatchEvent(new CustomEvent('pf2GalleryInitialized', {
      detail: {
        gallery,
        swiper: swiperInstance,
      },
    }));
  });
};

document.addEventListener('DOMContentLoaded', initGallery);
