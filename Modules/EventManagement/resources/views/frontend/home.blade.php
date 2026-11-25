@extends('eventmanagement::frontend.layouts.master')

@section('content')

    <!-- =====================================================================
         1. HERO SECTION WITH FLOATING CENTERED WHITE HEADER BOX AT TOP
         ===================================================================== -->
    <section class="ev-hero" id="home">

        <!-- Right-Side Full-Height Speaker Image with Diagonal Split & Play Trailer Badge -->
        <div class="ev-hero-right-bg" onclick="openTicketModal('PREMIUM', 69)" title="Play Trailer / Reserve Seat">
            <img src="{{ $s['hero_speaker_image'] ?? '/module-media/EventManagement/theme/hero-speaker.jpg' }}"
                 alt="Eventify Keynote Speaker">
        </div>

        <!-- Bottom-Left White Double Wavy Line Decoration -->
        <svg class="ev-hero-wave" viewBox="0 0 260 72" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M-10 66 C 45 6, 105 6, 145 50 C 165 72, 185 72, 200 72" stroke="#FFFFFF" stroke-width="2" fill="none" opacity="0.9"/>
            <path d="M90 72 C 135 32, 175 45, 205 72" stroke="#FFFFFF" stroke-width="1.4" fill="none" opacity="0.6"/>
        </svg>

        <!-- Bottom-Right Stacked White Circular Slider Buttons (> and <) -->
        <div class="ev-hero-slider-btns">
            <button type="button" class="ev-slider-arrow" onclick="openTicketModal('PREMIUM', 69)" title="Next Slide">
                <i class="bi bi-chevron-right"></i>
            </button>
            <button type="button" class="ev-slider-arrow" onclick="openTicketModal('REGULAR', 49)" title="Previous Slide">
                <i class="bi bi-chevron-left"></i>
            </button>
        </div>

        <!-- Unified Centered Container (.ev-container) for Header Box & Hero Content -->
        <div class="ev-container">

            <!-- FLOATING WHITE HEADER CARD (Topbar + Navbar) -->
            <header class="ev-header-box">
                <!-- Top Announcement Row -->
                <div class="ev-topbar-row">
                    <div class="ev-topbar-left">
                        <i class="bi bi-calendar2-check-fill bag-icon"></i>
                        <span>{{ $s['topbar_text'] ?? 'Are you Planning to Grow Up Conference?' }}</span>
                        <a href="#pricing" onclick="openTicketModal('PREMIUM', 69); return false;">
                            {{ $s['topbar_cta_text'] ?? "Don't Miss!" }}
                        </a>
                    </div>
                    <div class="ev-topbar-right">
                        <span><i class="bi bi-envelope me-1"></i> {{ $s['topbar_email'] ?? 'event@conference.com' }}</span>
                        <span>
                            <i class="bi bi-telephone me-1"></i> {{ $s['topbar_phone'] ?? '(202) 666-0112' }}
                            <i class="bi bi-chat-square-text-fill ms-1" style="font-size: 11px;"></i>
                        </span>
                    </div>
                </div>

                <!-- Main Navbar Row -->
                <div class="ev-Navbar-row">
                    <!-- Eventify 3-Hexagon Cluster Logo -->
                    <a href="#home" class="ev-brand">
                        <svg width="28" height="28" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M16 3L10 6.5V13.5L16 17L22 13.5V6.5L16 3Z" fill="#04222A"/>
                            <circle cx="16" cy="10" r="2.2" fill="#FFFFFF"/>
                            <path d="M9.5 14L3.5 17.5V24.5L9.5 28L15.5 24.5V17.5L9.5 14Z" fill="#04222A"/>
                            <circle cx="9.5" cy="21" r="2.2" fill="#FFFFFF"/>
                            <path d="M22.5 14L16.5 17.5V24.5L22.5 28L28.5 24.5V17.5L22.5 14Z" fill="#04222A"/>
                            <circle cx="22.5" cy="21" r="2.2" fill="#FFFFFF"/>
                        </svg>
                        <span>{{ $s['brand_name'] ?? 'Eventify' }}</span>
                    </a>

                    <!-- Navigation Links -->
                    <ul class="ev-nav-menu d-none d-lg-flex">
                        <li><a href="#home">Home <i class="bi bi-chevron-down"></i></a></li>
                        <li><a href="#about">About Event <i class="bi bi-chevron-down"></i></a></li>
                        <li><a href="#speakers">Speakers <i class="bi bi-chevron-down"></i></a></li>
                        <li><a href="#schedule">Schedule <i class="bi bi-chevron-down"></i></a></li>
                        <li><a href="#memories">Blog <i class="bi bi-chevron-down"></i></a></li>
                        <li><a href="#pricing">Pages <i class="bi bi-chevron-down"></i> <i class="bi bi-chevron-down"></i></a></li>
                    </ul>

                    <!-- Right Icons: Search | User | LinkedIn | Cart (2) -->
                    <div class="ev-nav-icons">
                        <button type="button" onclick="openTicketModal('PREMIUM', 69)" title="Search">
                            <i class="bi bi-search"></i>
                        </button>
                        <span class="ev-nav-divider"></span>
                        <a href="#speakers" title="Attendee Account"><i class="bi bi-person" style="font-size: 19px;"></i></a>
                        <a href="#venue" title="LinkedIn" class="fw-bolder" style="font-size: 14px;">in</a>
                        <button type="button" onclick="openTicketModal('PREMIUM', 69)" title="Ticket Cart">
                            <i class="bi bi-cart2" style="font-size: 18px;"></i>
                            <span class="ev-cart-badge">2</span>
                        </button>
                    </div>
                </div>
            </header>

            @if(session('em_booking_success'))
                <div class="mt-3 position-relative" style="z-index: 20;">
                    <div class="alert alert-success border-0 rounded-4 shadow-sm d-flex align-items-center justify-content-between">
                        <div>
                            <i class="bi bi-check-circle-fill me-2"></i>
                            <strong>Booking Confirmed!</strong> {{ session('em_booking_success') }}
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                </div>
            @endif

            <!-- HERO LEFT CONTENT (Aligned Directly Under Eventify Logo) -->
            <div class="ev-hero-body">
                <div class="ev-hero-eyebrow">
                    <span class="ev-lime-dot"></span>
                    <span>{{ $s['hero_eyebrow'] ?? 'LEAD PURPOSE. INNOVATE BETTER TOGETHER.' }}</span>
                </div>

                <h1 class="ev-hero-title">
                    {{ $s['hero_title_line1'] ?? 'YEARLY BUSINESS' }}<br>
                    <span class="ev-conf-outline-box">{{ $s['hero_title_highlight'] ?? 'CONFERENCES' }}</span>
                    <span>{{ $s['hero_title_edition'] ?? '“ 13 ”' }}</span>
                </h1>

                <!-- 4-Column Countdown with Individual Neon Lime Bottom Bars -->
                <div class="ev-hero-countdown">
                    <div class="ev-cd-col">
                        <div class="ev-cd-num" id="heroCdDays">{{ $s['hero_countdown_days'] ?? '1' }}</div>
                        <div class="ev-cd-label">Days</div>
                    </div>
                    <div class="ev-cd-col">
                        <div class="ev-cd-num" id="heroCdHours">{{ $s['hero_countdown_hours'] ?? '23' }}</div>
                        <div class="ev-cd-label">Hours</div>
                    </div>
                    <div class="ev-cd-col">
                        <div class="ev-cd-num" id="heroCdMinutes">{{ $s['hero_countdown_minutes'] ?? '58' }}</div>
                        <div class="ev-cd-label">Minutes</div>
                    </div>
                    <div class="ev-cd-col">
                        <div class="ev-cd-num" id="heroCdSeconds">{{ $s['hero_countdown_seconds'] ?? '27' }}</div>
                        <div class="ev-cd-label">Seconds</div>
                    </div>
                </div>

                <!-- REGISTER for NOW + Right Chevron Circle -->
                <div>
                    <button type="button" class="ev-btn-group" onclick="openTicketModal('PREMIUM', 69)">
                        <span class="ev-btn-pill">{{ $s['hero_btn_text'] ?? 'REGISTER for NOW' }}</span>
                        <span class="ev-btn-circle"><i class="bi bi-chevron-right"></i></span>
                    </button>
                </div>
            </div>

        </div>
    </section>

    <!-- =====================================================================
         2. ABOUT OUR BUSINESS CONFERENCE (3-IMAGE COLLAGE + 2 STAT PILLS)
         ===================================================================== -->
    <section class="ev-about-section" id="about">
        <div class="ev-container">
            <div class="row g-4 align-items-center">
                <!-- Left 3-Image Collage -->
                <div class="col-lg-6">
                    <div class="ev-collage-wrap">
                        <div class="ev-target-rings"></div>
                        <div class="row g-3 align-items-center">
                            <div class="col-6">
                                <img src="{{ $s['about_img_tall'] ?? '/module-media/EventManagement/theme/about-tall.jpg' }}"
                                     alt="Business Conference Networking"
                                     class="ev-collage-img-tall">
                            </div>
                            <div class="col-6 d-flex flex-column gap-3">
                                <img src="{{ $s['about_img_top_right'] ?? '/module-media/EventManagement/theme/about-top-right.jpg' }}"
                                     alt="Conference Keynote Hall"
                                     class="ev-collage-img-sm">
                                <img src="{{ $s['about_img_bottom_right'] ?? '/module-media/EventManagement/theme/about-bottom-right.jpg' }}"
                                     alt="Conference Audience Applause"
                                     class="ev-collage-img-sm">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Content -->
                <div class="col-lg-6 ps-lg-4">
                    <span class="ev-eyebrow-pill">
                        <span class="ev-lime-dot"></span>
                        <span>{{ $s['about_eyebrow'] ?? 'ABOUT OUR BUSINESS CONFERENCE' }}</span>
                    </span>
                    <h2 class="ev-section-title">
                        {!! nl2br(e($s['about_title'] ?? "Growth Through World\nClass And Conferences")) !!}
                    </h2>
                    <p style="color: #C2D6DC; font-size: 14.5px; line-height: 1.68; margin-bottom: 4px;">
                        {{ $s['about_description'] ?? 'At Business, we bring together brightest minds, industry, and innovators from across industries to explore latest trends, technologies, and strategies shaping the future.' }}
                    </p>

                    <!-- Two Separate Rounded Pill Badges: [30+ Speakers] [10+ Sponsors] -->
                    <div class="ev-about-stat-pills">
                        <div class="ev-stat-badge-pill">
                            <i class="bi bi-gem"></i>
                            <span>{{ $s['about_stat1_text'] ?? '30+ Speakers' }}</span>
                        </div>
                        <div class="ev-stat-badge-pill">
                            <i class="bi bi-people-fill"></i>
                            <span>{{ $s['about_stat2_text'] ?? '10+ Sponsors' }}</span>
                        </div>
                    </div>

                    <!-- REGISTER AN ATTENDEE + Right Chevron Circle -->
                    <div>
                        <button type="button" class="ev-btn-group" onclick="openTicketModal('REGULAR', 49)">
                            <span class="ev-btn-pill">{{ $s['about_btn_text'] ?? 'REGISTER AN ATTENDEE' }}</span>
                            <span class="ev-btn-circle"><i class="bi bi-chevron-right"></i></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- =====================================================================
         3. SPONSORS BAND (Join 6,000+ Companies Already Growing)
         ===================================================================== -->
    <section class="ev-sponsors-band" id="sponsors">
        <div class="ev-container">
            <h4 class="text-center fw-bolder mb-4" style="font-size: 18px; color: #FFFFFF;">
                {{ $s['sponsors_heading'] ?? 'Join 6,000+ Companies Already Growing' }}
            </h4>
            <div class="row g-3 justify-content-center">
                @foreach($sponsors as $sp)
                    <div class="col-6 col-md-3">
                        <a href="{{ $sp->website_url ?: '#sponsors' }}" class="ev-sponsor-card">
                            @if(stripos($sp->name, 'Netz') !== false || stripos($sp->name, 'Nietz') !== false)
                                <svg width="26" height="26" viewBox="0 0 28 28" fill="none">
                                    <circle cx="14" cy="14" r="6" stroke="#FFFFFF" stroke-width="2"/>
                                    <path d="M14 2V6M14 22V26M2 14H6M22 14H26M5.5 5.5L8.5 8.5M19.5 19.5L22.5 22.5M22.5 5.5L19.5 8.5M8.5 19.5L5.5 22.5" stroke="#FFFFFF" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                            @elseif(stripos($sp->name, 'Epicurious') !== false)
                                <svg width="26" height="24" viewBox="0 0 28 24" fill="none">
                                    <path d="M8 4H26L23 8H5L8 4Z" fill="#FFFFFF"/>
                                    <path d="M5 10H21L18 14H2L5 10Z" fill="#FFFFFF"/>
                                    <path d="M2 16H15L12 20H0L2 16Z" fill="#FFFFFF"/>
                                </svg>
                            @elseif(stripos($sp->name, 'Shift') !== false)
                                <svg width="26" height="26" viewBox="0 0 28 28" fill="none">
                                    <circle cx="14" cy="14" r="12" fill="#FFFFFF"/>
                                    <path d="M15.5 6L8.5 15H14L12.5 22L19.5 13H14L15.5 6Z" fill="#133640"/>
                                </svg>
                            @else
                                <svg width="24" height="26" viewBox="0 0 26 28" fill="none">
                                    <path d="M13 2L3 7.8V19.5L13 25.5L23 19.5V7.8L13 2Z" stroke="#FFFFFF" stroke-width="2.4" fill="none"/>
                                    <path d="M3 8L13 14L23 8M13 14V25.5" stroke="#FFFFFF" stroke-width="2.4"/>
                                </svg>
                            @endif
                            <span>{{ $sp->name }}</span>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- =====================================================================
         4. OUR EVENT SPEAKERS (OUR KEYNOTE SPEAKERS)
         ===================================================================== -->
    <section class="ev-speakers-section" id="speakers">
        <div class="ev-container">
            <div class="text-center mb-5">
                <span class="ev-eyebrow-pill">
                    <span class="ev-lime-dot"></span>
                    <span>{{ $s['speakers_eyebrow'] ?? 'OUR KEYNOTE SPEAKERS' }}</span>
                </span>
                <h2 class="ev-section-title mb-0">{{ $s['speakers_heading'] ?? 'Our Event Speakers' }}</h2>
            </div>

            <div class="row g-4 justify-content-center">
                @foreach($speakers as $speaker)
                    <div class="col-6 col-md-3">
                        <div class="ev-speaker-card">
                            <div class="ev-speaker-ring">
                                <img src="{{ $speaker->avatar }}" alt="{{ $speaker->name }}">
                            </div>
                            <div class="ev-speaker-name">{{ $speaker->name }}</div>
                            <div class="ev-speaker-role">{{ $speaker->designation }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- =====================================================================
         5. OUR EVENTS SCHEDULE PLAN (3 TABS + 01 DEC CIRCLE & VIEW DETAILS ->)
         ===================================================================== -->
    <section class="ev-schedule-section" id="schedule">
        <div class="ev-container">
            <div class="text-center mb-4">
                <span class="ev-eyebrow-pill">
                    <span class="ev-lime-dot"></span>
                    <span>{{ $s['schedule_eyebrow'] ?? 'EVENT SCHEDULE' }}</span>
                </span>
                <h2 class="ev-section-title">{{ $s['schedule_heading'] ?? 'Our Events Schedule Plan' }}</h2>
            </div>

            <!-- 3 Filter Tabs: All Events (Jan - Dec 2024) | Business Event (Nov 12, 2024) | Tech Talks (Dec 19, 2024) -->
            <div class="ev-day-tabs">
                <button type="button" class="ev-day-tab-btn active" onclick="filterScheduleDay('day1', this)">
                    <span class="tab-icon"><i class="bi bi-calendar2-week"></i></span>
                    <span>
                        <span class="day-title">{{ $s['day1_title'] ?? 'All Events' }}</span>
                        <span class="day-date">{{ $s['day1_date'] ?? 'Jan - Dec 2024' }}</span>
                    </span>
                </button>
                <button type="button" class="ev-day-tab-btn" onclick="filterScheduleDay('day2', this)">
                    <span class="tab-icon"><i class="bi bi-briefcase-fill"></i></span>
                    <span>
                        <span class="day-title">{{ $s['day2_title'] ?? 'Business Event' }}</span>
                        <span class="day-date">{{ $s['day2_date'] ?? 'Nov 12, 2024' }}</span>
                    </span>
                </button>
                <button type="button" class="ev-day-tab-btn" onclick="filterScheduleDay('day3', this)">
                    <span class="tab-icon"><i class="bi bi-display"></i></span>
                    <span>
                        <span class="day-title">{{ $s['day3_title'] ?? 'Tech Talks' }}</span>
                        <span class="day-date">{{ $s['day3_date'] ?? 'Dec 19, 2024' }}</span>
                    </span>
                </button>
            </div>

            <!-- Schedule Cards with Left 01 Dec Circle -->
            <div>
                @foreach($schedules as $sch)
                    <div class="ev-schedule-row schedule-day-item"
                         data-day="{{ $sch->day_key }}"
                         style="{{ $sch->day_key === 'day1' ? '' : 'display: none;' }}">
                        <div class="ev-schedule-date-col">
                            <div class="ev-schedule-date-circle">
                                <span class="num">{{ $sch->session_number }}</span>
                                <span class="mon">Dec</span>
                            </div>
                            <div class="ev-schedule-connector"></div>
                        </div>

                        <div class="ev-schedule-card">
                            <div class="row g-4 align-items-center">
                                <div class="col-md-4">
                                    <img src="{{ $sch->image }}" alt="{{ $sch->title }}" class="ev-schedule-thumb">
                                </div>
                                <div class="col-md-8">
                                    <div class="ev-schedule-meta">
                                        <span><i class="bi bi-clock me-1"></i> {{ $sch->time_range }}</span>
                                        <span class="opacity-50">|</span>
                                        <span><i class="bi bi-tags me-1"></i> {{ $sch->venue_location }}</span>
                                    </div>
                                    <h3 class="ev-schedule-title">{{ $sch->title }}</h3>

                                    <div class="ev-schedule-speakers">
                                        <div class="ev-mini-speaker">
                                            <img src="{{ $sch->speaker1_avatar ?: '/module-media/EventManagement/theme/speaker-mini-1.jpg' }}"
                                                 alt="{{ $sch->speaker1_name }}">
                                            <div>
                                                <div class="sp-name">{{ $sch->speaker1_name }}</div>
                                                <div class="sp-role">{{ $sch->speaker1_role }}</div>
                                            </div>
                                        </div>
                                        @if(!empty($sch->speaker2_name))
                                            <div class="ev-mini-speaker">
                                                <img src="{{ $sch->speaker2_avatar ?: '/module-media/EventManagement/theme/speaker-mini-2.jpg' }}"
                                                     alt="{{ $sch->speaker2_name }}">
                                                <div>
                                                    <div class="sp-name">{{ $sch->speaker2_name }}</div>
                                                    <div class="sp-role">{{ $sch->speaker2_role }}</div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="text-end mt-2">
                                        <button type="button"
                                                class="ev-view-details-link"
                                                onclick="openTicketModal('PREMIUM', 69, @js($sch->title))">
                                            <span class="txt">{{ $sch->button_text ?: 'View Details' }}</span>
                                            <i class="bi bi-arrow-right"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- =====================================================================
         6. RECENT MEMORIES 2024 (LAST YEAR MEMORY)
         ===================================================================== -->
    <section class="ev-memories-section" id="memories">
        <div class="ev-container">
            <div class="d-flex align-items-end justify-content-between flex-wrap gap-3 mb-4">
                <div>
                    <span class="ev-eyebrow-pill">
                        <span class="ev-lime-dot"></span>
                        <span>{{ $s['memories_eyebrow'] ?? 'LAST YEAR MEMORY' }}</span>
                    </span>
                    <h2 class="ev-section-title mb-0">{{ $s['memories_heading'] ?? 'Recent Memories 2024' }}</h2>
                </div>
                <div>
                    <a href="#pricing" class="ev-btn-group">
                        <span class="ev-btn-pill">{{ $s['memories_btn_text'] ?? 'SEE RECENT PHOTOS' }}</span>
                        <span class="ev-btn-circle"><i class="bi bi-chevron-right"></i></span>
                    </a>
                </div>
            </div>

            <div class="row g-4">
                @foreach($recentMemories as $mem)
                    <div class="col-md-6">
                        <div class="ev-memory-card">
                            <img src="{{ $mem->image }}" alt="{{ $mem->title }}">
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- =====================================================================
         7. PRICING PLAN (GET YOUR ENTRY TICKET: $49 / $69 / $99)
         ===================================================================== -->
    <section class="ev-pricing-section" id="pricing">
        <div class="ev-container">
            <div class="text-center mb-5">
                <span class="ev-eyebrow-pill">
                    <span class="ev-lime-dot"></span>
                    <span>{{ $s['pricing_eyebrow'] ?? 'PRICING PLAN' }}</span>
                </span>
                <h2 class="ev-section-title mb-0">{{ $s['pricing_heading'] ?? 'Get Your Entry Ticket' }}</h2>
            </div>

            <div class="row g-4 align-items-center justify-content-center">
                @foreach($pricingPlans as $plan)
                    <div class="col-md-4">
                        <div class="ev-price-card {{ $plan->is_highlighted ? 'highlighted' : '' }}">
                            <div>
                                <div class="ev-price-tier">{{ $plan->name }}</div>
                                <div class="ev-price-value">{{ $plan->currency }}{{ number_format((float)$plan->price, 0) }}</div>
                                <div class="ev-price-divider"></div>
                                <div class="ev-price-sub">{{ $plan->subtitle ?: 'ATTENDEE' }}</div>
                            </div>
                            <div>
                                <button type="button"
                                        class="ev-btn-group {{ $plan->is_highlighted ? 'ev-btn-dark' : '' }}"
                                        onclick="openTicketModal(@js($plan->name), {{ (float)$plan->price }})">
                                    <span class="ev-btn-pill">{{ $plan->button_text ?: 'BUY TICKET' }}</span>
                                    <span class="ev-btn-circle"><i class="bi bi-chevron-right"></i></span>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- =====================================================================
         8. DIRECTION TO THE VENUE (HOW TO REACH)
         ===================================================================== -->
    <section class="ev-venue-section" id="venue">
        <div class="ev-container">
            <div class="text-center mb-5">
                <span class="ev-eyebrow-pill">
                    <span class="ev-lime-dot"></span>
                    <span>{{ $s['venue_eyebrow'] ?? 'HOW TO REACH' }}</span>
                </span>
                <h2 class="ev-section-title mb-0">{{ $s['venue_heading'] ?? 'Direction To The Venue' }}</h2>
            </div>

            <div class="row g-4 align-items-stretch">
                <!-- Left Venue Map -->
                <div class="col-lg-7">
                    <div class="ev-map-frame">
                        <img src="{{ $s['venue_map_image'] ?? '/module-media/EventManagement/theme/venue-map.jpg' }}"
                             alt="Direction To The Venue Map">
                    </div>
                </div>

                <!-- Right 3 Contact Cards -->
                <div class="col-lg-5 d-flex flex-column justify-content-between">
                    <div class="ev-venue-contact-card">
                        <span class="ev-venue-icon"><i class="bi bi-envelope"></i></span>
                        <div>
                            <div class="ev-venue-label">{{ $s['venue_email_label'] ?? 'OUR EMAIL' }}</div>
                            <div class="ev-venue-val">{{ $s['venue_email_value'] ?? 'event@conference.com' }}</div>
                        </div>
                    </div>

                    <div class="ev-venue-contact-card">
                        <span class="ev-venue-icon"><i class="bi bi-telephone"></i></span>
                        <div>
                            <div class="ev-venue-label">{{ $s['venue_phone_label'] ?? 'CALL NUMBER' }}</div>
                            <div class="ev-venue-val">{{ $s['venue_phone_value'] ?? '(202) 666-0112' }}</div>
                        </div>
                    </div>

                    <div class="ev-venue-contact-card">
                        <span class="ev-venue-icon"><i class="bi bi-calendar3"></i></span>
                        <div>
                            <div class="ev-venue-label">{{ $s['venue_schedule_label'] ?? 'SCHEDULE' }}</div>
                            <div class="ev-venue-val">{{ $s['venue_schedule_value'] ?? 'Sunday, 08:00 AM - 07 PM' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- =====================================================================
         9. BOTTOM NEON LIME COUNTDOWN CTA BANNER
         ===================================================================== -->
    <div style="background: var(--ev-dark); padding-bottom: 16px;">
        <div class="ev-container">
            <div class="ev-lime-banner">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <!-- 4 White Countdown Boxes -->
                    <div class="d-flex align-items-center flex-wrap gap-3">
                        <div class="ev-white-cd-box">
                            <div class="num" id="botCdDays">{{ $s['hero_countdown_days'] ?? '1' }}</div>
                            <div class="lbl">Days</div>
                        </div>
                        <div class="ev-white-cd-box">
                            <div class="num" id="botCdHours">{{ $s['hero_countdown_hours'] ?? '23' }}</div>
                            <div class="lbl">Hours</div>
                        </div>
                        <div class="ev-white-cd-box">
                            <div class="num" id="botCdMinutes">{{ $s['hero_countdown_minutes'] ?? '58' }}</div>
                            <div class="lbl">Minutes</div>
                        </div>
                        <div class="ev-white-cd-box">
                            <div class="num" id="botCdSeconds">20</div>
                            <div class="lbl">Seconds</div>
                        </div>
                    </div>

                    <!-- Buy A Ticket Dark CTA -->
                    <div>
                        <button type="button" class="ev-btn-group ev-btn-dark" onclick="openTicketModal('PREMIUM', 69)">
                            <span class="ev-btn-pill">{{ $s['banner_btn_text'] ?? 'BUY A TICKET' }}</span>
                            <span class="ev-btn-circle"><i class="bi bi-chevron-right"></i></span>
                        </button>
                    </div>
                </div>

                <!-- Date & Location Meta -->
                <div class="d-flex align-items-center flex-wrap gap-4 mt-3 pt-1 fw-bold" style="font-size: 13px; color: #04222A;">
                    <span><i class="bi bi-calendar2-event me-1"></i> {{ $s['banner_date_time'] ?? '30 January 2025 - 9am to 11.30pm' }}</span>
                    <span><i class="bi bi-geo-alt-fill me-1"></i> {{ $s['banner_location'] ?? 'Secret Location In The UK' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- =====================================================================
         10. DARK FOOTER WITH RECENT EVENT GALLERY
         ===================================================================== -->
    <footer class="ev-footer">
        <div class="ev-container">
            <div class="row g-4 pb-4">
                <!-- Col 1: Brand & Bio -->
                <div class="col-lg-4 col-md-6">
                    <a href="#home" class="ev-brand text-white mb-3 d-inline-flex">
                        <svg width="26" height="26" viewBox="0 0 32 32" fill="none">
                            <path d="M16 3L10 6.5V13.5L16 17L22 13.5V6.5L16 3Z" fill="#BAF92C"/>
                            <circle cx="16" cy="10" r="2.2" fill="#04222A"/>
                            <path d="M9.5 14L3.5 17.5V24.5L9.5 28L15.5 24.5V17.5L9.5 14Z" fill="#BAF92C"/>
                            <circle cx="9.5" cy="21" r="2.2" fill="#04222A"/>
                            <path d="M22.5 14L16.5 17.5V24.5L22.5 28L28.5 24.5V17.5L22.5 14Z" fill="#BAF92C"/>
                            <circle cx="22.5" cy="21" r="2.2" fill="#04222A"/>
                        </svg>
                        <span>{{ $s['brand_name'] ?? 'Eventify' }}</span>
                    </a>
                    <p class="mb-3" style="line-height: 1.65; max-width: 320px;">
                        {{ $s['footer_about'] ?? 'We are committed to creating a platform where business leaders, innovators, and professionals can come together to exchange ideas' }}
                    </p>
                    <div class="ev-footer-socials">
                        <a href="#home"><i class="bi bi-facebook"></i></a>
                        <a href="#home"><i class="bi bi-linkedin"></i></a>
                        <a href="#home"><i class="bi bi-instagram"></i></a>
                        <a href="#home"><i class="bi bi-pinterest"></i></a>
                    </div>
                </div>

                <!-- Col 2: Quick Links -->
                <div class="col-lg-2 col-md-6">
                    <h5 class="ev-footer-title">Quick Links</h5>
                    <ul class="ev-footer-links">
                        <li><a href="#about">About Us</a></li>
                        <li><a href="#memories">Our Blogs</a></li>
                        <li><a href="#schedule">Event Listing</a></li>
                        <li><a href="#pricing">Pricing Plan</a></li>
                        <li><a href="#venue">Contact Us</a></li>
                    </ul>
                </div>

                <!-- Col 3: Contact Us -->
                <div class="col-lg-3 col-md-6">
                    <h5 class="ev-footer-title">Contact Us</h5>
                    <ul class="ev-footer-links">
                        <li><i class="bi bi-telephone me-2 text-white"></i> {{ $s['footer_phone'] ?? '(202) 666-0112' }}</li>
                        <li><i class="bi bi-geo-alt me-2 text-white"></i> {{ $s['footer_location'] ?? 'NYC, USA, Main Event Room' }}</li>
                        <li><i class="bi bi-envelope me-2 text-white"></i> {{ $s['footer_email'] ?? 'event@conference.com' }}</li>
                        <li><i class="bi bi-globe2 me-2 text-white"></i> {{ $s['footer_website'] ?? 'eventifyevent.com' }}</li>
                    </ul>
                </div>

                <!-- Col 4: Our Recent Event Gallery (2x3 Grid) -->
                <div class="col-lg-3 col-md-6">
                    <h5 class="ev-footer-title">Our Recent Event Gallery</h5>
                    <div class="ev-footer-gallery-grid">
                        @foreach($footerGallery as $fg)
                            <a href="#memories" title="{{ $fg->title }}">
                                <img src="{{ $fg->image }}" alt="{{ $fg->title }}">
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="text-center pt-4 mt-2" style="border-top: 1px solid rgba(255,255,255,0.08); font-size: 12.5px;">
                {{ $s['footer_copyright'] ?? '© Copyright 2024 - Eventify. All Right Reserved' }}
            </div>
        </div>
    </footer>

    <!-- =====================================================================
         11. INTERACTIVE TICKET BOOKING MODAL
         ===================================================================== -->
    <div class="modal fade" id="ticketBookingModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('eventmanagement.book.submit') }}" method="POST"
                  class="modal-content border-0 rounded-4 shadow-lg"
                  style="background: #082932; color: #FFFFFF; border: 1px solid rgba(186, 249, 44, 0.35) !important;">
                @csrf
                <div class="modal-header border-bottom border-secondary border-opacity-25">
                    <div>
                        <span class="ev-eyebrow-pill mb-1">
                            <span class="ev-lime-dot"></span>
                            <span>INSTANT CONFERENCE PASS</span>
                        </span>
                        <h5 class="modal-title fw-bolder text-white">Reserve Your Eventify Seat</h5>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-white-50">Full Name *</label>
                            <input type="text" name="attendee_name" class="form-control bg-dark text-white border-secondary" required placeholder="Alex R. Robertson">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-white-50">Email Address *</label>
                            <input type="email" name="email" class="form-control bg-dark text-white border-secondary" required placeholder="you@conference.com">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-white-50">Phone Number</label>
                            <input type="text" name="phone" class="form-control bg-dark text-white border-secondary" placeholder="(202) 666-0112">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-white-50">Company / Organization</label>
                            <input type="text" name="company" class="form-control bg-dark text-white border-secondary" placeholder="BataShift Inc.">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label small fw-bold text-white-50">Ticket Tier *</label>
                            <select name="plan_name" id="modalPlanSelect" class="form-select bg-dark text-white border-secondary">
                                @foreach($pricingPlans as $plan)
                                    <option value="{{ $plan->name }}" data-price="{{ (float)$plan->price }}">
                                        {{ $plan->name }} PASS — {{ $plan->currency }}{{ number_format((float)$plan->price, 0) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-white-50">Seats *</label>
                            <input type="number" name="quantity" id="modalQtyInput" value="1" min="1" max="20" class="form-control bg-dark text-white border-secondary" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-white-50">Preferred Keynote Session</label>
                            <input type="text" name="session_choice" id="modalSessionInput" class="form-control bg-dark text-white border-secondary" value="Unlock the World Event Introduction">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top border-secondary border-opacity-25 d-flex justify-content-between">
                    <button type="button" class="btn btn-sm btn-outline-light rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="ev-btn-group">
                        <span class="ev-btn-pill">CONFIRM TICKET BOOKING</span>
                        <span class="ev-btn-circle"><i class="bi bi-chevron-right"></i></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    // 1. Interactive Schedule Day Tab Switcher
    function filterScheduleDay(dayKey, btn) {
        document.querySelectorAll('.ev-day-tab-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        document.querySelectorAll('.schedule-day-item').forEach(row => {
            if (row.getAttribute('data-day') === dayKey) {
                row.style.display = 'flex';
            } else {
                row.style.display = 'none';
            }
        });
    }

    // 2. Open Ticket Booking Modal pre-filled with selected Plan & Session
    function openTicketModal(planName, price, sessionTitle) {
        const select = document.getElementById('modalPlanSelect');
        if (select && planName) {
            for (let i = 0; i < select.options.length; i++) {
                if (select.options[i].value.toUpperCase() === planName.toUpperCase()) {
                    select.selectedIndex = i;
                    break;
                }
            }
        }
        const sessionInput = document.getElementById('modalSessionInput');
        if (sessionInput && sessionTitle) {
            sessionInput.value = sessionTitle;
        }
        const modalEl = document.getElementById('ticketBookingModal');
        if (modalEl) {
            bootstrap.Modal.getOrCreateInstance(modalEl).show();
        }
    }

    // 3. Live ticking seconds on both Hero & Bottom Banner Countdowns
    (function () {
        let totalSeconds =
            (parseInt(document.getElementById('heroCdDays')?.textContent || '1', 10) * 86400) +
            (parseInt(document.getElementById('heroCdHours')?.textContent || '23', 10) * 3600) +
            (parseInt(document.getElementById('heroCdMinutes')?.textContent || '58', 10) * 60) +
            parseInt(document.getElementById('heroCdSeconds')?.textContent || '27', 10);

        setInterval(function () {
            if (totalSeconds <= 0) return;
            totalSeconds--;

            const d = Math.floor(totalSeconds / 86400);
            const h = Math.floor((totalSeconds % 86400) / 3600);
            const m = Math.floor((totalSeconds % 3600) / 60);
            const s = totalSeconds % 60;

            const elD = document.getElementById('heroCdDays');
            const elH = document.getElementById('heroCdHours');
            const elM = document.getElementById('heroCdMinutes');
            const elS = document.getElementById('heroCdSeconds');

            if (elD) elD.textContent = d;
            if (elH) elH.textContent = h;
            if (elM) elM.textContent = m;
            if (elS) elS.textContent = s < 10 ? '0' + s : s;

            const bD = document.getElementById('botCdDays');
            const bH = document.getElementById('botCdHours');
            const bM = document.getElementById('botCdMinutes');
            const bS = document.getElementById('botCdSeconds');

            if (bD) bD.textContent = d;
            if (bH) bH.textContent = h;
            if (bM) bM.textContent = m;
            if (bS) bS.textContent = s < 10 ? '0' + s : s;
        }, 1000);
    })();
</script>
@endpush
