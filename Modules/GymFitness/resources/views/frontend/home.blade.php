@extends('gymfitness::frontend.layouts.master')

@section('gym_content')
@php
    $isBn = ($locale ?? 'en') === 'bn';
    $defaultSections = [
        'hero', 'why_choose', 'programs', 'trainers', 'pricing', 'schedule', 'equipment', 'plans', 'gallery', 'videos', 'testimonials', 'registration', 'faq_cta'
    ];
    $sectionOrder = $t['section_order'] ?? $defaultSections;
    if (in_array('faq_cta', $sectionOrder, true)) {
        $faqIdx = array_search('faq_cta', $sectionOrder, true);
        foreach ($defaultSections as $reqSec) {
            if (!in_array($reqSec, $sectionOrder, true)) {
                array_splice($sectionOrder, $faqIdx, 0, [$reqSec]);
                $faqIdx++;
            }
        }
    } else {
        foreach ($defaultSections as $reqSec) {
            if (!in_array($reqSec, $sectionOrder, true)) {
                $sectionOrder[] = $reqSec;
            }
        }
    }
    $vis = $t['visibility'] ?? [];
@endphp

@foreach($sectionOrder as $sectionKey)

    {{-- =====================================================================
         SECTION 1: HERO SECTION (100% Same-to-Same with Reference Image)
         ===================================================================== --}}
    @if($sectionKey === 'hero' && ($vis['show_hero'] ?? '1') === '1')
    <div class="gym-hero-wrapper">
        <section class="gym-hero" id="home">
            <div class="gym-container">
                {{-- Top 2-Column Headline & Right Copy + CTAs --}}
                <div class="gym-hero-top">
                    <div>
                        <h1 class="gym-hero-title">
                            <span class="white-part">{{ $tr['hero_line_1'] ?? 'NO EXCUSES.' }}</span>
                            <span class="lime-part">{{ $tr['hero_line_2'] ?? 'ONLY' }}</span><br>
                            <span class="lime-part">{{ $tr['hero_line_3'] ?? 'RESULTS.' }}</span>
                        </h1>
                    </div>

                    <div class="gym-hero-right-copy">
                        <p class="gym-hero-subtitle">
                            {{ $tr['hero_subtitle'] ?? 'Join a results-driven fitness community with certified trainers, modern equipment, and personalized programs designed to transform your body and mindset.' }}
                        </p>
                        <div class="gym-hero-ctas">
                            <a href="#register" data-open-register class="gym-btn-lime">
                                <span>{{ $tr['hero_primary_cta'] ?? ($isBn ? 'ফ্রি ট্রায়াল শুরু করুন' : 'Start Free Trial') }}</span>
                                <svg class="gym-arrow-ne" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M2.5 9.5L9.5 2.5M9.5 2.5H3.5M9.5 2.5V8.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </a>
                            <a href="#programs" class="gym-btn-outline">
                                <span>{{ $tr['hero_secondary_cta'] ?? ($isBn ? 'প্রোগ্রাম দেখুন' : 'Explore Programs') }}</span>
                                <svg class="gym-arrow-ne" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M2.5 9.5L9.5 2.5M9.5 2.5H3.5M9.5 2.5V8.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </a>
                        </div>

                        {{-- Hand-Drawn Solid White Loop Arrow SVG (Exact match to Reference) --}}
                        <svg class="gym-hero-dashed-arrow" viewBox="0 0 130 165" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M26 156 C 68 122, 82 78, 44 68 C 12 60, 14 102, 54 96 C 98 90, 96 34, 48 9" stroke="rgba(255,255,255,0.88)" stroke-width="1.35" stroke-linecap="round" fill="none"/>
                            <path d="M68 5 L46 8 L58 25" stroke="rgba(255,255,255,0.88)" stroke-width="1.35" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                        </svg>
                    </div>
                </div>

                {{-- Bottom Wide Olive-Lime Banner Card with Cutout Bodybuilder + Frosted Stats --}}
                <div class="gym-hero-banner-card">
                    <div class="gym-hero-athlete-wrap">
                        @php
                            $heroImg = $t['hero']['hero_image'] ?? '/module-media/GymFitness/theme/hero-athlete.png';
                            if (str_ends_with($heroImg, 'hero-athlete.jpg')) {
                                $heroImg = '/module-media/GymFitness/theme/hero-athlete.png';
                            }
                        @endphp
                        <img src="{{ $heroImg }}?v=4"
                             alt="IronPulse Athlete"
                             class="gym-hero-athlete-img">
                    </div>

                    <div class="gym-hero-stats-glass">
                        <div class="gym-stat-badge">
                            <span class="gym-avatar-stack">
                                <img src="/module-media/GymFitness/theme/testi-1.jpg?v=4" alt="Member">
                                <img src="/module-media/GymFitness/theme/testi-2.jpg?v=4" alt="Member">
                                <img src="/module-media/GymFitness/theme/testi-3.jpg?v=4" alt="Member">
                                <img src="/module-media/GymFitness/theme/testi-4.jpg?v=4" alt="Member">
                            </span>
                            <strong>{{ $t['hero']['stat_1_value'] ?? '10,000+' }}</strong>
                            <span>{{ $tr['hero_stat_1_label'] ?? 'Happy Members' }}</span>
                        </div>

                        <div class="gym-stat-badge">
                            <strong>{{ ($t['hero']['stat_3_value'] ?? '8+') === '50+' ? '8+' : ($t['hero']['stat_3_value'] ?? '8+') }}</strong>
                            <span>{{ ($tr['hero_stat_3_label'] ?? 'Years Experience') === 'Certified Trainers' ? 'Years Experience' : ($tr['hero_stat_3_label'] ?? 'Years Experience') }}</span>
                        </div>

                        <div class="gym-stat-badge">
                            <strong>{{ $t['hero']['stat_2_value'] ?? '4.9' }}</strong>
                            <span>{{ $tr['hero_stat_2_label'] ?? 'Rating' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    @endif

    {{-- =====================================================================
         SECTION 2: WHY TRAIN WITH US ? (Photo + Center Dot Line + 4 Cards)
         ===================================================================== --}}
    @if($sectionKey === 'why_choose' && ($vis['show_why_choose'] ?? '1') === '1')
    <section class="gym-section" id="about">
        <div class="gym-container">
            <div class="gym-sec-header">
                <h2 class="gym-sec-title">{{ $tr['why_title'] ?? 'Why Train With Us ?' }}</h2>
            </div>

            <div class="gym-why-layout">
                {{-- Left Rounded Gym Photo --}}
                <div class="gym-why-photo">
                    <img src="{{ $t['why_choose']['gym_image'] ?? '/module-media/GymFitness/theme/gym-interior.jpg' }}?v=3"
                         alt="Why Train With Us">
                </div>

                {{-- Center Vertical Line (Top Lime, Middle Glowing Dot, Bottom Dark) --}}
                <div class="gym-why-divider">
                    <span class="gym-why-line-top"></span>
                    <span class="gym-why-dot"></span>
                    <span class="gym-why-line-bottom"></span>
                </div>

                {{-- Right 4 Stacked Feature Cards (#2 Highlighted in Solid Neon Lime) --}}
                <div class="gym-why-list">
                    <div class="gym-why-card">
                        <h4>{{ $tr['why_feat_2_title'] ?? 'Certified Trainers' }}</h4>
                        <p>{{ $tr['why_feat_2_desc'] ?? 'Work with experienced coaches dedicated to helping you reach your goals.' }}</p>
                    </div>

                    <div class="gym-why-card active-lime">
                        <h4>{{ $tr['why_feat_1_title'] ?? 'Modern Equipment' }}</h4>
                        <p>{{ $tr['why_feat_1_desc'] ?? 'Train with advanced machines for strength, cardio & recovery.' }}</p>
                    </div>

                    <div class="gym-why-card">
                        <h4>{{ $tr['why_feat_3_title'] ?? 'Personalized Plans' }}</h4>
                        <p>{{ $tr['why_feat_3_desc'] ?? 'Custom workout & nutrition plans tailored to your body & goals.' }}</p>
                    </div>

                    <div class="gym-why-card">
                        <h4>{{ $tr['why_feat_4_title'] ?? 'Supportive Community' }}</h4>
                        <p>{{ $tr['why_feat_4_desc'] ?? 'Stay motivated with a supportive & energetic fitness environment.' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif

    {{-- =====================================================================
         SECTION 3: OUR TRAINING PROGRAMS (2 Wide Top + 3 Bottom Bento Grid)
         ===================================================================== --}}
    @if($sectionKey === 'programs' && ($vis['show_programs'] ?? '1') === '1')
    <section class="gym-section" id="programs">
        <div class="gym-container">
            <div class="gym-sec-header">
                <h2 class="gym-sec-title">{{ $tr['programs_title'] ?? 'Our Training Programs' }}</h2>
                <a href="#register" data-open-register class="gym-link-underline">
                    {{ $tr['programs_view_all'] ?? 'View All Programs' }}
                    <i class="fa-solid fa-arrow-up-right-from-square" style="font-size:9px;"></i>
                </a>
            </div>

            @php
                $topPrograms = $programs->take(2);
                $bottomPrograms = $programs->slice(2, 3)->values();
            @endphp

            {{-- Row 1: 2 Wide Cards --}}
            <div class="gym-programs-bento-top">
                @foreach($topPrograms as $prog)
                    <a href="#register" data-open-register class="gym-prog-card">
                        <img src="{{ $prog->image }}?v=3" alt="{{ $prog->title }}">
                        <div class="gym-prog-overlay">
                            <h3>{{ $isBn ? ($prog->title_bn ?: $prog->title) : $prog->title }}</h3>
                            <p>{{ $isBn ? ($prog->subtitle_bn ?: $prog->subtitle) : $prog->subtitle }}</p>
                        </div>
                    </a>
                @endforeach
            </div>

            {{-- Row 2: 3 Cards (Middle Card Has Neon Lime Circle Arrow Button) --}}
            <div class="gym-programs-bento-bottom">
                @foreach($bottomPrograms as $idx => $prog)
                    <a href="#register" data-open-register class="gym-prog-card">
                        <img src="{{ $prog->image }}?v=3" alt="{{ $prog->title }}">
                        @if($idx === 1)
                            <span class="gym-prog-center-arrow">
                                <i class="fa-solid fa-arrow-up-right-from-square"></i>
                            </span>
                        @endif
                        <div class="gym-prog-overlay">
                            <h3>{{ $isBn ? ($prog->title_bn ?: $prog->title) : $prog->title }}</h3>
                            <p>{{ $isBn ? ($prog->subtitle_bn ?: $prog->subtitle) : $prog->subtitle }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- =====================================================================
         SECTION 4: MEET OUR EXPERT TRAINERS (3 Cards + Centered Book A Session)
         ===================================================================== --}}
    @if($sectionKey === 'trainers' && ($vis['show_trainers'] ?? '1') === '1')
    <section class="gym-section gym-trainers-section" id="trainers">
        <div class="gym-container">
            <div class="gym-sec-header" style="margin-bottom:32px;">
                <h2 class="gym-sec-title gym-trainer-sec-title">{{ $tr['trainers_title'] ?? 'Meet Our Expert Trainers' }}</h2>
            </div>

            <div class="gym-trainers-grid-3">
                @foreach($trainers->take(3) as $trainer)
                    @php
                        $specText = $isBn ? ($trainer->specialization_bn ?: $trainer->specialization) : $trainer->specialization;
                        $expText  = $isBn ? ($trainer->experience_bn ?: $trainer->experience) : $trainer->experience;
                        $expText  = str_ireplace(['+ Years Experience', ' Years Experience', '+ Years Exp'], ' Years', $expText);
                    @endphp
                    <div class="gym-trainer-card">
                        <img src="{{ $trainer->photo }}?v=8" alt="{{ $trainer->name }}">
                        <div class="gym-trainer-info">
                            <h3>{{ $isBn ? ($trainer->name_bn ?: $trainer->name) : $trainer->name }}</h3>
                            <span>{{ $isBn ? 'বিশেষত্ব: ' : 'Specialty: ' }}{{ $specText }}</span>
                            <small>{{ $isBn ? 'অভিজ্ঞতা: ' : 'Experience: ' }}{{ $expText }}</small>
                        </div>
                    </div>
                @endforeach
            </div>

            <div style="text-align:center;margin-top:38px;">
                <a href="#register" data-open-register class="gym-btn-lime">
                    <span>{{ $isBn ? 'সেশন বুক করুন' : 'Book A Session' }}</span>
                    <svg class="gym-arrow-ne" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M2.5 9.5L9.5 2.5M9.5 2.5H3.5M9.5 2.5V8.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
            </div>
        </div>
    </section>
    @endif

    {{-- =====================================================================
         SECTION 5: CHOOSE YOUR PLAN (Gym Photo Backdrop + 3 Stacked Cards)
         ===================================================================== --}}
    @if($sectionKey === 'pricing' && ($vis['show_pricing'] ?? '1') === '1')
    <section class="gym-plan-photo-section" id="pricing" style="background-image:url('{{ $t['hero']['hero_bg_image'] ?? '/module-media/GymFitness/theme/hero-gym-bg.jpg' }}?v=3');">
        <div class="gym-container">
            <div class="gym-plan-split">
                {{-- Left Column: Title + 2 Toggle Buttons (Monthly / Yearly) + Dashed Loop Arrow --}}
                <div>
                    <h2 class="gym-sec-title" style="font-size:clamp(26px,3.2vw,38px);">
                        {{ $isBn ? ($tr['pricing_title'] ?? 'আপনার প্ল্যান বেছে নিন') : 'Choose Your Plan' }}
                    </h2>

                    <div class="gym-billing-switch">
                        <button type="button" class="gym-billing-btn active" data-billing-cycle="monthly" data-unit-label="{{ $isBn ? '/মাস' : '/month' }}">
                            {{ $tr['billing_monthly'] ?? 'Monthly' }}
                        </button>
                        <button type="button" class="gym-billing-btn" data-billing-cycle="yearly" data-unit-label="{{ $isBn ? '/বছর' : '/year' }}">
                            {{ $isBn ? 'বার্ষিক' : 'Yearly' }}
                        </button>
                    </div>

                    {{-- Hand-Drawn Curved Dashed Arrow Pointing to Middle Pro Plan --}}
                    <div style="margin-top:34px;padding-left:28px;">
                        <svg width="200" height="95" viewBox="0 0 200 95" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 75 C 55 18, 125 14, 108 56 C 96 84, 62 62, 84 34 C 106 8, 158 26, 188 44" stroke="rgba(255,255,255,0.85)" stroke-width="1.8" stroke-dasharray="4 4" fill="none"/>
                            <path d="M180 35 L190 45 L177 49" stroke="rgba(255,255,255,0.9)" stroke-width="1.8" fill="none"/>
                        </svg>
                    </div>
                </div>

                {{-- Right Column: 3 Stacked Horizontal Plan Cards --}}
                <div class="gym-plan-stack">
                    @foreach($packages as $pkg)
                        @php
                            $pkgFeatures = $isBn && !empty($pkg->features_bn) ? $pkg->features_bn : ($pkg->features ?? []);
                            $shortPlanName = str_replace(' Plan', '', $isBn ? ($pkg->name_bn ?: $pkg->name) : $pkg->name);
                            if ($shortPlanName === 'Premium') {
                                $shortPlanName = 'Pro';
                            }
                        @endphp
                        <div class="gym-horiz-plan-card {{ $pkg->is_featured ? 'featured' : '' }}"
                             data-currency="{{ $pkg->currency ?? '$' }}"
                             data-monthly="{{ (float) $pkg->monthly_price }}"
                             data-quarterly="{{ (float) $pkg->quarterly_price }}"
                             data-six_month="{{ (float) $pkg->six_month_price }}"
                             data-yearly="{{ (float) $pkg->yearly_price }}">

                            @if($pkg->is_featured)
                                <span class="gym-popular-tag">
                                    {{ $isBn ? ($pkg->badge_text_bn ?: 'জনপ্রিয়') : 'Most Popular' }}
                                </span>
                            @endif

                            <div>
                                <div class="gym-horiz-plan-title">
                                    {{ $shortPlanName }} -
                                    <span class="val">{{ $pkg->currency ?? '$' }}{{ (float) $pkg->monthly_price }}</span><span class="unit">{{ $isBn ? '/মাস' : '/month' }}</span>
                                </div>

                                <a href="#register"
                                   data-open-register
                                   data-select-package="{{ $pkg->id }}"
                                   data-selected-cycle="monthly"
                                   class="{{ $pkg->is_featured ? 'gym-btn-dark' : 'gym-btn-lime' }}">
                                    {{ $isBn ? 'প্ল্যান নিন' : 'Choose Plan' }}
                                    <i class="fa-solid fa-arrow-up-right-from-square" style="font-size:9px;"></i>
                                </a>
                            </div>

                            <ul class="gym-horiz-features">
                                @foreach($pkgFeatures as $feat)
                                    <li>• {{ $feat }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
    @endif

    {{-- =====================================================================
         SECTION 6: REAL RESULTS. REAL PEOPLE. (4-Col Asymmetric Strip)
         ===================================================================== --}}
    @if($sectionKey === 'testimonials' && ($vis['show_testimonials'] ?? '1') === '1')
    <section class="gym-section gym-results-section" id="testimonials">
        <div class="gym-container">
            <div class="gym-sec-header">
                <h2 class="gym-sec-title">{{ $isBn ? ($tr['testi_title'] ?? 'আমাদের মেম্বারদের সফলতা') : 'Real Results. Real People.' }}</h2>
                <a href="#register" data-open-register class="gym-link-underline">
                    {{ $isBn ? 'সব দেখুন' : 'View All' }}
                    <i class="fa-solid fa-arrow-up-right-from-square" style="font-size:9px;"></i>
                </a>
            </div>

            @php
                $t1 = $testimonials->get(0);
                $t2 = $testimonials->get(1) ?? $t1;
                $t3 = $testimonials->get(2) ?? $t1;
            @endphp

            <div class="gym-results-grid">
                {{-- Card 1: Portrait Card (Woman in gym) --}}
                <div class="gym-result-portrait">
                    <img src="/module-media/GymFitness/theme/testi-1.jpg?v=3" alt="Member Transformation">
                    <span class="gym-quote-badge"><i class="fa-solid fa-quote-right"></i></span>
                    <span class="gym-portrait-name">{{ $isBn ? 'সাদিয়া ইসলাম' : 'Sadia Islam' }}</span>
                </div>

                {{-- Card 2: Wide Featured Horizontal Quote Card + Sine Wave --}}
                <div class="gym-result-featured">
                    <img src="/module-media/GymFitness/theme/testi-2.jpg?v=3" alt="Featured Member">
                    <div class="gym-result-featured-body">
                        <h4>
                            "{{ $isBn ? ($t1->review_text_bn ?? 'এই জিমে জয়েন করার পর মাত্র ৬ মাসে আমার জীবন পুরোপুরি বদলে গেছে।') : 'Joining this gym completely changed my life in 6 months.' }}"
                        </h4>
                        <div class="gym-result-meta">
                            <strong style="color:#fff;display:block;">{{ $isBn ? 'তানভীর আহমেদ' : 'Tanvir Ahmed' }}</strong>
                            <span>{{ $isBn ? '৬ মাসে ১২ কেজি ওজন কমিয়েছেন' : 'Lost 12kg in 6 Months' }}</span>
                        </div>

                        {{-- Neon Lime Double Sine-Wave Graphic --}}
                        <svg class="gym-wave-svg" viewBox="0 0 210 120" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M5 112 C 40 28, 82 118, 122 48 C 152 0, 182 82, 205 18" stroke="#CCF936" stroke-width="2" fill="none"/>
                            <path d="M22 118 C 58 48, 96 116, 142 58 C 172 18, 188 86, 208 34" stroke="rgba(204,249,54,0.55)" stroke-width="1.5" fill="none"/>
                        </svg>
                    </div>
                </div>

                {{-- Card 3: Portrait Card --}}
                <div class="gym-result-portrait">
                    <img src="/module-media/GymFitness/theme/testi-3.jpg?v=3" alt="Member Result">
                    <span class="gym-quote-badge"><i class="fa-solid fa-quote-right"></i></span>
                    <span class="gym-portrait-name">{{ $isBn ? 'রাকিবুল ইসলাম' : 'Rakibul Islam' }}</span>
                </div>

                {{-- Card 4: Portrait Card --}}
                <div class="gym-result-portrait">
                    <img src="/module-media/GymFitness/theme/testi-4.jpg?v=3" alt="Member Result">
                    <span class="gym-quote-badge"><i class="fa-solid fa-quote-right"></i></span>
                    <span class="gym-portrait-name">{{ $isBn ? 'আরিফ মাহমুদ' : 'Arif Mahmud' }}</span>
                </div>
            </div>
        </div>
    </section>
    @endif

    {{-- =====================================================================
         SECTION 7: CENTERED FAQ OVER DARK DUMBBELL BG + TEAL TRANSFORM BANNER
         ===================================================================== --}}
    @if($sectionKey === 'faq_cta' && ($vis['show_faq_cta'] ?? '1') === '1')
    <section class="gym-faq-section" id="faq" style="background-image:url('/module-media/GymFitness/theme/faq-bg.jpg?v=3');">
        <div class="gym-container">
            <div style="text-align:center;margin-bottom:26px;">
                <h2 class="gym-sec-title">{{ $tr['faq_title'] ?? 'Frequently Asked Questions' }}</h2>
            </div>

            <div class="gym-faq-container">
                @foreach($faqs as $idx => $faq)
                    <div class="gym-faq-item {{ $idx === 1 ? 'open' : '' }}">
                        <button type="button" class="gym-faq-q">
                            <span>{{ $isBn ? ($faq->question_bn ?: $faq->question) : $faq->question }}</span>
                            <i class="fa-solid {{ $idx === 1 ? 'fa-minus' : 'fa-plus' }}"></i>
                        </button>
                        <div class="gym-faq-a">
                            {{ $isBn ? ($faq->answer_bn ?: $faq->answer) : $faq->answer }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Ready To Transform Your Life? Teal-Emerald Banner --}}
    <section class="gym-transform-wrap">
        <div class="gym-container">
            <div class="gym-transform-banner">
                <img src="{{ $t['cta_section']['cta_image'] ?? '/module-media/GymFitness/theme/cta-transform.jpg' }}?v=3"
                     alt="Ready To Transform Your Life"
                     class="gym-transform-img">

                <div class="gym-transform-body">
                    <h3>{{ $tr['cta_title'] ?? 'Ready To Transform Your Life?' }}</h3>
                    <p>{{ $tr['cta_subtitle'] ?? 'Join FitPulse Gym Today and start your fitness journey!' }}</p>
                    <a href="#register" data-open-register class="gym-btn-lime">
                        {{ $isBn ? 'এখনই জয়েন করুন' : 'Join FitPulse Now' }}
                        <i class="fa-solid fa-arrow-up-right-from-square" style="font-size:9px;"></i>
                    </a>

                    <svg class="gym-wave-svg" viewBox="0 0 240 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0 92 C 50 38, 95 98, 145 48 C 180 12, 210 76, 238 26" stroke="#CCF936" stroke-width="2" fill="none"/>
                        <path d="M18 98 C 65 52, 108 96, 158 56 C 192 24, 216 80, 240 40" stroke="rgba(204,249,54,0.5)" stroke-width="1.4" fill="none"/>
                    </svg>
                </div>
            </div>
        </div>
    </section>
    @endif

    {{-- =====================================================================
         SECTION 8: WEEKLY CLASS SCHEDULE TABLE (Restored & Styled)
         ===================================================================== --}}
    @if($sectionKey === 'schedule' && ($vis['show_schedule'] ?? '1') === '1')
    <section class="gym-section gym-schedule-section" id="schedule">
        <div class="gym-container">
            <div class="gym-sec-header">
                <h2 class="gym-sec-title">{{ $tr['schedule_title'] ?? 'Our Class Schedule' }}</h2>
                <a href="#register-section" class="gym-link-underline">
                    {{ $tr['schedule_view_all'] ?? ($isBn ? 'ক্লাস বুক করুন' : 'Book a Class') }}
                    <svg class="gym-arrow-ne" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M2.5 9.5L9.5 2.5M9.5 2.5H3.5M9.5 2.5V8.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
            </div>
            <div class="gym-schedule-wrap">
                <table class="gym-schedule-table">
                    <thead>
                        <tr>
                            <th>{{ $tr['schedule_time_col'] ?? 'Time' }}</th>
                            @foreach($daysOfWeek as $day)
                                <th>{{ $day }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($scheduleMatrix as $slot => $dayMap)
                            <tr>
                                <td class="gym-schedule-time">{{ $slot }}</td>
                                @foreach($daysOfWeek as $day)
                                    @php $cell = $dayMap[$day] ?? null; @endphp
                                    <td>
                                        @if($cell)
                                            @php
                                                $catLower = strtolower($cell->category ?? $cell->title ?? '');
                                                $isRest = str_contains($catLower, 'rest');
                                            @endphp
                                            <div class="gym-schedule-pill {{ $isRest ? 'rest-pill' : 'active-pill' }}">
                                                <strong>{{ $isBn ? ($cell->title_bn ?: $cell->title) : $cell->title }}</strong>
                                                @if(!$isRest && $cell->trainer)
                                                    <span>{{ $isBn ? ($cell->trainer->name_bn ?: $cell->trainer->name) : $cell->trainer->name }}</span>
                                                @endif
                                            </div>
                                        @else
                                            <span style="color:#4B5563;">—</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>
    @endif

    {{-- =====================================================================
         SECTION 9: STATE-OF-THE-ART EQUIPMENT (6-Card Grid Restored & Styled)
         ===================================================================== --}}
    @if($sectionKey === 'equipment' && ($vis['show_equipment'] ?? '1') === '1')
    <section class="gym-section gym-equipment-section" id="equipment">
        <div class="gym-container">
            <div class="gym-sec-header">
                <h2 class="gym-sec-title">{{ $tr['equipment_title'] ?? 'State-of-the-Art Equipment' }}</h2>
                <a href="#register-section" class="gym-link-underline">
                    {{ $tr['equipment_view_all'] ?? ($isBn ? 'সব ইকুইপমেন্ট দেখুন' : 'View All Equipment') }}
                    <svg class="gym-arrow-ne" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M2.5 9.5L9.5 2.5M9.5 2.5H3.5M9.5 2.5V8.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
            </div>
            <div class="gym-equip-grid">
                @foreach($equipments as $eq)
                    <div class="gym-equip-card">
                        <div class="gym-equip-img-wrap">
                            <img src="{{ $eq->image }}?v=4" alt="{{ $eq->name }}">
                            <span class="gym-equip-badge">{{ $eq->category ?? 'Pro Gear' }}</span>
                        </div>
                        <div class="gym-equip-body">
                            <h4>{{ $isBn ? ($eq->name_bn ?: $eq->name) : $eq->name }}</h4>
                            <p>{{ $eq->brand ? $eq->brand . ' — ' : '' }}{{ $eq->notes ?? 'Commercial grade fitness equipment.' }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- =====================================================================
         SECTION 9B: WORKOUT ROUTINES & NUTRITION DIET PLANS (gym_fitness_plans)
         ===================================================================== --}}
    @if($sectionKey === 'plans' && ($vis['show_plans'] ?? '1') === '1' && isset($fitnessPlans) && $fitnessPlans->count() > 0)
    <section class="gym-section gym-plans-section" id="plans">
        <div class="gym-container">
            <div class="gym-sec-header">
                <h2 class="gym-sec-title">{{ $isBn ? 'ওয়ার্কআউট রুটিন ও ডায়েট প্ল্যান' : 'Workout & Nutrition Plans' }}</h2>
                <div class="gym-billing-switch" style="margin-top:0;">
                    <button type="button" class="gym-billing-btn active" data-plan-filter="all">{{ $isBn ? 'সবগুলো' : 'All Plans' }}</button>
                    <button type="button" class="gym-billing-btn" data-plan-filter="Workout">{{ $isBn ? 'ওয়ার্কআউট' : 'Workout' }}</button>
                    <button type="button" class="gym-billing-btn" data-plan-filter="Diet">{{ $isBn ? 'ডায়েট চার্ট' : 'Diet & Nutrition' }}</button>
                </div>
            </div>

            <div class="gym-plans-grid">
                @foreach($fitnessPlans as $fPlan)
                    <div class="gym-plan-routine-card" data-plan-type="{{ $fPlan->plan_type }}">
                        <div class="gym-plan-routine-top">
                            <span class="gym-equip-badge" style="position:static;">
                                {{ $fPlan->plan_type === 'Diet' ? ($isBn ? 'ডায়েট প্ল্যান' : 'Nutrition Plan') : ($isBn ? 'ওয়ার্কআউট রুটিন' : 'Workout Split') }}
                            </span>
                            <span class="gym-plan-meta-pill">{{ $fPlan->goal }} • {{ $fPlan->duration_weeks }} {{ $isBn ? 'সপ্তাহ' : 'Weeks' }}</span>
                        </div>
                        <h4>{{ $isBn ? ($fPlan->title_bn ?: $fPlan->title) : $fPlan->title }}</h4>
                        @if($fPlan->calories_target)
                            <div class="gym-plan-cal">
                                <i class="fa-solid fa-fire-flame-curved"></i> {{ $fPlan->calories_target }}
                            </div>
                        @endif
                        <div class="gym-plan-schedule-lines">
                            @foreach(array_filter(explode("\n", $isBn ? ($fPlan->schedule_details_bn ?: $fPlan->schedule_details) : $fPlan->schedule_details)) as $line)
                                <div class="gym-plan-line">• {{ $line }}</div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- =====================================================================
         SECTION 9C: PHOTO & MEDIA GALLERY (gym_media)
         ===================================================================== --}}
    @if($sectionKey === 'gallery' && ($vis['show_gallery'] ?? '1') === '1' && isset($mediaItems) && $mediaItems->count() > 0)
    <section class="gym-section gym-gallery-section" id="gallery">
        <div class="gym-container">
            <div class="gym-sec-header">
                <h2 class="gym-sec-title">{{ $isBn ? 'ফটো ও মিডিয়া গ্যালারি' : 'Inside Our Gym Gallery' }}</h2>
                <a href="#register" data-open-register class="gym-link-underline">
                    {{ $isBn ? 'ভিজিট বুক করুন' : 'Book Free Tour' }}
                    <svg class="gym-arrow-ne" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M2.5 9.5L9.5 2.5M9.5 2.5H3.5M9.5 2.5V8.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
            </div>

            <div class="gym-gallery-grid">
                @foreach($mediaItems as $mItem)
                    <div class="gym-gallery-card" data-open-image="{{ $mItem->file_path }}" data-image-title="{{ $mItem->title }}">
                        <img src="{{ $mItem->file_path }}?v=4" alt="{{ $mItem->alt_text ?: $mItem->title }}">
                        <div class="gym-gallery-overlay">
                            <span class="gym-gallery-cat">{{ $mItem->category ?: 'Gym Arena' }}</span>
                            <h4>{{ $mItem->title }}</h4>
                        </div>
                        <span class="gym-gallery-zoom"><i class="fa-solid fa-expand"></i></span>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- =====================================================================
         SECTION 9D: WORKOUT & FACILITY VIDEO GALLERY (gym_videos)
         ===================================================================== --}}
    @if($sectionKey === 'videos' && ($vis['show_videos'] ?? '1') === '1' && isset($videos) && $videos->count() > 0)
    <section class="gym-section gym-videos-section" id="videos">
        <div class="gym-container">
            <div class="gym-sec-header">
                <h2 class="gym-sec-title">{{ $isBn ? 'ওয়ার্কআউট ও জিম ভিডিও গ্যালারি' : 'Workout & Facility Videos' }}</h2>
                <a href="#register" data-open-register class="gym-link-underline">
                    {{ $isBn ? 'সব ভিডিও' : 'Join Training' }}
                    <svg class="gym-arrow-ne" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M2.5 9.5L9.5 2.5M9.5 2.5H3.5M9.5 2.5V8.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
            </div>

            <div class="gym-videos-grid">
                @foreach($videos as $vid)
                    @php
                        $vidTitle = $isBn ? ($vid->title_bn ?: $vid->title) : $vid->title;
                    @endphp
                    <div class="gym-video-card" data-open-video="{{ $vid->video_url }}" data-video-title="{{ $vidTitle }}">
                        <div class="gym-video-thumb">
                            <img src="{{ $vid->thumbnail ?: '/module-media/GymFitness/theme/gym-interior.jpg' }}?v=4" alt="{{ $vidTitle }}">
                            <span class="gym-video-play-btn">
                                <i class="fa-solid fa-play"></i>
                            </span>
                            @if($vid->duration)
                                <span class="gym-video-duration">{{ $vid->duration }}</span>
                            @endif
                        </div>
                        <div class="gym-video-body">
                            <span class="gym-video-cat">{{ $vid->category ?: 'Workout Video' }}</span>
                            <h4>{{ $vidTitle }}</h4>
                            @if($vid->description)
                                <p>{{ $vid->description }}</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- =====================================================================
         SECTION 10: FRONTEND MEMBERSHIP REGISTRATION FORM (Restored & Styled)
         ===================================================================== --}}
    @if($sectionKey === 'registration' && ($vis['show_registration'] ?? '1') === '1')
    <section class="gym-section gym-reg-section" id="register-section">
        <div class="gym-container">
            <div class="gym-reg-card">
                <div class="gym-sec-header" style="margin-bottom:18px;">
                    <div>
                        <span class="gym-reg-eyebrow">{{ $tr['reg_eyebrow'] ?? 'MEMBERSHIP REGISTRATION' }}</span>
                        <h2 class="gym-sec-title" style="margin-top:4px;">{{ $tr['reg_title'] ?? 'Join IronPulse Gym Today' }}</h2>
                        <p style="font-size:12.5px;color:#9CA3AF;margin-top:4px;">
                            {{ $tr['reg_subtitle'] ?? 'Complete your registration below to activate your membership and unlock instant Member Portal & QR Check-in access.' }}
                        </p>
                    </div>
                    <a href="{{ route('gymfitness.portal') }}" class="gym-btn-outline">
                        <span>{{ $tr['nav_portal'] ?? 'Member Portal' }}</span>
                        <svg class="gym-arrow-ne" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M2.5 9.5L9.5 2.5M9.5 2.5H3.5M9.5 2.5V8.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                </div>

                <form action="{{ route('gymfitness.register.submit') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="gym-form-grid">
                        <div class="gym-form-group">
                            <label>{{ $tr['reg_full_name'] ?? 'Full Name' }} *</label>
                            <input type="text" name="name" class="gym-input" required placeholder="e.g. Tanvir Ahmed" value="{{ old('name') }}">
                        </div>

                        <div class="gym-form-group">
                            <label>{{ $tr['reg_email'] ?? 'Email Address' }} *</label>
                            <input type="email" name="email" class="gym-input" required placeholder="member@example.com" value="{{ old('email') }}">
                        </div>

                        <div class="gym-form-group">
                            <label>{{ $tr['reg_phone'] ?? 'Phone Number' }} *</label>
                            <input type="text" name="phone" class="gym-input" required placeholder="+880 1711-000000" value="{{ old('phone') }}">
                        </div>

                        <div class="gym-form-group">
                            <label>{{ $tr['reg_dob'] ?? 'Date of Birth' }}</label>
                            <input type="date" name="date_of_birth" class="gym-input" value="{{ old('date_of_birth', '1997-05-15') }}">
                        </div>

                        <div class="gym-form-group">
                            <label>{{ $tr['reg_gender'] ?? 'Gender' }} *</label>
                            <select name="gender" class="gym-select" required>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        <div class="gym-form-group">
                            <label>Blood Group</label>
                            <select name="blood_group" class="gym-select">
                                <option value="O+">O+</option>
                                <option value="A+">A+</option>
                                <option value="B+">B+</option>
                                <option value="AB+">AB+</option>
                                <option value="O-">O-</option>
                            </select>
                        </div>

                        <div class="gym-form-group">
                            <label>{{ $tr['reg_select_package'] ?? 'Select Membership Package' }} *</label>
                            <select name="package_id" class="gym-select" required>
                                @foreach($packages as $pkg)
                                    <option value="{{ $pkg->id }}" {{ $pkg->is_featured ? 'selected' : '' }}>
                                        {{ $isBn ? ($pkg->name_bn ?: $pkg->name) : $pkg->name }} — {{ $pkg->currency ?? '$' }}{{ (float) $pkg->monthly_price }}/mo
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="gym-form-group">
                            <label>{{ $tr['reg_select_duration'] ?? 'Membership Duration' }} *</label>
                            <select name="billing_cycle" class="gym-select" required>
                                <option value="monthly">{{ $tr['billing_monthly'] ?? 'Monthly (1 Month)' }}</option>
                                <option value="quarterly">{{ $tr['billing_quarterly'] ?? 'Quarterly (3 Months)' }}</option>
                                <option value="six_month">{{ $tr['billing_six_month'] ?? '6 Months' }}</option>
                                <option value="yearly">{{ $tr['billing_yearly'] ?? 'Yearly (12 Months)' }}</option>
                            </select>
                        </div>

                        <div class="gym-form-group">
                            <label>{{ $tr['reg_payment_method'] ?? 'Select Payment Method' }} *</label>
                            <select name="payment_method" class="gym-select" required>
                                <option value="bKash">bKash</option>
                                <option value="Nagad">Nagad</option>
                                <option value="Card">Visa / MasterCard</option>
                                <option value="Cash">Cash at Reception</option>
                            </select>
                        </div>

                        <div class="gym-form-group">
                            <label>{{ $tr['reg_emergency_name'] ?? 'Emergency Contact Name' }} *</label>
                            <input type="text" name="emergency_contact_name" class="gym-input" required placeholder="Guardian / Spouse Name" value="{{ old('emergency_contact_name') }}">
                        </div>

                        <div class="gym-form-group">
                            <label>{{ $tr['reg_emergency_phone'] ?? 'Emergency Contact Phone' }} *</label>
                            <input type="text" name="emergency_contact_phone" class="gym-input" required placeholder="+880 1811-000000" value="{{ old('emergency_contact_phone') }}">
                        </div>

                        <div class="gym-form-group">
                            <label>{{ $tr['reg_address'] ?? 'Present Address' }} *</label>
                            <input type="text" name="address" class="gym-input" required placeholder="House, Road, Area, Dhaka" value="{{ old('address') }}">
                        </div>
                    </div>

                    <div style="margin-top:18px;display:flex;justify-content:flex-end;">
                        <button type="submit" class="gym-btn-lime">
                            <span>{{ $tr['reg_submit_btn'] ?? 'Complete Registration & Join Now' }}</span>
                            <svg class="gym-arrow-ne" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M2.5 9.5L9.5 2.5M9.5 2.5H3.5M9.5 2.5V8.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>
    @endif

@endforeach
@endsection
