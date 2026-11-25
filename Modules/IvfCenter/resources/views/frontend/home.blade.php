@extends('ivfcenter::frontend.layouts.master')

@php
    // All theme images live inside Modules/IvfCenter/Public/uploads/theme (self-contained module media)
    $img = fn (string $file) => route('ivfcenter.media', ['folder' => 'theme', 'file' => $file])
        . '?v=' . (@filemtime(module_path('IvfCenter', 'Public/uploads/theme/' . $file)) ?: time());

    // Reusable brand mark (heart + family + floating mini hearts matching mockup)
    $logoSvg = '<svg viewBox="0 0 56 52" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
        <defs>
            <linearGradient id="ivfHeartLeft" x1="4" y1="6" x2="28" y2="46" gradientUnits="userSpaceOnUse">
                <stop stop-color="#159f93"/>
                <stop offset="1" stop-color="#0d7a7b"/>
            </linearGradient>
            <linearGradient id="ivfHeartRight" x1="26" y1="6" x2="50" y2="46" gradientUnits="userSpaceOnUse">
                <stop stop-color="#6b2cc9"/>
                <stop offset="1" stop-color="#44177e"/>
            </linearGradient>
        </defs>
        <!-- Small decorative floating hearts on top-right -->
        <path d="M35.5 7.2c-.8-1.4-2.8-1.6-3.8-.3-.9 1.1-.4 2.7 1.9 4.2 2.3-1.5 2.8-3.1 1.9-4.2-1-1.3-3-.9-3.8.3Z" fill="#7c3aed" opacity=".85"/>
        <!-- Left Teal Heart Arc -->
        <path d="M27 45.5C27 45.5 6 33.8 6 19.2 6 12 11.5 6.8 18.2 6.8c3.9 0 7 1.9 8.8 4.8" stroke="url(#ivfHeartLeft)" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
        <!-- Right Purple Heart Arc -->
        <path d="M27 11.6c1.8-2.9 4.9-4.8 8.8-4.8 6.7 0 12.2 5.2 12.2 12.4 0 14.6-21 26.3-21 26.3" stroke="url(#ivfHeartRight)" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
        <!-- Family inside heart: Left Parent (Teal), Right Parent (Purple), Baby (Center) -->
        <circle cx="21" cy="19.5" r="3.4" fill="#108e86"/>
        <circle cx="33" cy="19.5" r="3.4" fill="#4b1d8f"/>
        <circle cx="27" cy="26.5" r="2.7" fill="#159f93"/>
        <path d="M15.5 31.5c1.6-5.2 5.1-7.4 8.8-6.4" stroke="#108e86" stroke-width="3.2" stroke-linecap="round"/>
        <path d="M38.5 31.5c-1.6-5.2-5.1-7.4-8.8-6.4" stroke="#4b1d8f" stroke-width="3.2" stroke-linecap="round"/>
        <path d="M20.5 34.5c2 2.4 4.3 3.6 6.5 3.6s4.5-1.2 6.5-3.6" stroke="#4b1d8f" stroke-width="2.8" stroke-linecap="round"/>
    </svg>';

    // Decorative leafy branch for the testimonials section
    $leafSvg = '<svg viewBox="0 0 170 300" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
        <path d="M20 300C40 230 70 160 120 20" stroke="#cfe9e5" stroke-width="3" stroke-linecap="round"/>
        <ellipse cx="58" cy="230" rx="30" ry="12" transform="rotate(-35 58 230)" fill="#dcefeb"/>
        <ellipse cx="96" cy="210" rx="30" ry="12" transform="rotate(-140 96 210)" fill="#e6f4f1"/>
        <ellipse cx="70" cy="160" rx="30" ry="12" transform="rotate(-40 70 160)" fill="#dcefeb"/>
        <ellipse cx="112" cy="140" rx="30" ry="12" transform="rotate(-145 112 140)" fill="#e6f4f1"/>
        <ellipse cx="90" cy="92" rx="28" ry="11" transform="rotate(-45 90 92)" fill="#dcefeb"/>
        <ellipse cx="128" cy="74" rx="26" ry="10" transform="rotate(-150 128 74)" fill="#e6f4f1"/>
        <ellipse cx="118" cy="30" rx="22" ry="9" transform="rotate(-60 118 30)" fill="#dcefeb"/>
    </svg>';
@endphp

@section('title', 'IVF Center.com — Your Dream of Parenthood Is Our Priority')

@section('content')

<div class="ivf-page">

    {{-- ================================================================
         1. HEADER & 2. HERO (Unified soft lavender-white atmospheric top)
         ================================================================ --}}
    <div class="ivf-top-wrap" id="home">
        <!-- Subtle decorative background waves -->
        <svg class="ivf-hero-waves" viewBox="0 0 1440 620" fill="none" preserveAspectRatio="none" aria-hidden="true">
            <path d="M-80 490C180 410 420 540 740 460C1060 380 1240 480 1520 430" stroke="#e9e0f8" stroke-width="2"/>
            <path d="M-80 540C220 460 460 580 780 500C1100 420 1280 510 1520 470" stroke="#efe8fb" stroke-width="1.5"/>
        </svg>

        <header class="ivf-header">
            <div class="ivf-container">
                <nav class="ivf-nav">
                    <a href="{{ url('/') }}" class="ivf-logo">
                        {!! $logoSvg !!}
                        <span class="ivf-logo-txt">IVF<small>Center.com</small></span>
                    </a>

                    <ul class="ivf-menu" id="ivfMenu">
                        <li><a href="#home" class="active">Home</a></li>
                        <li><a href="#about">About Us</a></li>
                        <li><a href="#services">Services</a></li>
                        <li><a href="#stories">Success Stories</a></li>
                        <li><a href="#faq">FAQ</a></li>
                    </ul>

                    <a href="#" class="ivf-btn ivf-btn-purple ivf-btn-sm ivf-nav-cta js-consult">
                        Book Consultation <i class="fa-solid fa-arrow-right"></i>
                    </a>
                    <button type="button" class="ivf-burger" id="ivfBurger" aria-label="Menu">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                </nav>
            </div>
        </header>

        <section class="ivf-hero">
            <div class="ivf-container">
                <div class="ivf-hero-grid">
                    <!-- Left Column: Hero Text -->
                    <div class="ivf-hero-text">
                        <span class="ivf-pill">
                            <i class="fa-solid fa-heart"></i> Advanced Fertility Care
                        </span>
                        <h1>
                            Your Dream<br>
                            of <span>Parenthood</span><br>
                            Is Our Priority
                        </h1>
                        <p>Expert fertility care, advanced technology,<br>and personalized support — all under one roof.</p>
                        <div class="ivf-hero-btns">
                            <a href="#" class="ivf-btn ivf-btn-purple js-consult">
                                Book Consultation <i class="fa-solid fa-arrow-right"></i>
                            </a>
                            <a href="#about" class="ivf-watch">
                                <span class="play"><i class="fa-solid fa-play"></i></span>
                                <span class="lbl">Watch Video</span>
                            </a>
                        </div>
                    </div>

                    <!-- Right Column: Hero Family Image with Soft Lavender Arch -->
                    <div class="ivf-hero-visual">
                        <div class="ivf-hero-aura"></div>
                        <div class="ivf-hero-ring"></div>
                        <div class="ivf-hero-photo">
                            <img src="{{ $img('hero-family.jpg') }}" alt="Happy parents holding their newborn baby">
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    {{-- ================================================================
         FEATURE STRIP (4 Columns overlapping bottom of Hero)
         ================================================================ --}}
    <div class="ivf-strip-wrap">
        <div class="ivf-container">
            <div class="ivf-strip">
                <div class="ivf-strip-item">
                    <span class="ivf-strip-ico">
                        <svg viewBox="0 0 40 40" fill="none">
                            <circle cx="20" cy="12" r="6" fill="#0f9b8e"/>
                            <path d="M9 33c1.2-6.5 5.4-10 11-10s9.8 3.5 11 10" stroke="#0f9b8e" stroke-width="2.6" stroke-linecap="round"/>
                            <path d="M15 23v5a5 5 0 0 0 10 0v-5" stroke="#4b1d8f" stroke-width="2.2" stroke-linecap="round"/>
                            <circle cx="25" cy="29" r="2" fill="#4b1d8f"/>
                        </svg>
                    </span>
                    <span>Expert Doctors<br>&amp; Specialists</span>
                </div>

                <div class="ivf-strip-item">
                    <span class="ivf-strip-ico">
                        <svg viewBox="0 0 40 40" fill="none">
                            <circle cx="15" cy="14" r="3" fill="#4b1d8f"/>
                            <path d="M23 9l4 4-7 7-4-4 7-7Z" stroke="#0f9b8e" stroke-width="2.4" stroke-linejoin="round"/>
                            <path d="M12 32h18M17 32v-5a6 6 0 0 1 12 0v5" stroke="#0f9b8e" stroke-width="2.4" stroke-linecap="round"/>
                            <path d="M9 24c2-2 5-2 7 0" stroke="#4b1d8f" stroke-width="2.2" stroke-linecap="round"/>
                        </svg>
                    </span>
                    <span>State-of-the-Art<br>Technology</span>
                </div>

                <div class="ivf-strip-item">
                    <span class="ivf-strip-ico">
                        <svg viewBox="0 0 40 40" fill="none">
                            <path d="M8 29l8-8 5 4 10-11" stroke="#0f9b8e" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M25 14h6v6" stroke="#0f9b8e" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M14 14c0-2 1.6-3.5 3.5-3.5 1.2 0 2.1.6 2.5 1.5.4-.9 1.3-1.5 2.5-1.5 1.9 0 3.5 1.5 3.5 3.5 0 3.3-6 6.5-6 6.5s-6-3.2-6-6.5Z" fill="#4b1d8f" opacity=".2"/>
                        </svg>
                    </span>
                    <span>High Success<br>Rate</span>
                </div>

                <div class="ivf-strip-item">
                    <span class="ivf-strip-ico">
                        <svg viewBox="0 0 40 40" fill="none">
                            <circle cx="20" cy="20" r="13" stroke="#0f9b8e" stroke-width="2.4"/>
                            <path d="M20 26.5s-6.5-4.1-6.5-8.4c0-2.1 1.7-3.7 3.7-3.7 1.2 0 2.2.6 2.8 1.6.6-1 1.6-1.6 2.8-1.6 2 0 3.7 1.6 3.7 3.7 0 4.3-6.5 8.4-6.5 8.4Z" stroke="#0f9b8e" stroke-width="2.2" fill="#e3f6f3"/>
                        </svg>
                    </span>
                    <span>Compassionate<br>Care</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ================================================================
         3. ABOUT IVF CENTER
         ================================================================ --}}
    <section class="ivf-about" id="about">
        <div class="ivf-container">
            <div class="ivf-about-grid">
                <!-- Left Circular Image + Floating Badge -->
                <div class="ivf-about-media">
                    <div class="ivf-about-circle-wrap">
                        <div class="ivf-about-circle">
                            <img src="{{ $img('about-doctor.jpg') }}" alt="Fertility doctor consulting a patient">
                        </div>
                        <div class="ivf-trust">
                            <i class="fa-solid fa-heart"></i>
                            <span>Trusted by</span>
                            <strong class="teal">Thousands</strong>
                            <strong>of Families</strong>
                        </div>
                    </div>
                </div>

                <!-- Right Text Content -->
                <div class="ivf-about-text">
                    <span class="ivf-pill">
                        <i class="fa-solid fa-notes-medical"></i> About IVF Center
                    </span>
                    <h2 class="ivf-title">
                        Complete Fertility<br>
                        Solutions Under <span class="t-hl">One Roof</span>
                    </h2>
                    <p>At IVF Center.com, we provide world-class fertility treatments with a caring approach, helping you build the family you've always dreamed of.</p>
                    <ul class="ivf-checks">
                        <li><i class="fa-solid fa-check"></i> Personalized Treatment Plans</li>
                        <li><i class="fa-solid fa-check"></i> Advanced Reproductive Technology</li>
                        <li><i class="fa-solid fa-check"></i> Ongoing Emotional Support</li>
                    </ul>
                    <a href="#services" class="ivf-btn ivf-btn-purple">
                        Learn More <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- ================================================================
         4. OUR SERVICES
         ================================================================ --}}
    <section class="ivf-services" id="services">
        <div class="ivf-container">
            <span class="ivf-pill">
                <i class="fa-solid fa-wand-magic-sparkles"></i> Our Services
            </span>
            <h2 class="ivf-title">
                Advanced Treatments for<br>
                Your <span class="t-hl">Fertility Journey</span>
            </h2>

            <div class="ivf-svc-grid">
                <!-- 1. IVF Treatment -->
                <div class="ivf-svc">
                    <div class="ivf-svc-ico">
                        <svg viewBox="0 0 48 48" fill="none">
                            <circle cx="24" cy="26" r="14" stroke="#0f9b8e" stroke-width="2.5" fill="#eaf8f6"/>
                            <path d="M24 12c0-3 2.5-5 5-4 1.5.6 2 2.5.5 4" stroke="#0f9b8e" stroke-width="2.4" stroke-linecap="round"/>
                            <circle cx="19" cy="24" r="1.8" fill="#0f9b8e"/>
                            <circle cx="29" cy="24" r="1.8" fill="#0f9b8e"/>
                            <path d="M20 30c1.2 1.8 2.6 2.6 4 2.6s2.8-.8 4-2.6" stroke="#0f9b8e" stroke-width="2.4" stroke-linecap="round"/>
                            <path d="M9 26a2 2 0 1 1 1 3.7M39 26a2 2 0 1 0-1 3.7" stroke="#0f9b8e" stroke-width="2.2" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <h3>IVF Treatment</h3>
                    <p>(In Vitro Fertilization)</p>
                </div>

                <!-- 2. ICSI Treatment -->
                <div class="ivf-svc">
                    <div class="ivf-svc-ico">
                        <svg viewBox="0 0 48 48" fill="none">
                            <circle cx="21" cy="26" r="12" stroke="#0f9b8e" stroke-width="2.5" fill="#eaf8f6"/>
                            <circle cx="20" cy="26" r="5" stroke="#0f9b8e" stroke-width="2" stroke-dasharray="2.5 2.5"/>
                            <path d="M28 19l11-7M25 22l7-4.5" stroke="#0f9b8e" stroke-width="2.5" stroke-linecap="round"/>
                            <path d="M12 14v18M8 32h10" stroke="#0f9b8e" stroke-width="2.4" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <h3>ICSI Treatment</h3>
                    <p>(Advanced Fertilization)</p>
                </div>

                <!-- 3. Egg Freezing -->
                <div class="ivf-svc">
                    <div class="ivf-svc-ico">
                        <svg viewBox="0 0 48 48" fill="none">
                            <circle cx="22" cy="22" r="12" stroke="#0f9b8e" stroke-width="2.5" fill="#eaf8f6"/>
                            <circle cx="22" cy="22" r="5" stroke="#0f9b8e" stroke-width="2" stroke-dasharray="2 2"/>
                            <path d="M31 31l8 8" stroke="#0f9b8e" stroke-width="3" stroke-linecap="round"/>
                            <path d="M22 14v4M22 26v4M14 22h4M26 22h4" stroke="#0f9b8e" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <h3>Egg Freezing</h3>
                    <p>(Fertility Preservation)</p>
                </div>

                <!-- 4. Donor Programs -->
                <div class="ivf-svc">
                    <div class="ivf-svc-ico">
                        <svg viewBox="0 0 48 48" fill="none">
                            <circle cx="24" cy="24" r="14" stroke="#0f9b8e" stroke-width="2.5" fill="#eaf8f6"/>
                            <path d="M24 30s-6-3.8-6-7.6c0-1.9 1.5-3.4 3.4-3.4 1.1 0 2 .5 2.6 1.4.6-.9 1.5-1.4 2.6-1.4 1.9 0 3.4 1.5 3.4 3.4 0 3.8-6 7.6-6 7.6Z" fill="#0f9b8e"/>
                            <path d="M24 10v3M35 15l-2 2M13 15l2 2" stroke="#0f9b8e" stroke-width="2.2" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <h3>Donor Programs</h3>
                    <p>(Egg &amp; Sperm)</p>
                </div>

                <!-- 5. Genetic Testing -->
                <div class="ivf-svc">
                    <div class="ivf-svc-ico">
                        <svg viewBox="0 0 48 48" fill="none">
                            <path d="M16 10c0 9 16 9 16 18s-16 9-16 18" stroke="#0f9b8e" stroke-width="2.6" stroke-linecap="round"/>
                            <path d="M32 10c0 9-16 9-16 18s16 9 16 18" stroke="#0f9b8e" stroke-width="2.6" stroke-linecap="round"/>
                            <path d="M18 15h12M18 33h12M20 24h8" stroke="#0f9b8e" stroke-width="2.2" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <h3>Genetic Testing</h3>
                    <p>(Embryo Screening)</p>
                </div>

                <!-- 6. Fertility Consultation -->
                <div class="ivf-svc">
                    <div class="ivf-svc-ico">
                        <svg viewBox="0 0 48 48" fill="none">
                            <path d="M16 12h16v9a8 8 0 0 1-16 0v-9Z" stroke="#0f9b8e" stroke-width="2.5" fill="#eaf8f6"/>
                            <path d="M16 15h-4a3 3 0 0 0 0 6h4M32 15h4a3 3 0 0 1 0 6h-4" stroke="#0f9b8e" stroke-width="2.4" stroke-linecap="round"/>
                            <path d="M24 29v7M17 36h14" stroke="#0f9b8e" stroke-width="2.5" stroke-linecap="round"/>
                            <circle cx="24" cy="19" r="2.2" fill="#0f9b8e"/>
                        </svg>
                    </div>
                    <h3>Fertility Consultation</h3>
                    <p>&amp; Support</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ================================================================
         5. SUCCESS STORIES
         ================================================================ --}}
    <section class="ivf-stories" id="stories">
        <div class="ivf-container">
            <div class="ivf-stories-grid">
                <!-- Left Column -->
                <div class="ivf-stories-text">
                    <span class="ivf-pill">
                        <i class="fa-solid fa-heart"></i> Success Stories
                    </span>
                    <h2 class="ivf-title">Real Families, Real Happiness</h2>

                    <div class="ivf-quote-card">
                        <span class="ivf-quote-mark" aria-hidden="true">
                            <svg viewBox="0 0 38 28" fill="none">
                                <path d="M14.2 0C6.4 0 0 6.2 0 15.2 0 22.8 4.8 28 11 28c4.6 0 8-3.2 8-7.6 0-4.2-3-7.2-6.8-7.2-1.2 0-2.4.4-3 .8.6-4.8 4.2-8.4 8.6-9.6L14.2 0Zm20 0C26.4 0 20 6.2 20 15.2c0 7.6 4.8 12.8 11 12.8 4.6 0 8-3.2 8-7.6 0-4.2-3-7.2-6.8-7.2-1.2 0-2.4.4-3 .8.6-4.8 4.2-8.4 8.6-9.6L34.2 0Z" fill="#1aa397" opacity=".85"/>
                            </svg>
                        </span>
                        <p id="ivfStoryText">IVF Center.com gave us hope when we needed it most. Today, we are proud parents. Thank you for your unwavering support!</p>
                        <div class="ivf-author">
                            <img id="ivfStoryImg" src="{{ $img('avatar-emily-james.jpg') }}" alt="Emily & James">
                            <div>
                                <strong id="ivfStoryName">Emily &amp; James</strong>
                                <span id="ivfStoryRole">Happy Parents</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Smiling Baby under White Towel -->
                <div class="ivf-stories-media">
                    <div class="ivf-stories-photo">
                        <img src="{{ $img('success-baby.jpg') }}" alt="Smiling baby wrapped in a white towel">
                    </div>
                </div>
            </div>

            <div class="ivf-dots" id="ivfStoryDots">
                <span class="active"></span>
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </section>

    {{-- ================================================================
         6. WHY CHOOSE US
         ================================================================ --}}
    <section class="ivf-why">
        <div class="ivf-container">
            <div class="ivf-why-grid">
                <div class="ivf-why-text">
                    <span class="ivf-pill ivf-pill-teal">
                        <i class="fa-solid fa-circle-check"></i> Why Choose Us
                    </span>
                    <h2 class="ivf-title">
                        Why Families<br>
                        Choose <span class="t-hl">IVF Center?</span>
                    </h2>

                    <div class="ivf-why-list">
                        <div class="ivf-why-item">
                            <span class="ico">
                                <i class="fa-solid fa-trophy"></i>
                            </span>
                            <div>
                                <h4>High Success Rates</h4>
                                <p>Backed by Experience</p>
                            </div>
                        </div>

                        <div class="ivf-why-item">
                            <span class="ico">
                                <i class="fa-solid fa-shield-halved"></i>
                            </span>
                            <div>
                                <h4>Cutting-Edge Technology</h4>
                                <p>For Better Results</p>
                            </div>
                        </div>

                        <div class="ivf-why-item">
                            <span class="ico">
                                <i class="fa-solid fa-heart"></i>
                            </span>
                            <div>
                                <h4>Compassionate Care</h4>
                                <p>At Every Step</p>
                            </div>
                        </div>
                    </div>

                    <a href="#" class="ivf-btn ivf-btn-purple js-consult">
                        Get Started <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>

                <div class="ivf-why-media">
                    <img src="{{ $img('why-lab.jpg') }}" alt="Embryologist working with a microscope">
                </div>
            </div>
        </div>
    </section>

    {{-- ================================================================
         7. STATS BAR
         ================================================================ --}}
    <section class="ivf-stats-sec">
        <div class="ivf-container">
            <div class="ivf-stats">
                <div class="ivf-stat">
                    <span class="ivf-stat-ico">
                        <svg viewBox="0 0 44 44" fill="none">
                            <circle cx="22" cy="24" r="13" stroke="#0f9b8e" stroke-width="2.5" fill="#d3f1ec"/>
                            <path d="M22 11c0-2.5 2-4.2 4.2-3.4 1.3.5 1.8 2 .4 3.4" stroke="#0f9b8e" stroke-width="2.3" stroke-linecap="round"/>
                            <circle cx="17.5" cy="22.5" r="1.7" fill="#0f9b8e"/>
                            <circle cx="26.5" cy="22.5" r="1.7" fill="#0f9b8e"/>
                            <path d="M18.5 28c1 1.5 2.2 2.2 3.5 2.2s2.5-.7 3.5-2.2" stroke="#0f9b8e" stroke-width="2.3" stroke-linecap="round"/>
                        </svg>
                    </span>
                    <strong>98%+</strong>
                    <span>Success Rate</span>
                </div>

                <div class="ivf-stat">
                    <span class="ivf-stat-ico">
                        <svg viewBox="0 0 44 44" fill="none">
                            <circle cx="16" cy="15" r="4" fill="#0f9b8e"/>
                            <circle cx="28" cy="15" r="4" fill="#0f9b8e"/>
                            <circle cx="22" cy="21" r="3.2" fill="#0f9b8e"/>
                            <path d="M9 33c1-5 4-7.5 7.5-7.5M35 33c-1-5-4-7.5-7.5-7.5M15.5 34c1-4 3.5-6 6.5-6s5.5 2 6.5 6" stroke="#0f9b8e" stroke-width="2.5" stroke-linecap="round"/>
                        </svg>
                    </span>
                    <strong>10K+</strong>
                    <span>Happy Families</span>
                </div>

                <div class="ivf-stat">
                    <span class="ivf-stat-ico">
                        <svg viewBox="0 0 44 44" fill="none">
                            <path d="M15 11h14v8a7 7 0 0 1-14 0v-8Z" fill="#0f9b8e"/>
                            <path d="M15 13h-3.5a2.5 2.5 0 0 0 0 5H15M29 13h3.5a2.5 2.5 0 0 1 0 5H29" stroke="#0f9b8e" stroke-width="2.4" stroke-linecap="round"/>
                            <path d="M22 26v7M16 33h12" stroke="#0f9b8e" stroke-width="2.6" stroke-linecap="round"/>
                            <path d="M22 14l1.2 2.4 2.6.4-1.9 1.8.4 2.6-2.3-1.2-2.3 1.2.4-2.6-1.9-1.8 2.6-.4L22 14Z" fill="#e6f6f4"/>
                        </svg>
                    </span>
                    <strong>15+ Years</strong>
                    <span>of Experience</span>
                </div>

                <div class="ivf-stat">
                    <span class="ivf-stat-ico">
                        <svg viewBox="0 0 44 44" fill="none">
                            <rect x="12" y="10" width="20" height="24" rx="2" stroke="#0f9b8e" stroke-width="2.5" fill="#d3f1ec"/>
                            <path d="M8 34h28M19 15h6M22 12v6" stroke="#0f9b8e" stroke-width="2.5" stroke-linecap="round"/>
                            <rect x="16" y="22" width="4" height="4" rx=".8" fill="#0f9b8e"/>
                            <rect x="24" y="22" width="4" height="4" rx=".8" fill="#0f9b8e"/>
                            <rect x="19.5" y="28" width="5" height="6" fill="#0f9b8e"/>
                        </svg>
                    </span>
                    <strong>24/7</strong>
                    <span>Support</span>
                </div>
            </div>
        </div>
    </section>

    {{-- ================================================================
         8. TESTIMONIALS ("What Our Patients Say")
         ================================================================ --}}
    <section class="ivf-testi">
        <div class="ivf-leaf left">{!! $leafSvg !!}</div>
        <div class="ivf-leaf right">{!! $leafSvg !!}</div>

        <div class="ivf-container">
            <span class="ivf-pill ivf-pill-teal">
                <i class="fa-solid fa-circle-check"></i> Testimonials
            </span>
            <h2 class="ivf-title">What Our Patients Say</h2>

            <div class="ivf-testi-wrap">
                <button type="button" class="ivf-arrow prev" id="ivfPrev" aria-label="Previous">
                    <i class="fa-solid fa-arrow-left"></i>
                </button>

                <div class="ivf-testi-card">
                    <span class="ivf-quote-mark" aria-hidden="true">
                        <svg viewBox="0 0 38 28" fill="none">
                            <path d="M14.2 0C6.4 0 0 6.2 0 15.2 0 22.8 4.8 28 11 28c4.6 0 8-3.2 8-7.6 0-4.2-3-7.2-6.8-7.2-1.2 0-2.4.4-3 .8.6-4.8 4.2-8.4 8.6-9.6L14.2 0Zm20 0C26.4 0 20 6.2 20 15.2c0 7.6 4.8 12.8 11 12.8 4.6 0 8-3.2 8-7.6 0-4.2-3-7.2-6.8-7.2-1.2 0-2.4.4-3 .8.6-4.8 4.2-8.4 8.6-9.6L34.2 0Z" fill="#0f9b8e"/>
                        </svg>
                    </span>
                    <p id="ivfTestiText">The entire team at IVF Center.com was incredibly supportive and professional. We are forever grateful for their care and dedication.</p>
                    <div class="ivf-author">
                        <img id="ivfTestiImg" src="{{ $img('avatar-emily-james.jpg') }}" alt="Sophia & Daniel">
                        <div>
                            <strong id="ivfTestiName">Sophia &amp; Daniel</strong>
                            <span id="ivfTestiRole">Parents</span>
                        </div>
                    </div>
                </div>

                <button type="button" class="ivf-arrow next" id="ivfNext" aria-label="Next">
                    <i class="fa-solid fa-arrow-right"></i>
                </button>
            </div>

            <div class="ivf-dots" id="ivfTestiDots">
                <span></span>
                <span class="active"></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </section>

    {{-- ================================================================
         9. CTA BANNER ("Ready to Start Your Family Journey?")
         ================================================================ --}}
    <section class="ivf-cta-sec">
        <div class="ivf-container">
            <div class="ivf-cta">
                <div class="ivf-cta-text">
                    <h2>Ready to Start Your<br>Family Journey?</h2>
                    <p>Book a consultation today and take the first step<br>towards parenthood.</p>
                    <a href="#" class="ivf-btn ivf-btn-white js-consult">
                        Book Consultation <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>

                <div class="ivf-cta-visual">
                    <div class="ivf-cta-circle">
                        <img src="{{ $img('cta-hands.jpg') }}" alt="Parents' hands forming a heart around baby feet">
                        <!-- Delicate white heart outline overlay matching mockup -->
                        <svg class="ivf-cta-heart-svg" viewBox="0 0 240 200" fill="none" aria-hidden="true">
                            <path d="M120 168S42 122 42 68c0-25 20-44 44-44 15 0 27 8 34 20 7-12 19-20 34-20 24 0 44 19 44 44 0 54-78 100-78 100Z" stroke="rgba(255,255,255,0.85)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M158 134s-24-15-24-33c0-8 6.5-14.5 14.5-14.5 5 0 9 2.6 11.5 6.5 2.5-3.9 6.5-6.5 11.5-6.5 8 0 14.5 6.5 14.5 14.5 0 18-28 33-28 33Z" stroke="rgba(255,255,255,0.9)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ================================================================
         10. FOOTER
         ================================================================ --}}
    <footer class="ivf-footer" id="faq">
        <div class="ivf-container">
            <div class="ivf-foot-grid">
                <div class="ivf-foot-brand">
                    <a href="{{ url('/') }}" class="ivf-logo ivf-foot-logo">
                        {!! $logoSvg !!}
                        <span class="ivf-logo-txt">IVF<small>Center.com</small></span>
                    </a>
                    <p>Hope &nbsp;&bull;&nbsp; Care &nbsp;&bull;&nbsp; Family</p>
                </div>

                <div class="ivf-foot-col">
                    <h5>Quick Links</h5>
                    <ul>
                        <li><a href="#home">Home</a></li>
                        <li><a href="#about">About Us</a></li>
                        <li><a href="#services">Services</a></li>
                        <li><a href="#stories">Success Stories</a></li>
                        <li><a href="#faq">FAQ</a></li>
                    </ul>
                </div>

                <div class="ivf-foot-col">
                    <h5>Contact Us</h5>
                    <div class="ivf-contact"><i class="fa-solid fa-phone"></i><span>+1 (555) 123-4567</span></div>
                    <div class="ivf-contact"><i class="fa-solid fa-envelope"></i><span>hello@ivfcenter.com</span></div>
                    <div class="ivf-contact"><i class="fa-solid fa-location-dot"></i><span>123 Wellness Ave,<br>New York, NY 10001</span></div>
                    <div class="ivf-social">
                        <a href="#" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                        <a href="#" aria-label="YouTube"><i class="fa-brands fa-youtube"></i></a>
                        <a href="#" aria-label="LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
                        <a href="#" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                    </div>
                </div>
            </div>
        </div>

        <div class="ivf-copy">
            <svg class="ivf-copy-wave" viewBox="0 0 1440 54" preserveAspectRatio="none" aria-hidden="true">
                <defs>
                    <linearGradient id="ivfWave" x1="0" x2="1" y1="0" y2="0">
                        <stop offset="0%" stop-color="#3a1370"/>
                        <stop offset="50%" stop-color="#4e1d94"/>
                        <stop offset="100%" stop-color="#5d25ac"/>
                    </linearGradient>
                </defs>
                <path d="M0 4C340 42 640 50 940 38C1180 28 1340 14 1440 2V54H0V4Z" fill="#8b5cf6" opacity=".35"/>
                <path d="M0 10C340 46 640 52 940 42C1180 32 1340 18 1440 6V54H0V10Z" fill="url(#ivfWave)"/>
            </svg>
            <div class="ivf-container">
                <div class="ivf-copy-row">
                    <span>&copy; 2025 IVF Center.com. All rights reserved.</span>
                    <span>Designed with <i class="fa-solid fa-heart heart"></i> for Families</span>
                </div>
            </div>
        </div>
    </footer>

</div>

{{-- ================================================================
     11. CONSULTATION MODAL
     ================================================================ --}}
<div class="ivf-modal" id="ivfModal">
    <div class="ivf-modal-box">
        <button type="button" class="ivf-modal-close" id="ivfModalClose" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
        <span class="ivf-pill"><i class="fa-solid fa-heart"></i> Book Consultation</span>
        <h3>Start Your Family Journey</h3>
        <p>Our fertility specialists will contact you within 24 hours.</p>

        <form action="{{ route('ivfcenter.consultation.post') }}" method="POST" id="ivfForm">
            @csrf
            <div class="ivf-feedback" id="ivfFeedback"></div>
            <div class="ivf-field"><label>Full Name *</label><input type="text" name="full_name" required></div>
            <div class="ivf-field-row">
                <div class="ivf-field"><label>Email *</label><input type="email" name="email" required></div>
                <div class="ivf-field"><label>Phone</label><input type="tel" name="phone"></div>
            </div>
            <div class="ivf-field-row">
                <div class="ivf-field">
                    <label>Service</label>
                    <select name="service_interest">
                        <option>IVF Treatment</option>
                        <option>ICSI Treatment</option>
                        <option>Egg Freezing</option>
                        <option>Donor Programs</option>
                        <option>Genetic Testing</option>
                        <option>Fertility Consultation</option>
                    </select>
                </div>
                <div class="ivf-field"><label>Preferred Date</label><input type="date" name="preferred_date"></div>
            </div>
            <div class="ivf-field"><label>Message</label><textarea name="message" rows="3"></textarea></div>
            <button type="submit" class="ivf-btn ivf-btn-purple" style="width:100%;justify-content:center;">Confirm Consultation <i class="fa-solid fa-arrow-right"></i></button>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    window.IVF_TESTIMONIALS = [
        { text: "IVF Center.com gave us hope when we needed it most. Today, we are proud parents. Thank you for your unwavering support!", name: "Emily & James", role: "Happy Parents", img: @json($img('avatar-emily-james.jpg')) },
        { text: "The entire team at IVF Center.com was incredibly supportive and professional. We are forever grateful for their care and dedication.", name: "Sophia & Daniel", role: "Parents", img: @json($img('avatar-emily-james.jpg')) },
        { text: "From our first consultation to holding our baby, every step felt guided with expertise and genuine compassion.", name: "Emily & James", role: "Happy Parents", img: @json($img('avatar-emily-james.jpg')) },
        { text: "Their advanced technology and caring doctors made our dream of parenthood come true. We can't thank them enough.", name: "Sophia & Daniel", role: "Parents", img: @json($img('avatar-emily-james.jpg')) }
    ];
</script>
@endpush
