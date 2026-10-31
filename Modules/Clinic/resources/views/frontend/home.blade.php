@php
    $activeCount = isset($activeModuleWebsites) ? $activeModuleWebsites->count() : \App\Models\Website::activeModules()->count();
    $basePrefix = $activeCount === 1 ? '' : '/fertility-clinic';
    $homeUrl = $basePrefix === '' ? url('/') : url($basePrefix);
    $img = fn (string $file) => route('module.media.serve', ['module' => 'Clinic', 'folder' => 'theme', 'filename' => $file]) . '?v=' . (@filemtime(module_path('Clinic', 'Public/uploads/theme/' . $file)) ?: time());
    $cssUrl = route('clinic.asset', ['type' => 'css', 'file' => 'style.css']) . '?v=' . @filemtime(module_path('Clinic', 'Public/assets/css/style.css'));
    $jsUrl = route('clinic.asset', ['type' => 'js', 'file' => 'main.js']) . '?v=' . @filemtime(module_path('Clinic', 'Public/assets/js/main.js'));
    $isSplitView = request()->query('view') === 'split';
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FertilityClinic.com — Your Journey to Parenthood Starts Here</title>

    <!-- Google Fonts: Plus Jakarta Sans & Caveat (Handwritten Script Callouts) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Caveat:wght@600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome 6 Free Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <!-- Standalone Clinic Theme CSS -->
    <link rel="stylesheet" href="{{ $cssUrl }}">
</head>
<body class="{{ $isSplitView ? 'fc-split-mode' : '' }}">

<div class="fc-site-wrapper">

    <!-- ===================================================================
         LEFT WEBSITE PAGE (SECTIONS 1 TO 7)
         =================================================================== -->
    <div class="fc-page-sheet fc-left-sheet">

        <!-- 1. HEADER WITH LOGO, NAVIGATION, AND BOOK APPOINTMENT BUTTON -->
        <header class="fc-header">
            <div class="fc-container">
                <nav class="fc-navbar">
                    <!-- Brand Logo -->
                    <a href="{{ $homeUrl }}" class="fc-logo">
                        <svg class="fc-logo-icon" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M18 32C18 32 4 23.2 4 12.8C4 7.8 7.9 4 12.6 4C15.4 4 17.2 5.4 18 7.1C18.8 5.4 20.6 4 23.4 4C28.1 4 32 7.8 32 12.8C32 23.2 18 32 18 32Z" fill="#14B89A"/>
                            <circle cx="14.5" cy="13.5" r="2.2" fill="#FFFFFF"/>
                            <circle cx="21.5" cy="13.5" r="2.2" fill="#FFFFFF"/>
                            <path d="M11.5 20.5C12.5 17.8 16.5 17.8 18 20.2C19.5 17.8 23.5 17.8 24.5 20.5" stroke="#FFFFFF" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                        <span class="fc-logo-text">Fertility<span>Clinic.com</span></span>
                    </a>

                    <!-- Navigation Links -->
                    <ul class="fc-nav-list" id="fcNavList">
                        <li><a href="{{ $homeUrl }}" class="active">Home</a></li>
                        <li><a href="#about">About</a></li>
                        <li><a href="#services">Services</a></li>
                        <li><a href="#stories">Success Stories</a></li>
                        <li><a href="#blog">Blog</a></li>
                        <li><a href="#contact">Contact</a></li>
                    </ul>

                    <!-- Right Actions -->
                    <div class="fc-nav-actions">
                        <button type="button" class="fc-search-btn" id="fcSearchBtn" aria-label="Search">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </button>
                        <a href="#contact" class="fc-btn-primary">Book Appointment</a>
                        <button type="button" class="fc-mobile-toggle" id="fcMobileToggle" aria-label="Menu">
                            <i class="fa-solid fa-bars"></i>
                        </button>
                    </div>
                </nav>
            </div>
        </header>

        <!-- 2. HERO SECTION WITH HEADING, DESCRIPTION, COUPLE IMAGE, AND CTA BUTTONS -->
        <section class="fc-hero">
            <div class="fc-container">
                <div class="fc-hero-grid">
                    <!-- Left Hero Content -->
                    <div class="fc-hero-content">
                        <span class="fc-pill-badge">Trusted Fertility Care</span>
                        <h1 class="fc-h1">
                            Your Journey<br>
                            to Parenthood<br>
                            Starts Here
                        </h1>
                        <p class="fc-hero-desc">
                            Advanced fertility care, personalized treatment and compassionate support — helping you build the family you've always dreamed of.
                        </p>

                        <!-- CTA Buttons -->
                        <div class="fc-hero-actions">
                            <a href="#contact" class="fc-btn-primary">
                                <span>Book Your Consultation</span>
                                <i class="fa-solid fa-arrow-right"></i>
                            </a>
                            <button type="button" class="fc-btn-video" id="fcWatchVideoBtn">
                                <span class="fc-play-circle">
                                    <i class="fa-solid fa-play"></i>
                                </span>
                                <span>Watch Video</span>
                            </button>
                        </div>

                        <!-- 10,000+ Happy Families Social Proof -->
                        <div class="fc-hero-trust">
                            <div class="fc-avatar-stack">
                                <img src="{{ $img('avatar1.jpg') }}" alt="Happy Family 1">
                                <img src="{{ $img('avatar2.jpg') }}" alt="Happy Family 2">
                                <img src="{{ $img('avatar3.jpg') }}" alt="Happy Family 3">
                                <img src="{{ $img('doc1.jpg') }}" alt="Happy Family 4">
                            </div>
                            <div class="fc-trust-meta">
                                <strong>10,000+</strong>
                                <span>Happy Families</span>
                            </div>
                        </div>
                    </div>

                    <!-- Right Hero Couple Image & Floating Elements -->
                    <div class="fc-hero-visual">
                        <div class="fc-hero-photo-wrap">
                            <img src="{{ $img('hero-couple.jpg') }}" alt="Happy Expecting Couple Holding Pregnancy Test" class="fc-hero-photo">
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 3. ADVANCED FERTILITY SERVICES SECTION WITH FOUR SERVICE CARDS -->
        <section class="fc-services-sec" id="services">
            <div class="fc-container">
                <div class="fc-mint-panel">
                    <!-- Section Header -->
                    <div class="fc-sec-head-split">
                        <div class="fc-bar-heading">
                            <span class="fc-bar-accent"></span>
                            <h2 class="fc-h2">
                                Advanced Fertility Services<br>
                                for Every Stage of Your Journey
                            </h2>
                        </div>
                        <span class="fc-tagline-right">Personalized Care. Better Outcomes.</span>
                    </div>

                    <!-- 4 Service Cards -->
                    <div class="fc-services-grid">
                        <!-- Card 1: IVF Treatment -->
                        <div class="fc-service-card">
                            <div class="fc-service-icon fc-icon-mint">
                                <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="11" cy="12" r="7"/>
                                    <circle cx="16.5" cy="8.5" r="2.5"/>
                                </svg>
                            </div>
                            <h3>IVF Treatment</h3>
                            <p>High success rates<br>with advanced technology</p>
                            <a href="#contact" class="fc-card-arrow" aria-label="IVF Treatment">
                                <i class="fa-solid fa-arrow-right"></i>
                            </a>
                        </div>

                        <!-- Card 2: IUI Treatment -->
                        <div class="fc-service-card">
                            <div class="fc-service-icon fc-icon-blue">
                                <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M9 18h6M10 21h4M12 3a6 6 0 0 0-4 10.5V16h8v-2.5A6 6 0 0 0 12 3z"/>
                                </svg>
                            </div>
                            <h3>IUI Treatment</h3>
                            <p>Safe, simple and<br>effective solutions</p>
                            <a href="#contact" class="fc-card-arrow" aria-label="IUI Treatment">
                                <i class="fa-solid fa-arrow-right"></i>
                            </a>
                        </div>

                        <!-- Card 3: Fertility Testing -->
                        <div class="fc-service-card">
                            <div class="fc-service-icon fc-icon-purple">
                                <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="13" r="7"/>
                                    <path d="M12 10v3l2 1.5M10 3h4M12 3v3"/>
                                </svg>
                            </div>
                            <h3>Fertility Testing</h3>
                            <p>Find the right answers<br>for your health</p>
                            <a href="#contact" class="fc-card-arrow" aria-label="Fertility Testing">
                                <i class="fa-solid fa-arrow-right"></i>
                            </a>
                        </div>

                        <!-- Card 4: Reproductive Surgery -->
                        <div class="fc-service-card">
                            <div class="fc-service-icon fc-icon-mint">
                                <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M9.5 12.5l2 2 4.5-4.5"/>
                                    <path d="M12 21s-7-4.5-7-10a4 4 0 0 1 7-2.5A4 4 0 0 1 19 11c0 5.5-7 10-7 10z"/>
                                </svg>
                            </div>
                            <h3>Reproductive Surgery</h3>
                            <p>Minimally invasive<br>for better outcomes</p>
                            <a href="#contact" class="fc-card-arrow" aria-label="Reproductive Surgery">
                                <i class="fa-solid fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 4. WHY CHOOSE US SECTION WITH DOCTOR IMAGE AND FEATURE LIST -->
        <section class="fc-why-sec" id="about">
            <div class="fc-container">
                <div class="fc-why-grid">
                    <!-- Left Doctor Image -->
                    <div class="fc-why-photo-wrap">
                        <img src="{{ $img('why-doctor.jpg') }}" alt="Doctor Consulting a Patient">
                    </div>

                    <!-- Right Content & Feature Checklist -->
                    <div class="fc-why-content">
                        <span class="fc-pill-badge">Why Choose Us</span>
                        <h2 class="fc-h2">
                            Expertise. Technology.<br>
                            Compassion.
                        </h2>
                        <p class="fc-subtitle">
                            At FertilityClinic.com, we combine world-class medical expertise with the latest technology to give you the best chance at success.
                        </p>

                        <ul class="fc-check-list">
                            <li>
                                <span class="fc-check-icon"><i class="fa-solid fa-check"></i></span>
                                <span>Experienced Fertility Specialists</span>
                            </li>
                            <li>
                                <span class="fc-check-icon"><i class="fa-solid fa-check"></i></span>
                                <span>State-of-the-Art Laboratory</span>
                            </li>
                            <li>
                                <span class="fc-check-icon"><i class="fa-solid fa-check"></i></span>
                                <span>Personalized Treatment Plans</span>
                            </li>
                            <li>
                                <span class="fc-check-icon"><i class="fa-solid fa-check"></i></span>
                                <span>Emotional &amp; Professional Support</span>
                            </li>
                        </ul>

                        <a href="#contact" class="fc-btn-primary">
                            <span>Learn More</span>
                            <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- 5. REAL PEOPLE, REAL STORIES STATISTICS SECTION -->
        <section class="fc-stats-sec">
            <div class="fc-container">
                <div class="fc-mint-panel">
                    <div class="fc-stats-header">
                        <h2 class="fc-h2">Real People. Real Stories.</h2>
                        <p class="fc-subtitle">Thousands of families have found hope through our care.</p>
                    </div>

                    <div class="fc-stats-grid">
                        <!-- Stat 1 -->
                        <div class="fc-stat-item">
                            <div class="fc-stat-icon">
                                <i class="fa-solid fa-heart"></i>
                            </div>
                            <strong class="fc-stat-num">10,000+</strong>
                            <span class="fc-stat-label">Successful Pregnancies</span>
                        </div>

                        <!-- Stat 2 -->
                        <div class="fc-stat-item">
                            <div class="fc-stat-icon">
                                <i class="fa-regular fa-star"></i>
                            </div>
                            <strong class="fc-stat-num">98%</strong>
                            <span class="fc-stat-label">Patient Satisfaction</span>
                        </div>

                        <!-- Stat 3 -->
                        <div class="fc-stat-item">
                            <div class="fc-stat-icon">
                                <i class="fa-solid fa-briefcase-medical"></i>
                            </div>
                            <strong class="fc-stat-num">15+</strong>
                            <span class="fc-stat-label">Years of Experience</span>
                        </div>

                        <!-- Stat 4 -->
                        <div class="fc-stat-item">
                            <div class="fc-stat-icon">
                                <i class="fa-solid fa-user-doctor"></i>
                            </div>
                            <strong class="fc-stat-num">20+</strong>
                            <span class="fc-stat-label">Expert Doctors</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 6. PATIENT TESTIMONIALS SECTION -->
        <section class="fc-testimonials-sec" id="stories">
            <div class="fc-container">
                <div class="fc-sec-head-split">
                    <div>
                        <h2 class="fc-h2">What Our Patients Say</h2>
                        <p class="fc-subtitle">Real experiences. Real hope.</p>
                    </div>
                    <div class="fc-slider-Mix">
                        <button type="button" class="fc-Round-btn" id="fcTestiPrev" aria-label="Previous Testimonial">
                            <i class="fa-solid fa-chevron-left"></i>
                        </button>
                        <button type="button" class="fc-Round-btn" id="fcTestiNext" aria-label="Next Testimonial">
                            <i class="fa-solid fa-chevron-right"></i>
                        </button>
                    </div>
                </div>

                <div class="fc-testimonials-grid" id="fcTestimonialsGrid">
                    <!-- Testimonial 1 -->
                    <div class="fc-testi-card">
                        <div>
                            <div class="fc-testi-user">
                                <img src="{{ $img('avatar1.jpg') }}" alt="Sarah M.">
                                <div>
                                    <strong>Sarah M.</strong>
                                    <span>USA</span>
                                </div>
                            </div>
                            <p class="fc-testi-quote">
                                "This was a FertilityClinic.com and amazing. They gave us hope when we needed it most."
                            </p>
                        </div>
                        <div class="fc-stars">
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                        </div>
                    </div>

                    <!-- Testimonial 2 -->
                    <div class="fc-testi-card">
                        <div>
                            <div class="fc-testi-user">
                                <img src="{{ $img('avatar2.jpg') }}" alt="James & Emily">
                                <div>
                                    <strong>James &amp; Emily</strong>
                                    <span>UK</span>
                                </div>
                            </div>
                            <p class="fc-testi-quote">
                                "Professional, caring and extremely supportive throughout our journey."
                            </p>
                        </div>
                        <div class="fc-stars">
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                        </div>
                    </div>

                    <!-- Testimonial 3 -->
                    <div class="fc-testi-card">
                        <div>
                            <div class="fc-testi-user">
                                <img src="{{ $img('avatar3.jpg') }}" alt="Priya S.">
                                <div>
                                    <strong>Priya S.</strong>
                                    <span>Canada</span>
                                </div>
                            </div>
                            <p class="fc-testi-quote">
                                "We are so grateful for the incredible care and support we received."
                            </p>
                        </div>
                        <div class="fc-stars">
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 7. FINAL APPOINTMENT PROMOTIONAL BANNER -->
        <section class="fc-cta-family-sec">
            <div class="fc-container">
                <div class="fc-cta-family-box">
                    <div class="fc-cta-family-content">
                        <span class="fc-pill-badge fc-pill-badge-glass">Take the First Step</span>
                        <h2>
                            Your Family<br>
                            Is Closer Than You Think
                        </h2>
                        <p>
                            Book a consultation today and let our experts help you on your fertility journey.
                        </p>
                        <a href="#contact" class="fc-btn-white">
                            <span>Book Appointment</span>
                            <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                    <div class="fc-cta-family-media">
                        <img src="{{ $img('cta-hands.jpg') }}" alt="Parent Hands Holding Baby Hand">
                    </div>
                </div>
            </div>
        </section>

    </div><!-- /.fc-left-sheet -->


    <!-- ===================================================================
         RIGHT WEBSITE PAGE (SECTIONS 8 TO 13)
         =================================================================== -->
    <div class="fc-page-sheet fc-right-sheet">

        <!-- 8. FERTILITY SUCCESS RATE SECTION WITH STATISTICS AND BABY HAND IMAGE -->
        <section class="fc-success-sec">
            <div class="fc-container">
                <div class="fc-success-grid">
                    <!-- Left Statistics Content -->
                    <div>
                        <span class="fc-pill-badge fc-pill-badge-rose">Our Success Rate</span>
                        <h2 class="fc-h2">
                            More Possibilities.<br>
                            More Happy Families.
                        </h2>
                        <p class="fc-subtitle" style="max-width: 440px;">
                            Our success is measured in the dreams we help come true. With advanced technology and personalized care, we give you the best chance at parenthood.
                        </p>

                        <div class="fc-success-metrics">
                            <div class="fc-metric-box">
                                <strong>70%</strong>
                                <span>IVF Success Rate</span>
                            </div>
                            <div class="fc-metric-box">
                                <strong>85%</strong>
                                <span>IUI Success Rate</span>
                            </div>
                            <div class="fc-metric-box">
                                <strong>95%</strong>
                                <span>Patient Satisfaction</span>
                            </div>
                        </div>
                    </div>

                    <!-- Right Baby Hand Holding Adult Finger Graphic -->
                    <div class="fc-baby-visual">
                        <img src="{{ $img('baby-hand.jpg') }}" alt="Baby Holding an Adult's Finger" class="fc-baby-full-graphic">
                    </div>
                </div>
            </div>
        </section>

        <!-- 9. MEET OUR FERTILITY SPECIALISTS SECTION WITH FOUR DOCTOR CARDS -->
        <section class="fc-doctors-sec" id="doctors">
            <div class="fc-container">
                <div class="fc-sec-head-split">
                    <div>
                        <span class="fc-pill-badge">Our Expert Team</span>
                        <h2 class="fc-h2">Meet Our Fertility Specialists</h2>
                        <p class="fc-subtitle">Compassionate professionals, dedicated to your journey.</p>
                    </div>
                    <a href="#contact" class="fc-link-arrow">
                        <span>View All Doctors</span>
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>

                <div class="fc-doctors-grid">
                    <!-- Doctor 1 -->
                    <div class="fc-doctor-card">
                        <div class="fc-doc-avatar-wrap">
                            <img src="{{ $img('doc1.jpg') }}" alt="Dr. Sarah Johnson">
                        </div>
                        <h3>Dr. Sarah Johnson</h3>
                        <span class="fc-doc-spec">Reproductive Endocrinologist</span>
                        <div class="fc-doc-exp">
                            <i class="fa-regular fa-circle-check"></i>
                            <span>12+ Years Experience</span>
                        </div>
                        <a href="#contact" class="fc-doc-link">
                            <span>View Profile</span>
                            <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>

                    <!-- Doctor 2 -->
                    <div class="fc-doctor-card">
                        <div class="fc-doc-avatar-wrap">
                            <img src="{{ $img('doc2.jpg') }}" alt="Dr. Michael Lee">
                        </div>
                        <h3>Dr. Michael Lee</h3>
                        <span class="fc-doc-spec">Fertility Specialist</span>
                        <div class="fc-doc-exp">
                            <i class="fa-regular fa-circle-check"></i>
                            <span>10+ Years Experience</span>
                        </div>
                        <a href="#contact" class="fc-doc-link">
                            <span>View Profile</span>
                            <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>

                    <!-- Doctor 3 -->
                    <div class="fc-doctor-card">
                        <div class="fc-doc-avatar-wrap">
                            <img src="{{ $img('doc3.jpg') }}" alt="Dr. Aisha Rahman">
                        </div>
                        <h3>Dr. Aisha Rahman</h3>
                        <span class="fc-doc-spec">IVF Specialist</span>
                        <div class="fc-doc-exp">
                            <i class="fa-regular fa-circle-check"></i>
                            <span>9+ Years Experience</span>
                        </div>
                        <a href="#contact" class="fc-doc-link">
                            <span>View Profile</span>
                            <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>

                    <!-- Doctor 4 -->
                    <div class="fc-doctor-card">
                        <div class="fc-doc-avatar-wrap">
                            <img src="{{ $img('doc4.jpg') }}" alt="Dr. Daniel Carter">
                        </div>
                        <h3>Dr. Daniel Carter</h3>
                        <span class="fc-doc-spec">Andrologist</span>
                        <div class="fc-doc-exp">
                            <i class="fa-regular fa-circle-check"></i>
                            <span>11+ Years Experience</span>
                        </div>
                        <a href="#contact" class="fc-doc-link">
                            <span>View Profile</span>
                            <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- 10. DARK TEAL PROMOTIONAL CTA BANNER -->
        <section class="fc-priority-sec">
            <div class="fc-container">
                <div class="fc-priority-banner">
                    <!-- Bottom-Left Decorative Emerald Wave -->
                    <div class="fc-priority-deco-left"></div>

                    <!-- Left Heart Stethoscope Icon + Heading -->
                    <div class="fc-priority-left">
                        <svg class="fc-priority-heart" viewBox="0 0 72 72" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M36 60C36 60 10 44.2 10 25.5C10 16.5 17 10 25.5 10C30.4 10 34.2 12.6 36 15.8C37.8 12.6 41.6 10 46.5 10C55 10 62 16.5 62 25.5C62 44.2 36 60 36 60Z" fill="#14B89A" fill-opacity="0.25" stroke="#FFFFFF" stroke-width="4"/>
                            <path d="M28 24V31C28 35.4 31.6 39 36 39C40.4 39 44 35.4 44 31V24" stroke="#FFFFFF" stroke-width="3" stroke-linecap="round"/>
                            <path d="M36 39V47" stroke="#FFFFFF" stroke-width="3" stroke-linecap="round"/>
                            <circle cx="36" cy="48" r="3" fill="#14B89A" stroke="#FFFFFF" stroke-width="2"/>
                        </svg>
                        <div class="fc-priority-text">
                            <h3>Your Fertility, Our Priority.</h3>
                            <p>Trusted by families around the world for compassionate and advanced fertility care.</p>
                        </div>
                    </div>

                    <!-- Right CTA Button & Decorative Heart Line -->
                    <div class="fc-priority-right">
                        <a href="#contact" class="fc-btn-white">
                            <span>Book Appointment</span>
                            <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>

                    <!-- Bottom-Right Decorative Line & Leaves SVG -->
                    <svg class="fc-priority-deco-right" width="160" height="36" viewBox="0 0 160 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M4 26H95C102 26 106 18 110 14C114 18 118 26 125 26H154" stroke="rgba(255,255,255,0.45)" stroke-width="1.2"/>
                        <path d="M136 24C138 14 148 10 156 12C152 22 144 26 136 24Z" fill="#10B981"/>
                        <path d="M122 28C124 20 132 18 138 20C135 27 128 30 122 28Z" fill="#059669"/>
                    </svg>
                </div>
            </div>
        </section>

        <!-- 11. HELPFUL ARTICLES SECTION WITH THREE ARTICLE CARDS -->
        <section class="fc-blog-sec" id="blog">
            <div class="fc-container">
                <div class="fc-sec-head-split">
                    <div>
                        <span class="fc-pill-badge fc-pill-badge-blue">Latest News &amp; Insights</span>
                        <h2 class="fc-h2">Helpful Articles for Your Journey</h2>
                        <p class="fc-subtitle">Get the latest tips, advice, and knowledge about fertility, IVF, and family-building.</p>
                    </div>
                    <a href="#blog" class="fc-link-arrow">
                        <span>View All Blog</span>
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>

                <div class="fc-blog-grid">
                    <!-- Article 1 -->
                    <article class="fc-blog-card">
                        <div class="fc-blog-thumb">
                            <img src="{{ $img('blog1.jpg') }}" alt="10 Tips to Improve Your Fertility Naturally">
                        </div>
                        <div class="fc-blog-body">
                            <h3>10 Tips to Improve<br>Your Fertility Naturally</h3>
                            <span class="fc-blog-date">May 14, 2024</span>
                            <a href="#contact" class="fc-blog-more">
                                <span>Read More</span>
                                <i class="fa-solid fa-arrow-right"></i>
                            </a>
                        </div>
                    </article>

                    <!-- Article 2 -->
                    <article class="fc-blog-card">
                        <div class="fc-blog-thumb">
                            <img src="{{ $img('blog2.jpg') }}" alt="Understanding IVF: What You Need to Know">
                        </div>
                        <div class="fc-blog-body">
                            <h3>Understanding IVF:<br>What You Need to Know</h3>
                            <span class="fc-blog-date">May 14, 2024</span>
                            <a href="#contact" class="fc-blog-more">
                                <span>Read More</span>
                                <i class="fa-solid fa-arrow-right"></i>
                            </a>
                        </div>
                    </article>

                    <!-- Article 3 -->
                    <article class="fc-blog-card">
                        <div class="fc-blog-thumb">
                            <img src="{{ $img('blog3.jpg') }}" alt="Emotional Support During the Fertility Journey">
                        </div>
                        <div class="fc-blog-body">
                            <h3>Emotional Support During<br>the Fertility Journey</h3>
                            <span class="fc-blog-date">May 14, 2024</span>
                            <a href="#contact" class="fc-blog-more">
                                <span>Read More</span>
                                <i class="fa-solid fa-arrow-right"></i>
                            </a>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <!-- 12. APPOINTMENT CONTACT FORM SECTION WITH DOCTOR IMAGE -->
        <section class="fc-appointment-sec" id="contact">
            <div class="fc-container">
                <div class="fc-appointment-box">
                    <!-- Left Form Column -->
                    <div class="fc-appointment-form-col">
                        <span class="fc-pill-badge" style="background: #CBECE4;">Book Your Appointment</span>
                        <h2 class="fc-h2">
                            Let's Talk About<br>
                            Your Fertility Goals
                        </h2>
                        <p class="fc-subtitle">
                            Fill out the form and our team will get back to you as soon as possible.
                        </p>

                        <form id="fcAppointmentForm">
                            <div class="fc-form-grid">
                                <input type="text" class="fc-input" name="full_name" placeholder="Full Name" required>
                                <input type="email" class="fc-input" name="email" placeholder="Email Address" required>
                                <select class="fc-select" name="service" required>
                                    <option value="" selected disabled>Select Service</option>
                                    <option value="IVF Treatment">IVF Treatment</option>
                                    <option value="IUI Treatment">IUI Treatment</option>
                                    <option value="Fertility Testing">Fertility Testing</option>
                                    <option value="Reproductive Surgery">Reproductive Surgery</option>
                                    <option value="Consultation">General Consultation</option>
                                </select>
                                <input type="tel" class="fc-input" name="phone" placeholder="Phone Number" required>
                            </div>

                            <button type="submit" class="fc-btn-primary">
                                <span>Submit Request</span>
                                <i class="fa-solid fa-arrow-right"></i>
                            </button>

                            <div id="fcFormSuccessMsg" style="display: none; margin-top: 12px; padding: 10px 14px; border-radius: 10px; background: #D1FAE5; color: #065F46; font-size: 12.5px; font-weight: 600;">
                                <i class="fa-solid fa-circle-check"></i> Thank you! Your appointment request has been received.
                            </div>
                        </form>
                    </div>

                    <!-- Right Doctor Image & Handwritten Callout -->
                    <div class="fc-appointment-doc-col">
                        <img src="{{ $img('form-doctor.jpg') }}" alt="Fertility Specialist Ready to Help">
                    </div>
                </div>
            </div>
        </section>

        <!-- 13. DARK NAVY FOOTER WITH LOGO, QUICK LINKS, SERVICES, AND CONTACT INFORMATION -->
        <footer class="fc-footer">
            <div class="fc-container">
                <div class="fc-footer-grid">
                    <!-- Column 1: Brand -->
                    <div class="fc-footer-brand">
                        <a href="{{ $homeUrl }}" class="fc-logo">
                            <svg class="fc-logo-icon" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M18 32C18 32 4 23.2 4 12.8C4 7.8 7.9 4 12.6 4C15.4 4 17.2 5.4 18 7.1C18.8 5.4 20.6 4 23.4 4C28.1 4 32 7.8 32 12.8C32 23.2 18 32 18 32Z" fill="#FFFFFF"/>
                                <circle cx="14.5" cy="13.5" r="2.2" fill="#072F40"/>
                                <circle cx="21.5" cy="13.5" r="2.2" fill="#072F40"/>
                                <path d="M11.5 20.5C12.5 17.8 16.5 17.8 18 20.2C19.5 17.8 23.5 17.8 24.5 20.5" stroke="#072F40" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                            <span class="fc-logo-text">Fertility<span>Clinic.com</span></span>
                        </a>
                        <p class="fc-footer-tagline">Hope &bull; Science &bull; Families</p>
                    </div>

                    <!-- Column 2: Quick Links -->
                    <div class="fc-footer-col">
                        <h4>Quick Links</h4>
                        <ul class="fc-footer-links">
                            <li><a href="{{ $homeUrl }}">Home</a></li>
                            <li><a href="#about">About Us</a></li>
                            <li><a href="#services">Services</a></li>
                            <li><a href="#stories">Success Stories</a></li>
                            <li><a href="#blog">Blog</a></li>
                            <li><a href="#contact">Contact</a></li>
                        </ul>
                    </div>

                    <!-- Column 3: Our Services -->
                    <div class="fc-footer-col">
                        <h4>Our Services</h4>
                        <ul class="fc-footer-links">
                            <li><a href="#services">IVF Treatment</a></li>
                            <li><a href="#services">IUI Treatment</a></li>
                            <li><a href="#services">Fertility Testing</a></li>
                            <li><a href="#services">Reproductive Surgery</a></li>
                            <li><a href="#contact">Consultation</a></li>
                        </ul>
                    </div>

                    <!-- Column 4: Contact Us -->
                    <div class="fc-footer-col">
                        <h4>Contact Us</h4>
                        <ul class="fc-footer-links">
                            <li>
                                <i class="fa-solid fa-phone" style="color: #14B89A; margin-top: 3px;"></i>
                                <span>+1 (800) 123-4567</span>
                            </li>
                            <li>
                                <i class="fa-solid fa-envelope" style="color: #14B89A; margin-top: 3px;"></i>
                                <span>info@fertilityclinic.com</span>
                            </li>
                            <li>
                                <i class="fa-solid fa-location-dot" style="color: #14B89A; margin-top: 3px;"></i>
                                <span>123 Health Avenue,<br>New York, NY 10001</span>
                            </li>
                        </ul>

                        <div class="fc-footer-socials">
                            <a href="#contact" class="fc-social-icon" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                            <a href="#contact" class="fc-social-icon" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                            <a href="#contact" class="fc-social-icon" aria-label="LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
                            <a href="#contact" class="fc-social-icon" aria-label="YouTube"><i class="fa-brands fa-youtube"></i></a>
                        </div>
                    </div>
                </div>

                <!-- Copyright Bar -->
                <div class="fc-footer-bottom">
                    <div>&copy; 2024 FertilityClinic.com. All rights reserved.</div>
                    <div class="fc-footer-legal">
                        <a href="#privacy">Privacy Policy</a>
                        <span>|</span>
                        <a href="#terms">Terms &amp; Conditions</a>
                    </div>
                </div>
            </div>
        </footer>

    </div><!-- /.fc-right-sheet -->

</div><!-- /.fc-site-wrapper -->

<!-- Video Modal -->
<div class="fc-modal-backdrop" id="fcVideoModal">
    <div class="fc-modal-card">
        <button type="button" class="fc-modal-close" id="fcCloseVideoModal">&times;</button>
        <span class="fc-pill-badge">Patient Care Story</span>
        <h3 class="fc-h2" style="font-size: 20px; margin-bottom: 10px;">Welcome to FertilityClinic.com</h3>
        <p class="fc-subtitle" style="margin-bottom: 16px;">
            Discover how our board-certified reproductive endocrinologists and state-of-the-art IVF laboratory help over 10,000 families achieve their dream of parenthood.
        </p>
        <div style="background: #072F40; border-radius: 14px; padding: 36px 20px; text-align: center; color: #fff;">
            <i class="fa-solid fa-circle-play" style="font-size: 44px; color: #14B89A; margin-bottom: 10px;"></i>
            <div style="font-weight: 700; font-size: 14px;">FertilityClinic.com — Patient Journey Overview</div>
            <div style="font-size: 12px; opacity: 0.75; margin-top: 4px;">Duration: 2:45 &bull; Full HD</div>
        </div>
    </div>
</div>

<!-- Search Modal -->
<div class="fc-modal-backdrop" id="fcSearchModal">
    <div class="fc-modal-card">
        <button type="button" class="fc-modal-close" id="fcCloseSearchModal">&times;</button>
        <h3 class="fc-h2" style="font-size: 20px; margin-bottom: 12px;">Search FertilityClinic.com</h3>
        <form action="#services" onsubmit="document.getElementById('fcSearchModal').classList.remove('open');">
            <input type="text" class="fc-input" placeholder="Search IVF, IUI, Fertility Testing, Doctors..." style="margin-bottom: 12px;">
            <button type="submit" class="fc-btn-primary" style="width: 100%;">Search</button>
        </form>
    </div>
</div>

<!-- Standalone Clinic Theme JS -->
<script src="{{ $jsUrl }}"></script>
</body>
</html>
