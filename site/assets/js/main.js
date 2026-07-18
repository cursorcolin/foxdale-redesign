// Copyright © 2026 Foxdale Village. All rights reserved.
// Foxdale Village 2026 refresh — shared behavior

// Mobile nav
const toggle = document.querySelector('.nav-toggle');
const nav = document.querySelector('nav.primary');
if (toggle && nav) {
  const setOpen = (open) => {
    nav.classList.toggle('open', open);
    toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    toggle.textContent = open ? 'Close' : 'Menu';
  };
  toggle.addEventListener('click', () => {
    setOpen(!nav.classList.contains('open'));
  });
  // Close when tapping outside the header or choosing a link
  document.addEventListener('click', (e) => {
    if (!nav.classList.contains('open')) return;
    if (e.target.closest('nav.primary a')) { setOpen(false); return; }
    if (!e.target.closest('header.site')) setOpen(false);
  });
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && nav.classList.contains('open')) {
      setOpen(false);
      toggle.focus();
    }
  });
}

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

// Demo form handler
const form = document.querySelector('form.visit');
if (form) {
  form.addEventListener('submit', (e) => {
    e.preventDefault();
    form.innerHTML = '<h3 style="font-family:var(--serif);color:var(--green-deep);font-size:26px;">Thank you!</h3><p style="margin-top:10px;color:var(--ink-soft);">A Residency Planning Counselor will reach out shortly to schedule your visit. We look forward to welcoming you to Foxdale Village.</p>';
  });
}
