<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $globalSiteName ?? 'SiteBuilder' }} Cloud — Modern Multi-Industry SaaS Website Platform</title>
    <meta name="description" content="Launch production-ready industry websites in seconds. Multi-module SaaS website builder with isolated databases, visual CMS, and instant deployment.">

    <!-- Google Fonts: Plus Jakarta Sans & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3 & Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            --saas-primary: #EC134A;
            --saas-primary-dark: #C90E3D;
            --saas-indigo: #4F46E5;
            --saas-cyan: #0EA5E9;
            --saas-dark: #090D16;
            --saas-dark-card: #111827;
            --saas-slate: #0F172A;
            --saas-muted: #64748B;
            --saas-border: #E2E8F0;
            --saas-bg: #F8FAFC;
        }

        * {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--saas-bg);
            color: var(--saas-slate);
            margin: 0;
            overflow-x: hidden;
        }

        h1, h2, h3, h4, h5, .font-display {
            font-family: 'Plus Jakarta Sans', sans-serif;
            letter-spacing: -0.025em;
        }

        /* =====================================================================
           1. STICKY GLASSMORPHIC SAAS NAVBAR
           ===================================================================== */
        .saas-navbar {
            background: rgba(255, 255, 255, 0.86);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(226, 232, 240, 0.85);
            padding: 14px 0;
            position: sticky;
            top: 0;
            z-index: 1000;
            transition: all 0.2s ease;
        }

        .saas-brand {
            display: inline-flex;
            align-items: center;
            gap: 11px;
            text-decoration: none;
        }

        .saas-brand-badge {
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            background: linear-gradient(135deg, rgba(236, 19, 74, 0.12), rgba(79, 70, 229, 0.12));
            color: var(--saas-primary);
            border: 1px solid rgba(236, 19, 74, 0.25);
            padding: 2px 8px;
            border-radius: 999px;
        }

        .saas-nav-links {
            display: flex;
            align-items: center;
            gap: 28px;
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .saas-nav-links a {
            text-decoration: none;
            color: #334155;
            font-size: 14px;
            font-weight: 600;
            transition: color 0.18s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .saas-nav-links a:hover {
            color: var(--saas-primary);
        }

        .nav-count-pill {
            background: #DCFCE7;
            color: #15803D;
            font-size: 11px;
            font-weight: 700;
            padding: 1px 8px;
            border-radius: 999px;
        }

        .btn-saas-ghost {
            color: #334155;
            font-weight: 600;
            font-size: 13.5px;
            padding: 8px 16px;
            border-radius: 10px;
            text-decoration: none;
            transition: all 0.18s;
        }

        .btn-saas-ghost:hover {
            background: #F1F5F9;
            color: #0F172A;
        }

        .btn-saas-primary {
            background: linear-gradient(135deg, #EC134A 0%, #D00F40 100%);
            color: #FFFFFF;
            font-weight: 700;
            font-size: 13.5px;
            padding: 10px 20px;
            border-radius: 11px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: none;
            box-shadow: 0 8px 20px -4px rgba(236, 19, 74, 0.45);
            transition: all 0.22s ease;
        }

        .btn-saas-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 26px -4px rgba(236, 19, 74, 0.55);
            color: #FFFFFF;
        }

        /* =====================================================================
           2. ULTRA-MODERN SAAS HERO SECTION
           ===================================================================== */
        .saas-hero {
            position: relative;
            background: var(--saas-dark);
            color: #FFFFFF;
            padding: 84px 0 96px;
            overflow: hidden;
        }

        /* Subtle SaaS Grid & Glowing Mesh Orbs */
        .saas-hero::before {
            content: "";
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(to right, rgba(255, 255, 255, 0.04) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(255, 255, 255, 0.04) 1px, transparent 1px);
            background-size: 48px 48px;
            mask-image: radial-gradient(ellipse 80% 70% at 50% 35%, #000 60%, transparent 100%);
            pointer-events: none;
        }

        .hero-orb-1 {
            position: absolute;
            top: -120px;
            left: 12%;
            width: 480px;
            height: 480px;
            background: radial-gradient(circle, rgba(236, 19, 74, 0.28) 0%, transparent 70%);
            filter: blur(60px);
            pointer-events: none;
        }

        .hero-orb-2 {
            position: absolute;
            top: -80px;
            right: 10%;
            width: 520px;
            height: 520px;
            background: radial-gradient(circle, rgba(79, 70, 229, 0.28) 0%, rgba(14, 165, 233, 0.16) 50%, transparent 70%);
            filter: blur(65px);
            pointer-events: none;
        }

        .hero-pill-announcement {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.14);
            backdrop-filter: blur(10px);
            padding: 6px 16px 6px 8px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 600;
            color: #E2E8F0;
            margin-bottom: 24px;
            text-decoration: none;
            transition: all 0.2s;
        }

        .hero-pill-announcement:hover {
            border-color: rgba(236, 19, 74, 0.5);
            background: rgba(255, 255, 255, 0.1);
            color: #FFFFFF;
        }

        .hero-pill-tag {
            background: linear-gradient(135deg, #EC134A, #6366F1);
            color: #FFFFFF;
            font-size: 11px;
            font-weight: 800;
            padding: 3px 10px;
            border-radius: 999px;
            text-transform: uppercase;
        }

        .saas-hero-title {
            font-size: clamp(36px, 5.2vw, 64px);
            font-weight: 800;
            line-height: 1.08;
            margin: 0 auto 22px;
            max-width: 920px;
            letter-spacing: -0.03em;
        }

        .text-gradient-saas {
            background: linear-gradient(135deg, #FF7597 0%, #EC134A 40%, #818CF8 85%, #38BDF8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .saas-hero-sub {
            font-size: clamp(16px, 1.35vw, 19px);
            color: #94A3B8;
            max-width: 720px;
            margin: 0 auto 34px;
            line-height: 1.65;
            font-weight: 400;
        }

        .btn-hero-lg {
            padding: 15px 30px;
            font-size: 15.5px;
            font-weight: 700;
            border-radius: 13px;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            transition: all 0.22s;
        }

        .btn-hero-secondary {
            background: rgba(255, 255, 255, 0.07);
            color: #FFFFFF;
            border: 1px solid rgba(255, 255, 255, 0.16);
            backdrop-filter: blur(8px);
        }

        .btn-hero-secondary:hover {
            background: rgba(255, 255, 255, 0.14);
            color: #FFFFFF;
            transform: translateY(-2px);
        }

        /* Hero Dashboard Mockup Frame */
        .hero-console-preview {
            max-width: 1120px;
            margin: 56px auto 0;
            background: rgba(17, 24, 39, 0.85);
            border: 1px solid rgba(255, 255, 255, 0.14);
            border-radius: 20px;
            box-shadow: 0 30px 80px -15px rgba(0, 0, 0, 0.7), 0 0 40px rgba(79, 70, 229, 0.15);
            overflow: hidden;
            backdrop-filter: blur(16px);
        }

        .console-topbar {
            background: rgba(15, 23, 42, 0.95);
            padding: 12px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .mac-dots {
            display: flex;
            align-items: center;
            gap: 7px;
        }

        .mac-dot {
            width: 11px;
            height: 11px;
            border-radius: 50%;
        }

        .console-url-bar {
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            padding: 4px 18px;
            font-size: 12px;
            color: #94A3B8;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .console-body {
            padding: 24px;
            text-align: left;
        }

        .console-stat-card {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 14px;
            padding: 16px 18px;
            height: 100%;
        }

        /* =====================================================================
           3. TRUST METRICS & ARCHITECTURE BAR
           ===================================================================== */
        .saas-metrics-strip {
            background: #FFFFFF;
            border-bottom: 1px solid var(--saas-border);
            padding: 28px 0;
        }

        .metric-item {
            text-align: center;
            padding: 8px 16px;
        }

        .metric-value {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 28px;
            font-weight: 800;
            color: var(--saas-slate);
            line-height: 1.1;
        }

        .metric-label {
            font-size: 13px;
            font-weight: 600;
            color: var(--saas-muted);
            margin-top: 4px;
        }

        /* =====================================================================
           4. LIVE INDUSTRY TEMPLATES & DEMOS SHOWCASE
           ===================================================================== */
        .saas-section {
            padding: 88px 0;
        }

        .section-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(236, 19, 74, 0.08);
            color: var(--saas-primary);
            border: 1px solid rgba(236, 19, 74, 0.2);
            font-size: 12.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 5px 14px;
            border-radius: 999px;
            margin-bottom: 14px;
        }

        .section-heading {
            font-size: clamp(28px, 3.4vw, 42px);
            font-weight: 800;
            color: var(--saas-slate);
            margin-bottom: 14px;
        }

        .section-subheading {
            font-size: 16.5px;
            color: var(--saas-muted);
            max-width: 660px;
            margin: 0 auto;
            line-height: 1.6;
        }

        /* Category Filter Pills */
        .filter-pills-wrap {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 36px;
        }

        .filter-pill-btn {
            background: #FFFFFF;
            border: 1px solid #CBD5E1;
            color: #334155;
            font-size: 13.5px;
            font-weight: 600;
            padding: 8px 18px;
            border-radius: 999px;
            cursor: pointer;
            transition: all 0.18s;
        }

        .filter-pill-btn:hover,
        .filter-pill-btn.active {
            background: var(--saas-slate);
            color: #FFFFFF;
            border-color: var(--saas-slate);
            box-shadow: 0 6px 16px rgba(15, 23, 42, 0.14);
        }

        /* SaaS Browser-Style Template Cards */
        .saas-template-card {
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 20px;
            overflow: hidden;
            transition: all 0.28s cubic-bezier(0.16, 1, 0.3, 1);
            height: 100%;
            display: flex;
            flex-direction: column;
            box-shadow: 0 4px 20px rgba(15, 23, 42, 0.04);
        }

        .saas-template-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 22px 44px rgba(15, 23, 42, 0.11);
            border-color: #CBD5E1;
        }

        .template-browser-bar {
            background: #F1F5F9;
            padding: 9px 14px;
            border-bottom: 1px solid #E2E8F0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }

        .template-domain-chip {
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 6px;
            padding: 2px 10px;
            font-size: 11px;
            font-weight: 600;
            color: #475569;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .template-thumb-box {
            position: relative;
            height: 225px;
            overflow: hidden;
            background: #0F172A;
        }

        .template-thumb-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.45s ease;
        }

        .saas-template-card:hover .template-thumb-box img {
            transform: scale(1.06);
        }

        .template-category-badge {
            position: absolute;
            top: 12px;
            right: 12px;
            background: rgba(15, 23, 42, 0.84);
            backdrop-filter: blur(8px);
            color: #FFFFFF;
            font-size: 11.5px;
            font-weight: 700;
            padding: 5px 12px;
            border-radius: 999px;
            border: 1px solid rgba(255, 255, 255, 0.15);
        }

        .template-card-body {
            padding: 22px 24px 24px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .status-pulse-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #10B981;
            display: inline-block;
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.18);
        }

        /* =====================================================================
           5. BENTO-GRID SAAS FEATURES SECTION
           ===================================================================== */
        .bento-card {
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 22px;
            padding: 30px;
            height: 100%;
            transition: all 0.25s ease;
            position: relative;
            overflow: hidden;
        }

        .bento-card:hover {
            border-color: #CBD5E1;
            box-shadow: 0 16px 36px rgba(15, 23, 42, 0.07);
            transform: translateY(-4px);
        }

        .bento-icon-box {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 20px;
        }

        /* =====================================================================
           6. HOW IT WORKS (3-STEP SAAS PIPELINE)
           ===================================================================== */
        .step-card {
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 22px;
            padding: 32px 28px;
            height: 100%;
            position: relative;
        }

        .step-number-badge {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: var(--saas-slate);
            color: #FFFFFF;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 800;
            font-size: 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }

        /* =====================================================================
           7. SAAS PRICING SECTION (MONTHLY / YEARLY TOGGLE)
           ===================================================================== */
        .pricing-card {
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 24px;
            padding: 36px 32px;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: all 0.25s ease;
            position: relative;
        }

        .pricing-card.featured {
            background: var(--saas-dark);
            color: #FFFFFF;
            border: 2px solid var(--saas-primary);
            box-shadow: 0 24px 54px -12px rgba(15, 23, 42, 0.35);
            transform: translateY(-8px);
        }

        .popular-ribbon {
            position: absolute;
            top: -13px;
            left: 50%;
            transform: translateX(-50%);
            background: linear-gradient(135deg, #EC134A, #6366F1);
            color: #FFFFFF;
            font-size: 11.5px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 4px 16px;
            border-radius: 999px;
            white-space: nowrap;
        }

        .price-amount {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 46px;
            font-weight: 800;
            line-height: 1;
        }

        .pricing-feature-list {
            list-style: none;
            padding: 0;
            margin: 24px 0 30px;
        }

        .pricing-feature-list li {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            margin-bottom: 12px;
            font-weight: 500;
        }

        /* =====================================================================
           8. SAAS CTA BANNER & FOOTER
           ===================================================================== */
        .saas-cta-banner {
            background: linear-gradient(135deg, #090D16 0%, #1E1B4B 55%, #3B0764 100%);
            border-radius: 28px;
            padding: 64px 40px;
            color: #FFFFFF;
            position: relative;
            overflow: hidden;
            box-shadow: 0 25px 60px -15px rgba(15, 23, 42, 0.3);
        }

        .saas-footer {
            background: #090D16;
            color: #94A3B8;
            padding: 72px 0 36px;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
        }

        .saas-footer h6 {
            color: #FFFFFF;
            font-weight: 700;
            font-size: 14px;
            margin-bottom: 18px;
        }

        .saas-footer-links {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .saas-footer-links li {
            margin-bottom: 10px;
        }

        .saas-footer-links a {
            color: #94A3B8;
            text-decoration: none;
            font-size: 13.5px;
            transition: color 0.15s;
        }

        .saas-footer-links a:hover {
            color: #FFFFFF;
        }
    </style>
</head>
<body>

    <!-- =====================================================================
         1. STICKY GLASSMORPHIC SAAS NAVBAR
         ===================================================================== -->
    <header class="saas-navbar">
        <div class="container d-flex align-items-center justify-content-between">
            <a href="{{ url('/') }}" class="saas-brand">
                @if(!empty($globalSiteLogo))
                    <img src="{{ $globalSiteLogo }}" alt="{{ $globalSiteName ?? 'SiteBuilder' }}" style="width: 38px; height: 38px; object-fit: contain; border-radius: 9px;">
                @else
                    <svg width="38" height="38" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect width="48" height="48" rx="12" fill="#0F172A"/>
                        <path d="M24 8L10 16V21L24 29L38 21V16L24 8Z" fill="#EC134A"/>
                        <path d="M10 25L24 33L38 25V29L24 37L10 29V25Z" fill="#6366F1"/>
                        <path d="M10 32L24 40L38 32V36L24 44L10 36V32Z" fill="#38BDF8"/>
                    </svg>
                @endif
                <div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="fw-bolder fs-5 text-dark font-display">{{ $globalSiteName ?? 'SiteBuilder' }}</span>
                        <span class="saas-brand-badge">Cloud SaaS</span>
                    </div>
                    <small class="text-muted d-block" style="font-size: 11px; line-height: 1;">{{ $globalSiteTagline ?? 'Multi-Website SaaS Engine' }}</small>
                </div>
            </a>

            <ul class="saas-nav-links d-none d-lg-flex">
                <li>
                    <a href="#templates">
                        <span>Industry Demos</span>
                        <span class="nav-count-pill">{{ $activeSites->count() }} Live</span>
                    </a>
                </li>
                <li><a href="#features">Platform Features</a></li>
                <li><a href="#how-it-works">How It Works</a></li>
                <li><a href="#pricing">Pricing</a></li>
                <li><a href="#faq">FAQ</a></li>
            </ul>

            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('login') }}" class="btn-saas-ghost d-none d-sm-inline-block">Sign In</a>
                <a href="{{ route('admin.dashboard') }}" class="btn-saas-ghost d-none d-md-inline-block">
                    <i class="bi bi-grid-1x2-fill me-1 text-danger"></i> Admin Console
                </a>
                <a href="#pricing" class="btn-saas-primary">
                    <span>Start Free Trial</span>
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </header>

    <!-- =====================================================================
         2. ULTRA-MODERN SAAS HERO SECTION
         ===================================================================== -->
    <section class="saas-hero text-center">
        <div class="hero-orb-1"></div>
        <div class="hero-orb-2"></div>

        <div class="container position-relative" style="z-index: 5;">
            <a href="#templates" class="hero-pill-announcement">
                <span class="hero-pill-tag">New v3.5 Cloud</span>
                <span>{{ $activeSites->count() }}+ Ready-to-Deploy Industry Websites in One SaaS Hub</span>
                <i class="bi bi-arrow-right text-danger"></i>
            </a>

            <h1 class="saas-hero-title">
                Launch Production-Ready <br class="d-none d-md-inline">
                <span class="text-gradient-saas">Industry Websites in Seconds</span>
            </h1>

            <p class="saas-hero-sub">
                The all-in-one modular SaaS website platform for agencies, enterprises, and creators. Activate any industry niche—from <strong>Real Estate, Healthcare &amp; Pharmacy</strong> to <strong>Gym, Hajj &amp; Kids Learning</strong>—with isolated databases, visual CMS, and instant domain routing.
            </p>

            <div class="d-flex align-items-center justify-content-center flex-wrap gap-3">
                <a href="#templates" class="btn-saas-primary btn-hero-lg">
                    <i class="bi bi-rocket-takeoff-fill"></i>
                    <span>Explore {{ $activeSites->count() }} Live Demos</span>
                </a>
                <a href="{{ route('admin.websites') }}" class="btn-hero-lg btn-hero-secondary">
                    <i class="bi bi-sliders2-vertical"></i>
                    <span>Open SaaS Control Panel</span>
                </a>
            </div>

            <!-- Trust Checks Under CTA -->
            <div class="d-flex align-items-center justify-content-center flex-wrap gap-4 mt-4 pt-2 text-white-50 small fw-medium">
                <span><i class="bi bi-check-circle-fill text-success me-1"></i> Instant 1-Click Deployment</span>
                <span><i class="bi bi-check-circle-fill text-success me-1"></i> 100% Isolated Tenant Tables</span>
                <span><i class="bi bi-check-circle-fill text-success me-1"></i> Custom Domain &amp; White-Label Ready</span>
            </div>

            <!-- Interactive SaaS Cloud Console Preview Frame -->
            <div class="hero-console-preview">
                <div class="console-topbar">
                    <div class="mac-dots">
                        <span class="mac-dot" style="background: #EF4444;"></span>
                        <span class="mac-dot" style="background: #F59E0B;"></span>
                        <span class="mac-dot" style="background: #10B981;"></span>
                    </div>
                    <div class="console-url-bar">
                        <i class="bi bi-lock-fill text-success"></i>
                        <span>cloud.{{ strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $globalSiteName ?? 'sitebuilder')) }}.app/workspace/active-tenants</span>
                    </div>
                    <div class="d-none d-sm-flex align-items-center gap-2">
                        <span class="badge bg-success-subtle text-success rounded-pill px-2 py-1" style="font-size: 11px;">
                            ● {{ $activeSites->count() }} Active Modules
                        </span>
                    </div>
                </div>

                <div class="console-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-3 col-6">
                            <div class="console-stat-card">
                                <div class="text-white-50 small mb-1">Active Industry Modules</div>
                                <div class="fs-3 fw-bolder text-white font-display">{{ $activeSites->count() }} Websites</div>
                                <div class="small text-success mt-1"><i class="bi bi-lightning-charge-fill"></i> Multi-Tenant Hub Mode</div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="console-stat-card">
                                <div class="text-white-50 small mb-1">Database Isolation</div>
                                <div class="fs-3 fw-bolder text-white font-display">100% Prefixed</div>
                                <div class="small text-info mt-1"><i class="bi bi-shield-lock-fill"></i> Zero Schema Collision</div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="console-stat-card">
                                <div class="text-white-50 small mb-1">Global Admin &amp; CMS</div>
                                <div class="fs-3 fw-bolder text-white font-display">Unified Hub</div>
                                <div class="small text-warning mt-1"><i class="bi bi-palette-fill"></i> Visual Theme Builder</div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="console-stat-card">
                                <div class="text-white-50 small mb-1">Cloud Uptime SLA</div>
                                <div class="fs-3 fw-bolder text-white font-display">99.99%</div>
                                <div class="small text-success mt-1"><i class="bi bi-check2-all"></i> Production Ready</div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Live Module Pills inside Console Preview -->
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 pt-2 border-top border-secondary border-opacity-25">
                        <div class="d-flex align-items-center flex-wrap gap-2">
                            <span class="text-white-50 small fw-semibold me-1">Quick Launch:</span>
                            @foreach($activeSites as $quickSite)
                                <a href="{{ url('/' . $quickSite->slug) }}" class="badge text-decoration-none rounded-pill px-3 py-2"
                                   style="background: rgba(255,255,255,0.08); color: #E2E8F0; border: 1px solid rgba(255,255,255,0.12); font-weight: 600;">
                                    <span class="text-success me-1">●</span> {{ $quickSite->module }}
                                </a>
                            @endforeach
                        </div>
                        <a href="{{ route('admin.websites') }}" class="text-info small fw-bold text-decoration-none">
                            Manage All Tenants <i class="bi bi-arrow-up-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- =====================================================================
         3. SAAS METRICS & ARCHITECTURE BAR
         ===================================================================== -->
    <section class="saas-metrics-strip">
        <div class="container">
            <div class="row g-3 align-items-center">
                <div class="col-6 col-md-3">
                    <div class="metric-item">
                        <div class="metric-value">{{ $activeSites->count() }}+</div>
                        <div class="metric-label">Turnkey Industry Modules</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="metric-item">
                        <div class="metric-value">&lt; 30 Sec</div>
                        <div class="metric-label">Average Website Deployment</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="metric-item">
                        <div class="metric-value">100%</div>
                        <div class="metric-label">Self-Contained Assets &amp; DB</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="metric-item">
                        <div class="metric-value">EN / বাংলা / AR</div>
                        <div class="metric-label">Multilingual &amp; RTL Ready</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- =====================================================================
         4. LIVE INDUSTRY TEMPLATES & DEMOS SHOWCASE (#templates)
         ===================================================================== -->
    <section class="saas-section" id="templates">
        <div class="container">
            <div class="text-center mb-5">
                <div class="section-eyebrow">
                    <i class="bi bi-collection-play-fill"></i>
                    <span>Live Interactive Demos</span>
                </div>
                <h2 class="section-heading">Explore Ready-to-Use Industry Websites</h2>
                <p class="section-subheading">
                    Every module below is a complete, standalone business application with its own bespoke frontend theme, database tables, and dedicated Admin CRUD suite.
                </p>
            </div>

            <!-- Interactive Category Filter Pills & Search -->
            <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-3 mb-4">
                <div class="filter-pills-wrap mb-0">
                    <button type="button" class="filter-pill-btn active" onclick="filterDemos('all', this)">
                        All Industries ({{ $activeSites->count() }})
                    </button>
                    <button type="button" class="filter-pill-btn" onclick="filterDemos('property', this)">
                        Real Estate &amp; Interior
                    </button>
                    <button type="button" class="filter-pill-btn" onclick="filterDemos('health', this)">
                        Healthcare &amp; Medical
                    </button>
                    <button type="button" class="filter-pill-btn" onclick="filterDemos('lifestyle', this)">
                        Fitness, Travel &amp; Kids
                    </button>
                    <button type="button" class="filter-pill-btn" onclick="filterDemos('tech', this)">
                        Tech &amp; Portfolio
                    </button>
                </div>

                <div class="position-relative" style="min-width: 260px;">
                    <i class="bi bi-search position-absolute text-muted" style="left: 14px; top: 50%; transform: translateY(-50%);"></i>
                    <input type="text" id="demoSearchInput" class="form-control rounded-pill ps-5 py-2"
                           placeholder="Search industry or domain..."
                           oninput="searchDemos(this.value)">
                </div>
            </div>

            @if($activeSites->isEmpty())
                <div class="text-center bg-white border rounded-4 p-5 shadow-sm mx-auto" style="max-width: 560px;">
                    <i class="bi bi-power text-danger" style="font-size: 48px;"></i>
                    <h4 class="fw-bold mt-3">No Modules Currently Active</h4>
                    <p class="text-muted small mb-4">Open the SaaS Admin Panel &rarr; Websites and toggle at least one industry module to launch your live demos.</p>
                    <a href="{{ route('admin.websites') }}" class="btn-saas-primary">
                        <i class="bi bi-globe2"></i> Open Manage Websites
                    </a>
                </div>
            @else
                <div class="row g-4" id="demosGrid">
                    @foreach($activeSites as $site)
                        @php
                            $modLower = strtolower($site->module_name . ' ' . $site->module);
                            $catGroup = 'lifestyle';
                            if (str_contains($modLower, 'real') || str_contains($modLower, 'interior')) {
                                $catGroup = 'property';
                            } elseif (str_contains($modLower, 'pharma') || str_contains($modLower, 'clinic') || str_contains($modLower, 'ivf')) {
                                $catGroup = 'health';
                            } elseif (str_contains($modLower, 'port') || str_contains($modLower, 'tech') || str_contains($modLower, 'soft')) {
                                $catGroup = 'tech';
                            }
                        @endphp
                        <div class="col-md-6 col-lg-4 demo-card-col"
                             data-category="{{ $catGroup }}"
                             data-keywords="{{ strtolower($site->name . ' ' . $site->module . ' ' . $site->domain . ' ' . $site->slug) }}">
                            <div class="saas-template-card">
                                <!-- Top Browser Bar -->
                                <div class="template-browser-bar">
                                    <div class="mac-dots">
                                        <span class="mac-dot" style="background: #F87171;"></span>
                                        <span class="mac-dot" style="background: #FBBF24;"></span>
                                        <span class="mac-dot" style="background: #34D399;"></span>
                                    </div>
                                    <span class="template-domain-chip">
                                        <i class="bi bi-globe2 text-primary"></i>
                                        {{ $site->domain }}
                                    </span>
                                    <span class="status-pulse-dot" title="Live & Operational"></span>
                                </div>

                                <!-- Preview Image -->
                                <div class="template-thumb-box">
                                    <img src="{{ $site->thumbnail }}" alt="{{ $site->name }}">
                                    <span class="template-category-badge">{{ $site->module }}</span>
                                </div>

                                <!-- Card Body -->
                                <div class="template-card-body">
                                    <div>
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <h4 class="fw-bolder mb-0 font-display" style="font-size: 20px; color: #0F172A;">
                                                {{ $site->name }}
                                            </h4>
                                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1" style="font-size: 11px;">
                                                Live Demo
                                            </span>
                                        </div>

                                        <p class="text-muted small mb-3" style="line-height: 1.55;">
                                            {{ $site->description ?? 'Turnkey industry website module with standalone frontend theme, isolated database, and full admin CRUD.' }}
                                        </p>

                                        <div class="mb-3 px-3 py-2 rounded-3 bg-light border d-flex align-items-center justify-content-between small">
                                            <span class="text-muted fw-medium">Tenant Endpoint:</span>
                                            <code class="fw-bold text-dark">/{{ $site->slug }}</code>
                                        </div>
                                    </div>

                                    <div class="d-flex gap-2 pt-1">
                                        <a href="{{ url('/' . $site->slug) }}" class="btn-saas-primary flex-grow-1 justify-content-center">
                                            <span>Live Demo</span>
                                            <i class="bi bi-box-arrow-up-right"></i>
                                        </a>
                                        <button type="button"
                                                class="btn btn-dark rounded-3 px-3 fw-semibold small d-inline-flex align-items-center gap-1"
                                                onclick="openDeployModal(@js($site->name), @js($site->module), @js($site->thumbnail), @js(url('/' . $site->slug)))"
                                                title="Deploy This SaaS Template">
                                            <i class="bi bi-rocket-takeoff"></i>
                                            <span>Use Template</span>
                                        </button>
                                        @if($site->admin_route && Route::has($site->admin_route))
                                            <a href="{{ route($site->admin_route) }}"
                                               class="btn btn-outline-secondary rounded-3 px-3 d-inline-flex align-items-center"
                                               title="Configure in Admin CMS">
                                                <i class="bi bi-sliders"></i>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <!-- =====================================================================
         5. BENTO-GRID SAAS PLATFORM FEATURES (#features)
         ===================================================================== -->
    <section class="saas-section bg-white border-top border-bottom" id="features">
        <div class="container">
            <div class="text-center mb-5">
                <div class="section-eyebrow">
                    <i class="bi bi-cpu-fill"></i>
                    <span>Enterprise SaaS Architecture</span>
                </div>
                <h2 class="section-heading">Built for Multi-Tenant Scale &amp; Speed</h2>
                <p class="section-subheading">
                    Engineered with strict modular isolation so you can host dozens of industry vertical websites or deploy single-tenant client projects with zero friction.
                </p>
            </div>

            <div class="row g-4">
                <div class="col-md-6 col-lg-4">
                    <div class="bento-card">
                        <div class="bento-icon-box" style="background: #FFE4EC; color: #EC134A;">
                            <i class="bi bi-diagram-3-fill"></i>
                        </div>
                        <h4 class="fw-bold fs-5 mb-2">Dual Routing Engine</h4>
                        <p class="text-muted small mb-0" style="line-height: 1.65;">
                            Run all modules simultaneously as a <strong>Multi-Theme SaaS Showcase</strong> (`/{slug}`), or toggle a single module active to take over the root domain (`/`) automatically.
                        </p>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="bento-card">
                        <div class="bento-icon-box" style="background: #E0E7FF; color: #4F46E5;">
                            <i class="bi bi-database-fill-lock"></i>
                        </div>
                        <h4 class="fw-bold fs-5 mb-2">Zero-Collision Prefixed DB</h4>
                        <p class="text-muted small mb-0" style="line-height: 1.65;">
                            Every industry module enforces its own table namespace (`int_`, `re_`, `pharma_`, `gym_`, `hajj_`, `wl_`) and self-contained media storage folder.
                        </p>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="bento-card">
                        <div class="bento-icon-box" style="background: #DCFCE7; color: #16A34A;">
                            <i class="bi bi-magic"></i>
                        </div>
                        <h4 class="fw-bold fs-5 mb-2">Unified Visual CMS</h4>
                        <p class="text-muted small mb-0" style="line-height: 1.65;">
                            Manage Menus, Sliders, Media Galleries, Video Libraries, Custom Pages, Color Themes, and SEO settings from one intuitive global admin shell.
                        </p>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="bento-card">
                        <div class="bento-icon-box" style="background: #FEF3C7; color: #D97706;">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </div>
                        <h4 class="fw-bold fs-5 mb-2">1-Click Default Restore</h4>
                        <p class="text-muted small mb-0" style="line-height: 1.65;">
                            Testing changes or running public client sandboxes? Reset any website module 100% back to its original default layout, text, and artwork in one click.
                        </p>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="bento-card">
                        <div class="bento-icon-box" style="background: #E0F2FE; color: #0284C7;">
                            <i class="bi bi-translate"></i>
                        </div>
                        <h4 class="fw-bold fs-5 mb-2">Multilingual &amp; Portals</h4>
                        <p class="text-muted small mb-0" style="line-height: 1.65;">
                            Built-in support for English, Bengali (বাংলা), and Arabic (RTL) along with interactive Member Portals, QR Check-in passes, and Story Readers.
                        </p>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="bento-card">
                        <div class="bento-icon-box" style="background: #F3E8FF; color: #9333EA;">
                            <i class="bi bi-terminal-fill"></i>
                        </div>
                        <h4 class="fw-bold fs-5 mb-2">Instant Client Handover CLI</h4>
                        <p class="text-muted small mb-0" style="line-height: 1.65;">
                            Delivering a standalone website to a client? Run <code>php artisan demo:handover {Module} --force</code> to package only their chosen module cleanly.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- =====================================================================
         6. HOW IT WORKS — 3-STEP SAAS WORKFLOW (#how-it-works)
         ===================================================================== -->
    <section class="saas-section" id="how-it-works">
        <div class="container">
            <div class="text-center mb-5">
                <div class="section-eyebrow">
                    <i class="bi bi-lightning-fill"></i>
                    <span>Simple 3-Step Workflow</span>
                </div>
                <h2 class="section-heading">From Idea to Live Website in 3 Steps</h2>
                <p class="section-subheading">
                    No coding required. Pick your industry template, customize your content, and publish immediately.
                </p>
            </div>

            <div class="row g-4">
                <div class="col-md-4">
                    <div class="step-card">
                        <div class="step-number-badge">01</div>
                        <h4 class="fw-bold fs-5 mb-2">Select Your Industry Module</h4>
                        <p class="text-muted small mb-0" style="line-height: 1.65;">
                            Choose from our growing catalog of specialized business verticals—Real Estate, Interior Design, Pharmacy, Fertility Clinic, Gym, Hajj &amp; Umrah, or Kids Learning.
                        </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="step-card">
                        <div class="step-number-badge" style="background: var(--saas-primary);">02</div>
                        <h4 class="fw-bold fs-5 mb-2">Customize via Admin CMS</h4>
                        <p class="text-muted small mb-0" style="line-height: 1.65;">
                            Update your brand logo, hero banners, pricing packages, team members, and navigation menus in real time from the unified Admin Dashboard.
                        </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="step-card">
                        <div class="step-number-badge" style="background: var(--saas-indigo);">03</div>
                        <h4 class="fw-bold fs-5 mb-2">Connect Domain &amp; Scale</h4>
                        <p class="text-muted small mb-0" style="line-height: 1.65;">
                            Map your custom domain, accept online bookings &amp; payments, and scale effortlessly with high-speed caching and isolated tenant security.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- =====================================================================
         7. INTERACTIVE SAAS PRICING SECTION (#pricing)
         ===================================================================== -->
    <section class="saas-section bg-white border-top" id="pricing">
        <div class="container">
            <div class="text-center mb-5">
                <div class="section-eyebrow">
                    <i class="bi bi-tags-fill"></i>
                    <span>Transparent SaaS Pricing</span>
                </div>
                <h2 class="section-heading">Flexible Plans for Every Business</h2>
                <p class="section-subheading mb-4">
                    Start free and upgrade as you grow. Switch between monthly and yearly billing anytime.
                </p>

                <!-- Monthly / Yearly Billing Toggle -->
                <div class="d-inline-flex align-items-center gap-2 bg-light border rounded-pill p-1">
                    <button type="button" id="btnMonthly" class="btn btn-sm rounded-pill px-3 py-2 fw-bold bg-dark text-white" onclick="toggleBilling('monthly')">
                        Monthly Billing
                    </button>
                    <button type="button" id="btnYearly" class="btn btn-sm rounded-pill px-3 py-2 fw-bold text-secondary" onclick="toggleBilling('yearly')">
                        Yearly Billing <span class="badge bg-success-subtle text-success rounded-pill ms-1">Save 20%</span>
                    </button>
                </div>
            </div>

            <div class="row g-4 align-items-stretch">
                <!-- Plan 1: Starter -->
                <div class="col-lg-4">
                    <div class="pricing-card">
                        <div>
                            <div class="fw-bold text-uppercase small text-muted mb-1">Starter</div>
                            <h4 class="fw-bolder mb-2">Single Business</h4>
                            <p class="text-muted small mb-4">Perfect for launching one standalone business website on your custom domain.</p>

                            <div class="d-flex align-items-baseline gap-1 mb-2">
                                <span class="price-amount" data-monthly="$29" data-yearly="$23">$29</span>
                                <span class="text-muted fw-semibold small">/ month</span>
                            </div>
                            <small class="text-muted d-block mb-3 billing-note">Billed monthly</small>

                            <ul class="pricing-feature-list">
                                <li><i class="bi bi-check-circle-fill text-success"></i> <strong>1 Active Industry Module</strong></li>
                                <li><i class="bi bi-check-circle-fill text-success"></i> Custom Domain &amp; Free SSL</li>
                                <li><i class="bi bi-check-circle-fill text-success"></i> Full Visual CMS &amp; Media Library</li>
                                <li><i class="bi bi-check-circle-fill text-success"></i> Lead &amp; Booking Management</li>
                                <li><i class="bi bi-check-circle-fill text-success"></i> 1-Click Default Template Reset</li>
                            </ul>
                        </div>
                        <a href="{{ route('admin.websites') }}" class="btn btn-outline-dark w-100 py-3 rounded-3 fw-bold">
                            Get Started with Starter
                        </a>
                    </div>
                </div>

                <!-- Plan 2: Agency Pro (Featured) -->
                <div class="col-lg-4">
                    <div class="pricing-card featured">
                        <span class="popular-ribbon"><i class="bi bi-stars me-1"></i> Most Popular</span>
                        <div>
                            <div class="fw-bold text-uppercase small text-info mb-1">Agency Pro</div>
                            <h4 class="fw-bolder mb-2 text-white">Multi-Website Hub</h4>
                            <p class="text-white-50 small mb-4">Ideal for agencies and growing businesses managing multiple industry verticals.</p>

                            <div class="d-flex align-items-baseline gap-1 mb-2">
                                <span class="price-amount text-white" data-monthly="$79" data-yearly="$63">$79</span>
                                <span class="text-white-50 fw-semibold small">/ month</span>
                            </div>
                            <small class="text-info d-block mb-3 billing-note">Billed monthly</small>

                            <ul class="pricing-feature-list text-white-50">
                                <li class="text-white"><i class="bi bi-check-circle-fill text-danger"></i> <strong>All {{ $activeSites->count() }}+ Industry Modules Unlocked</strong></li>
                                <li class="text-white"><i class="bi bi-check-circle-fill text-danger"></i> Up to 15 Active Client Websites</li>
                                <li class="text-white"><i class="bi bi-check-circle-fill text-danger"></i> Multilingual (EN / বাংলা / AR + RTL)</li>
                                <li class="text-white"><i class="bi bi-check-circle-fill text-danger"></i> Member Portals &amp; QR Check-in Suite</li>
                                <li class="text-white"><i class="bi bi-check-circle-fill text-danger"></i> Role-Based Team Access &amp; 2FA</li>
                            </ul>
                        </div>
                        <a href="{{ route('admin.websites') }}" class="btn-saas-primary w-100 py-3 justify-content-center">
                            <span>Start 14-Day Free Trial</span>
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Plan 3: Enterprise SaaS -->
                <div class="col-lg-4">
                    <div class="pricing-card">
                        <div>
                            <div class="fw-bold text-uppercase small text-primary mb-1">Enterprise</div>
                            <h4 class="fw-bolder mb-2">White-Label SaaS</h4>
                            <p class="text-muted small mb-4">Full platform ownership with unlimited tenants and automated client handover CLI.</p>

                            <div class="d-flex align-items-baseline gap-1 mb-2">
                                <span class="price-amount" data-monthly="$199" data-yearly="$159">$199</span>
                                <span class="text-muted fw-semibold small">/ month</span>
                            </div>
                            <small class="text-muted d-block mb-3 billing-note">Billed monthly</small>

                            <ul class="pricing-feature-list">
                                <li><i class="bi bi-check-circle-fill text-primary"></i> <strong>Unlimited Active Websites &amp; Domains</strong></li>
                                <li><i class="bi bi-check-circle-fill text-primary"></i> 100% White-Label Custom Branding</li>
                                <li><i class="bi bi-check-circle-fill text-primary"></i> <code>demo:handover</code> Standalone Export CLI</li>
                                <li><i class="bi bi-check-circle-fill text-primary"></i> Payment Gateway &amp; Wallet Settlement</li>
                                <li><i class="bi bi-check-circle-fill text-primary"></i> Dedicated Priority Engineering Support</li>
                            </ul>
                        </div>
                        <a href="{{ route('admin.settings') }}" class="btn btn-dark w-100 py-3 rounded-3 fw-bold">
                            Launch Enterprise Console
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- =====================================================================
         8. FREQUENTLY ASKED QUESTIONS (#faq)
         ===================================================================== -->
    <section class="saas-section" id="faq">
        <div class="container" style="max-width: 860px;">
            <div class="text-center mb-5">
                <div class="section-eyebrow">
                    <i class="bi bi-question-circle-fill"></i>
                    <span>Got Questions?</span>
                </div>
                <h2 class="section-heading">Frequently Asked Questions</h2>
                <p class="section-subheading">
                    Everything you need to know about our modular multi-website SaaS architecture.
                </p>
            </div>

            <div class="accordion accordion-flush bg-white border rounded-4 p-3 shadow-sm" id="saasFaqAccordion">
                <div class="accordion-item border-bottom">
                    <h2 class="accordion-header">
                        <button class="accordion-button fw-bold py-3" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                            How does Single Active Module mode vs. Multi-Module SaaS Hub mode work?
                        </button>
                    </h2>
                    <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#saasFaqAccordion">
                        <div class="accordion-body text-muted small" style="line-height: 1.7;">
                            When <strong>multiple modules</strong> are turned Active in <em>Admin &rarr; Websites</em>, the root URL (<code>/</code>) displays this SaaS Hub landing page and each module runs on its own slug (e.g., <code>/wonder-learn</code>, <code>/gym-fitness</code>, <code>/real-estate</code>). When <strong>only 1 module</strong> is Active, that single website automatically runs directly on the root domain (<code>/</code>)!
                        </div>
                    </div>
                </div>

                <div class="accordion-item border-bottom">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed fw-bold py-3" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                            Are the databases and uploaded media files isolated between modules?
                        </button>
                    </h2>
                    <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#saasFaqAccordion">
                        <div class="accordion-body text-muted small" style="line-height: 1.7;">
                            Yes! Every module has its own strict table prefix (<code>int_</code>, <code>re_</code>, <code>pharma_</code>, <code>gym_</code>, <code>hajj_</code>, <code>wl_</code>) and stores its theme assets and uploads inside <code>Modules/{Module}/Public/</code> so modules never collide.
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed fw-bold py-3" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                            Can we convert this into a full automated subscription SaaS later?
                        </button>
                    </h2>
                    <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#saasFaqAccordion">
                        <div class="accordion-body text-muted small" style="line-height: 1.7;">
                            Absolutely. The core database already tracks websites, domains, roles, permissions, and PGW transactions, making it ready to hook into automated tenant provisioning and recurring billing whenever you are ready.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- =====================================================================
         9. BOTTOM CTA BANNER & MODERN SAAS FOOTER
         ===================================================================== -->
    <section class="pb-5">
        <div class="container">
            <div class="saas-cta-banner text-center">
                <span class="badge bg-white bg-opacity-10 text-white border border-white border-opacity-25 rounded-pill px-3 py-2 mb-3">
                    <i class="bi bi-stars text-warning me-1"></i> Ready for Instant Deployment
                </span>
                <h2 class="fw-bolder font-display mb-3" style="font-size: clamp(28px, 3.5vw, 44px);">
                    Start Building Your Next Industry Website Today
                </h2>
                <p class="text-white-50 mx-auto mb-4" style="max-width: 600px; font-size: 16px;">
                    Launch a live demo in one click or open the unified Admin Console to customize themes, menus, and content.
                </p>
                <div class="d-flex align-items-center justify-content-center flex-wrap gap-3">
                    <a href="{{ route('admin.websites') }}" class="btn-saas-primary btn-hero-lg">
                        <i class="bi bi-globe2"></i>
                        <span>Manage Websites in Admin</span>
                    </a>
                    <a href="#templates" class="btn-hero-lg btn-hero-secondary">
                        <span>Browse {{ $activeSites->count() }} Live Demos</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <footer class="saas-footer">
        <div class="container">
            <div class="row g-4 pb-5 border-bottom border-secondary border-opacity-25">
                <div class="col-lg-4">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <svg width="32" height="32" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect width="48" height="48" rx="10" fill="#1E293B"/>
                            <path d="M24 8L10 16V21L24 29L38 21V16L24 8Z" fill="#EC134A"/>
                            <path d="M10 25L24 33L38 25V29L24 37L10 29V25Z" fill="#6366F1"/>
                            <path d="M10 32L24 40L38 32V36L24 44L10 36V32Z" fill="#38BDF8"/>
                        </svg>
                        <span class="fw-bolder fs-5 text-white font-display">{{ $globalSiteName ?? 'SiteBuilder' }} Cloud</span>
                    </div>
                    <p class="small mb-3" style="max-width: 320px; line-height: 1.6;">
                        Next-generation modular multi-website SaaS hub. Build, manage, and scale standalone industry websites from a single unified codebase.
                    </p>
                    <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill" style="background: rgba(16, 185, 129, 0.12); border: 1px solid rgba(16, 185, 129, 0.3); color: #34D399; font-size: 12px; font-weight: 600;">
                        <span>●</span> All Cloud Systems Operational
                    </div>
                </div>

                <div class="col-6 col-lg-2">
                    <h6>Platform</h6>
                    <ul class="saas-footer-links">
                        <li><a href="#templates">Live Demos</a></li>
                        <li><a href="#features">Bento Features</a></li>
                        <li><a href="#how-it-works">How It Works</a></li>
                        <li><a href="#pricing">Pricing Plans</a></li>
                    </ul>
                </div>

                <div class="col-6 col-lg-3">
                    <h6>Active Industry Modules</h6>
                    <ul class="saas-footer-links">
                        @foreach($activeSites->take(6) as $footSite)
                            <li>
                                <a href="{{ url('/' . $footSite->slug) }}">
                                    {{ $footSite->name }} ({{ $footSite->module }})
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="col-lg-3">
                    <h6>Admin &amp; Console</h6>
                    <ul class="saas-footer-links">
                        <li><a href="{{ route('admin.dashboard') }}">Global Dashboard</a></li>
                        <li><a href="{{ route('admin.websites') }}">Manage Websites</a></li>
                        <li><a href="{{ route('admin.themes') }}">Visual Theme Presets</a></li>
                        <li><a href="{{ route('admin.settings') }}">System Configuration</a></li>
                    </ul>
                </div>
            </div>

            <div class="d-flex flex-column flex-sm-row align-items-center justify-content-between pt-4 small">
                <div>
                    &copy; {{ date('Y') }} {{ $globalSiteName ?? 'SiteBuilder' }} Cloud SaaS. All rights reserved.
                </div>
                <div class="d-flex gap-3 mt-2 mt-sm-0">
                    <a href="#faq" class="text-decoration-none text-secondary">Privacy Policy</a>
                    <a href="#faq" class="text-decoration-none text-secondary">Terms of Service</a>
                    <a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-secondary">Admin Login</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- SaaS "Use Template / Instant Provisioning" Preview Modal -->
    <div class="modal fade" id="saasDeployModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
                <div class="modal-header border-0 text-white" style="background: linear-gradient(135deg, #090D16 0%, #1E1B4B 100%);">
                    <div>
                        <span class="badge bg-danger rounded-pill mb-1" id="deployModalModule">Industry Template</span>
                        <h5 class="modal-title fw-bolder font-display mb-0" id="deployModalTitle">Deploy Website</h5>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="d-flex align-items-center gap-3 p-2 rounded-3 bg-light border mb-3">
                        <img id="deployModalThumb" src="" alt="" style="width: 72px; height: 50px; object-fit: cover; border-radius: 8px;">
                        <div>
                            <div class="fw-bold small text-dark" id="deployModalName">Template Name</div>
                            <small class="text-success fw-semibold"><i class="bi bi-check-circle-fill me-1"></i> Pre-configured DB, Theme &amp; Admin CMS</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Your Business / Website Name</label>
                        <input type="text" class="form-control rounded-3" placeholder="e.g. Apex Global Ltd.">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Choose Subdomain or Custom Domain</label>
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="yourbrand">
                            <span class="input-group-text bg-light fw-semibold small">.sitebuilder.cloud</span>
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-bold">Select SaaS Subscription Tier</label>
                        <select class="form-select rounded-3">
                            <option>Starter Plan — $29/month (14-Day Free Trial)</option>
                            <option selected>Agency Pro Plan — $79/month (All Features Unlocked)</option>
                            <option>Enterprise White-Label — $199/month</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light justify-content-between">
                    <a href="#" id="deployModalDemoBtn" class="btn btn-outline-secondary rounded-3 fw-semibold">
                        <i class="bi bi-eye me-1"></i> View Live Demo
                    </a>
                    <a href="{{ route('admin.websites') }}" class="btn-saas-primary">
                        <i class="bi bi-rocket-takeoff-fill"></i>
                        <span>Provision in Admin Console</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function openDeployModal(name, mod, thumb, demoUrl) {
            document.getElementById('deployModalTitle').textContent = 'Deploy ' + name;
            document.getElementById('deployModalName').textContent = name + ' (' + mod + ' Module)';
            document.getElementById('deployModalModule').textContent = mod + ' SaaS Template';
            document.getElementById('deployModalThumb').src = thumb;
            document.getElementById('deployModalDemoBtn').href = demoUrl;
            new bootstrap.Modal(document.getElementById('saasDeployModal')).show();
        }
        // 1. Category Filter for Live Demos
        function filterDemos(cat, btn) {
            document.querySelectorAll('.filter-pill-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            document.querySelectorAll('.demo-card-col').forEach(col => {
                const itemCat = col.getAttribute('data-category');
                col.style.display = (cat === 'all' || itemCat === cat) ? '' : 'none';
            });
        }

        // 2. Live Search Filter for Demos
        function searchDemos(query) {
            const q = (query || '').trim().toLowerCase();
            document.querySelectorAll('.demo-card-col').forEach(col => {
                const kw = col.getAttribute('data-keywords') || '';
                col.style.display = (!q || kw.includes(q)) ? '' : 'none';
            });
        }

        // 3. Monthly / Yearly SaaS Pricing Toggle
        function toggleBilling(cycle) {
            const btnM = document.getElementById('btnMonthly');
            const btnY = document.getElementById('btnYearly');

            if (cycle === 'yearly') {
                btnY.className = 'btn btn-sm rounded-pill px-3 py-2 fw-bold bg-dark text-white';
                btnM.className = 'btn btn-sm rounded-pill px-3 py-2 fw-bold text-secondary';
            } else {
                btnM.className = 'btn btn-sm rounded-pill px-3 py-2 fw-bold bg-dark text-white';
                btnY.className = 'btn btn-sm rounded-pill px-3 py-2 fw-bold text-secondary';
            }

            document.querySelectorAll('.price-amount').forEach(el => {
                el.textContent = el.getAttribute('data-' + cycle);
            });

            document.querySelectorAll('.billing-note').forEach(el => {
                el.textContent = cycle === 'yearly' ? 'Billed annually (20% discount applied)' : 'Billed monthly';
            });
        }
    </script>
</body>
</html>
