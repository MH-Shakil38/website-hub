document.addEventListener('DOMContentLoaded', function () {
    /* ---------- 1. Mobile menu ---------- */
    const burger = document.getElementById('ivfBurger');
    const menu = document.getElementById('ivfMenu');
    if (burger && menu) {
        burger.addEventListener('click', function () {
            menu.classList.toggle('open');
            const icon = burger.querySelector('i');
            icon.classList.toggle('fa-bars');
            icon.classList.toggle('fa-xmark');
        });
        menu.querySelectorAll('a').forEach(function (a) {
            a.addEventListener('click', function () {
                menu.classList.remove('open');
                const icon = burger.querySelector('i');
                icon.classList.add('fa-bars');
                icon.classList.remove('fa-xmark');
            });
        });
    }

    /* ---------- 2. Active menu link on scroll ---------- */
    const links = document.querySelectorAll('.ivf-menu a[href^="#"]');
    const sections = Array.from(links).map(function (l) { return document.querySelector(l.getAttribute('href')); });
    window.addEventListener('scroll', function () {
        const y = window.scrollY + 140;
        let current = 0;
        sections.forEach(function (s, i) { if (s && s.offsetTop <= y) current = i; });
        links.forEach(function (l, i) { l.classList.toggle('active', i === current); });
    }, { passive: true });

    /* ---------- 3. Consultation modal ---------- */
    const modal = document.getElementById('ivfModal');
    const closeBtn = document.getElementById('ivfModalClose');
    const open = function (e) { e.preventDefault(); modal.classList.add('open'); document.body.style.overflow = 'hidden'; };
    const close = function () { modal.classList.remove('open'); document.body.style.overflow = ''; };
    document.querySelectorAll('.js-consult').forEach(function (b) { b.addEventListener('click', open); });
    if (closeBtn) closeBtn.addEventListener('click', close);
    if (modal) modal.addEventListener('click', function (e) { if (e.target === modal) close(); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') close(); });

    /* ---------- 4. Consultation form (AJAX) ---------- */
    const form = document.getElementById('ivfForm');
    const fb = document.getElementById('ivfFeedback');
    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const btn = form.querySelector('button[type="submit"]');
            const label = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Sending...';
            fb.className = 'ivf-feedback';

            fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            })
                .then(function (r) { return r.json().then(function (d) { return { ok: r.ok, d: d }; }); })
                .then(function (res) {
                    if (res.ok && res.d.success) {
                        fb.className = 'ivf-feedback ok';
                        fb.textContent = res.d.message;
                        form.reset();
                        setTimeout(close, 2500);
                    } else {
                        const errs = res.d.errors ? Object.values(res.d.errors).flat().join(' ') : (res.d.message || 'Please check the form and try again.');
                        fb.className = 'ivf-feedback err';
                        fb.textContent = errs;
                    }
                })
                .catch(function () {
                    fb.className = 'ivf-feedback err';
                    fb.textContent = 'Something went wrong. Please try again.';
                })
                .finally(function () { btn.disabled = false; btn.innerHTML = label; });
        });
    }

    /* ---------- 5. Success Stories & Testimonials sliders ---------- */
    const data = window.IVF_TESTIMONIALS || [];

    // Testimonials section slider
    const tText = document.getElementById('ivfTestiText');
    const tName = document.getElementById('ivfTestiName');
    const tRole = document.getElementById('ivfTestiRole');
    const tImg = document.getElementById('ivfTestiImg');
    const dots = document.querySelectorAll('#ivfTestiDots span');
    let idx = 1;

    function showTesti(i) {
        if (!data.length || !tText) return;
        idx = (i + data.length) % data.length;
        tText.style.opacity = 0;
        setTimeout(function () {
            tText.textContent = data[idx].text;
            tName.textContent = data[idx].name;
            tRole.textContent = data[idx].role;
            tImg.src = data[idx].img;
            tText.style.opacity = 1;
        }, 180);
        dots.forEach(function (d, k) { d.classList.toggle('active', k === idx); });
    }

    const prev = document.getElementById('ivfPrev');
    const next = document.getElementById('ivfNext');
    if (prev) prev.addEventListener('click', function () { showTesti(idx - 1); });
    if (next) next.addEventListener('click', function () { showTesti(idx + 1); });
    dots.forEach(function (d, k) { d.addEventListener('click', function () { showTesti(k); }); });

    // Success Stories dots slider
    const sText = document.getElementById('ivfStoryText');
    const sName = document.getElementById('ivfStoryName');
    const sRole = document.getElementById('ivfStoryRole');
    const sImg = document.getElementById('ivfStoryImg');
    const sDots = document.querySelectorAll('#ivfStoryDots span');

    function showStory(i) {
        if (!data.length || !sText) return;
        const k = (i + data.length) % data.length;
        sText.style.opacity = 0;
        setTimeout(function () {
            sText.textContent = data[k].text;
            sName.textContent = data[k].name;
            sRole.textContent = data[k].role;
            sImg.src = data[k].img;
            sText.style.opacity = 1;
        }, 180);
        sDots.forEach(function (d, j) { d.classList.toggle('active', j === k); });
    }

    sDots.forEach(function (d, k) { d.addEventListener('click', function () { showStory(k); }); });
});
