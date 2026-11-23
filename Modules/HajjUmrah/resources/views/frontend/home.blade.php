@extends('hajjumrah::frontend.layouts.master')

@section('title', $tr['meta_title'] ?? 'Al Hajenra — Hajj & Umrah Travel Agency')

@section('content')
    @php
        $sectionOrder = $t['section_order'] ?? [
            'hero',
            'trust_badges',
            'special_offers',
            'services',
            'partners',
            'pricing',
            'learning',
            'testimonials',
            'gallery',
            'blog',
        ];
        $vis = $t['visibility'] ?? [];
    @endphp

    @foreach($sectionOrder as $sectionKey)
        {{-- =================================================================
             1. HERO SECTION (Text Overlaid on Top of Makkah Tawaf Slider Image)
             ================================================================= --}}
        @if($sectionKey === 'hero' && (($vis['show_hero'] ?? $vis['hero'] ?? '1') === '1'))
            @php
                $heroPrimaryImg = $t['hero']['hero_image'] ?? '/module-media/HajjUmrah/theme/hero-makkah-tawaf.jpg';
                $slideImages = [$heroPrimaryImg];
                if (isset($sliders) && $sliders->isNotEmpty()) {
                    foreach ($sliders as $sl) {
                        if (!empty($sl->image) && !in_array($sl->image, $slideImages, true)) {
                            $slideImages[] = $sl->image;
                        }
                    }
                }
                if (count($slideImages) === 1) {
                    $slideImages[] = '/module-media/HajjUmrah/theme/hero-kaaba-haram.jpg';
                }
            @endphp
            <section class="hajj-hero" id="hero">
                <!-- Background Slider Images -->
                <div class="hajj-hero-slider-bg" id="hajjHeroSlider">
                    @foreach($slideImages as $idx => $sImg)
                        <div class="hajj-hero-slide {{ $idx === 0 ? 'active' : '' }}" data-slide-index="{{ $idx }}">
                            <img src="{{ $sImg }}" alt="Holy Kaaba Tawaf in Makkah">
                        </div>
                    @endforeach
                </div>

                <!-- Dark Gradient Overlay for Crisp Text Readability -->
                <div class="hajj-hero-overlay"></div>

                <!-- Overlaid Hero Headline, Subtitle & CTA Button -->
                <div class="hajj-hero-content">
                    <div class="hajj-hero-badge">
                        <i class="fa-solid fa-kaaba"></i>
                        <span>{{ $tr['brand_name'] ?? 'Al Hajenra' }} • {{ $tr['brand_subtitle'] ?? 'Hajj & Umrah' }}</span>
                    </div>
                    <h1 class="hajj-hero-title">{{ $tr['hero_title'] ?? 'Economy Hajj & Umrah Package' }}</h1>
                    <div class="hajj-hero-subtitle">
                        {!! $tr['hero_subtitle'] ?? 'Budget-friendly & luxury package for a comfortable hajj and umrah journey to the holy makah & madinah for muslim brothers and sisters.' !!}
                    </div>
                    <a href="{{ $t['hero']['cta_url'] ?? '#pricing' }}" class="hajj-btn-gold">
                        <span>{{ $tr['hero_cta'] ?? 'Get a Package Price' }}</span>
                        <i class="fa-solid fa-chevron-right rtl-flip" style="font-size: 11px;"></i>
                    </a>
                </div>

                <!-- Slider Dots -->
                @if(count($slideImages) > 1)
                    <div class="hajj-hero-dots" id="hajjHeroDots">
                        @foreach($slideImages as $idx => $sImg)
                            <button type="button" class="hajj-hero-dot {{ $idx === 0 ? 'active' : '' }}" data-go-slide="{{ $idx }}" aria-label="Slide {{ $idx + 1 }}"></button>
                        @endforeach
                    </div>
                @endif
            </section>
        @endif

        {{-- =================================================================
             2. TRUST & REVIEW BADGES STRIP (Screenshot 1)
             ================================================================= --}}
        @if(in_array($sectionKey, ['trust_badges', 'trust_strip'], true) && (($vis['show_trust_badges'] ?? $vis['trust_strip'] ?? '1') === '1') && $trustBadges->isNotEmpty())
            <section class="hajj-trust-strip" id="trust-badges">
                <div class="hajj-container">
                    <div class="hajj-trust-grid">
                        @foreach($trustBadges as $badge)
                            <div class="hajj-trust-item">
                                <div class="hajj-trust-brand">
                                    @if($badge->logo)
                                        <img src="{{ $badge->logo }}" alt="{{ $badge->name }}" style="max-height: 26px;">
                                    @else
                                        @if(stripos($badge->name, 'Trustpilot') !== false)
                                            <i class="fa-solid fa-star" style="color: #00B67A; font-size: 21px;"></i>
                                            <span style="color: #111827; font-weight: 800;">Trustpilot</span>
                                        @elseif(stripos($badge->name, 'facebook') !== false)
                                            <span style="color: #1877F2; font-weight: 900; font-size: 22px; letter-spacing: -0.03em;">facebook</span>
                                        @elseif(stripos($badge->name, 'Yellow') !== false)
                                            <span style="background: #FACC15; color: #111827; padding: 1px 5px; border-radius: 4px; font-weight: 900; font-size: 16px;">yp</span>
                                            <span style="font-size: 11px; text-align: start; line-height: 1.1; font-weight: 700;">The Real<br>Yellow Pages®</span>
                                        @elseif(stripos($badge->name, 'BAAT') !== false)
                                            <i class="fa-solid fa-kaaba" style="color: #D97706; font-size: 18px;"></i>
                                            <span style="font-size: 12px; font-weight: 800; letter-spacing: 0.03em;">BAAT | UMRAH</span>
                                        @elseif(stripos($badge->name, 'Angi') !== false)
                                            <span style="color: #FF5A5F; font-weight: 900; font-size: 22px; font-family: Georgia, serif;">Angi</span>
                                        @else
                                            <i class="{{ $badge->icon_class ?: 'fa-solid fa-star' }}" style="color: {{ $badge->brand_color ?: '#00B67A' }};"></i>
                                            <span>{{ $badge->name }}</span>
                                        @endif
                                    @endif
                                </div>
                                <div class="hajj-trust-rating">
                                    <i class="fa-solid fa-star"></i>
                                    <span>{{ $badge->rating_text ?: '5/5' }}</span>
                                </div>
                                <div class="hajj-trust-reviews">{{ $badge->reviews_count ?: '500+ reviews' }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        {{-- =================================================================
             3. HAJJ AND UMRAH SPECIAL OFFERS (Screenshot 1: 4 Cards)
             ================================================================= --}}
        @if($sectionKey === 'special_offers' && (($vis['show_special_offers'] ?? '1') === '1'))
            <section class="hajj-offers-section" id="special-offers">
                <div class="hajj-container">
                    <div class="hajj-section-header">
                        <h2 class="hajj-section-title">{{ $tr['offers_title'] ?? 'Hajj And Umrah Special Offers' }}</h2>
                        <div class="hajj-section-subtitle">{!! $tr['offers_subtitle'] ?? "Don't miss out on these special offers from us!" !!}</div>
                    </div>

                    <div class="hajj-offers-grid">
                        @foreach($specialOffers as $offer)
                            <div class="hajj-offer-card">
                                <a href="{{ route('hajjumrah.package.show', $offer->slug) }}" class="hajj-offer-thumb">
                                    <img src="{{ $offer->thumbnail }}" alt="{{ $offer->trans('title', $locale) }}">
                                </a>
                                <div class="hajj-offer-body">
                                    <span class="hajj-offer-cat">{{ $offer->trans('category_label', $locale) ?: 'Category' }}</span>
                                    <h3 class="hajj-offer-title">
                                        <a href="{{ route('hajjumrah.package.show', $offer->slug) }}">
                                            {{ $offer->trans('title', $locale) }}
                                        </a>
                                    </h3>
                                    <div class="hajj-offer-price">
                                        {{ $offer->price_display }}<span>{{ $offer->trans('price_unit', $locale) ?: '/Person' }}</span>
                                    </div>
                                    <div class="hajj-offer-meta">
                                        <span class="hajj-offer-duration">
                                            <i class="fa-regular fa-calendar-days"></i>
                                            <span>{{ $offer->trans('duration', $locale) }}</span>
                                        </span>
                                        <a href="{{ route('hajjumrah.package.show', $offer->slug) }}" class="hajj-offer-link">
                                            <span>{{ $tr['offers_view_details'] ?? 'View Details' }}</span>
                                            <i class="fa-solid fa-arrow-right rtl-flip" style="font-size: 10px;"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        {{-- =================================================================
             4. WHAT DO WE OFFER - SERVICES SECTION (Screenshot 2)
             ================================================================= --}}
        @if($sectionKey === 'services' && (($vis['show_services'] ?? '1') === '1'))
            <section class="hajj-services-section" id="facilities">
                <div class="hajj-container">
                    <div class="hajj-services-wrap">
                        <!-- Left Column: 6 Services Grid + Learn More Button -->
                        <div class="hajj-services-left">
                            <span class="hajj-eyebrow">{{ $tr['services_eyebrow'] ?? 'OUR SERVICES' }}</span>
                            <h2 class="hajj-services-heading">{{ $tr['services_title'] ?? 'What Do We Offer' }}</h2>
                            <div class="hajj-services-desc">
                                {!! $tr['services_subtitle'] ?? 'At Al Hajenra, we provide complete Hajj and Umrah services with care, comfort and reliability. From flight to accommodation, we make your spiritual journey easy and memorable.' !!}
                            </div>

                            <div class="hajj-services-grid">
                                @foreach($services->take(6) as $srv)
                                    <div class="hajj-service-item">
                                        <div class="hajj-service-icon">
                                            <i class="{{ $srv->icon_class ?: 'fa-solid fa-plane-up' }}"></i>
                                        </div>
                                        <div class="hajj-service-text">
                                            <h4>{{ $srv->trans('title', $locale) }}</h4>
                                            <p>{{ $srv->trans('subtitle', $locale) }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <a href="{{ $t['services_section']['learn_more_url'] ?? '#pricing' }}" class="hajj-btn-green-pill">
                                <span>{{ $tr['services_btn'] ?? 'Learn More' }}</span>
                                <i class="fa-solid fa-arrow-right rtl-flip" style="font-size: 12px;"></i>
                            </a>
                        </div>

                        <!-- Right Column: Pilgrim in Ihram with Luggage & Airplane -->
                        <div class="hajj-services-right">
                            <img src="{{ $t['services_section']['pilgrim_image'] ?? '/module-media/HajjUmrah/theme/services-pilgrim.jpg' }}"
                                 alt="Hajj & Umrah Pilgrim Services">
                        </div>
                    </div>
                </div>
            </section>
        @endif

        {{-- =================================================================
             5. AFFILIATED AIRLINES & HOTELS STRIP (Screenshot 2)
             ================================================================= --}}
        @if($sectionKey === 'partners' && (($vis['show_partners'] ?? '1') === '1') && $airlinePartners->isNotEmpty())
            <section class="hajj-partners-strip" id="partners">
                <div class="hajj-container">
                    <div class="hajj-partners-title-wrap">
                        <h3 class="hajj-partners-title">{{ $tr['partners_title'] ?? 'Our Affiliated Tigers & Airlines' }}</h3>
                    </div>

                    <div class="hajj-partners-logos">
                        @foreach($airlinePartners as $partner)
                            @if($partner->logo)
                                <div class="hajj-partner-logo-item">
                                    <img src="{{ $partner->logo }}" alt="{{ $partner->name }}">
                                </div>
                            @elseif(stripos($partner->name, 'Saudia') !== false)
                                <div class="hajj-partner-logo-item" style="color: #036939;">
                                    <i class="fa-solid fa-tree" style="font-size: 24px;"></i>
                                    <span style="font-size: 20px; font-weight: 800;">Saudia</span>
                                </div>
                            @elseif(stripos($partner->name, 'Turkish') !== false)
                                <div class="hajj-partner-logo-item" style="color: #111827;">
                                    <span style="width: 26px; height: 26px; border-radius: 50%; background: #C8102E; color: #FFF; display: inline-flex; align-items: center; justify-content: center; font-size: 12px;">
                                        <i class="fa-solid fa-dove"></i>
                                    </span>
                                    <span style="font-size: 12.5px; font-weight: 900; line-height: 1.05; text-transform: uppercase;">TURKISH<br>AIRLINES</span>
                                </div>
                            @elseif(stripos($partner->name, 'Qatar') !== false)
                                <div class="hajj-partner-logo-item" style="color: #5C0632;">
                                    <span style="font-size: 20px; font-weight: 900; letter-spacing: 0.02em;">QATAR<small style="display: block; font-size: 8.5px; letter-spacing: 0.2em; margin-top: -4px;">AIRWAYS</small></span>
                                    <i class="fa-solid fa-feather-pointed" style="font-size: 20px;"></i>
                                </div>
                            @elseif(stripos($partner->name, 'Singapore') !== false)
                                <div class="hajj-partner-logo-item" style="color: #1D3557;">
                                    <span style="font-size: 11px; font-weight: 800; line-height: 1.1; text-align: end;">SINGAPORE<br>AIRLINES</span>
                                    <i class="fa-solid fa-paper-plane" style="color: #D99B26; font-size: 22px;"></i>
                                </div>
                            @elseif(stripos($partner->name, 'Hilton') !== false)
                                <div class="hajj-partner-logo-item hotel-box">
                                    <span>Hilton</span>
                                </div>
                            @elseif(stripos($partner->name, 'HYATT') !== false)
                                <div class="hajj-partner-logo-item" style="color: #2B547E; font-weight: 600; letter-spacing: 0.16em; font-size: 20px;">
                                    <span>HYATT</span>
                                </div>
                            @else
                                <div class="hajj-partner-logo-item" style="color: {{ $partner->brand_color ?: '#0F172A' }};">
                                    <i class="{{ $partner->icon_class ?: 'fa-solid fa-building' }}"></i>
                                    <span>{{ $partner->name }}</span>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        {{-- =================================================================
             6. ALL INCLUSIVE PACKAGES - 3 PRICING CARDS (Screenshot 2)
             ================================================================= --}}
        @if($sectionKey === 'pricing' && (($vis['show_pricing'] ?? '1') === '1'))
            <section class="hajj-pricing-section" id="pricing">
                <div class="hajj-container">
                    <div class="hajj-pricing-header">
                        <span class="hajj-eyebrow-center">{{ $tr['pricing_eyebrow'] ?? 'OUR PACKAGES' }}</span>
                        <h2 class="hajj-section-title">{{ $tr['pricing_title'] ?? 'All Inclusive Packages' }}</h2>
                        <div class="hajj-section-subtitle">
                            {!! $tr['pricing_subtitle'] ?? 'Choose from our carefully designed Hajj and Umrah packages for a hassle-free journey.' !!}
                        </div>
                    </div>

                    <div class="hajj-pricing-grid">
                        @foreach($pricingPlans as $plan)
                            <div class="hajj-price-card {{ $plan->is_highlighted ? 'highlighted' : '' }}">
                                <!-- Top Plan Icon + Name + Tagline -->
                                <div class="hajj-plan-top">
                                    <div class="hajj-plan-icon">
                                        <i class="{{ $plan->icon_class ?: 'fa-solid fa-crown' }}"></i>
                                    </div>
                                    <div>
                                        <h3 class="hajj-plan-name">{{ $plan->trans('name', $locale) }}</h3>
                                        <div class="hajj-plan-tagline">{!! $plan->trans('tagline', $locale) !!}</div>
                                    </div>
                                </div>

                                <!-- BDT 6,35,000 / 7,35,000 / 8,85,000 STARTS FROM -->
                                <div class="hajj-plan-price-wrap">
                                    <span class="hajj-plan-currency">{{ $plan->currency ?: 'BDT' }}</span>
                                    <div class="hajj-plan-amount-row">
                                        <span class="hajj-plan-amount">{{ $plan->price }}</span>
                                        <span class="hajj-plan-starts">{{ $tr['pricing_starts_from'] ?? ($plan->starts_from_label ?: 'STARTS FROM') }}</span>
                                    </div>
                                </div>

                                <!-- 7 Specification Rows with Green Icons -->
                                <div class="hajj-plan-specs">
                                    <div class="hajj-plan-spec-item">
                                        <i class="fa-solid fa-clock"></i>
                                        <span>{{ $plan->trans('duration', $locale) }}</span>
                                    </div>
                                    <div class="hajj-plan-spec-item">
                                        <i class="fa-solid fa-plane-up"></i>
                                        <span>{{ $plan->trans('direct_flight', $locale) }}</span>
                                    </div>
                                    <div class="hajj-plan-spec-item">
                                        <i class="fa-solid fa-bed"></i>
                                        <span>{{ $plan->trans('hotel_aziziyah', $locale) }}</span>
                                    </div>
                                    <div class="hajj-plan-spec-item">
                                        <i class="fa-solid fa-hotel"></i>
                                        <span>{{ $plan->trans('hotel_makkah', $locale) }}</span>
                                    </div>
                                    <div class="hajj-plan-spec-item">
                                        <i class="fa-solid fa-building"></i>
                                        <span>{{ $plan->trans('hotel_madinah', $locale) }}</span>
                                    </div>
                                    <div class="hajj-plan-spec-item">
                                        <i class="fa-solid fa-utensils"></i>
                                        <span>{{ $plan->trans('food_service', $locale) }}</span>
                                    </div>
                                    <div class="hajj-plan-spec-item">
                                        <i class="fa-solid fa-bus"></i>
                                        <span>{{ $plan->trans('special_services', $locale) }}</span>
                                    </div>
                                </div>

                                <!-- SELECT PACKAGE Button -->
                                <button type="button"
                                        class="hajj-btn-select-pkg"
                                        data-open-booking
                                        data-package-name="{{ $plan->name }} ({{ $plan->currency }} {{ $plan->price }})">
                                    {{ $plan->trans('button_text', $locale) ?: ($tr['pricing_select_btn'] ?? 'SELECT PACKAGE') }}
                                </button>

                                <!-- *Terms & Conditions Applicable -->
                                <div class="hajj-plan-terms">
                                    {{ $plan->trans('terms_note', $locale) ?: ($tr['pricing_terms'] ?? '*Terms & Conditions Applicable') }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        {{-- =================================================================
             7. FEATURED LEARNING SECTION (Educational Umrah/Hajj Videos)
             ================================================================= --}}
        @if($sectionKey === 'learning' && (($vis['show_learning'] ?? '1') === '1') && $videos->isNotEmpty())
            <section class="hajj-learning-section" id="learning">
                <div class="hajj-container">
                    <div class="hajj-section-header">
                        <span class="hajj-eyebrow-center">{{ $tr['learning_eyebrow'] ?? 'FEATURED LEARNING' }}</span>
                        <h2 class="hajj-section-title">{{ $tr['learning_title'] ?? 'Learn Correctly How to Perform Umrah' }}</h2>
                        <p class="hajj-section-subtitle">{{ $tr['learning_subtitle'] ?? 'Watch step-by-step educational guides prepared by our senior Islamic scholars and Muallims.' }}</p>
                    </div>

                    <div class="hajj-video-grid">
                        @foreach($videos->take(3) as $vid)
                            <div class="hajj-video-card">
                                <div class="hajj-video-thumb">
                                    <img src="{{ $vid->thumbnail ?: '/module-media/HajjUmrah/theme/offer-kaaba-door.jpg' }}" alt="{{ $vid->trans('title', $locale) }}">
                                    <div class="hajj-video-overlay">
                                        <button type="button" class="hajj-play-btn" data-video-url="{{ $vid->video_url }}" aria-label="Play Video">
                                            <i class="fa-solid fa-play"></i>
                                        </button>
                                    </div>
                                    @if($vid->duration)
                                        <span class="hajj-video-duration">{{ $vid->duration }}</span>
                                    @endif
                                </div>
                                <div class="hajj-video-body">
                                    <span class="hajj-video-cat">{{ $vid->category ?: 'Tutorial' }}</span>
                                    <h3 class="hajj-video-title">{{ $vid->trans('title', $locale) }}</h3>
                                    <div class="hajj-video-desc">{!! $vid->trans('description', $locale) !!}</div>
                                    <button type="button" class="hajj-btn-green-pill" style="align-self: flex-start; border: none; cursor: pointer;" data-video-url="{{ $vid->video_url }}">
                                        <i class="fa-solid fa-circle-play"></i>
                                        <span>{{ $tr['learning_watch_btn'] ?? 'Watch Video' }}</span>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        {{-- =================================================================
             8. PILGRIM TESTIMONIALS SECTION
             ================================================================= --}}
        @if($sectionKey === 'testimonials' && (($vis['show_testimonials'] ?? '1') === '1') && $testimonials->isNotEmpty())
            <section class="hajj-testimonials-section" id="testimonials">
                <div class="hajj-container">
                    <div class="hajj-section-header">
                        <span class="hajj-eyebrow-center">{{ $tr['testimonials_eyebrow'] ?? 'PILGRIM STORIES' }}</span>
                        <h2 class="hajj-section-title">{{ $tr['testimonials_title'] ?? 'What Our Honored Pilgrims Say' }}</h2>
                        <p class="hajj-section-subtitle">{{ $tr['testimonials_subtitle'] ?? 'Real experiences from brothers and sisters who completed Hajj & Umrah with Al Hajenra.' }}</p>
                    </div>

                    <div class="hajj-testi-grid">
                        @foreach($testimonials->take(3) as $testi)
                            <div class="hajj-testi-card">
                                <div class="hajj-testi-stars">
                                    @for($i = 0; $i < ($testi->rating ?: 5); $i++)
                                        <i class="fa-solid fa-star"></i>
                                    @endfor
                                </div>
                                <div class="hajj-testi-text">{!! $testi->trans('review_text', $locale) !!}</div>
                                <div class="hajj-testi-author">
                                    <img src="{{ $testi->avatar ?: '/module-media/HajjUmrah/theme/services-pilgrim.jpg' }}" alt="{{ $testi->trans('client_name', $locale) }}">
                                    <div>
                                        <h5>{{ $testi->trans('client_name', $locale) }}</h5>
                                        <span>{{ $testi->trans('designation', $locale) }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        {{-- =================================================================
             9. PHOTO GALLERY SECTION
             ================================================================= --}}
        @if($sectionKey === 'gallery' && (($vis['show_gallery'] ?? '1') === '1') && $galleryItems->isNotEmpty())
            <section class="hajj-gallery-section" id="gallery">
                <div class="hajj-container">
                    <div class="hajj-section-header">
                        <span class="hajj-eyebrow-center">{{ $tr['gallery_eyebrow'] ?? 'SACRED MOMENTS' }}</span>
                        <h2 class="hajj-section-title">{{ $tr['gallery_title'] ?? 'Makkah & Madinah Photo Gallery' }}</h2>
                        <p class="hajj-section-subtitle">{{ $tr['gallery_subtitle'] ?? 'Glimpses from our recent Hajj & Umrah groups in the Holy Cities.' }}</p>
                    </div>

                    <div class="hajj-gallery-grid">
                        @foreach($galleryItems as $item)
                            <div class="hajj-gallery-item">
                                <img src="{{ $item->file_path }}" alt="{{ $item->title }}">
                                <div class="hajj-gallery-caption">{{ $item->title }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        {{-- =================================================================
             10. BLOG & TRAVEL GUIDES SECTION
             ================================================================= --}}
        @if($sectionKey === 'blog' && (($vis['show_blog'] ?? '1') === '1') && $blogs->isNotEmpty())
            <section class="hajj-blog-section" id="blog">
                <div class="hajj-container">
                    <div class="hajj-section-header">
                        <span class="hajj-eyebrow-center">{{ $tr['blog_eyebrow'] ?? 'TRAVEL GUIDES & TIPS' }}</span>
                        <h2 class="hajj-section-title">{{ $tr['blog_title'] ?? 'Latest Hajj & Umrah Articles' }}</h2>
                        <p class="hajj-section-subtitle">{{ $tr['blog_subtitle'] ?? 'Helpful checklists, visa guidelines, and spiritual preparation tips.' }}</p>
                    </div>

                    <div class="hajj-video-grid">
                        @foreach($blogs as $blog)
                            <div class="hajj-video-card">
                                <a href="{{ route('hajjumrah.blog.show', $blog->slug) }}" class="hajj-video-thumb">
                                    <img src="{{ $blog->image }}" alt="{{ $blog->trans('title', $locale) }}">
                                </a>
                                <div class="hajj-video-body">
                                    <span class="hajj-video-cat">{{ $blog->trans('category', $locale) }} • {{ $blog->published_date }}</span>
                                    <h3 class="hajj-video-title">
                                        <a href="{{ route('hajjumrah.blog.show', $blog->slug) }}">{{ $blog->trans('title', $locale) }}</a>
                                    </h3>
                                    <div class="hajj-video-desc">{!! $blog->trans('short_description', $locale) !!}</div>
                                    <a href="{{ route('hajjumrah.blog.show', $blog->slug) }}" class="hajj-offer-link">
                                        <span>{{ $tr['blog_read_more'] ?? 'Read Article' }}</span>
                                        <i class="fa-solid fa-arrow-right rtl-flip" style="font-size: 11px;"></i>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif
    @endforeach
@endsection
