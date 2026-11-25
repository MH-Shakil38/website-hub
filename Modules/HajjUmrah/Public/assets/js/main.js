/* ==========================================================================
   Al Hajenra - Hajj & Umrah Travel Agency Frontend JS
   ========================================================================== */

document.addEventListener('DOMContentLoaded', function () {
    // 1. Sticky Header Scroll Effect
    const header = document.querySelector('.hajj-header');
    if (header) {
        window.addEventListener('scroll', function () {
            if (window.scrollY > 30) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });
    }

    // 2. Mobile Navigation Toggle
    const mobileToggle = document.getElementById('hajjMobileToggle');
    const mainNav = document.getElementById('hajjMainNav');
    if (mobileToggle && mainNav) {
        mobileToggle.addEventListener('click', function () {
            mainNav.classList.toggle('open');
        });
    }

    // 3. Booking Inquiry Modal Trigger (Select Package / Get a Package Price / Contact Us)
    const bookingModal = document.getElementById('hajjBookingModal');
    const packageInput = document.getElementById('hajjSelectedPackageInput');
    const modalTitleSpan = document.getElementById('hajjModalPackageLabel');

    document.querySelectorAll('[data-open-booking]').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const pkgName = this.getAttribute('data-package-name') || 'Economy Hajj & Umrah Package';
            if (packageInput) packageInput.value = pkgName;
            if (modalTitleSpan) modalTitleSpan.textContent = pkgName;
            if (bookingModal) bookingModal.classList.add('open');
        });
    });

    document.querySelectorAll('[data-close-modal]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.hajj-modal-backdrop').forEach(function (m) {
                m.classList.remove('open');
            });
            const iframe = document.getElementById('hajjVideoIframe');
            if (iframe) iframe.src = '';
        });
    });

    // 4. Educational Video Modal Trigger
    const videoModal = document.getElementById('hajjVideoModal');
    const videoIframe = document.getElementById('hajjVideoIframe');
    document.querySelectorAll('[data-video-url]').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const url = this.getAttribute('data-video-url');
            if (videoIframe && url) {
                videoIframe.src = url;
            }
            if (videoModal) {
                videoModal.classList.add('open');
            }
        });
    });

    // 5. Hero Background Image Slider (Makkah Tawaf & Kaaba Slides)
    const slides = document.querySelectorAll('#hajjHeroSlider .hajj-hero-slide');
    const dots = document.querySelectorAll('#hajjHeroDots .hajj-hero-dot');
    if (slides.length > 1) {
        let currentSlide = 0;
        const goToSlide = function (idx) {
            slides.forEach((s, i) => s.classList.toggle('active', i === idx));
            dots.forEach((d, i) => d.classList.toggle('active', i === idx));
            currentSlide = idx;
        };
        dots.forEach(function (dot) {
            dot.addEventListener('click', function () {
                const idx = parseInt(this.getAttribute('data-go-slide'), 10);
                goToSlide(idx);
            });
        });
        setInterval(function () {
            const next = (currentSlide + 1) % slides.length;
            goToSlide(next);
        }, 5500);
    }
});
