@extends('portfolio::frontend.layouts.master')

@section('portfolio_content')
@php
    $vis = $t['visibility'] ?? [];
@endphp

<!-- ===================================================================
     02. HERO / BANNER SECTION (EXACT REFERENCE MATCH + USER CUTOUT PHOTO)
     =================================================================== -->
@if(($vis['hero'] ?? '1') === '1')
<section class="pf-hero" id="home">
    <canvas id="pfHero3dCanvas" class="pf-hero-3d-canvas"></canvas>
    <div class="pf-hero-dots"></div>
    <div class="pf-container">
        <div class="pf-hero-grid">
            <!-- Left Hero Content -->
            <div class="pf-hero-content-3d">
                <div class="pf-hero-badge">
                    <span>{{ $t['hero']['badge'] ?? "Hello, I'm a Professional" }}</span>
                    <span class="pf-badge-dot"></span>
                </div>

                <h1 class="pf-hero-title">
                    {{ $t['hero']['title_line_1'] ?? 'Web Developer' }}<br>
                    {{ $t['hero']['title_line_2'] ?? 'Crafting Premium' }}<br>
                    {{ $t['hero']['title_line_3'] ?? 'Digital' }} <span class="pf-highlight">{{ $t['hero']['title_highlight'] ?? 'Experiences' }}</span>
                </h1>

                <p class="pf-hero-desc">
                    {{ $t['hero']['description'] ?? 'I build modern, fast and scalable web applications that help businesses grow and make an impact.' }}
                </p>

                <div class="pf-hero-btns">
                    <a href="{{ $t['hero']['btn_work_url'] ?? '#portfolio' }}" class="pf-btn-primary pf-magnetic">
                        <span>{{ $t['hero']['btn_work_text'] ?? 'View My Work' }}</span>
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                    <a href="{{ $t['hero']['btn_cv_url'] ?? '/module-media/Portfolio/cv/Alex-Morgan-CV.pdf' }}" download class="pf-btn-outline pf-magnetic">
                        <span>{{ $t['hero']['btn_cv_text'] ?? 'Download CV' }}</span>
                        <i class="fa-solid fa-download"></i>
                    </a>
                </div>

                <!-- Social Media Links -->
                <div class="pf-social-row">
                    <a href="{{ $t['socials']['facebook'] ?? '#' }}" target="_blank" rel="noopener" class="pf-social-icon pf-magnetic" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="{{ $t['socials']['github'] ?? '#' }}" target="_blank" rel="noopener" class="pf-social-icon pf-magnetic" aria-label="GitHub"><i class="fa-brands fa-github"></i></a>
                    <a href="{{ $t['socials']['linkedin'] ?? '#' }}" target="_blank" rel="noopener" class="pf-social-icon pf-magnetic" aria-label="LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
                    <a href="{{ $t['socials']['youtube'] ?? '#' }}" target="_blank" rel="noopener" class="pf-social-icon pf-magnetic" aria-label="YouTube"><i class="fa-brands fa-youtube"></i></a>
                    <a href="{{ $t['socials']['twitter'] ?? '#' }}" target="_blank" rel="noopener" class="pf-social-icon pf-magnetic" aria-label="X"><i class="fa-brands fa-x-twitter"></i></a>
                </div>
            </div>

            <!-- Right Hero Visual: 3D Parallax Stage + Glowing Electric Blue Orb + Orbital Rings + Cutout Portrait -->
            <div class="pf-hero-visual" id="pfHero3dStage">
                <div class="pf-hero-code-deco" data-depth="45">&lt;/&gt;</div>
                <div class="pf-hero-orb" data-depth="-25"></div>
                <div class="pf-hero-ring pf-hero-ring-1" data-depth="-15"></div>
                <div class="pf-hero-ring pf-hero-ring-2" data-depth="20"></div>

                <!-- Floating 3D Tech Chips -->
                <div class="pf-float-chip pf-float-chip-1" data-depth="55">
                    <i class="fa-brands fa-laravel" style="color: #FF2D20;"></i>
                    <span>Laravel 12</span>
                </div>
                <div class="pf-float-chip pf-float-chip-2" data-depth="65">
                    <i class="fa-brands fa-react" style="color: #35B6FF;"></i>
                    <span>Next.js & React</span>
                </div>

                <div class="pf-hero-portrait-wrap" data-depth="30">
                    <img src="{{ $t['hero']['portrait_image'] ?? '/module-media/Portfolio/theme/hero-developer-cutout.png' }}"
                         alt="{{ $t['header']['brand_name'] ?? 'Alex Morgan' }}"
                         class="pf-hero-portrait-img">
                </div>

                <div class="pf-hero-script" data-depth="70">
                    <span>{{ $t['hero']['script_line_1'] ?? 'Code' }}</span>
                    <span>{{ $t['hero']['script_line_2'] ?? 'Innovate' }}</span>
                    <span>{{ $t['hero']['script_line_3'] ?? 'Create' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Organic Flowing Wave Transition to About Section -->
    <div class="pf-wave-bottom">
        <svg viewBox="0 0 1440 75" preserveAspectRatio="none">
            <path d="M0,42 C320,82 580,-10 920,38 C1160,72 1320,20 1440,8 L1440,75 L0,75 Z" fill="rgba(7, 139, 255, 0.35)"></path>
            <path d="M0,56 C380,88 680,6 1040,48 C1240,70 1360,36 1440,22 L1440,75 L0,75 Z" fill="var(--pf-wave-light-fill)"></path>
        </svg>
    </div>
</section>
@endif

<!-- ===================================================================
     03. ABOUT ME SECTION (3-COLUMN REFERENCE LAYOUT)
     =================================================================== -->
@if(($vis['about'] ?? '1') === '1')
<section class="pf-about" id="about">
    <div class="pf-container">
        <div class="pf-about-grid">
            <!-- Left Column: Color-Corrected Developer Workspace Image -->
            <div class="pf-about-photo-card">
                <img src="{{ $t['about']['workspace_image'] ?? '/module-media/Portfolio/theme/about-workspace.jpg' }}"
                     alt="Developer Workspace">
            </div>

            <!-- Center Column: Bio & 3-Column Stats -->
            <div>
                <div class="pf-sec-tag">{{ $t['about']['tag'] ?? 'ABOUT ME' }}</div>
                <h2 class="pf-sec-heading">
                    {{ $t['about']['heading_line_1'] ?? 'Code is my medium.' }}<br>
                    {{ $t['about']['heading_line_2'] ?? 'Empathy is my' }} <span class="pf-italic-blue">{{ $t['about']['heading_highlight'] ?? 'superpower.' }}</span>
                </h2>
                <p class="pf-about-bio">
                    {{ $t['about']['biography'] ?? "I'm a passionate web developer with 3+ years of experience building modern web applications using Laravel, PHP, and more. I love turning ideas into reality with clean code and great UX." }}
                </p>

                <div class="pf-about-stats">
                    <div class="pf-about-stat-item">
                        <div class="pf-about-stat-num">{{ $t['about']['stat_1_number'] ?? '3+' }}</div>
                        <div class="pf-about-stat-label">{{ $t['about']['stat_1_label'] ?? 'Years Experience' }}</div>
                    </div>
                    <div class="pf-about-stat-item">
                        <div class="pf-about-stat-num">{{ $t['about']['stat_2_number'] ?? '50+' }}</div>
                        <div class="pf-about-stat-label">{{ $t['about']['stat_2_label'] ?? 'Projects Delivered' }}</div>
                    </div>
                    <div class="pf-about-stat-item">
                        <div class="pf-about-stat-num">{{ $t['about']['stat_3_number'] ?? '100%' }}</div>
                        <div class="pf-about-stat-label">{{ $t['about']['stat_3_label'] ?? 'Client Satisfaction' }}</div>
                    </div>
                </div>
            </div>

            <!-- Right Column: 4 Core Strengths & Highlights -->
            <div class="pf-about-highlights">
                @foreach(($t['about']['highlights'] ?? []) as $hl)
                    <div class="pf-highlight-item">
                        <div class="pf-highlight-icon">
                            <i class="{{ $hl['icon'] ?? 'fa-solid fa-bolt' }}"></i>
                        </div>
                        <div>
                            <div class="pf-highlight-title">{{ $hl['title'] ?? '' }}</div>
                            <div class="pf-highlight-sub">{{ $hl['sub'] ?? '' }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Wave Transition to Dark Skills Section -->
    <div class="pf-wave-bottom">
        <svg viewBox="0 0 1440 65" preserveAspectRatio="none">
            <path d="M0,18 C280,68 620,-12 980,38 C1220,66 1350,30 1440,14 L1440,65 L0,65 Z" fill="rgba(7, 139, 255, 0.28)"></path>
            <path d="M0,34 C340,78 680,4 1040,46 C1260,68 1370,40 1440,26 L1440,65 L0,65 Z" fill="var(--pf-wave-dark-fill)"></path>
        </svg>
    </div>
</section>
@endif

<!-- ===================================================================
     04. SKILLS & TECHNOLOGIES SECTION (EXACT REFERENCE MATCH + CATEGORY FILTER)
     =================================================================== -->
@if(($vis['skills'] ?? '1') === '1')
<section class="pf-skills" id="skills">
    <div class="pf-container">
        <div class="pf-split-layout">
            <!-- Left Intro + Category Filter -->
            <div class="pf-side-intro">
                <div class="pf-sec-tag">{{ $t['skills_section']['tag'] ?? 'MY SKILLS' }}</div>
                <h2 class="pf-sec-heading">
                    {{ $t['skills_section']['heading_line_1'] ?? 'Expertise with' }}<br>
                    <span class="pf-italic-blue">{{ $t['skills_section']['heading_highlight'] ?? 'Passion.' }}</span>
                </h2>
                <p>{{ $t['skills_section']['description'] ?? 'I work with modern technologies to build innovative solutions.' }}</p>

                <!-- Interactive Category Filter Chips -->
                <div class="pf-skill-filter-bar">
                    <button type="button" class="pf-filter-chip active" data-skill-filter="all">All ({{ $skills->count() }})</button>
                    @foreach($skillCategories as $sCat)
                        <button type="button" class="pf-filter-chip" data-skill-filter="{{ $sCat }}">{{ $sCat }}</button>
                    @endforeach
                </div>

                <button type="button" class="pf-btn-outline" style="height: 38px; padding: 0 18px; font-size: 12.5px;" onclick="document.querySelectorAll('.pf-extra-skill').forEach(el => el.style.display = 'flex'); this.style.display='none';">
                    <span>{{ $t['skills_section']['btn_text'] ?? 'Explore All Skills' }} ({{ $skills->count() }})</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </button>
            </div>

            <!-- Right 4-Column Skills Grid (First 8 shown like reference, rest expandable/filterable) -->
            <div class="pf-skills-grid">
                @foreach($skills as $idx => $skill)
                    <div class="pf-skill-card {{ $idx >= 8 ? 'pf-extra-skill' : '' }}"
                         data-skill-cat="{{ $skill->category }}"
                         style="{{ $idx >= 8 ? 'display: none;' : '' }}">
                        <div class="pf-skill-icon" style="color: {{ $skill->icon_color ?: '#078BFF' }};">
                            <i class="{{ $skill->icon_class ?: 'fa-solid fa-code' }}"></i>
                        </div>
                        <div class="pf-skill-info" style="flex: 1;">
                            <div style="display: flex; align-items: center; justify-content: space-between;">
                                <h5>{{ $skill->name }}</h5>
                                <small style="font-size: 11px; color: var(--pf-light-blue); font-weight: 700;">{{ $skill->level_percent }}%</small>
                            </div>
                            <span>{{ $skill->subtitle ?: $skill->category }}</span>
                            <div class="pf-skill-progress-track">
                                <div class="pf-skill-progress-fill" data-progress="{{ $skill->level_percent }}"></div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="pf-wave-bottom">
        <svg viewBox="0 0 1440 65" preserveAspectRatio="none">
            <path d="M0,38 C360,-8 720,74 1120,24 C1290,6 1380,28 1440,40 L1440,65 L0,65 Z" fill="rgba(7, 139, 255, 0.32)"></path>
            <path d="M0,48 C380,6 740,80 1140,34 C1310,16 1390,36 1440,48 L1440,65 L0,65 Z" fill="var(--pf-wave-light-fill)"></path>
        </svg>
    </div>
</section>
@endif

<!-- ===================================================================
     05. SERVICES SECTION (5-COLUMN REFERENCE LAYOUT + EXPANDABLE ALL 10)
     =================================================================== -->
@if(($vis['services'] ?? '1') === '1')
<section class="pf-services" id="services">
    <div class="pf-container">
        <div class="pf-split-layout">
            <!-- Left Intro -->
            <div class="pf-side-intro">
                <div class="pf-sec-tag">{{ $t['services_section']['tag'] ?? 'MY SERVICES' }}</div>
                <h2 class="pf-sec-heading">{{ $t['services_section']['heading'] ?? 'What I Do Best' }}</h2>
                <p>{{ $t['services_section']['description'] ?? 'I offer a wide range of web development services to meet your business needs.' }}</p>

                <button type="button" class="pf-btn-primary" style="height: 38px; padding: 0 18px; font-size: 12.5px;" onclick="document.querySelectorAll('.pf-extra-service').forEach(el => el.style.display = 'flex'); this.style.display='none';">
                    <span>{{ $t['services_section']['btn_text'] ?? 'View All Services' }}</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </button>
            </div>

            <!-- Right 5-Column Services Grid -->
            <div class="pf-services-grid">
                @foreach($services as $sIdx => $srv)
                    <a href="{{ route('portfolio.service.show', $srv->slug) }}"
                       class="pf-service-card {{ $sIdx >= 5 ? 'pf-extra-service' : '' }}"
                       style="{{ $sIdx >= 5 ? 'display: none;' : '' }}">
                        <div class="pf-service-icon">
                            <i class="{{ $srv->icon_class ?: 'fa-solid fa-code' }}"></i>
                        </div>
                        <h5>{{ $srv->title }}</h5>
                        <p>{{ $srv->subtitle ?: $srv->short_description }}</p>
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <div class="pf-wave-bottom">
        <svg viewBox="0 0 1440 65" preserveAspectRatio="none">
            <path d="M0,22 C340,74 720,-8 1080,42 C1280,68 1380,32 1440,18 L1440,65 L0,65 Z" fill="rgba(7, 139, 255, 0.28)"></path>
            <path d="M0,36 C360,82 740,6 1100,50 C1290,72 1390,42 1440,28 L1440,65 L0,65 Z" fill="var(--pf-wave-dark-fill)"></path>
        </svg>
    </div>
</section>
@endif

<!-- ===================================================================
     06. PORTFOLIO / FEATURED PROJECTS SECTION (EXACT REFERENCE MATCH + FILTER)
     =================================================================== -->
@if(($vis['portfolio'] ?? '1') === '1')
<section class="pf-portfolio" id="portfolio">
    <div class="pf-container">
        <div class="pf-split-layout">
            <!-- Left Intro + Category Filter + View All Link -->
            <div class="pf-side-intro">
                <div class="pf-sec-tag">{{ $t['portfolio_section']['tag'] ?? 'MY PORTFOLIO' }}</div>
                <h2 class="pf-sec-heading">{!! nl2br(e($t['portfolio_section']['heading'] ?? "My Recent\nWorks")) !!}</h2>
                <p>{{ $t['portfolio_section']['description'] ?? 'A collection of some of my best projects. Feel free to explore.' }}</p>

                <!-- Project Category Filter Chips -->
                <div class="pf-skill-filter-bar">
                    <button type="button" class="pf-filter-chip active" data-proj-filter="all">All</button>
                    @foreach($projectCategories->take(4) as $pCat)
                        <button type="button" class="pf-filter-chip" data-proj-filter="{{ $pCat }}">{{ str_replace(' Website', '', $pCat) }}</button>
                    @endforeach
                </div>

                <a href="{{ route('portfolio.projects') }}" class="pf-btn-outline" style="height: 38px; padding: 0 18px; font-size: 12.5px;">
                    <span>{{ $t['portfolio_section']['btn_text'] ?? 'View All Projects' }}</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>

            <!-- Right 4-Column Projects Grid -->
            <div class="pf-projects-grid">
                @foreach($projects->take(8) as $pIdx => $proj)
                    <a href="{{ route('portfolio.project.show', $proj->slug) }}"
                       class="pf-project-card"
                       data-proj-cat="{{ $proj->category }}"
                       style="{{ $pIdx >= 4 ? 'display: none;' : '' }}">
                        <div class="pf-project-thumb">
                            <img src="{{ $proj->thumbnail ?: '/module-media/Portfolio/theme/project-1.jpg' }}" alt="{{ $proj->title }}" loading="lazy">
                        </div>
                        <div class="pf-project-body">
                            <div>
                                <h5>{{ $proj->title }}</h5>
                                <span>{{ $proj->subtitle ?: $proj->category }}</span>
                            </div>
                            <span class="pf-project-arrow">
                                <i class="fa-solid fa-arrow-right"></i>
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <div class="pf-wave-bottom">
        <svg viewBox="0 0 1440 65" preserveAspectRatio="none">
            <path d="M0,40 C380,-6 740,76 1120,26 C1300,8 1390,30 1440,42 L1440,65 L0,65 Z" fill="rgba(7, 139, 255, 0.32)"></path>
            <path d="M0,50 C400,8 760,82 1140,36 C1310,18 1390,38 1440,50 L1440,65 L0,65 Z" fill="var(--pf-wave-light-fill)"></path>
        </svg>
    </div>
</section>
@endif

<!-- ===================================================================
     09. EXPERIENCE / CAREER TIMELINE SECTION (HORIZONTAL ON DESKTOP)
     =================================================================== -->
@if(($vis['experience'] ?? '1') === '1')
<section class="pf-experience" id="experience">
    <div class="pf-container">
        <div class="pf-split-layout">
            <!-- Left Intro -->
            <div class="pf-side-intro">
                <div class="pf-sec-tag">{{ $t['experience_section']['tag'] ?? 'MY EXPERIENCE' }}</div>
                <h2 class="pf-sec-heading">{!! nl2br(e($t['experience_section']['heading'] ?? "My Journey\nSo Far")) !!}</h2>
                <p>{{ $t['experience_section']['description'] ?? 'A journey of learning, building and growing in the tech world.' }}</p>
            </div>

            <!-- Right 4-Step Horizontal Timeline -->
            <div class="pf-timeline-horizontal">
                @foreach($experiences as $exp)
                    <div class="pf-timeline-step">
                        <div class="pf-timeline-node">
                            <i class="{{ $exp->icon_class ?: 'fa-solid fa-briefcase' }}"></i>
                        </div>
                        <div class="pf-timeline-period">{{ $exp->period }}</div>
                        <div class="pf-timeline-role">{{ $exp->job_title }}</div>
                        <div class="pf-timeline-desc">{{ $exp->description }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="pf-wave-bottom">
        <svg viewBox="0 0 1440 65" preserveAspectRatio="none">
            <path d="M0,20 C340,72 720,-8 1080,42 C1280,68 1380,32 1440,18 L1440,65 L0,65 Z" fill="rgba(7, 139, 255, 0.28)"></path>
            <path d="M0,36 C360,82 740,6 1100,50 C1290,72 1390,42 1440,28 L1440,65 L0,65 Z" fill="var(--pf-wave-dark-fill)"></path>
        </svg>
    </div>
</section>
@endif

<!-- ===================================================================
     13. TESTIMONIALS SECTION (EXACT REFERENCE 3-CARD LAYOUT)
     =================================================================== -->
@if(($vis['testimonials'] ?? '1') === '1')
<section class="pf-testimonials" id="testimonials">
    <div class="pf-container">
        <div class="pf-split-layout">
            <!-- Left Intro -->
            <div class="pf-side-intro">
                <div class="pf-sec-tag">{{ $t['testimonials_section']['tag'] ?? 'TESTIMONIALS' }}</div>
                <h2 class="pf-sec-heading">{!! nl2br(e($t['testimonials_section']['heading'] ?? "What Clients\nSay")) !!}</h2>
                <p>{{ $t['testimonials_section']['description'] ?? 'Trusted by clients worldwide for delivering exceptional work.' }}</p>
            </div>

            <!-- Right 3-Column Testimonial Cards -->
            <div class="pf-testi-grid">
                @foreach($testimonials->take(3) as $testi)
                    <div class="pf-testi-card">
                        <div>
                            <div class="pf-quote-mark"><i class="fa-solid fa-quote-left"></i></div>
                            <p class="pf-testi-text">{{ $testi->review_text }}</p>
                        </div>

                        <div class="pf-testi-footer">
                            <div class="pf-testi-author">
                                <img src="{{ $testi->avatar ?: '/module-media/Portfolio/theme/avatar-1.jpg' }}" alt="{{ $testi->client_name }}">
                                <div>
                                    <h6>{{ $testi->client_name }}</h6>
                                    <span>{{ $testi->designation }}{{ $testi->company ? ', ' . $testi->company : '' }}</span>
                                </div>
                            </div>
                            <div class="pf-stars">
                                @for($i = 0; $i < ($testi->rating ?: 5); $i++)
                                    <i class="fa-solid fa-star"></i>
                                @endfor
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="pf-wave-bottom">
        <svg viewBox="0 0 1440 65" preserveAspectRatio="none">
            <path d="M0,38 C360,-8 720,74 1120,24 C1290,6 1380,28 1440,40 L1440,65 L0,65 Z" fill="rgba(7, 139, 255, 0.32)"></path>
            <path d="M0,48 C380,6 740,80 1140,34 C1310,16 1390,36 1440,48 L1440,65 L0,65 Z" fill="var(--pf-wave-light-fill)"></path>
        </svg>
    </div>
</section>
@endif

<!-- ===================================================================
     14 & 17. LATEST ARTICLES (BLOG) + GET IN TOUCH SPLIT SECTION (EXACT REFERENCE MATCH)
     =================================================================== -->
<section class="pf-blog-contact-split" id="blog">
    <div class="pf-container">
        <div class="pf-bc-grid">
            <!-- Left 62%: Latest Articles (Blog) -->
            @if(($vis['blog'] ?? '1') === '1')
            <div class="pf-blog-part">
                <div class="pf-side-intro">
                    <div class="pf-sec-tag" style="margin-bottom: 6px;">{{ $t['blog_section']['tag'] ?? 'FROM BLOG' }}</div>
                    <h2 class="pf-sec-heading" style="font-size: 28px;">{{ $t['blog_section']['heading'] ?? 'Latest Articles' }}</h2>
                    <p style="color: var(--pf-text-muted-light); font-size: 13px; margin-bottom: 16px;">
                        {{ $t['blog_section']['description'] ?? 'Insights, tips and updates from my development journey.' }}
                    </p>
                    <a href="{{ route('portfolio.blog') }}" class="pf-btn-outline" style="height: 36px; padding: 0 16px; font-size: 12px; background: #020817; color: #FFFFFF; border-color: #020817;">
                        <span>{{ $t['blog_section']['btn_text'] ?? 'View All Posts' }}</span>
                    </a>
                </div>

                <div class="pf-blog-cards">
                    @foreach($blogs->take(3) as $blog)
                        <a href="{{ route('portfolio.blog.show', $blog->slug) }}" class="pf-blog-card">
                            <div class="pf-blog-thumb">
                                <img src="{{ $blog->image ?: '/module-media/Portfolio/theme/blog-1.jpg' }}" alt="{{ $blog->title }}" loading="lazy">
                                <span class="pf-blog-date">{{ $blog->published_date ?: 'Oct 20, 2025' }}</span>
                            </div>
                            <div class="pf-blog-card-body">
                                <h6>{{ $blog->title }}</h6>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Right 38%: Get In Touch Quick Block -->
            @if(($vis['contact'] ?? '1') === '1')
            <div class="pf-contact-quick">
                <div>
                    <div class="pf-sec-tag" style="margin-bottom: 6px;">{{ $t['contact']['tag'] ?? 'GET IN TOUCH' }}</div>
                    <h2 class="pf-sec-heading" style="font-size: 26px;">{!! nl2br(e($t['contact']['heading'] ?? "Let's Work\nTogether")) !!}</h2>
                    <p style="font-size: 12.5px; color: var(--pf-text-muted-light); margin-bottom: 14px;">
                        {{ $t['contact']['description'] ?? 'Have a project in mind? Feel free to reach out anytime.' }}
                    </p>
                    <a href="#contact-form-box" class="pf-btn-primary" style="height: 36px; padding: 0 16px; font-size: 12px;">
                        <span>{{ $t['contact']['btn_text'] ?? 'Contact Me' }}</span>
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>

                <div class="pf-contact-info-list">
                    <div class="pf-c-item">
                        <div class="pf-c-icon"><i class="fa-solid fa-envelope"></i></div>
                        <div>
                            <strong>Email</strong>
                            <span>{{ $t['contact']['email'] ?? 'hello@alexmorgan.dev' }}</span>
                        </div>
                    </div>
                    <div class="pf-c-item">
                        <div class="pf-c-icon"><i class="fa-solid fa-phone"></i></div>
                        <div>
                            <strong>Phone</strong>
                            <span>{{ $t['contact']['phone'] ?? '+880 1712 345678' }}</span>
                        </div>
                    </div>
                    <div class="pf-c-item">
                        <div class="pf-c-icon"><i class="fa-solid fa-location-dot"></i></div>
                        <div>
                            <strong>Location</strong>
                            <span>{{ $t['contact']['location'] ?? 'Dhaka, Bangladesh' }}</span>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</section>

<!-- ===================================================================
     07, 08, 10, 11, 12. PROCESS, WHY WORK WITH ME, EDUCATION & CLIENTS
     =================================================================== -->
@if(($vis['process'] ?? '1') === '1' || ($vis['why_me'] ?? '1') === '1')
<section class="pf-extra-dark">
    <div class="pf-container">
        @if(($vis['why_me'] ?? '1') === '1')
        <div style="margin-bottom: 54px;">
            <div style="text-align: center; max-width: 640px; margin: 0 auto 32px;">
                <div class="pf-sec-tag">{{ $t['why_me']['tag'] ?? 'WHY WORK WITH ME' }}</div>
                <h2 class="pf-sec-heading">{{ $t['why_me']['heading'] ?? 'Engineering Excellence Meets Business Value' }}</h2>
            </div>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 16px;">
                @foreach(($t['why_me']['items'] ?? []) as $wItem)
                    <div class="pf-process-card">
                        <div style="width: 40px; height: 40px; border-radius: 10px; background: rgba(7, 139, 255, 0.16); color: var(--pf-light-blue); display: flex; align-items: center; justify-content: center; font-size: 18px; margin-bottom: 12px;">
                            <i class="{{ $wItem['icon'] ?? 'fa-solid fa-code' }}"></i>
                        </div>
                        <h5 style="font-size: 15px; font-weight: 700; margin-bottom: 6px;">{{ $wItem['title'] ?? '' }}</h5>
                        <p style="font-size: 12.5px; color: var(--pf-dark-sec-muted);">{{ $wItem['desc'] ?? '' }}</p>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        @if(($vis['process'] ?? '1') === '1')
        <div>
            <div style="text-align: center; max-width: 640px; margin: 0 auto 32px;">
                <div class="pf-sec-tag">{{ $t['process']['tag'] ?? 'WORKING PROCESS' }}</div>
                <h2 class="pf-sec-heading">{{ $t['process']['heading'] ?? 'My 7-Step Development Workflow' }}</h2>
            </div>
            <div class="pf-process-grid">
                @foreach(($t['process']['steps'] ?? []) as $step)
                    <div class="pf-process-card">
                        <span class="pf-step-badge">STEP {{ $step['step'] ?? '01' }}</span>
                        <h5 style="font-size: 14.5px; font-weight: 700; margin-bottom: 6px;">{{ $step['title'] ?? '' }}</h5>
                        <p style="font-size: 12px; color: var(--pf-dark-sec-muted);">{{ $step['desc'] ?? '' }}</p>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        @if(($vis['education'] ?? '1') === '1' && $educations->isNotEmpty())
        <div style="margin-top: 54px;">
            <div style="text-align: center; max-width: 640px; margin: 0 auto 28px;">
                <div class="pf-sec-tag">EDUCATION &amp; CERTIFICATIONS</div>
                <h2 class="pf-sec-heading">Academic &amp; Professional Credentials</h2>
            </div>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px;">
                @foreach($educations as $edu)
                    <div class="pf-process-card">
                        <span class="pf-step-badge">{{ strtoupper($edu->type) }} • {{ $edu->completion_date }}</span>
                        <h5 style="font-size: 15px; font-weight: 700; margin-bottom: 4px;">{{ $edu->title }}</h5>
                        <div style="font-size: 12.5px; color: var(--pf-light-blue); font-weight: 600; margin-bottom: 8px;">{{ $edu->institution }}</div>
                        <p style="font-size: 12px; color: var(--pf-dark-sec-muted);">{{ $edu->description }}</p>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</section>
@endif

<!-- ===================================================================
     15, 16, 17, 18. FAQ ACCORDION + CALL TO ACTION + WORKING CONTACT FORM
     =================================================================== -->
<section class="pf-extra-light" id="contact">
    <div class="pf-container">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(340px, 1fr)); gap: 40px; align-items: start;">
            <!-- Left: FAQ Accordion & Call To Action -->
            @if(($vis['faq'] ?? '1') === '1')
            <div>
                <div class="pf-sec-tag">FREQUENTLY ASKED QUESTIONS</div>
                <h2 class="pf-sec-heading" style="font-size: 30px; margin-bottom: 20px;">Got Questions? Here Are Answers</h2>

                @foreach($faqs as $fIdx => $faq)
                    <div class="pf-faq-item {{ $fIdx === 0 ? 'open' : '' }}">
                        <button type="button" class="pf-faq-btn">
                            <span>{{ $faq->question }}</span>
                            <i class="fa-solid fa-chevron-down" style="font-size: 12px; color: var(--pf-electric);"></i>
                        </button>
                        <div class="pf-faq-body">
                            {{ $faq->answer }}
                        </div>
                    </div>
                @endforeach

                @if(($vis['newsletter'] ?? '1') === '1')
                <div class="pf-form-card" style="margin-top: 24px; padding: 22px;">
                    <h5 style="font-size: 16px; font-weight: 800; margin-bottom: 6px;">Subscribe to Developer Insights</h5>
                    <p style="font-size: 12.5px; color: var(--pf-text-muted-light); margin-bottom: 14px;">Get Laravel, Next.js, and system architecture tips straight to your inbox.</p>
                    <form action="{{ route('portfolio.newsletter.submit') }}" method="POST" style="display: flex; gap: 10px;">
                        @csrf
                        <input type="email" name="newsletter_email" class="pf-input" placeholder="Enter your email address..." required>
                        <button type="submit" class="pf-btn-primary" style="flex-shrink: 0;">Subscribe</button>
                    </form>
                </div>
                @endif
            </div>
            @endif

            <!-- Right: Complete Working Contact Form -->
            @if(($vis['contact'] ?? '1') === '1')
            <div class="pf-form-card" id="contact-form-box">
                <div class="pf-sec-tag">{{ $t['cta']['tag'] ?? 'HAVE A PROJECT IN MIND?' }}</div>
                <h3 class="pf-sec-heading" style="font-size: 28px; margin-bottom: 8px;">{{ $t['cta']['heading'] ?? 'Have a Project in Mind?' }}</h3>
                <p style="font-size: 13.5px; color: var(--pf-text-muted-light); margin-bottom: 22px;">
                    {{ $t['cta']['description'] ?? "Send me a message and I'll respond within 24 hours." }}
                </p>

                <form action="{{ route('portfolio.contact.submit') }}" method="POST">
                    @csrf
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 14px;">
                        <div>
                            <label style="font-size: 12px; font-weight: 700; display: block; margin-bottom: 5px;">Your Name *</label>
                            <input type="text" name="name" class="pf-input" placeholder="John Doe" required>
                        </div>
                        <div>
                            <label style="font-size: 12px; font-weight: 700; display: block; margin-bottom: 5px;">Email Address *</label>
                            <input type="email" name="email" class="pf-input" placeholder="john@company.com" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 14px;">
                        <div>
                            <label style="font-size: 12px; font-weight: 700; display: block; margin-bottom: 5px;">Phone Number</label>
                            <input type="text" name="phone" class="pf-input" placeholder="+880 17XX-XXXXXX">
                        </div>
                        <div>
                            <label style="font-size: 12px; font-weight: 700; display: block; margin-bottom: 5px;">Subject</label>
                            <input type="text" name="subject" class="pf-input" placeholder="Custom Laravel Web App">
                        </div>
                    </div>

                    <div style="margin-bottom: 18px;">
                        <label style="font-size: 12px; font-weight: 700; display: block; margin-bottom: 5px;">Project Details / Message *</label>
                        <textarea name="message" rows="4" class="pf-textarea" placeholder="Tell me about your project requirements, timeline, and goals..." required></textarea>
                    </div>

                    <button type="submit" class="pf-btn-primary" style="width: 100%; justify-content: center; height: 48px; font-size: 14px;">
                        <span>Send Message</span>
                        <i class="fa-solid fa-paper-plane"></i>
                    </button>
                </form>
            </div>
            @endif
        </div>
    </div>
</section>
@endsection
