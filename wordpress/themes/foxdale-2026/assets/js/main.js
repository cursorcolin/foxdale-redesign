// Copyright © 2026 Foxdale Village. All rights reserved.
// Foxdale Village 2026 theme — shared behavior.
// (Mobile navigation is handled by the core Navigation block; the demo
// form handler from the static site was removed — use a form plugin.)

// Reveal on scroll
const io = new IntersectionObserver((entries) => {
  entries.forEach((e) => {
    if (e.isIntersecting) {
      e.target.classList.add('in');
      io.unobserve(e.target);
    }
  });
}, { threshold: 0.12 });
document.querySelectorAll('.reveal').forEach((el) => io.observe(el));

// Floor plan filters
const filterBtns = document.querySelectorAll('.plan-filters button');
const plans = document.querySelectorAll('.plan');
filterBtns.forEach((btn) => {
  btn.addEventListener('click', () => {
    filterBtns.forEach((b) => {
      b.classList.remove('active');
      b.setAttribute('aria-pressed', 'false');
    });
    btn.classList.add('active');
    btn.setAttribute('aria-pressed', 'true');
    const f = btn.dataset.filter;
    plans.forEach((p) => {
      p.classList.toggle('hidden', f !== 'all' && p.dataset.type !== f);
    });
  });
});

// Lightbox for floor plans
const lightbox = document.querySelector('.lightbox');
if (lightbox) {
  const lbImg = lightbox.querySelector('img');
  const closeBtn = lightbox.querySelector('.lightbox-close');
  let opener = null;

  const closeLightbox = () => {
    lightbox.classList.remove('open');
    document.body.style.overflow = '';
    if (opener) opener.focus();
  };

  document.querySelectorAll('.plan img').forEach((img) => {
    img.setAttribute('role', 'button');
    img.setAttribute('tabindex', '0');
    img.setAttribute('aria-label', `Enlarge ${img.alt}`);
    const openLightbox = () => {
      opener = img;
      lbImg.src = img.src;
      lbImg.alt = img.alt;
      lightbox.classList.add('open');
      document.body.style.overflow = 'hidden';
      closeBtn.focus();
    };
    img.addEventListener('click', openLightbox);
    img.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        openLightbox();
      }
    });
  });
  closeBtn.addEventListener('click', closeLightbox);
  lightbox.addEventListener('click', (e) => {
    if (e.target === lightbox) closeLightbox();
  });
  lightbox.addEventListener('keydown', (e) => {
    if (e.key === 'Tab') {
      e.preventDefault();
      closeBtn.focus();
    }
  });
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && lightbox.classList.contains('open')) closeLightbox();
  });
}
