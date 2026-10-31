/* ==========================================================================
   PORTFOLIO MODULE — 3D MOTION, PARALLAX, TILT & INTERACTIVE FRONTEND ENGINE
   ========================================================================== */
document.addEventListener('DOMContentLoaded', function () {
    const htmlEl = document.documentElement;
    const themeToggleBtn = document.getElementById('pfThemeToggle');
    const themeLabel = document.getElementById('pfThemeLabel');
    const themeIcon = document.getElementById('pfThemeIcon');

    /* ----------------------------------------------------------------------
       1. LIGHT / DARK / WAVE THEME SWITCHER
       ---------------------------------------------------------------------- */
    function applyTheme(mode) {
        htmlEl.setAttribute('data-theme', mode);
        localStorage.setItem('pf_theme_mode', mode);
        if (themeLabel && themeIcon) {
            if (mode === 'light') {
                themeLabel.textContent = 'Light Mode';
                themeIcon.className = 'fa-solid fa-sun';
            } else if (mode === 'full-dark') {
                themeLabel.textContent = 'Full Dark';
                themeIcon.className = 'fa-solid fa-moon';
            } else {
                themeLabel.textContent = 'Dark Wave';
                themeIcon.className = 'fa-solid fa-circle-half-stroke';
            }
        }
    }

    const savedMode = localStorage.getItem('pf_theme_mode') || htmlEl.getAttribute('data-theme') || 'dark';
    applyTheme(savedMode);

    if (themeToggleBtn) {
        themeToggleBtn.addEventListener('click', function () {
            const current = htmlEl.getAttribute('data-theme') || 'dark';
            const next = current === 'dark' ? 'light' : (current === 'light' ? 'full-dark' : 'dark');
            applyTheme(next);
        });
    }

    /* ----------------------------------------------------------------------
       2. MOBILE NAVIGATION MENU
       ---------------------------------------------------------------------- */
    const mobileBtn = document.getElementById('pfMobileToggle');
    const navMenu = document.getElementById('pfNavMenu');
    if (mobileBtn && navMenu) {
        mobileBtn.addEventListener('click', function () {
            navMenu.classList.toggle('open');
        });
    }

    /* ----------------------------------------------------------------------
       3. TOP SCROLL PROGRESS BAR & BACK TO TOP
       ---------------------------------------------------------------------- */
    const scrollProgress = document.getElementById('pfScrollProgress');
    const backTopBtn = document.getElementById('pfBackTop');

    window.addEventListener('scroll', function () {
        const scrollTop = window.scrollY || document.documentElement.scrollTop;
        const docHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
        if (scrollProgress && docHeight > 0) {
            scrollProgress.style.width = ((scrollTop / docHeight) * 100).toFixed(2) + '%';
        }
        if (backTopBtn) {
            if (scrollTop > 400) {
                backTopBtn.classList.add('show');
            } else {
                backTopBtn.classList.remove('show');
            }
        }
    }, { passive: true });

    if (backTopBtn) {
        backTopBtn.addEventListener('click', function () {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    /* ----------------------------------------------------------------------
       4. AMBIENT 3D CURSOR SPOTLIGHT FOLLOWER
       ---------------------------------------------------------------------- */
    const cursorGlow = document.getElementById('pfCursorGlow');
    let mouseX = window.innerWidth / 2;
    let mouseY = window.innerHeight / 2;
    let glowX = mouseX;
    let glowY = mouseY;

    if (cursorGlow && window.matchMedia('(pointer: fine)').matches) {
        window.addEventListener('mousemove', function (e) {
            mouseX = e.clientX;
            mouseY = e.clientY;
        }, { passive: true });

        function animateCursorGlow() {
            glowX += (mouseX - glowX) * 0.12;
            glowY += (mouseY - glowY) * 0.12;
            cursorGlow.style.transform = `translate3d(${glowX}px, ${glowY}px, 0) translate(-50%, -50%)`;
            requestAnimationFrame(animateCursorGlow);
        }
        requestAnimationFrame(animateCursorGlow);
    }

    /* ----------------------------------------------------------------------
       5. INTERACTIVE 3D HERO PARTICLE & CONSTELLATION CANVAS (#pfHero3dCanvas)
       ---------------------------------------------------------------------- */
    const canvas = document.getElementById('pfHero3dCanvas');
    if (canvas) {
        const ctx = canvas.getContext('2d');
        let width = 0;
        let height = 0;
        let particles = [];
        const particleCount = window.innerWidth < 768 ? 28 : 62;
        let targetRotX = 0;
        let targetRotY = 0;
        let currentRotX = 0;
        let currentRotY = 0;

        function resizeCanvas() {
            const hero = canvas.parentElement;
            width = canvas.width = hero ? hero.offsetWidth : window.innerWidth;
            height = canvas.height = hero ? hero.offsetHeight : 650;
        }

        function createParticles() {
            particles = [];
            for (let i = 0; i < particleCount; i++) {
                particles.push({
                    x: (Math.random() - 0.5) * width * 1.2,
                    y: (Math.random() - 0.5) * height * 1.2,
                    z: Math.random() * 500 + 50,
                    vz: -(Math.random() * 0.65 + 0.2),
                    vx: (Math.random() - 0.5) * 0.35,
                    vy: (Math.random() - 0.5) * 0.35,
                    size: Math.random() * 2.4 + 1,
                    color: Math.random() > 0.4 ? '#35B6FF' : '#078BFF'
                });
            }
        }

        resizeCanvas();
        createParticles();
        window.addEventListener('resize', function () {
            resizeCanvas();
            createParticles();
        });

        window.addEventListener('mousemove', function (e) {
            targetRotY = ((e.clientX / window.innerWidth) - 0.5) * 0.35;
            targetRotX = ((e.clientY / window.innerHeight) - 0.5) * 0.25;
        }, { passive: true });

        function render3dCanvas() {
            ctx.clearRect(0, 0, width, height);
            currentRotX += (targetRotX - currentRotX) * 0.06;
            currentRotY += (targetRotY - currentRotY) * 0.06;

            const cx = width / 2;
            const cy = height / 2;
            const fov = 420;
            const projected = [];

            for (let i = 0; i < particles.length; i++) {
                const p = particles[i];
                p.z += p.vz;
                p.x += p.vx;
                p.y += p.vy;

                if (p.z < 20) {
                    p.z = 520;
                    p.x = (Math.random() - 0.5) * width * 1.1;
                    p.y = (Math.random() - 0.5) * height * 1.1;
                }

                // 3D rotation around center
                const cosY = Math.cos(currentRotY);
                const sinY = Math.sin(currentRotY);
                const cosX = Math.cos(currentRotX);
                const sinX = Math.sin(currentRotX);

                const x1 = p.x * cosY - p.z * sinY;
                const z1 = p.z * cosY + p.x * sinY;
                const y1 = p.y * cosX - z1 * sinX;
                const z2 = z1 * cosX + p.y * sinX;

                const scale = fov / (fov + z2 + 250);
                if (scale > 0) {
                    const px = cx + x1 * scale;
                    const py = cy + y1 * scale;
                    const alpha = Math.min(0.85, Math.max(0.12, scale * 0.9));
                    projected.push({ px, py, scale, alpha, color: p.color, r: p.size * scale * 1.4 });
                }
            }

            // Draw 3D constellation lines between nearby nodes
            for (let i = 0; i < projected.length; i++) {
                const a = projected[i];
                ctx.beginPath();
                ctx.arc(a.px, a.py, a.r, 0, Math.PI * 2);
                ctx.fillStyle = a.color;
                ctx.globalAlpha = a.alpha;
                ctx.fill();

                for (let j = i + 1; j < projected.length; j++) {
                    const b = projected[j];
                    const dx = a.px - b.px;
                    const dy = a.py - b.py;
                    const dist = Math.sqrt(dx * dx + dy * dy);
                    if (dist < 135) {
                        ctx.beginPath();
                        ctx.moveTo(a.px, a.py);
                        ctx.lineTo(b.px, b.py);
                        ctx.strokeStyle = '#078BFF';
                        ctx.globalAlpha = (1 - dist / 135) * 0.22;
                        ctx.lineWidth = 1;
                        ctx.stroke();
                    }
                }
            }
            ctx.globalAlpha = 1;
            requestAnimationFrame(render3dCanvas);
        }
        requestAnimationFrame(render3dCanvas);
    }

    /* ----------------------------------------------------------------------
       6. HERO 3D MULTI-PLANE PARALLAX STAGE (#pfHero3dStage)
       ---------------------------------------------------------------------- */
    const heroSection = document.querySelector('.pf-hero');
    const heroStage = document.getElementById('pfHero3dStage');
    if (heroSection && heroStage && window.matchMedia('(pointer: fine)').matches) {
        const depthLayers = heroStage.querySelectorAll('[data-depth]');

        heroSection.addEventListener('mousemove', function (e) {
            const rect = heroSection.getBoundingClientRect();
            const relX = (e.clientX - rect.left) / rect.width - 0.5;
            const relY = (e.clientY - rect.top) / rect.height - 0.5;

            const rotY = relX * 16;
            const rotX = -relY * 14;
            heroStage.style.transform = `perspective(1200px) rotateX(${rotX.toFixed(2)}deg) rotateY(${rotY.toFixed(2)}deg)`;

            depthLayers.forEach(function (layer) {
                const depth = parseFloat(layer.getAttribute('data-depth') || '20');
                const moveX = relX * depth * 0.85;
                const moveY = relY * depth * 0.85;
                layer.style.transform = `translate3d(${moveX.toFixed(1)}px, ${moveY.toFixed(1)}px, ${depth}px)`;
            });
        });

        heroSection.addEventListener('mouseleave', function () {
            heroStage.style.transform = 'perspective(1200px) rotateX(0deg) rotateY(0deg)';
            depthLayers.forEach(function (layer) {
                layer.style.transform = '';
            });
        });
    }

    /* ----------------------------------------------------------------------
       7. UNIVERSAL 3D PERSPECTIVE TILT & SPECULAR GLARE ENGINE FOR ALL CARDS
       ---------------------------------------------------------------------- */
    const cardSelectors = [
        '.pf-about-photo-card',
        '.pf-skill-card',
        '.pf-service-card',
        '.pf-project-card',
        '.pf-testi-card',
        '.pf-blog-card',
        '.pf-process-card',
        '.pf-form-card'
    ];
    const allCards = document.querySelectorAll(cardSelectors.join(','));

    if (window.matchMedia('(pointer: fine)').matches) {
        allCards.forEach(function (card) {
            card.classList.add('pf-3d-card');

            // Inject specular 3D glare layer
            let glare = card.querySelector('.pf-card-glare');
            if (!glare) {
                glare = document.createElement('div');
                glare.className = 'pf-card-glare';
                card.appendChild(glare);
            }

            card.addEventListener('mousemove', function (e) {
                const rect = card.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                const centerX = rect.width / 2;
                const centerY = rect.height / 2;

                const maxTilt = card.classList.contains('pf-form-card') ? 4 : 11;
                const rotateX = ((y - centerY) / centerY) * -maxTilt;
                const rotateY = ((x - centerX) / centerX) * maxTilt;

                card.style.transform = `perspective(1000px) rotateX(${rotateX.toFixed(2)}deg) rotateY(${rotateY.toFixed(2)}deg) translate3d(0, -6px, 12px) scale3d(1.02, 1.02, 1.02)`;

                const glareX = ((x / rect.width) * 100).toFixed(1);
                const glareY = ((y / rect.height) * 100).toFixed(1);
                glare.style.background = `radial-gradient(circle at ${glareX}% ${glareY}%, rgba(53, 182, 255, 0.28) 0%, rgba(255, 255, 255, 0.08) 35%, transparent 70%)`;
            });

            card.addEventListener('mouseleave', function () {
                card.style.transform = 'perspective(1000px) rotateX(0deg) rotateY(0deg) translate3d(0, 0, 0) scale3d(1, 1, 1)';
            });
        });
    }

    /* ----------------------------------------------------------------------
       8. MAGNETIC 3D BUTTONS & SOCIAL ICONS
       ---------------------------------------------------------------------- */
    if (window.matchMedia('(pointer: fine)').matches) {
        const magnetics = document.querySelectorAll('.pf-magnetic, .pf-btn-primary, .pf-btn-outline, .pf-btn-talk, .pf-social-icon');
        magnetics.forEach(function (btn) {
            btn.addEventListener('mousemove', function (e) {
                const rect = btn.getBoundingClientRect();
                const dx = e.clientX - (rect.left + rect.width / 2);
                const dy = e.clientY - (rect.top + rect.height / 2);
                btn.style.transform = `translate3d(${(dx * 0.24).toFixed(1)}px, ${(dy * 0.24).toFixed(1)}px, 10px)`;
            });
            btn.addEventListener('mouseleave', function () {
                btn.style.transform = '';
            });
        });
    }

    /* ----------------------------------------------------------------------
       9. SCROLL-TRIGGERED 3D REVEAL, ANIMATED SKILL BARS & STAT COUNTERS
       ---------------------------------------------------------------------- */
    const revealGroups = [
        '.pf-about-grid > *',
        '.pf-side-intro',
        '.pf-skill-card',
        '.pf-service-card',
        '.pf-project-card',
        '.pf-timeline-step',
        '.pf-testi-card',
        '.pf-blog-card',
        '.pf-contact-quick',
        '.pf-process-card',
        '.pf-faq-item',
        '.pf-form-card'
    ];

    const revealElements = document.querySelectorAll(revealGroups.join(','));
    revealElements.forEach(function (el, idx) {
        el.classList.add('pf-reveal-3d');
        const siblingIndex = Array.from(el.parentElement ? el.parentElement.children : []).indexOf(el);
        const delayMs = Math.min(siblingIndex * 75, 450);
        el.style.setProperty('--pf-delay', delayMs + 'ms');
    });

    if ('IntersectionObserver' in window) {
        const revealObserver = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('pf-revealed');

                    // Animate skill progress bar if inside this card
                    const progressFill = entry.target.querySelector('.pf-skill-progress-fill');
                    if (progressFill) {
                        const targetPct = progressFill.getAttribute('data-progress') || '90';
                        setTimeout(function () {
                            progressFill.style.width = targetPct + '%';
                        }, 180);
                    }
                }
            });
        }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

        revealElements.forEach(function (el) {
            revealObserver.observe(el);
        });

        // Animate About Section Stat Numbers (e.g. 3+, 50+, 100%)
        const statNums = document.querySelectorAll('.pf-about-stat-num');
        const statObserver = new IntersectionObserver(function (entries, obs) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    const el = entry.target;
                    const raw = el.textContent.trim();
                    const match = raw.match(/^(\d+)(.*)$/);
                    if (match) {
                        const targetNum = parseInt(match[1], 10);
                        const suffix = match[2] || '';
                        let current = 0;
                        const duration = 1200;
                        const stepTime = 24;
                        const increment = Math.max(1, Math.ceil(targetNum / (duration / stepTime)));
                        const timer = setInterval(function () {
                            current += increment;
                            if (current >= targetNum) {
                                current = targetNum;
                                clearInterval(timer);
                            }
                            el.textContent = current + suffix;
                        }, stepTime);
                    }
                    obs.unobserve(el);
                }
            });
        }, { threshold: 0.5 });

        statNums.forEach(function (stat) {
            statObserver.observe(stat);
        });
    } else {
        revealElements.forEach(el => el.classList.add('pf-revealed'));
    }

    /* ----------------------------------------------------------------------
       10. SKILLS & PORTFOLIO CATEGORY FILTERS (WITH 3D RE-ENTRY MOTION)
       ---------------------------------------------------------------------- */
    const skillChips = document.querySelectorAll('[data-skill-filter]');
    const skillCards = document.querySelectorAll('[data-skill-cat]');
    skillChips.forEach(function (chip) {
        chip.addEventListener('click', function () {
            const cat = this.getAttribute('data-skill-filter');
            skillChips.forEach(c => c.classList.remove('active'));
            this.classList.add('active');
            skillCards.forEach(function (card) {
                if (cat === 'all' || card.getAttribute('data-skill-cat') === cat) {
                    card.style.display = 'flex';
                    requestAnimationFrame(() => card.classList.add('pf-revealed'));
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });

    const projChips = document.querySelectorAll('[data-proj-filter]');
    const projCards = document.querySelectorAll('[data-proj-cat]');
    projChips.forEach(function (chip) {
        chip.addEventListener('click', function () {
            const cat = this.getAttribute('data-proj-filter');
            projChips.forEach(c => c.classList.remove('active'));
            this.classList.add('active');
            projCards.forEach(function (card) {
                if (cat === 'all' || card.getAttribute('data-proj-cat') === cat) {
                    card.style.display = 'flex';
                    requestAnimationFrame(() => card.classList.add('pf-revealed'));
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });

    /* ----------------------------------------------------------------------
       11. FAQ ACCORDION
       ---------------------------------------------------------------------- */
    document.querySelectorAll('.pf-faq-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const item = this.closest('.pf-faq-item');
            if (item) {
                item.classList.toggle('open');
            }
        });
    });
});
