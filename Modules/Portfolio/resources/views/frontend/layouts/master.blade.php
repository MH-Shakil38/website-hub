@php
    $activeCount = isset($activeModuleWebsites) ? $activeModuleWebsites->count() : \App\Models\Website::activeModules()->count();
    $basePrefix = $activeCount === 1 ? '' : '/developer-portfolio';
    $homeUrl = $basePrefix === '' ? url('/') : url($basePrefix);
    $projectsUrl = route('portfolio.projects');
    $blogUrl = route('portfolio.blog');
    $cssUrl = route('portfolio.asset', ['type' => 'css', 'file' => 'style.css']) . '?v=' . (@filemtime(module_path('Portfolio', 'Public/assets/css/style.css')) ?: time());
    $jsUrl = route('portfolio.asset', ['type' => 'js', 'file' => 'main.js']) . '?v=' . (@filemtime(module_path('Portfolio', 'Public/assets/js/main.js')) ?: time());
    $defaultTheme = $t['meta']['default_theme'] ?? 'dark';
@endphp
<!DOCTYPE html>
<html lang="en" data-theme="{{ $defaultTheme }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('page_title', $t['meta']['page_title'] ?? 'Alex Morgan — Web Developer Portfolio')</title>
    <meta name="description" content="@yield('meta_description', $t['meta']['meta_description'] ?? '')">
    <meta name="keywords" content="{{ $t['meta']['meta_keywords'] ?? '' }}">
    <meta property="og:title" content="@yield('page_title', $t['meta']['og_title'] ?? '')">
    <meta property="og:description" content="@yield('meta_description', $t['meta']['meta_description'] ?? '')">
    <meta property="og:type" content="website">
    <link rel="sitemap" type="application/xml" title="Sitemap" href="{{ route('portfolio.sitemap') }}">

    <!-- Google Fonts: Playfair Display (Serif Headings), Plus Jakarta Sans (Body), Caveat (Script Callout) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Caveat:wght@600;700&family=Playfair+Display:ital,wght@0,600;0,700;0,800;1,600;1,700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome 6 Free Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <!-- Standalone Portfolio Theme CSS -->
    <link rel="stylesheet" href="{{ $cssUrl }}">
</head>
<body>
    <!-- Top 3D Scroll Progress Bar & Interactive Ambient Cursor Glow -->
    <div class="pf-scroll-progress" id="pfScrollProgress"></div>
    <div class="pf-cursor-glow" id="pfCursorGlow"></div>

    <!-- ===================================================================
         01. STICKY HEADER & NAVIGATION
         =================================================================== -->
    <header class="pf-header">
        <div class="pf-container">
            <nav class="pf-navbar">
                <!-- Left Brand Logo + Developer Name -->
                <a href="{{ $homeUrl }}" class="pf-brand">
                    <div class="pf-brand-logo">
                        @if(!empty($t['header']['logo']))
                            <img src="{{ $t['header']['logo'] }}" alt="{{ $t['header']['brand_name'] ?? 'Alex Morgan' }}" style="width: 36px; height: 36px; object-fit: contain; border-radius: 8px;">
                        @else
                            <svg width="36" height="36" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <defs>
                                    <linearGradient id="pfLogoGrad" x1="0" y1="0" x2="40" y2="40" gradientUnits="userSpaceOnUse">
                                        <stop offset="0%" stop-color="#35B6FF"/>
                                        <stop offset="100%" stop-color="#078BFF"/>
                                    </linearGradient>
                                </defs>
                                <path d="M20 4L5 34H13.5L20 19.5L26.5 34H35L20 4Z" fill="url(#pfLogoGrad)"/>
                                <path d="M13 26.5H27L24 32H16L13 26.5Z" fill="#35B6FF" opacity="0.85"/>
                            </svg>
                        @endif
                    </div>
                    <div class="pf-brand-text">
                        <span class="pf-brand-name">{{ $t['header']['brand_name'] ?? 'Alex Morgan' }}</span>
                        <span class="pf-brand-sub">{{ $t['header']['brand_sub'] ?? 'Web Developer' }}</span>
                    </div>
                </a>

                <!-- Center Navigation Menu -->
                <ul class="pf-nav-menu" id="pfNavMenu">
                    <li><a href="{{ $homeUrl }}#home" class="pf-nav-link {{ request()->routeIs('portfolio.home', 'hub.home', 'hub.module') ? 'active' : '' }}">Home</a></li>
                    <li><a href="{{ $homeUrl }}#about" class="pf-nav-link">About</a></li>
                    <li><a href="{{ $homeUrl }}#services" class="pf-nav-link">Services</a></li>
                    <li><a href="{{ $homeUrl }}#skills" class="pf-nav-link">Skills</a></li>
                    <li><a href="{{ $homeUrl }}#portfolio" class="pf-nav-link {{ request()->routeIs('portfolio.projects', 'portfolio.project.show') ? 'active' : '' }}">Portfolio</a></li>
                    <li><a href="{{ $homeUrl }}#experience" class="pf-nav-link">Experience</a></li>
                    <li><a href="{{ $homeUrl }}#testimonials" class="pf-nav-link">Testimonials</a></li>
                    <li><a href="{{ $homeUrl }}#blog" class="pf-nav-link {{ request()->routeIs('portfolio.blog', 'portfolio.blog.show') ? 'active' : '' }}">Blog</a></li>
                    <li><a href="{{ $homeUrl }}#contact" class="pf-nav-link">Contact</a></li>
                </ul>

                <!-- Right Actions: Light / Dark Theme Toggle + Let's Talk Button -->
                <div class="pf-header-actions">
                    <button type="button" class="pf-theme-toggle" id="pfThemeToggle" title="Switch Light / Dark Theme">
                        <i class="fa-solid fa-circle-half-stroke" id="pfThemeIcon"></i>
                        <span id="pfThemeLabel">Dark Wave</span>
                    </button>

                    <a href="{{ $homeUrl }}{{ $t['header']['cta_url'] ?? '#contact' }}" class="pf-btn-talk">
                        <span>{{ $t['header']['cta_text'] ?? "Let's Talk" }}</span>
                        <i class="fa-solid fa-arrow-right" style="font-size: 11px;"></i>
                    </a>

                    <button type="button" class="pf-mobile-toggle" id="pfMobileToggle" aria-label="Menu">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                </div>
            </nav>
        </div>
    </header>

    <!-- Flash Notifications -->
    @if(session('portfolio_success'))
        <div style="background: linear-gradient(90deg, #078BFF, #10B981); color: #FFFFFF; text-align: center; padding: 12px 20px; font-size: 13.5px; font-weight: 700;">
            <i class="fa-solid fa-circle-check" style="margin-right: 6px;"></i> {{ session('portfolio_success') }}
        </div>
    @endif
    @if($errors->any())
        <div style="background: #EF4444; color: #FFFFFF; text-align: center; padding: 12px 20px; font-size: 13.5px; font-weight: 700;">
            <i class="fa-solid fa-triangle-exclamation" style="margin-right: 6px;"></i> {{ $errors->first() }}
        </div>
    @endif

    <!-- Main Page Content -->
    <main>
        @yield('portfolio_content')
    </main>

    <!-- ===================================================================
         19. FOOTER SECTION (EXACT REFERENCE MATCH)
         =================================================================== -->
    <footer class="pf-footer">
        <div class="pf-wave-top">
            <svg viewBox="0 0 1440 70" preserveAspectRatio="none">
                <path d="M0,0 L1440,0 L1440,28 C1180,68 920,-8 620,34 C340,72 160,18 0,42 Z" fill="var(--pf-wave-light-fill)"></path>
                <path d="M0,26 C320,68 620,-6 960,38 C1180,64 1340,24 1440,16 L1440,48 C1220,66 960,18 620,52 C320,76 140,42 0,58 Z" fill="rgba(7, 139, 255, 0.38)"></path>
            </svg>
        </div>

        <div class="pf-container" style="padding-top: 28px;">
            <div class="pf-footer-row">
                <!-- Left Brand + Copyright -->
                <div>
                    <a href="{{ $homeUrl }}" class="pf-brand" style="margin-bottom: 12px;">
                        <div class="pf-brand-logo">
                            <svg width="34" height="34" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M20 4L5 34H13.5L20 19.5L26.5 34H35L20 4Z" fill="#078BFF"/>
                                <path d="M13 26.5H27L24 32H16L13 26.5Z" fill="#35B6FF"/>
                            </svg>
                        </div>
                        <div class="pf-brand-text">
                            <span class="pf-brand-name" style="color: #FFFFFF;">{{ $t['header']['brand_name'] ?? 'Alex Morgan' }}</span>
                            <span class="pf-brand-sub" style="color: #94A3B8;">{{ $t['header']['brand_sub'] ?? 'Web Developer' }}</span>
                        </div>
                    </a>
                    <p style="font-size: 11.5px; color: #64748B;">
                        {{ $t['footer']['copyright'] ?? '© 2025 Alex Morgan. All rights reserved.' }}
                    </p>
                </div>

                <!-- Center Navigation Links -->
                <div class="pf-footer-nav">
                    <a href="{{ $homeUrl }}#home">Home</a>
                    <a href="{{ $homeUrl }}#about">About</a>
                    <a href="{{ $homeUrl }}#services">Services</a>
                    <a href="{{ $projectsUrl }}">Portfolio</a>
                    <a href="{{ $blogUrl }}">Blog</a>
                    <a href="{{ $homeUrl }}#testimonials">Testimonials</a>
                    <a href="{{ $homeUrl }}#contact">Contact</a>
                </div>

                <!-- Right Social Media Links -->
                <div>
                    <div style="font-size: 12px; font-weight: 700; color: #FFFFFF; margin-bottom: 8px;">Follow Me</div>
                    <div class="pf-social-row">
                        <a href="{{ $t['socials']['facebook'] ?? '#' }}" target="_blank" rel="noopener" class="pf-social-icon" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                        <a href="{{ $t['socials']['github'] ?? '#' }}" target="_blank" rel="noopener" class="pf-social-icon" aria-label="GitHub"><i class="fa-brands fa-github"></i></a>
                        <a href="{{ $t['socials']['linkedin'] ?? '#' }}" target="_blank" rel="noopener" class="pf-social-icon" aria-label="LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
                        <a href="{{ $t['socials']['youtube'] ?? '#' }}" target="_blank" rel="noopener" class="pf-social-icon" aria-label="YouTube"><i class="fa-brands fa-youtube"></i></a>
                        <a href="{{ $t['socials']['twitter'] ?? '#' }}" target="_blank" rel="noopener" class="pf-social-icon" aria-label="X"><i class="fa-brands fa-x-twitter"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Back To Top Button -->
    <button type="button" class="pf-back-top" id="pfBackTop" title="Back to top">
        <i class="fa-solid fa-arrow-up"></i>
    </button>

    <!-- Standalone Portfolio Interactive JS -->
    <script src="{{ $jsUrl }}"></script>
</body>
</html>
