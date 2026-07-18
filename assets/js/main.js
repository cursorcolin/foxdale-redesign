// Copyright © 2026 Foxdale Village. All rights reserved.
// Foxdale Village 2026 refresh — shared behavior

// Mobile nav
const toggle = document.querySelector('.nav-toggle');
const nav = document.querySelector('nav.primary');
if (toggle && nav) {
  toggle.addEventListener('click', () => {
    const open = nav.classList.toggle('open');
    toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
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
    filterBtns.forEach((b) => b.classList.remove('active'));
    btn.classList.add('active');
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
  document.querySelectorAll('.plan img').forEach((img) => {
    img.addEventListener('click', () => {
      lbImg.src = img.src;
      lbImg.alt = img.alt;
      lightbox.classList.add('open');
    });
  });
  lightbox.addEventListener('click', () => lightbox.classList.remove('open'));
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') lightbox.classList.remove('open');
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
