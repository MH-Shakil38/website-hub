@extends('wonderlearn::frontend.layouts.master')

@section('content')
<div class="wl-page-canvas" id="home">

    @if(session('wl_success'))
        <div class="alert alert-success alert-dismissible fade show m-3 rounded-4 fw-bold shadow-sm border-success" role="alert">
            <i class="bi bi-stars text-warning me-2 fs-5"></i> {{ session('wl_success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- =====================================================================
         1. TOP CLOUD NAVBAR
         ===================================================================== -->
    <header class="wl-navbar d-flex align-items-center justify-content-between">
        <a href="#home" class="wl-brand">
            <img src="{{ $s['logo_icon'] ?? '/module-media/WonderLearn/theme/logo-book-icon.png' }}"
                 alt="{{ $s['brand_name'] ?? 'WonderLearn' }}"
                 class="wl-brand-icon">
            <div>
                <div class="wl-brand-title">
                    <span class="w-blue">Wonder</span><span class="w-orange">Learn</span>
                </div>
                <span class="wl-brand-sub">{{ $s['brand_tagline'] ?? 'Learn • Discover • Grow' }}</span>
            </div>
        </a>

        <ul class="wl-nav-links">
            @if($headerMenus->isNotEmpty())
                @foreach($headerMenus as $menu)
                    <li>
                        <a href="{{ $menu->url }}" target="{{ $menu->target ?? '_self' }}" class="{{ $loop->first ? 'active' : '' }}">
                            {{ $menu->title }}
                        </a>
                    </li>
                @endforeach
            @else
                <li><a href="#home" class="active">Home</a></li>
                <li><a href="#explore">Explore</a></li>
                <li><a href="#stories">Stories</a></li>
                <li><a href="#games">Games</a></li>
                <li><a href="#about">About Us</a></li>
            @endif
        </ul>

        <div class="wl-nav-actions">
            <button type="button" class="wl-btn-search" data-bs-toggle="modal" data-bs-target="#wlSearchModal" title="Search Stories & Games">
                <i class="bi bi-search"></i>
            </button>
            <button type="button" class="wl-btn-login" data-bs-toggle="modal" data-bs-target="#wlLoginModal">
                {{ $s['login_text'] ?? 'Log In' }}
            </button>
            <button type="button" class="wl-btn-join" data-bs-toggle="modal" data-bs-target="#wlJoinModal">
                <i class="bi bi-person-plus-fill"></i>
                <span>{{ $s['join_text'] ?? 'Join Now' }}</span>
            </button>
        </div>
    </header>

    <!-- =====================================================================
         2. MAGICAL TREEHOUSE HERO SECTION (1:1 Same-to-Same)
         ===================================================================== -->
    <section class="wl-hero" style="background-image: url('{{ $s['hero_bg_image'] ?? '/module-media/WonderLearn/theme/hero-magic-world.jpg' }}');">
        <div class="wl-hero-center-overlay">
            <h1 class="wl-hero-title-1">{{ $s['hero_title_line1'] ?? 'Let Curiosity' }}</h1>
            <h2 class="wl-hero-title-2">{{ $s['hero_title_line2'] ?? 'Lead the Way' }}</h2>
            <p class="wl-hero-sub">{{ $s['hero_subtitle'] ?? 'A magical world where learning feels like an adventure!' }}</p>
            <a href="#explore" class="wl-btn-explore">
                <span>{{ $s['hero_btn_text'] ?? 'Start Exploring' }}</span>
                <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </section>

    <!-- =====================================================================
         3. FLOATING 4-BADGE TRUST BAR
         ===================================================================== -->
    <div class="wl-trust-wrap">
        <div class="wl-trust-bar">
            <div class="wl-trust-item">
                <span class="wl-trust-icon" style="background: #DCFCE7; color: #16A34A;">
                    <i class="bi bi-shield-fill-check"></i>
                </span>
                <span>{{ $s['trust_badge_1'] ?? 'Safe & Ad-Free' }}</span>
            </div>
            <div class="wl-trust-item">
                <span class="wl-trust-icon" style="background: #FEF3C7; color: #F59E0B;">
                    <i class="bi bi-star-fill"></i>
                </span>
                <span>{{ $s['trust_badge_2'] ?? 'Expert Curated' }}</span>
            </div>
            <div class="wl-trust-item">
                <span class="wl-trust-icon" style="background: #DBEAFE; color: #2563EB;">
                    <i class="bi bi-controller"></i>
                </span>
                <span>{{ $s['trust_badge_3'] ?? 'Learn Through Play' }}</span>
            </div>
            <div class="wl-trust-item">
                <span class="wl-trust-icon" style="background: #FEE2E2; color: #EF4444;">
                    <i class="bi bi-heart-fill"></i>
                </span>
                <span>{{ $s['trust_badge_4'] ?? 'Age 4+ to 12' }}</span>
            </div>
        </div>
    </div>

    <!-- =====================================================================
         4. EXPLORE BY TOPIC (With Reading Bunny & Wooden Signpost)
         ===================================================================== -->
    <section class="wl-topics-section" id="explore">
        <div class="text-center">
            <h2 class="wl-section-title">
                <span class="wl-sparkle">✦</span>
                <span>{{ $s['topics_heading'] ?? 'Explore by Topic' }}</span>
                <span class="wl-sparkle">✦</span>
            </h2>
        </div>

        <div class="wl-topics-stage">
            <img src="{{ $s['mascot_bunny'] ?? '/module-media/WonderLearn/theme/mascot-bunny.png' }}"
                 alt="Reading Bunny Mascot"
                 class="wl-mascot-bunny">

            <div class="wl-topics-grid">
                @foreach($topics as $topic)
                    <div class="wl-topic-card"
                         onclick="openTopicModal(@js($topic->title), @js($topic->description), @js($topic->image), @js($topic->age_group), @js($topic->activities_count), @js($topic->card_bg))">
                        <div class="wl-topic-inner" style="background: {{ $topic->card_bg ?: '#FFF4CC' }}; border-color: {{ $topic->border_color ?: '#FDE08D' }};">
                            <img src="{{ $topic->image }}" alt="{{ $topic->title }}" class="wl-topic-img">
                            <h3 class="wl-topic-name">{{ $topic->title }}</h3>
                        </div>
                    </div>
                @endforeach
            </div>

            <img src="{{ $s['mascot_signpost'] ?? '/module-media/WonderLearn/theme/mascot-signpost.png' }}"
                 alt="Discover Learn Have Fun Signpost"
                 class="wl-mascot-signpost">
        </div>
    </section>

    <!-- =====================================================================
         5. MAGICAL STORIES SECTION
         ===================================================================== -->
    <section class="wl-stories-section" id="stories">
        <div class="wl-section-header-row">
            <h2 class="wl-section-title">
                <span>{{ $s['stories_heading'] ?? 'Magical Stories' }}</span>
            </h2>
            <button type="button" class="wl-btn-view-all" data-bs-toggle="modal" data-bs-target="#wlAllStoriesModal">
                <span>{{ $s['stories_btn_text'] ?? 'View All Stories' }}</span>
                <i class="bi bi-arrow-right"></i>
            </button>
        </div>

        <div class="wl-carousel-stage">
            <button type="button" class="wl-nav-arrow prev" onclick="cycleStories(-1)" aria-label="Previous Stories">
                <i class="bi bi-chevron-left"></i>
            </button>

            <div class="wl-stories-grid" id="wlStoriesGrid">
                @foreach($stories as $story)
                    <div class="wl-story-card"
                         onclick="openStoryReader(@js($story->title), @js($story->category_tag), @js($story->image), @js($story->story_content ?: $story->summary), @js($story->reading_time), @js($story->age_range), @js($story->tag_bg), @js($story->tag_color))">
                        <img src="{{ $story->image }}" alt="{{ $story->title }}" class="wl-story-thumb">
                        <div class="wl-story-body">
                            <h3 class="wl-story-title">{{ $story->title }}</h3>
                            <span class="wl-story-tag" style="background: {{ $story->tag_bg ?: '#E0F2FE' }}; color: {{ $story->tag_color ?: '#0369A1' }};">
                                {{ $story->category_tag }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>

            <button type="button" class="wl-nav-arrow next" onclick="cycleStories(1)" aria-label="Next Stories">
                <i class="bi bi-chevron-right"></i>
            </button>
        </div>
    </section>

    <!-- =====================================================================
         6. PLAY & LEARN GAMES SECTION
         ===================================================================== -->
    <section class="wl-games-section" id="games">
        <div class="wl-section-header-row">
            <h2 class="wl-section-title">
                <span class="wl-sparkle">✦</span>
                <span>{{ $s['games_heading'] ?? 'Play & Learn' }}</span>
                <span class="wl-sparkle">✦</span>
            </h2>
            <button type="button" class="wl-btn-view-all" onclick="openGameModal(0)">
                <span>{{ $s['games_btn_text'] ?? 'View All Games' }}</span>
                <i class="bi bi-arrow-right"></i>
            </button>
        </div>

        <div class="wl-games-grid">
            @foreach($games as $idx => $game)
                <div class="wl-game-card" onclick="openGameModal({{ $idx }})" title="Play {{ $game->title }}">
                    <img src="{{ $game->image }}" alt="{{ $game->title }}" class="wl-game-img">
                    <span class="wl-game-play-btn">
                        <i class="bi bi-play-fill"></i>
                    </span>
                </div>
            @endforeach
        </div>
    </section>

    <!-- =====================================================================
         7. FEATURED ADVENTURES BANNER CAROUSEL
         ===================================================================== -->
    <section class="wl-adventures-section">
        <div class="text-center">
            <h2 class="wl-section-title">
                <span class="wl-sparkle">✦</span>
                <span>{{ $s['adventures_heading'] ?? 'Featured Adventures' }}</span>
                <span class="wl-sparkle">✦</span>
            </h2>
        </div>

        @php
            $firstAdv = $adventures->first();
        @endphp

        <div class="wl-adventure-banner-wrap">
            <button type="button" class="wl-adv-arrow prev" onclick="switchAdventure(-1)" aria-label="Previous Adventure">
                <i class="bi bi-chevron-left"></i>
            </button>

            <div class="wl-adventure-card" id="wlAdventureCard"
                 style="background-image: url('{{ $firstAdv?->banner_image ?? '/module-media/WonderLearn/theme/adventure-island-banner.jpg' }}');">
                <div class="wl-adventure-content">
                    <h3 class="wl-adventure-title" id="wlAdvTitle">{{ $firstAdv?->title ?? 'Island of Imagination' }}</h3>
                    <p class="wl-adventure-desc" id="wlAdvDesc">
                        {{ $firstAdv?->description ?? 'Join Mia, Leo and Ava on a tiny adventure across the island of wonder, puzzles and new friends!' }}
                    </p>
                    <button type="button" class="wl-btn-adventure" onclick="openGameModal(0)">
                        <span id="wlAdvBtnText">{{ $firstAdv?->button_text ?? 'Start Adventure' }}</span>
                        <i class="bi bi-arrow-right"></i>
                    </button>
                </div>
            </div>

            <button type="button" class="wl-adv-arrow next" onclick="switchAdventure(1)" aria-label="Next Adventure">
                <i class="bi bi-chevron-right"></i>
            </button>

            <div class="wl-dots" id="wlAdvDots">
                @foreach($adventures as $idx => $adv)
                    <button type="button" class="wl-dot {{ $idx === 0 ? 'active' : '' }}" onclick="setAdventureIndex({{ $idx }})"></button>
                @endforeach
                @if($adventures->count() < 4)
                    <button type="button" class="wl-dot" onclick="setAdventureIndex(0)"></button>
                @endif
            </div>
        </div>
    </section>

    <!-- =====================================================================
         8. LOVED BY PARENTS TESTIMONIALS SECTION
         ===================================================================== -->
    <section class="wl-parents-section" id="about">
        <div class="text-center">
            <h2 class="wl-section-title">
                <span class="wl-sparkle">✦</span>
                <span>{{ $s['parents_heading'] ?? 'Loved by Parents' }}</span>
                <span class="wl-sparkle">✦</span>
            </h2>
        </div>

        <div class="wl-parents-stage">
            <img src="{{ $s['deco_butterfly'] ?? '/module-media/WonderLearn/theme/deco-butterfly.png' }}"
                 alt="Butterfly"
                 class="wl-butterfly-deco">

            <div class="wl-parents-grid">
                @foreach($testimonials as $testi)
                    <div class="wl-parent-card">
                        <img src="{{ $testi->avatar }}" alt="{{ $testi->parent_name }}" class="wl-parent-avatar">
                        <div class="flex-grow-1">
                            <p class="wl-parent-quote">“{{ $testi->quote }}”</p>
                            <div class="wl-parent-meta">
                                <span class="wl-parent-name">– {{ $testi->parent_name }}</span>
                                <span class="wl-stars">
                                    @for($i = 1; $i <= ($testi->rating ?: 5); $i++)
                                        ★
                                    @endfor
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- =====================================================================
         9. LEARNING ON THE GO! APP PROMO BANNER
         ===================================================================== -->
    <section class="wl-app-section">
        <div class="wl-app-inner">
            <img src="{{ $s['mascot_monster'] ?? '/module-media/WonderLearn/theme/mascot-monster-tablet.png' }}"
                 alt="WonderLearn Tablet Mascot"
                 class="wl-app-mascot-left">

            <div class="wl-app-center">
                <h2 class="wl-app-title">{{ $s['app_heading'] ?? 'Learning on the Go!' }}</h2>
                <p class="wl-app-sub">{{ $s['app_subtext'] ?? 'Download the WonderLearn app and access fun educational content anytime!' }}</p>
                <div class="wl-store-badges">
                    <a href="#join" data-bs-toggle="modal" data-bs-target="#wlJoinModal" class="wl-store-btn">
                        <i class="bi bi-apple"></i>
                        <div class="wl-store-text">
                            <small>Download on the</small>
                            <strong>App Store</strong>
                        </div>
                    </a>
                    <a href="#join" data-bs-toggle="modal" data-bs-target="#wlJoinModal" class="wl-store-btn">
                        <i class="bi bi-google-play text-info"></i>
                        <div class="wl-store-text">
                            <small>GET IT ON</small>
                            <strong>Google Play</strong>
                        </div>
                    </a>
                </div>
            </div>

            <img src="{{ $s['mascot_fox'] ?? '/module-media/WonderLearn/theme/mascot-fox-phone.png' }}"
                 alt="WonderLearn Fox Phone Mascot"
                 class="wl-app-mascot-right">
        </div>
    </section>

    <!-- =====================================================================
         10. EMERALD GREEN CURVED HILL FOOTER
         ===================================================================== -->
    <svg class="wl-footer-wave" viewBox="0 0 1200 44" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M0,36 C260,-8 940,-8 1200,36 L1200,44 L0,44 Z" fill="{{ $activeTheme->secondary_color ?? '#14884B' }}"/>
    </svg>

    <footer class="wl-footer">
        <div class="wl-footer-grid">
            <!-- Col 1: Brand -->
            <div>
                <div class="d-flex align-items-center gap-2">
                    <img src="{{ $s['logo_icon'] ?? '/module-media/WonderLearn/theme/logo-book-icon.png' }}"
                         alt="WonderLearn"
                         style="width: 38px; height: 32px; object-fit: contain; background: #FFFFFF; padding: 3px; border-radius: 8px;">
                    <div>
                        <div class="wl-footer-brand-title">{{ $s['brand_name'] ?? 'WonderLearn' }}</div>
                        <span class="wl-footer-brand-sub">{{ $s['brand_tagline'] ?? 'Learn • Discover • Grow' }}</span>
                    </div>
                </div>
                <p class="wl-footer-tagline">{{ $s['footer_tagline'] ?? 'Making learning magical for every child.' }}</p>
            </div>

            <!-- Col 2: Quick Links -->
            <div class="wl-footer-col">
                <h5>Quick Links</h5>
                <ul class="wl-footer-links">
                    <li><a href="#home">Home</a></li>
                    <li><a href="#explore">Explore</a></li>
                    <li><a href="#stories">Stories</a></li>
                    <li><a href="#games">Games</a></li>
                </ul>
            </div>

            <!-- Col 3: For Parents -->
            <div class="wl-footer-col">
                <h5>For Parents</h5>
                <ul class="wl-footer-links">
                    <li><a href="#about" data-bs-toggle="modal" data-bs-target="#wlSafetyModal">Safety</a></li>
                    <li><a href="#about" data-bs-toggle="modal" data-bs-target="#wlSafetyModal">Privacy</a></li>
                    <li><a href="#about" data-bs-toggle="modal" data-bs-target="#wlSafetyModal">FAQ</a></li>
                    <li><a href="#join" data-bs-toggle="modal" data-bs-target="#wlJoinModal">Contact Us</a></li>
                </ul>
            </div>

            <!-- Col 4: Support -->
            <div class="wl-footer-col">
                <h5>Support</h5>
                <ul class="wl-footer-links">
                    <li><a href="#about">About Us</a></li>
                    <li><a href="#stories">Blog</a></li>
                    <li><a href="#about" data-bs-toggle="modal" data-bs-target="#wlSafetyModal">Privacy Policy</a></li>
                    <li><a href="#about" data-bs-toggle="modal" data-bs-target="#wlSafetyModal">Terms of Use</a></li>
                </ul>
            </div>

            <!-- Col 5: Stay Connected Newsletter -->
            <div class="wl-footer-col">
                <h5>{{ $s['newsletter_title'] ?? 'Stay Connected' }}</h5>
                <p class="mb-2" style="font-size: 12px; color: #E2F8EC; font-weight: 600;">
                    {{ $s['newsletter_subtitle'] ?? 'Get the latest updates and more.' }}
                </p>

                <form action="{{ route('wonderlearn.newsletter.submit') }}" method="POST" class="wl-newsletter-form">
                    @csrf
                    <input type="email" name="email" required placeholder="Enter your email">
                    <button type="submit">Subscribe</button>
                </form>

                <div class="wl-social-row">
                    <a href="#home" class="wl-social-btn" style="background: #1D4ED8;" aria-label="Facebook">
                        <i class="bi bi-facebook"></i>
                    </a>
                    <a href="#home" class="wl-social-btn" style="background: #DC2626;" aria-label="YouTube">
                        <i class="bi bi-youtube"></i>
                    </a>
                    <a href="#home" class="wl-social-btn" style="background: linear-gradient(45deg, #F59E0B, #EC4899, #8B5CF6);" aria-label="Instagram">
                        <i class="bi bi-instagram"></i>
                    </a>
                    <a href="#home" class="wl-social-btn" style="background: #0284C7;" aria-label="LinkedIn">
                        <i class="bi bi-linkedin"></i>
                    </a>
                </div>
            </div>
        </div>
    </footer>
</div>

<!-- =========================================================================
     INTERACTIVE MODALS (Join Now, Log In, Search, Topic Explorer, Story Reader, Mini-Game)
     ========================================================================= -->

<!-- 1. JOIN NOW / KID EXPLORER ENROLLMENT MODAL -->
<div class="modal fade" id="wlJoinModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
            <div class="modal-header border-0 text-white" style="background: linear-gradient(135deg, #1554B4 0%, #16A34A 100%);">
                <h5 class="modal-title wl-display fw-bold">
                    <i class="bi bi-stars text-warning me-2"></i> Join WonderLearn Free!
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('wonderlearn.join.submit') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <p class="text-muted small fw-semibold mb-3">
                        Create your child's safe, ad-free learning explorer pass for ages 4 to 12.
                    </p>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Parent's Name *</label>
                            <input type="text" name="parent_name" class="form-control rounded-3" required placeholder="e.g. Sarah Mitchell">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Child's First Name *</label>
                            <input type="text" name="child_name" class="form-control rounded-3" required placeholder="e.g. Leo">
                        </div>
                        <div class="col-md-5">
                            <label class="form-label small fw-bold">Child's Age (4–12) *</label>
                            <input type="number" name="child_age" min="3" max="14" value="7" class="form-control rounded-3" required>
                        </div>
                        <div class="col-md-7">
                            <label class="form-label small fw-bold">Favorite Topic</label>
                            <select name="favorite_topic" class="form-select rounded-3">
                                @foreach($topics as $tp)
                                    <option value="{{ $tp->title }}">{{ $tp->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-7">
                            <label class="form-label small fw-bold">Parent Email *</label>
                            <input type="email" name="email" class="form-control rounded-3" required placeholder="parent@example.com">
                        </div>
                        <div class="col-md-5">
                            <label class="form-label small fw-bold">Phone (Optional)</label>
                            <input type="text" name="phone" class="form-control rounded-3" placeholder="+1 555-0199">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light">
                    <button type="button" class="btn btn-light rounded-pill px-3 fw-bold" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="wl-btn-join border-0">
                        <i class="bi bi-rocket-takeoff-fill"></i> Start Magical Journey
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 2. LOG IN MODAL -->
<div class="modal fade" id="wlLoginModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title wl-display fw-bold text-primary">
                    <i class="bi bi-person-circle me-1"></i> Explorer Log In
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label small fw-bold">Parent Email</label>
                    <input type="email" id="wlLoginEmail" class="form-control rounded-3" placeholder="parent@example.com">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Explorer PIN / Password</label>
                    <input type="password" class="form-control rounded-3" value="123456">
                </div>
                <button type="button" class="wl-btn-login w-100 justify-content-center py-2"
                        onclick="bootstrap.Modal.getInstance(document.getElementById('wlLoginModal')).hide(); openGameModal(0);">
                    Log In & Play Now
                </button>
                <div class="text-center mt-3">
                    <a href="{{ route('admin.wonderlearn.dashboard') }}" class="small text-decoration-none fw-bold text-secondary">
                        <i class="bi bi-gear-fill me-1"></i> Educator / Admin Panel
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 3. SEARCH MODAL -->
<div class="modal fade" id="wlSearchModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-0">
                <h5 class="modal-title wl-display fw-bold text-primary">
                    <i class="bi bi-search me-2"></i> Search WonderLearn
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 pt-0">
                <input type="text" id="wlSearchInput" class="form-control form-control-lg rounded-pill mb-3"
                       placeholder="Type a story, topic, or game (e.g. Math, Luna, Sailor)..."
                       oninput="filterSearchItems(this.value)">
                <div id="wlSearchResults" class="d-flex flex-column gap-2" style="max-height: 260px; overflow-y: auto;">
                    @foreach($stories as $st)
                        <div class="p-2 border rounded-3 d-flex align-items-center gap-3 wl-search-row" style="cursor:pointer;"
                             data-keywords="{{ strtolower($st->title . ' ' . $st->category_tag) }}"
                             onclick="bootstrap.Modal.getInstance(document.getElementById('wlSearchModal')).hide(); openStoryReader(@js($st->title), @js($st->category_tag), @js($st->image), @js($st->story_content ?: $st->summary), @js($st->reading_time), @js($st->age_range), @js($st->tag_bg), @js($st->tag_color));">
                            <img src="{{ $st->image }}" alt="{{ $st->title }}" style="width: 54px; height: 38px; object-fit: cover; border-radius: 8px;">
                            <div>
                                <div class="fw-bold small text-dark">{{ $st->title }}</div>
                                <span class="badge bg-info-subtle text-info-emphasis rounded-pill" style="font-size: 10px;">Story • {{ $st->category_tag }}</span>
                            </div>
                        </div>
                    @endforeach
                    @foreach($games as $gIdx => $gm)
                        <div class="p-2 border rounded-3 d-flex align-items-center gap-3 wl-search-row" style="cursor:pointer;"
                             data-keywords="{{ strtolower($gm->title . ' ' . $gm->category) }}"
                             onclick="bootstrap.Modal.getInstance(document.getElementById('wlSearchModal')).hide(); openGameModal({{ $gIdx }});">
                            <img src="{{ $gm->image }}" alt="{{ $gm->title }}" style="width: 54px; height: 38px; object-fit: cover; border-radius: 8px;">
                            <div>
                                <div class="fw-bold small text-dark">{{ $gm->title }}</div>
                                <span class="badge bg-success-subtle text-success-emphasis rounded-pill" style="font-size: 10px;">Game • {{ $gm->category }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 4. TOPIC EXPLORER MODAL -->
<div class="modal fade" id="wlTopicModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
            <div class="modal-header border-0" id="wlTopicModalHeader" style="background: #FFF4CC;">
                <div class="d-flex align-items-center gap-3">
                    <img id="wlTopicModalImg" src="" alt="" style="width: 56px; height: 56px; object-fit: contain;">
                    <div>
                        <h4 class="modal-title wl-display fw-bold mb-0" id="wlTopicModalTitle">Math Magic</h4>
                        <span class="badge bg-white text-dark border rounded-pill" id="wlTopicModalMeta">Ages 4-12 • 42 Activities</span>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <p class="fw-semibold text-secondary mb-3" id="wlTopicModalDesc"></p>
                <div class="p-3 rounded-4 bg-light border d-flex align-items-center justify-content-between">
                    <div>
                        <div class="fw-bold text-dark">Interactive Learning Quest Ready!</div>
                        <small class="text-muted">Earn golden stars by completing fun challenges.</small>
                    </div>
                    <button type="button" class="wl-btn-explore"
                            onclick="bootstrap.Modal.getInstance(document.getElementById('wlTopicModal')).hide(); openGameModal(0);">
                        <span>Play Topic Game</span>
                        <i class="bi bi-play-fill"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 5. STORY READER MODAL (WITH READ-ALOUD VOICE) -->
<div class="modal fade" id="wlStoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
            <div class="modal-header border-0 bg-primary text-white">
                <div>
                    <span class="badge bg-warning text-dark rounded-pill me-2" id="wlStoryModalTag">Friendly</span>
                    <small class="text-white-50 fw-bold" id="wlStoryModalMeta">5 min read • Ages 4-10</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" onclick="stopStorySpeech()"></button>
            </div>
            <div class="modal-body p-0">
                <img id="wlStoryModalImg" src="" alt="" style="width: 100%; max-height: 290px; object-fit: cover;">
                <div class="p-4">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                        <h3 class="wl-display fw-bold text-primary mb-0" id="wlStoryModalTitle">The Starry Balloon</h3>
                        <button type="button" class="btn btn-sm btn-warning fw-bold rounded-pill px-3" onclick="readStoryAloud()">
                            <i class="bi bi-volume-up-fill me-1"></i> Read Aloud to Me
                        </button>
                    </div>
                    <div id="wlStoryModalContent" class="fs-6 fw-semibold text-dark" style="line-height: 1.75; white-space: pre-line;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 6. VIEW ALL STORIES MODAL -->
<div class="modal fade" id="wlAllStoriesModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-0">
                <h4 class="modal-title wl-display fw-bold text-primary">
                    <i class="bi bi-book-half me-2"></i> All Magical Stories ({{ $stories->count() }})
                </h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 pt-0">
                <div class="row g-3">
                    @foreach($stories as $story)
                        <div class="col-md-6">
                            <div class="p-3 border rounded-4 d-flex gap-3 align-items-center h-100" style="cursor:pointer;"
                                 onclick="bootstrap.Modal.getInstance(document.getElementById('wlAllStoriesModal')).hide(); openStoryReader(@js($story->title), @js($story->category_tag), @js($story->image), @js($story->story_content ?: $story->summary), @js($story->reading_time), @js($story->age_range), @js($story->tag_bg), @js($story->tag_color));">
                                <img src="{{ $story->image }}" alt="{{ $story->title }}" style="width: 96px; height: 70px; object-fit: cover; border-radius: 12px;">
                                <div>
                                    <h6 class="wl-display fw-bold mb-1 text-dark">{{ $story->title }}</h6>
                                    <span class="badge rounded-pill" style="background: {{ $story->tag_bg }}; color: {{ $story->tag_color }};">
                                        {{ $story->category_tag }}
                                    </span>
                                    <div class="small text-muted mt-1">{{ $story->reading_time }} • {{ $story->age_range }}</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 7. PLAY & LEARN INTERACTIVE MINI-GAME MODAL -->
<div class="modal fade" id="wlGameModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
            <div class="modal-header border-0 text-white" style="background: linear-gradient(135deg, #1D63C8 0%, #16A34A 100%);">
                <h5 class="modal-title wl-display fw-bold" id="wlGameModalTitle">
                    <i class="bi bi-controller me-2"></i> Play & Learn Challenge
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <img id="wlGameModalImg" src="" alt="" class="rounded-4 shadow-sm mb-3" style="max-width: 240px; width: 100%; height: auto;">
                <div class="badge bg-info-subtle text-primary rounded-pill px-3 py-1 mb-2 fw-bold" id="wlGameModalCategory">Math Magic</div>
                <h5 class="fw-bold text-dark mb-3" id="wlGameModalPrompt">What is 5 - 2?</h5>
                <div id="wlGameOptionsBox" class="d-grid gap-2" style="grid-template-columns: repeat(2, 1fr);"></div>
                <div id="wlGameFeedback" class="mt-3 fw-bold fs-6 d-none p-2 rounded-3"></div>
            </div>
            <div class="modal-footer border-0 bg-light justify-content-between">
                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill fw-bold" onclick="nextMiniGame(-1)">
                    <i class="bi bi-arrow-left"></i> Prev Game
                </button>
                <button type="button" class="btn btn-sm btn-primary rounded-pill fw-bold" onclick="nextMiniGame(1)">
                    Next Game <i class="bi bi-arrow-right"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- 8. CHILD SAFETY & FAQ MODAL -->
<div class="modal fade" id="wlSafetyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-0 bg-success text-white">
                <h5 class="modal-title wl-display fw-bold">
                    <i class="bi bi-shield-fill-check me-2"></i> WonderLearn Safety & Parent FAQ
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                @foreach($pages as $pg)
                    <div class="mb-3">
                        <h6 class="fw-bold text-success mb-1">{{ $pg->title }}</h6>
                        <p class="small text-secondary mb-0">{{ $pg->content }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // 1. Topic Explorer Modal
    function openTopicModal(title, desc, img, age, count, bg) {
        document.getElementById('wlTopicModalTitle').textContent = title;
        document.getElementById('wlTopicModalDesc').textContent = desc || 'Explore interactive lessons, stories, and games curated for curious minds!';
        document.getElementById('wlTopicModalImg').src = img;
        document.getElementById('wlTopicModalMeta').textContent = (age || 'Ages 4-12') + ' • ' + (count || 25) + ' Activities';
        document.getElementById('wlTopicModalHeader').style.background = bg || '#FFF4CC';
        new bootstrap.Modal(document.getElementById('wlTopicModal')).show();
    }

    // 2. Story Reader Modal + Web Speech Read-Aloud
    let currentSpeechText = '';
    function openStoryReader(title, tag, img, content, time, age, tagBg, tagColor) {
        stopStorySpeech();
        document.getElementById('wlStoryModalTitle').textContent = title;
        const tagEl = document.getElementById('wlStoryModalTag');
        tagEl.textContent = tag;
        tagEl.style.background = tagBg || '#E0F2FE';
        tagEl.style.color = tagColor || '#0369A1';
        document.getElementById('wlStoryModalMeta').textContent = (time || '5 min read') + ' • ' + (age || 'Ages 4-10');
        document.getElementById('wlStoryModalImg').src = img;
        document.getElementById('wlStoryModalContent').textContent = content;
        currentSpeechText = title + '. ' + content;
        new bootstrap.Modal(document.getElementById('wlStoryModal')).show();
    }

    function readStoryAloud() {
        if (!('speechSynthesis' in window)) return;
        window.speechSynthesis.cancel();
        const utter = new SpeechSynthesisUtterance(currentSpeechText);
        utter.rate = 0.95;
        utter.pitch = 1.1;
        window.speechSynthesis.speak(utter);
    }

    function stopStorySpeech() {
        if ('speechSynthesis' in window) {
            window.speechSynthesis.cancel();
        }
    }

    // 3. Cycle Stories Carousel Cards
    function cycleStories(dir) {
        const grid = document.getElementById('wlStoriesGrid');
        if (!grid || grid.children.length < 2) return;
        if (dir > 0) {
            grid.appendChild(grid.firstElementChild);
        } else {
            grid.insertBefore(grid.lastElementChild, grid.firstElementChild);
        }
    }

    // 4. Play & Learn Mini-Games
    const wlGamesData = @json($games->values());
    let activeGameIdx = 0;

    function openGameModal(idx) {
        if (!wlGamesData.length) return;
        activeGameIdx = (idx + wlGamesData.length) % wlGamesData.length;
        renderMiniGame();
        const modalEl = document.getElementById('wlGameModal');
        bootstrap.Modal.getOrCreateInstance(modalEl).show();
    }

    function nextMiniGame(step) {
        if (!wlGamesData.length) return;
        activeGameIdx = (activeGameIdx + step + wlGamesData.length) % wlGamesData.length;
        renderMiniGame();
    }

    function renderMiniGame() {
        const g = wlGamesData[activeGameIdx];
        document.getElementById('wlGameModalTitle').textContent = g.title;
        document.getElementById('wlGameModalImg').src = g.image;
        document.getElementById('wlGameModalCategory').textContent = g.category || 'Play & Learn';
        document.getElementById('wlGameModalPrompt').textContent = g.question_prompt || 'Pick the correct answer!';

        const fb = document.getElementById('wlGameFeedback');
        fb.className = 'mt-3 fw-bold fs-6 d-none p-2 rounded-3';

        const box = document.getElementById('wlGameOptionsBox');
        box.innerHTML = '';
        const opts = Array.isArray(g.options_json) && g.options_json.length ? g.options_json : ['2', '3', '4', '5'];

        opts.forEach(opt => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'btn btn-outline-primary fw-bold rounded-pill py-2';
            btn.textContent = opt;
            btn.onclick = () => {
                if (String(opt).trim().toLowerCase() === String(g.correct_answer).trim().toLowerCase()) {
                    btn.className = 'btn btn-success fw-bold rounded-pill py-2';
                    fb.textContent = '🎉 Super Job! "' + opt + '" is the right answer! You earned a Golden Star ⭐';
                    fb.className = 'mt-3 fw-bold fs-6 p-2 rounded-3 bg-success-subtle text-success';
                } else {
                    btn.className = 'btn btn-warning fw-bold rounded-pill py-2';
                    fb.textContent = '✨ Almost there! Try another choice!';
                    fb.className = 'mt-3 fw-bold fs-6 p-2 rounded-3 bg-warning-subtle text-dark';
                }
            };
            box.appendChild(btn);
        });
    }

    // 5. Featured Adventures Banner Slider
    const wlAdventuresData = @json($adventures->values());
    let activeAdvIdx = 0;

    function switchAdventure(step) {
        if (!wlAdventuresData.length) return;
        setAdventureIndex((activeAdvIdx + step + wlAdventuresData.length) % wlAdventuresData.length);
    }

    function setAdventureIndex(idx) {
        if (!wlAdventuresData.length) return;
        activeAdvIdx = idx % wlAdventuresData.length;
        const adv = wlAdventuresData[activeAdvIdx];
        document.getElementById('wlAdvTitle').textContent = adv.title;
        document.getElementById('wlAdvDesc').textContent = adv.description;
        document.getElementById('wlAdvBtnText').textContent = adv.button_text || 'Start Adventure';
        if (adv.banner_image) {
            document.getElementById('wlAdventureCard').style.backgroundImage = "url('" + adv.banner_image + "')";
        }

        const dots = document.querySelectorAll('#wlAdvDots .wl-dot');
        dots.forEach((d, i) => d.classList.toggle('active', i === activeAdvIdx));
    }

    // 6. Live Search Filter
    function filterSearchItems(q) {
        const term = (q || '').trim().toLowerCase();
        document.querySelectorAll('.wl-search-row').forEach(row => {
            const kw = row.getAttribute('data-keywords') || '';
            row.style.display = (!term || kw.includes(term)) ? 'flex' : 'none';
        });
    }
</script>
@endpush
