document.addEventListener('DOMContentLoaded', function () {
    // 1. Mobile Navigation Toggle
    const mobileToggle = document.getElementById('gymMobileToggle');
    const mainNav = document.getElementById('gymMainNav');
    if (mobileToggle && mainNav) {
        mobileToggle.addEventListener('click', function () {
            mainNav.classList.toggle('open');
        });
    }

    // 2. Billing Duration Switcher on Choose Your Plan Section
    const billingTabs = document.querySelectorAll('[data-billing-cycle]');
    const priceCards = document.querySelectorAll('.gym-horiz-plan-card');
    const regBillingSelect = document.getElementById('gymRegBillingSelect');

    billingTabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            const cycle = tab.getAttribute('data-billing-cycle');
            billingTabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');

            priceCards.forEach(function (card) {
                const priceEl = card.querySelector('.val');
                const unitEl = card.querySelector('.unit');
                const joinBtn = card.querySelector('[data-select-package]');

                if (priceEl && card.dataset[cycle]) {
                    priceEl.textContent = card.dataset.currency + card.dataset[cycle];
                }
                if (unitEl && tab.dataset.unitLabel) {
                    unitEl.textContent = tab.dataset.unitLabel;
                }
                if (joinBtn) {
                    joinBtn.setAttribute('data-selected-cycle', cycle);
                }
            });

            if (regBillingSelect) {
                regBillingSelect.value = cycle;
            }
        });
    });

    // 3. Open Registration Modal & Pre-select Package
    const regModal = document.getElementById('gymRegisterModal');
    const regPackageSelect = document.getElementById('gymRegPackageSelect');

    document.querySelectorAll('[data-open-register], [data-select-package]').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            const pkgId = btn.getAttribute('data-select-package');
            const cycle = btn.getAttribute('data-selected-cycle');

            if (regPackageSelect && pkgId) {
                regPackageSelect.value = pkgId;
            }
            if (regBillingSelect && cycle) {
                regBillingSelect.value = cycle;
            }
            if (regModal) {
                e.preventDefault();
                regModal.classList.add('active');
            }
        });
    });

    // 4. FAQ Accordion (Delegated for 100% Reliability)
    document.addEventListener('click', function (e) {
        const faqBtn = e.target.closest('.gym-faq-q');
        if (!faqBtn) return;

        e.preventDefault();
        const item = faqBtn.closest('.gym-faq-item');
        if (!item) return;

        const isOpen = item.classList.toggle('open');
        const icon = faqBtn.querySelector('i');
        if (icon) {
            icon.className = isOpen ? 'fa-solid fa-minus' : 'fa-solid fa-plus';
        }
    });

    // 5. Workout & Diet Plan Filter Tabs
    const planFilterBtns = document.querySelectorAll('[data-plan-filter]');
    const planRoutineCards = document.querySelectorAll('.gym-plan-routine-card');
    planFilterBtns.forEach(function (btn) {
        btn.addEventListener('click', function () {
            const filter = btn.getAttribute('data-plan-filter');
            planFilterBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            planRoutineCards.forEach(function (card) {
                const pType = card.getAttribute('data-plan-type');
                if (filter === 'all' || pType === filter) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });

    // 6. Video Gallery Lightbox Modal
    const videoModal = document.getElementById('gymVideoModal');
    const videoIframe = document.getElementById('gymVideoIframe');
    const videoModalTitle = document.getElementById('gymVideoModalTitle');

    function toEmbedUrl(url) {
        if (!url) return '';
        if (url.includes('youtube.com/watch?v=')) {
            const id = url.split('v=')[1].split('&')[0];
            return 'https://www.youtube.com/embed/' + id + '?autoplay=1';
        }
        if (url.includes('youtu.be/')) {
            const id = url.split('youtu.be/')[1].split('?')[0];
            return 'https://www.youtube.com/embed/' + id + '?autoplay=1';
        }
        return url + (url.includes('?') ? '&autoplay=1' : '?autoplay=1');
    }

    document.querySelectorAll('[data-open-video]').forEach(function (card) {
        card.addEventListener('click', function (e) {
            e.preventDefault();
            const url = card.getAttribute('data-open-video');
            const title = card.getAttribute('data-video-title') || 'Workout Video';
            if (videoModal && videoIframe) {
                if (videoModalTitle) videoModalTitle.textContent = title;
                videoIframe.src = toEmbedUrl(url);
                videoModal.classList.add('active');
            }
        });
    });

    // 7. Image Gallery Lightbox Modal
    const imageModal = document.getElementById('gymImageModal');
    const imageModalSrc = document.getElementById('gymImageModalSrc');
    const imageModalTitle = document.getElementById('gymImageModalTitle');

    document.querySelectorAll('[data-open-image]').forEach(function (card) {
        card.addEventListener('click', function (e) {
            e.preventDefault();
            const src = card.getAttribute('data-open-image');
            const title = card.getAttribute('data-image-title') || '';
            if (imageModal && imageModalSrc) {
                imageModalSrc.src = src;
                if (imageModalTitle) imageModalTitle.textContent = title;
                imageModal.classList.add('active');
            }
        });
    });

    // 8. Close Modals & Stop Video Playback
    function closeGymModal(modal) {
        if (!modal) return;
        modal.classList.remove('active');
        if (modal.id === 'gymVideoModal' && videoIframe) {
            videoIframe.src = '';
        }
    }

    document.querySelectorAll('[data-close-modal]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            closeGymModal(btn.closest('.gym-modal-backdrop'));
        });
    });

    document.querySelectorAll('.gym-modal-backdrop').forEach(function (backdrop) {
        backdrop.addEventListener('click', function (e) {
            if (e.target === backdrop) {
                closeGymModal(backdrop);
            }
        });
    });
});
