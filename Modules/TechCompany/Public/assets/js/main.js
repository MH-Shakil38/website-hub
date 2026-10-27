// YourCompany — Agency Template scripts
(function () {
  // Sticky header
  const header = document.querySelector('.header');
  const toTop = document.querySelector('.to-top');
  const onScroll = () => {
    const y = window.scrollY;
    header && header.classList.toggle('scrolled', y > 20);
    toTop && toTop.classList.toggle('show', y > 600);
  };
  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();
  toTop && toTop.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));

  // Mobile menu
  const burger = document.querySelector('.burger');
  const menu = document.querySelector('.mobile-menu');
  burger && burger.addEventListener('click', () => {
    const open = menu.classList.toggle('open');
    burger.setAttribute('aria-expanded', open);
    document.body.style.overflow = open ? 'hidden' : '';
  });

  // Reveal on scroll
  const io = new IntersectionObserver((entries) => {
    entries.forEach((e) => { if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); } });
  }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
  document.querySelectorAll('.reveal').forEach((el) => io.observe(el));

  // Word-by-word highlight statement
  document.querySelectorAll('.statement').forEach((el) => {
    const words = el.textContent.trim().split(/\s+/);
    el.innerHTML = words.map((w) => `<span class="w">${w}</span>`).join(' ');
    const spans = el.querySelectorAll('.w');
    const update = () => {
      const r = el.getBoundingClientRect();
      const vh = window.innerHeight;
      const p = Math.min(1, Math.max(0, (vh * 0.85 - r.top) / (r.height + vh * 0.35)));
      const n = Math.round(p * spans.length);
      spans.forEach((s, i) => s.classList.toggle('on', i < n));
    };
    window.addEventListener('scroll', update, { passive: true });
    update();
  });

  // Tabs
  document.querySelectorAll('[data-tabs]').forEach((wrap) => {
    const tabs = wrap.querySelectorAll('.tab');
    const panels = wrap.querySelectorAll('.tab-panel');
    tabs.forEach((t, i) => t.addEventListener('click', () => {
      tabs.forEach((x) => x.classList.remove('active'));
      panels.forEach((x) => x.classList.remove('active'));
      t.classList.add('active');
      panels[i].classList.add('active');
    }));
  });

  // FAQ accordion
  document.querySelectorAll('.faq-q').forEach((q) => {
    q.addEventListener('click', () => {
      const item = q.parentElement;
      const open = item.classList.contains('open');
      item.parentElement.querySelectorAll('.faq-item').forEach((i) => { i.classList.remove('open'); i.querySelector('.faq-q').setAttribute('aria-expanded', 'false'); });
      if (!open) { item.classList.add('open'); q.setAttribute('aria-expanded', 'true'); }
    });
  });

  // Pricing toggle (monthly / yearly)
  document.querySelectorAll('.billing-toggle').forEach((tg) => {
    const btns = tg.querySelectorAll('button');
    btns.forEach((b) => b.addEventListener('click', () => {
      btns.forEach((x) => x.classList.remove('active'));
      b.classList.add('active');
      const mode = b.dataset.mode;
      document.querySelectorAll('[data-once]').forEach((p) => {
        p.textContent = mode === 'saas' ? p.dataset.saas : p.dataset.once;
      });
      document.querySelectorAll('[data-period]').forEach((p) => {
        p.textContent = mode === 'saas' ? '/ month' : '/ project';
      });
    }));
  });

  // Count up stats
  const cio = new IntersectionObserver((entries) => {
    entries.forEach((e) => {
      if (!e.isIntersecting) return;
      const el = e.target; const end = +el.dataset.count; const suf = el.dataset.suffix || '';
      let start = null;
      const step = (ts) => {
        if (!start) start = ts;
        const p = Math.min(1, (ts - start) / 1400);
        el.textContent = Math.floor(end * (1 - Math.pow(1 - p, 3))) + suf;
        if (p < 1) requestAnimationFrame(step);
      };
      requestAnimationFrame(step);
      cio.unobserve(el);
    });
  }, { threshold: 0.5 });
  document.querySelectorAll('[data-count]').forEach((el) => cio.observe(el));

  // Forms (demo only — connect to your backend / Laravel route)
  document.querySelectorAll('form[data-demo]').forEach((f) => {
    f.addEventListener('submit', (e) => {
      e.preventDefault();
      const note = f.querySelector('.form-note');
      if (note) { note.style.display = 'block'; }
      f.reset();
    });
  });

  // Year
  document.querySelectorAll('[data-year]').forEach((el) => (el.textContent = new Date().getFullYear()));
})();
