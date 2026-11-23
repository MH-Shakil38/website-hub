/* ==========================================================================
   FertilityClinic.com — Standalone Theme JavaScript
   ========================================================================== */

document.addEventListener('DOMContentLoaded', function () {
    // 1. Mobile Navigation Toggle
    const mobileToggle = document.getElementById('fcMobileToggle');
    const navList = document.getElementById('fcNavList');

    if (mobileToggle && navList) {
        mobileToggle.addEventListener('click', function () {
            navList.classList.toggle('open');
        });

        navList.querySelectorAll('a').forEach(function (link) {
            link.addEventListener('click', function () {
                navList.classList.remove('open');
            });
        });
    }

    // 2. Testimonials Slider Rotation (Prev / Next buttons)
    const testiGrid = document.getElementById('fcTestimonialsGrid');
    const prevBtn = document.getElementById('fcTestiPrev');
    const nextBtn = document.getElementById('fcTestiNext');

    if (testiGrid && prevBtn && nextBtn) {
        prevBtn.addEventListener('click', function () {
            const cards = Array.from(testiGrid.children);
            if (cards.length > 1) {
                testiGrid.prepend(cards[cards.length - 1]);
            }
        });

        nextBtn.addEventListener('click', function () {
            const cards = Array.from(testiGrid.children);
            if (cards.length > 1) {
                testiGrid.appendChild(cards[0]);
            }
        });
    }

    // 3. Watch Video Modal Interaction
    const videoBtn = document.getElementById('fcWatchVideoBtn');
    const videoModal = document.getElementById('fcVideoModal');
    const closeVideoModal = document.getElementById('fcCloseVideoModal');

    if (videoBtn && videoModal) {
        videoBtn.addEventListener('click', function () {
            videoModal.classList.add('open');
        });
    }
    if (closeVideoModal && videoModal) {
        closeVideoModal.addEventListener('click', function () {
            videoModal.classList.remove('open');
        });
        videoModal.addEventListener('click', function (e) {
            if (e.target === videoModal) {
                videoModal.classList.remove('open');
            }
        });
    }

    // 4. Search Modal Interaction
    const searchBtn = document.getElementById('fcSearchBtn');
    const searchModal = document.getElementById('fcSearchModal');
    const closeSearchModal = document.getElementById('fcCloseSearchModal');

    if (searchBtn && searchModal) {
        searchBtn.addEventListener('click', function () {
            searchModal.classList.add('open');
            const input = searchModal.querySelector('input');
            if (input) setTimeout(() => input.focus(), 80);
        });
    }
    if (closeSearchModal && searchModal) {
        closeSearchModal.addEventListener('click', function () {
            searchModal.classList.remove('open');
        });
        searchModal.addEventListener('click', function (e) {
            if (e.target === searchModal) {
                searchModal.classList.remove('open');
            }
        });
    }

    // 5. Appointment Form Submission Interaction
    const appointmentForm = document.getElementById('fcAppointmentForm');
    const formSuccessMsg = document.getElementById('fcFormSuccessMsg');

    if (appointmentForm) {
        appointmentForm.addEventListener('submit', function (e) {
            e.preventDefault();
            if (formSuccessMsg) {
                formSuccessMsg.style.display = 'block';
            }
            appointmentForm.reset();
            setTimeout(function () {
                if (formSuccessMsg) {
                    formSuccessMsg.style.display = 'none';
                }
            }, 5000);
        });
    }
});
