@php
    $activeCount = isset($activeModuleWebsites) ? $activeModuleWebsites->count() : 1;
    $basePrefix = $activeCount === 1 ? '' : '/real-estate';
    $homeUrl = $basePrefix === '' ? url('/') : url($basePrefix);
    $img = fn (string $file) => route('module.media.serve', ['module' => 'RealEstate', 'folder' => 'theme', 'filename' => $file . '.jpg']);
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Antixor Real Estate — Premium Properties, Better Living</title>
    
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5.3 & Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            --ax-dark: #0A2622;
            --ax-dark-2: #0E332E;
            --ax-dark-3: #133D37;
            --ax-gold: #DFB45B;
            --ax-gold-hover: #CCA047;
            --ax-cream: #F8F7F4;
            --ax-white: #FFFFFF;
            --ax-text: #1C2B28;
            --ax-muted: #6A7B76;
            --ax-border: #E8E6DF;
            --ax-border-light: #F0EFEA;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            color: var(--ax-text);
            background-color: var(--ax-white);
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
            overflow-x: hidden;
        }

        a {
            text-decoration: none;
            color: inherit;
            transition: all 0.2s ease;
        }

        .container {
            max-width: 1240px;
        }

        /* Gold Pill Button */
        .ax-btn-gold {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background-color: var(--ax-gold);
            color: var(--ax-dark);
            font-weight: 700;
            font-size: 13.5px;
            padding: 11px 26px;
            border-radius: 999px;
            border: none;
            transition: all 0.25s ease;
            box-shadow: 0 4px 14px rgba(223, 180, 91, 0.25);
            cursor: pointer;
            white-space: nowrap;
        }

        .ax-btn-gold:hover {
            background-color: var(--ax-gold-hover);
            color: var(--ax-dark);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(223, 180, 91, 0.35);
        }

        .ax-btn-gold i {
            font-size: 14px;
            transition: transform 0.2s ease;
        }

        .ax-btn-gold:hover i {
            transform: translateX(3px);
        }

        /* Eyebrow Label */
        .ax-eyebrow {
            font-size: 11.5px;
            font-weight: 800;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            color: var(--ax-gold);
            display: inline-block;
            margin-bottom: 8px;
        }

        .ax-eyebrow-dark {
            color: #A3843D;
        }

        /* Section Headings */
        .ax-title {
            font-size: 32px;
            font-weight: 800;
            line-height: 1.22;
            color: var(--ax-text);
            letter-spacing: -0.02em;
        }

        .ax-title-white {
            color: #FFFFFF;
        }

        .ax-link-arrow {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 13.5px;
            font-weight: 700;
            color: var(--ax-text);
            transition: all 0.2s ease;
        }

        .ax-link-arrow:hover {
            color: var(--ax-gold);
            transform: translateX(3px);
        }

        /* ================= 1. NAVBAR & HERO ================= */
        .ax-hero-section {
            background-color: var(--ax-dark);
            position: relative;
            padding-bottom: 80px;
            border-bottom-left-radius: 64px;
            overflow: visible;
        }

        .ax-hero-bg-media {
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            width: 60%;
            z-index: 1;
            overflow: hidden;
        }

        .ax-hero-bg-media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
        }

        .ax-hero-bg-gradient {
            position: absolute;
            inset: 0;
            background: linear-gradient(90deg, #0A2622 0%, #0A2622 24%, rgba(10, 38, 34, 0.85) 50%, rgba(10, 38, 34, 0.2) 80%, transparent 100%);
            z-index: 2;
        }

        /* Navbar */
        .ax-navbar {
            padding: 26px 0 20px;
            position: relative;
            z-index: 20;
        }

        .ax-brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .ax-brand-name {
            font-size: 19px;
            font-weight: 800;
            letter-spacing: 0.12em;
            color: #FFFFFF;
            line-height: 1;
        }

        .ax-brand-tag {
            font-size: 8.5px;
            font-weight: 700;
            letter-spacing: 0.28em;
            color: var(--ax-gold);
            margin-top: 3px;
        }

        .ax-nav-menu {
            display: flex;
            align-items: center;
            gap: 32px;
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .ax-nav-menu a {
            font-size: 14px;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.8);
        }

        .ax-nav-menu a:hover,
        .ax-nav-menu a.active {
            color: #FFFFFF;
        }

        .ax-search-icon-btn {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.08);
            color: #FFFFFF;
            border: 1px solid rgba(255, 255, 255, 0.15);
            font-size: 15px;
            transition: all 0.2s ease;
        }

        .ax-search-icon-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            color: #FFFFFF;
        }

        /* Hero Content */
        .ax-hero-content {
            padding: 55px 0 40px;
            position: relative;
            z-index: 5;
        }

        .ax-hero-heading {
            font-size: 52px;
            font-weight: 800;
            line-height: 1.14;
            color: #FFFFFF;
            letter-spacing: -0.025em;
            margin: 16px 0 20px;
        }

        .ax-hero-heading .gold-text {
            color: var(--ax-gold);
        }

        .ax-hero-desc {
            font-size: 15px;
            line-height: 1.7;
            color: rgba(255, 255, 255, 0.72);
            max-width: 440px;
            margin-bottom: 25px;
        }

        /* Floating 12+ Experience Badge */
        .ax-hero-badge {
            position: absolute;
            top: 25px;
            right: 0;
            background: rgba(10, 38, 34, 0.82);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-radius: 14px;
            padding: 12px 18px;
            display: flex;
            align-items: center;
            gap: 12px;
            color: #FFFFFF;
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.35);
            z-index: 10;
        }

        .ax-badge-icon {
            width: 38px;
            height: 38px;
            border-radius: 8px;
            background: rgba(223, 180, 91, 0.15);
            border: 1px solid rgba(223, 180, 91, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--ax-gold);
            font-size: 18px;
        }

        .ax-badge-val {
            font-size: 20px;
            font-weight: 800;
            line-height: 1;
            color: #FFFFFF;
        }

        .ax-badge-label {
            font-size: 10px;
            color: rgba(255, 255, 255, 0.7);
            margin-top: 2px;
            white-space: nowrap;
        }

        /* Search Filter Bar (Overlapping) */
        .ax-filter-container {
            position: relative;
            z-index: 30;
            margin-top: 30px;
        }

        .ax-filter-box-wrap {
            max-width: 1040px;
        }

        .ax-filter-tabs {
            display: inline-flex;
            gap: 8px;
            margin-bottom: 12px;
            padding-left: 4px;
        }

        .ax-tab-btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 9px 22px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 700;
            color: rgba(255, 255, 255, 0.85);
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.15);
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .ax-tab-btn.active {
            background: #FFFFFF;
            color: var(--ax-dark);
            border-color: #FFFFFF;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.12);
        }

        .ax-filter-card {
            background: #FFFFFF;
            border-radius: 20px;
            padding: 22px 28px;
            box-shadow: 0 20px 48px rgba(10, 38, 34, 0.16);
            border: 1px solid rgba(0, 0, 0, 0.04);
        }

        .ax-filter-fields {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .ax-field-item {
            flex: 1;
            position: relative;
        }

        .ax-field-label {
            display: block;
            font-size: 11px;
            font-weight: 700;
            color: var(--ax-muted);
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin-bottom: 3px;
        }

        .ax-field-select {
            width: 100%;
            border: none;
            background: transparent;
            font-size: 14.5px;
            font-weight: 600;
            color: var(--ax-text);
            outline: none;
            padding: 2px 0;
            cursor: pointer;
        }

        .ax-field-divider {
            width: 1px;
            height: 40px;
            background-color: var(--ax-border);
        }

        .ax-filter-search-btn {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            background-color: var(--ax-gold);
            color: var(--ax-dark);
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 19px;
            box-shadow: 0 6px 18px rgba(223, 180, 91, 0.35);
            transition: all 0.2s ease;
            cursor: pointer;
            flex-shrink: 0;
        }

        .ax-filter-search-btn:hover {
            background-color: var(--ax-gold-hover);
            transform: scale(1.05);
        }

        /* ================= 2. 4-COLUMN FEATURE ROW ================= */
        .ax-features-row {
            background-color: var(--ax-cream);
            padding: 60px 0 55px;
            border-bottom: 1px solid var(--ax-border);
        }

        .ax-feat-col {
            position: relative;
        }

        @media (min-width: 992px) {
            .ax-feat-col:not(:last-child)::after {
                content: '';
                position: absolute;
                top: 8px;
                right: 0;
                bottom: 8px;
                width: 1px;
                background-color: var(--ax-border);
            }
        }

        .ax-feat-box {
            display: flex;
            align-items: flex-start;
            gap: 16px;
            padding: 0 16px;
        }

        .ax-feat-icon {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            background: #FFFFFF;
            border: 1px solid var(--ax-border);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--ax-dark);
            font-size: 20px;
            flex-shrink: 0;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.03);
        }

        .ax-feat-title {
            font-size: 15.5px;
            font-weight: 800;
            color: var(--ax-text);
            margin-bottom: 4px;
        }

        .ax-feat-desc {
            font-size: 12.5px;
            color: var(--ax-muted);
            line-height: 1.5;
            margin: 0;
        }

        /* ================= 3. FEATURED PROPERTIES ================= */
        .ax-section-pad {
            padding: 85px 0;
        }

        .ax-prop-card {
            background: #FFFFFF;
            border: 1px solid var(--ax-border);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 6px 22px rgba(0, 0, 0, 0.04);
            transition: all 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .ax-prop-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 16px 38px rgba(10, 38, 34, 0.1);
            border-color: var(--ax-gold);
        }

        .ax-prop-img-box {
            position: relative;
            width: 100%;
            height: 230px;
            overflow: hidden;
        }

        .ax-prop-img-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.45s ease;
        }

        .ax-prop-card:hover .ax-prop-img-box img {
            transform: scale(1.06);
        }

        .ax-prop-badge {
            position: absolute;
            top: 14px;
            left: 14px;
            background: var(--ax-dark);
            color: #FFFFFF;
            font-size: 11px;
            font-weight: 700;
            padding: 5px 14px;
            border-radius: 999px;
            letter-spacing: 0.03em;
        }

        .ax-prop-heart {
            position: absolute;
            top: 14px;
            right: 14px;
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.92);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--ax-dark);
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
            border: none;
        }

        .ax-prop-heart:hover {
            background: #FFFFFF;
            color: #E63946;
            transform: scale(1.1);
        }

        .ax-prop-body {
            padding: 22px 24px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .ax-prop-loc {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            color: var(--ax-muted);
            margin-bottom: 6px;
            font-weight: 500;
        }

        .ax-prop-loc i {
            color: var(--ax-gold);
            font-size: 13px;
        }

        .ax-prop-title {
            font-size: 16.5px;
            font-weight: 800;
            color: var(--ax-text);
            margin-bottom: 10px;
            line-height: 1.35;
        }

        .ax-prop-price {
            font-size: 19px;
            font-weight: 800;
            color: var(--ax-dark);
            margin-bottom: 16px;
        }

        .ax-prop-price span {
            font-size: 13px;
            font-weight: 500;
            color: var(--ax-muted);
        }

        .ax-prop-meta {
            margin-top: auto;
            padding-top: 15px;
            border-top: 1px solid var(--ax-border-light);
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 12.5px;
            color: var(--ax-muted);
        }

        .ax-prop-meta span {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .ax-prop-meta i {
            color: var(--ax-text);
            font-size: 14px;
        }

        /* ================= 4. WHY CHOOSE ANTIXOR (DIAGONAL SPLIT BANNER) ================= */
        .ax-why-banner {
            position: relative;
            background-color: var(--ax-dark);
            min-height: 520px;
            overflow: hidden;
            display: flex;
            align-items: center;
        }

        .ax-why-bg-media {
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            width: 70%;
            z-index: 1;
        }

        .ax-why-bg-media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
        }

        .ax-why-angled-overlay {
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            width: 50%;
            background-color: var(--ax-dark);
            clip-path: polygon(0 0, 100% 0, 72% 100%, 0 100%);
            z-index: 2;
        }

        .ax-why-stats-bar {
            position: absolute;
            bottom: 32px;
            right: 8%;
            background: #FFFFFF;
            border-radius: 18px;
            padding: 18px 38px;
            box-shadow: 0 18px 45px rgba(0, 0, 0, 0.28);
            display: flex;
            align-items: center;
            gap: 34px;
            z-index: 10;
        }

        .ax-stat-col {
            text-align: center;
        }

        .ax-stat-col strong {
            font-size: 23px;
            font-weight: 800;
            color: var(--ax-dark);
            display: block;
            line-height: 1;
        }

        .ax-stat-col span {
            font-size: 11.5px;
            color: var(--ax-muted);
            margin-top: 4px;
            display: block;
            white-space: nowrap;
            font-weight: 500;
        }

        .ax-stat-div {
            width: 1px;
            height: 34px;
            background-color: var(--ax-border);
        }

        /* ================= 5. REAL ESTATE SOLUTIONS (SERVICES) ================= */
        .ax-services-section {
            background-color: var(--ax-cream);
            padding: 85px 0;
        }

        .ax-service-card {
            background: #FFFFFF;
            border: 1px solid var(--ax-border);
            border-radius: 20px;
            padding: 30px 26px 26px;
            height: 100%;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.03);
            position: relative;
        }

        .ax-service-card:hover {
            transform: translateY(-5px);
            border-color: var(--ax-gold);
            box-shadow: 0 14px 34px rgba(10, 38, 34, 0.08);
        }

        .ax-serv-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: var(--ax-cream);
            border: 1px solid var(--ax-border);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--ax-dark);
            font-size: 22px;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }

        .ax-service-card:hover .ax-serv-icon {
            background: var(--ax-gold);
            border-color: var(--ax-gold);
            color: var(--ax-dark);
        }

        .ax-serv-title {
            font-size: 16.5px;
            font-weight: 800;
            color: var(--ax-text);
            margin-bottom: 8px;
        }

        .ax-serv-desc {
            font-size: 13px;
            color: var(--ax-muted);
            line-height: 1.55;
            margin-bottom: 24px;
            flex-grow: 1;
        }

        .ax-serv-arrow-btn {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--ax-dark);
            color: #FFFFFF;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            border: none;
            transition: all 0.25s ease;
            margin-top: auto;
        }

        .ax-service-card:hover .ax-serv-arrow-btn {
            background: var(--ax-gold);
            color: var(--ax-dark);
        }

        /* ================= 6. POPULAR LOCATIONS ================= */
        .ax-locations-section {
            padding: 85px 0;
            background: #FFFFFF;
        }

        .ax-loc-box {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .ax-loc-card {
            border-radius: 20px;
            overflow: hidden;
            position: relative;
            height: 320px;
            display: block;
            box-shadow: 0 6px 22px rgba(0, 0, 0, 0.06);
            transition: all 0.35s ease;
        }

        .ax-loc-card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .ax-loc-box:hover .ax-loc-card img {
            transform: scale(1.08);
        }

        .ax-loc-info {
            padding: 0 4px;
        }

        .ax-loc-name {
            font-size: 17px;
            font-weight: 800;
            color: var(--ax-text);
            margin-bottom: 2px;
        }

        .ax-loc-count {
            font-size: 12px;
            color: var(--ax-muted);
            font-weight: 500;
        }

        /* ================= 7. SMART INVESTMENTS BANNER ================= */
        .ax-invest-section {
            background: var(--ax-dark);
            position: relative;
            overflow: hidden;
            padding: 90px 0;
        }

        .ax-invest-img {
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 48px rgba(0, 0, 0, 0.45);
            height: 380px;
        }

        .ax-invest-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .ax-invest-stat-box {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-radius: 16px;
            padding: 22px 26px;
            color: #FFFFFF;
            transition: all 0.3s ease;
            margin-bottom: 16px;
        }

        .ax-invest-stat-box:last-child {
            margin-bottom: 0;
        }

        .ax-invest-stat-box:hover {
            background: rgba(255, 255, 255, 0.09);
            border-color: var(--ax-gold);
            transform: translateX(5px);
        }

        .ax-stat-num {
            font-size: 26px;
            font-weight: 800;
            color: var(--ax-gold);
            line-height: 1;
            margin-bottom: 4px;
        }

        .ax-stat-desc {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.78);
            font-weight: 500;
            margin: 0;
        }

        /* ================= 8. TESTIMONIALS ================= */
        .ax-testimonials-section {
            background-color: var(--ax-cream);
            padding: 85px 0;
        }

        .ax-test-card {
            background: #FFFFFF;
            border: 1px solid var(--ax-border);
            border-radius: 20px;
            padding: 30px;
            height: 100%;
            display: flex;
            flex-direction: column;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.03);
            transition: all 0.25s ease;
        }

        .ax-test-card:hover {
            border-color: var(--ax-gold);
            box-shadow: 0 12px 32px rgba(10, 38, 34, 0.08);
            transform: translateY(-4px);
        }

        .ax-test-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--ax-gold);
            margin-bottom: 18px;
        }

        .ax-test-quote {
            font-size: 13.5px;
            line-height: 1.68;
            color: var(--ax-text);
            font-style: italic;
            margin-bottom: 18px;
            flex-grow: 1;
        }

        .ax-test-name {
            font-size: 15px;
            font-weight: 800;
            color: var(--ax-text);
            margin: 0;
        }

        .ax-test-role {
            font-size: 12px;
            color: var(--ax-muted);
            margin: 2px 0 12px;
        }

        .ax-test-stars {
            color: var(--ax-gold);
            font-size: 13.5px;
            display: flex;
            gap: 3px;
        }

        /* ================= 9. CTA VIDEO BANNER ================= */
        .ax-cta-section {
            position: relative;
            background: var(--ax-dark);
            min-height: 400px;
            display: flex;
            align-items: center;
            overflow: hidden;
            padding: 80px 0;
        }

        .ax-cta-bg-img {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0.7;
        }

        .ax-cta-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(90deg, rgba(10, 38, 34, 0.95) 0%, rgba(10, 38, 34, 0.76) 55%, rgba(10, 38, 34, 0.35) 100%);
        }

        .ax-play-btn-pulse {
            width: 82px;
            height: 82px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.22);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 2px solid rgba(255, 255, 255, 0.45);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #FFFFFF;
            font-size: 30px;
            box-shadow: 0 0 35px rgba(223, 180, 91, 0.45);
            transition: all 0.3s ease;
            cursor: pointer;
            margin: 0 auto;
        }

        .ax-play-btn-pulse:hover {
            background: var(--ax-gold);
            color: var(--ax-dark);
            border-color: var(--ax-gold);
            transform: scale(1.1);
        }

        .ax-play-btn-pulse i {
            margin-left: 4px;
        }

        /* ================= 10. BLOG SECTION ================= */
        .ax-blog-section {
            background-color: #FFFFFF;
            padding: 85px 0;
        }

        .ax-blog-card {
            border: 1px solid var(--ax-border);
            border-radius: 20px;
            overflow: hidden;
            background: #FFFFFF;
            height: 100%;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.03);
        }

        .ax-blog-card:hover {
            transform: translateY(-5px);
            border-color: var(--ax-gold);
            box-shadow: 0 14px 32px rgba(10, 38, 34, 0.08);
        }

        .ax-blog-img {
            width: 100%;
            height: 220px;
            object-fit: cover;
            transition: transform 0.45s ease;
        }

        .ax-blog-card:hover .ax-blog-img {
            transform: scale(1.05);
        }

        .ax-blog-body {
            padding: 22px 24px 26px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .ax-blog-meta-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }

        .ax-blog-date {
            font-size: 12px;
            color: var(--ax-muted);
            font-weight: 500;
        }

        .ax-blog-pill {
            font-size: 11px;
            font-weight: 700;
            background: #F4F3EE;
            color: var(--ax-text);
            padding: 3px 12px;
            border-radius: 999px;
            letter-spacing: 0.02em;
        }

        .ax-blog-title {
            font-size: 16.5px;
            font-weight: 800;
            line-height: 1.4;
            color: var(--ax-text);
            margin: 0;
            transition: color 0.2s ease;
        }

        .ax-blog-card:hover .ax-blog-title {
            color: var(--ax-dark);
        }

        /* ================= 11. FOOTER ================= */
        .ax-footer {
            background-color: var(--ax-dark);
            color: rgba(255, 255, 255, 0.75);
            padding: 75px 0 32px;
            font-size: 13.5px;
        }

        .ax-footer-top {
            padding-bottom: 45px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 45px;
        }

        .ax-footer-title {
            color: #FFFFFF;
            font-size: 14.5px;
            font-weight: 800;
            margin-bottom: 18px;
            letter-spacing: 0.02em;
        }

        .ax-footer-links {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .ax-footer-links li {
            margin-bottom: 10px;
        }

        .ax-footer-links a {
            color: rgba(255, 255, 255, 0.7);
            font-size: 13px;
        }

        .ax-footer-links a:hover {
            color: var(--ax-gold);
            padding-left: 4px;
        }

        .ax-footer-contact li {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
            color: rgba(255, 255, 255, 0.7);
            font-size: 13px;
        }

        .ax-footer-contact i {
            color: var(--ax-gold);
            font-size: 16px;
            flex-shrink: 0;
        }

        .ax-social-links {
            display: flex;
            gap: 10px;
            margin-top: 16px;
        }

        .ax-social-btn {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.15);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #FFFFFF;
            font-size: 14px;
            transition: all 0.2s ease;
        }

        .ax-social-btn:hover {
            background: var(--ax-gold);
            color: var(--ax-dark);
            border-color: var(--ax-gold);
        }

        .ax-footer-bottom {
            padding-top: 30px;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            font-size: 12px;
            color: rgba(255, 255, 255, 0.55);
        }

        .ax-footer-bottom a {
            color: rgba(255, 255, 255, 0.55);
            margin-left: 20px;
        }

        .ax-footer-bottom a:hover {
            color: #FFFFFF;
        }

        /* Responsive Breakpoints */
        @media (max-width: 991px) {
            .ax-nav-menu { display: none; }
            .ax-hero-heading { font-size: 38px; }
            .ax-hero-bg-media { position: relative; width: 100%; height: 340px; }
            .ax-hero-bg-gradient { display: none; }
            .ax-hero-badge { top: 20px; right: 20px; }
            .ax-why-angled-overlay { display: none; }
            .ax-why-bg-media { position: relative; width: 100%; height: 320px; }
            .ax-filter-fields { flex-direction: column; align-items: stretch; }
            .ax-field-divider { display: none; }
            .ax-filter-search-btn { width: 100%; border-radius: 12px; height: 46px; }
            .ax-why-stats-bar { position: relative; bottom: auto; right: auto; margin: 20px auto 0; }
        }
    </style>
</head>
<body>

{{-- ================= 1. NAVBAR & HERO SECTION ================= --}}
<div class="ax-hero-section">
    <!-- Background Villa Photo with Soft Seamless Gradient -->
    <div class="ax-hero-bg-media">
        <img src="{{ $img('hero') }}" alt="Modern Villa with Swimming Pool">
        <div class="ax-hero-bg-gradient"></div>
    </div>

    <!-- Top Navbar -->
    <header class="ax-navbar">
        <div class="container">
            <div class="d-flex align-items-center justify-content-between">
                <!-- Logo -->
                <a href="{{ $homeUrl }}" class="ax-brand">
                    <svg width="34" height="34" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M6 34V14L14 8V34H6Z" stroke="#DFB45B" stroke-width="2.5" stroke-linejoin="round"/>
                        <path d="M14 34V6L26 6V34" stroke="#DFB45B" stroke-width="2.5" stroke-linejoin="round"/>
                        <path d="M26 34V14L34 20V34H26Z" stroke="#DFB45B" stroke-width="2.5" stroke-linejoin="round"/>
                        <path d="M20 12V22" stroke="#DFB45B" stroke-width="2" stroke-linecap="round"/>
                        <path d="M4 34H36" stroke="#DFB45B" stroke-width="2.5" stroke-linecap="round"/>
                    </svg>
                    <div>
                        <div class="ax-brand-name">ANTIXOR</div>
                        <div class="ax-brand-tag">REAL ESTATE</div>
                    </div>
                </a>

                <!-- Navigation Links -->
                <ul class="ax-nav-menu">
                    <li><a href="{{ $homeUrl }}" class="active">Home</a></li>
                    <li><a href="#properties">Properties</a></li>
                    <li><a href="#about">About</a></li>
                    <li><a href="#services">Services</a></li>
                    <li><a href="#blog">Blog</a></li>
                    <li><a href="#contact">Contact</a></li>
                </ul>

                <!-- Actions -->
                <div class="d-flex align-items-center gap-3">
                    <a href="#" class="ax-search-icon-btn" title="Search"><i class="bi bi-search"></i></a>
                    <a href="#contact" class="ax-btn-gold">Get Started <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Content -->
    <div class="container ax-hero-content">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <span class="ax-eyebrow">PREMIUM PROPERTIES, BETTER LIVING</span>
                <h1 class="ax-hero-heading">
                    Find Your <span class="gold-text">Dream Property</span> with Antixor
                </h1>
                <p class="ax-hero-desc">
                    Discover the perfect home, investment, or commercial space with our curated real estate selection.
                </p>
            </div>
            <div class="col-lg-6 position-relative" style="min-height: 260px;">
                <!-- Floating 12+ Experience Glass Badge -->
                <div class="ax-hero-badge">
                    <div class="ax-badge-icon">
                        <i class="bi bi-building"></i>
                    </div>
                    <div>
                        <div class="ax-badge-val">12+</div>
                        <div class="ax-badge-label">Years of Experience</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Overlapping Search Filter Bar -->
    <div class="container ax-filter-container">
        <div class="ax-filter-box-wrap">
            <!-- Top Tabs -->
            <div class="ax-filter-tabs">
                <button class="ax-tab-btn active"><i class="bi bi-house-door-fill text-warning"></i> Buy</button>
                <button class="ax-tab-btn"><i class="bi bi-key-fill text-white-50"></i> Rent</button>
                <button class="ax-tab-btn"><i class="bi bi-buildings-fill text-white-50"></i> Commercial</button>
            </div>
            <!-- Input Card -->
            <div class="ax-filter-card">
                <form onsubmit="event.preventDefault();">
                    <div class="ax-filter-fields">
                        <div class="ax-field-item">
                            <span class="ax-field-label">Location</span>
                            <select class="ax-field-select">
                                <option>Select location</option>
                                <option>Dubai, Downtown</option>
                                <option>London, Westminster</option>
                                <option>New York, Manhattan</option>
                                <option>Singapore, Marina</option>
                            </select>
                        </div>
                        <div class="ax-field-divider"></div>
                        <div class="ax-field-item">
                            <span class="ax-field-label">Property Type</span>
                            <select class="ax-field-select">
                                <option>Any Type</option>
                                <option>Luxury Villa</option>
                                <option>Modern Apartment</option>
                                <option>Penthouse</option>
                                <option>Commercial Office</option>
                            </select>
                        </div>
                        <div class="ax-field-divider"></div>
                        <div class="ax-field-item">
                            <span class="ax-field-label">Price Range</span>
                            <select class="ax-field-select">
                                <option>Any Range</option>
                                <option>$500,000 - $1,000,000</option>
                                <option>$1,000,000 - $2,500,000</option>
                                <option>$2,500,000 - $5,000,000</option>
                                <option>$5,000,000+</option>
                            </select>
                        </div>
                        <button type="submit" class="ax-filter-search-btn" title="Search Properties">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ================= 2. 4-COLUMN FEATURE ROW ================= --}}
<section class="ax-features-row">
    <div class="container">
        <div class="row g-4">
            <div class="col-sm-6 col-lg-3 ax-feat-col">
                <div class="ax-feat-box">
                    <div class="ax-feat-icon">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <div>
                        <div class="ax-feat-title">Verified Listings</div>
                        <p class="ax-feat-desc">100% authentic &amp; trusted properties.</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3 ax-feat-col">
                <div class="ax-feat-box">
                    <div class="ax-feat-icon">
                        <i class="bi bi-headset"></i>
                    </div>
                    <div>
                        <div class="ax-feat-title">Expert Support</div>
                        <p class="ax-feat-desc">Get guidance from real estate professionals.</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3 ax-feat-col">
                <div class="ax-feat-box">
                    <div class="ax-feat-icon">
                        <i class="bi bi-tag"></i>
                    </div>
                    <div>
                        <div class="ax-feat-title">Best Deals</div>
                        <p class="ax-feat-desc">Exclusive offers &amp; competitive prices.</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3 ax-feat-col">
                <div class="ax-feat-box">
                    <div class="ax-feat-icon">
                        <i class="bi bi-lock"></i>
                    </div>
                    <div>
                        <div class="ax-feat-title">Secure Process</div>
                        <p class="ax-feat-desc">Safe, transparent &amp; hassle-free transactions.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ================= 3. EXPLORE OUR FEATURED PROPERTIES ================= --}}
<section class="ax-section-pad" id="properties">
    <div class="container">
        <!-- Section Header -->
        <div class="d-flex justify-content-between align-items-end flex-wrap gap-3 mb-4 pb-2">
            <div>
                <span class="ax-eyebrow ax-eyebrow-dark">FEATURED PROPERTIES</span>
                <h2 class="ax-title mb-0">Explore Our Featured Properties</h2>
            </div>
            <a href="#properties" class="ax-link-arrow">
                View All Properties <i class="bi bi-arrow-right"></i>
            </a>
        </div>

        <!-- 3-Column Property Grid -->
        <div class="row g-4">
            <!-- Property 1 -->
            <div class="col-md-6 col-lg-4">
                <div class="ax-prop-card">
                    <div class="ax-prop-img-box">
                        <img src="{{ $img('prop1') }}" alt="Luxury Villa with Private Pool">
                        <span class="ax-prop-badge">For Sale</span>
                        <button class="ax-prop-heart" title="Save Property"><i class="bi bi-heart"></i></button>
                    </div>
                    <div class="ax-prop-body">
                        <div class="ax-prop-loc"><i class="bi bi-geo-alt-fill"></i> Beverly Hills, CA</div>
                        <h4 class="ax-prop-title">Luxury Villa with Private Pool</h4>
                        <div class="ax-prop-price">$2,850,000</div>
                        <div class="ax-prop-meta">
                            <span><i class="bi bi-door-closed"></i> 5 Beds</span>
                            <span><i class="bi bi-water"></i> 4 Baths</span>
                            <span><i class="bi bi-aspect-ratio"></i> 4,200 sqft</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Property 2 -->
            <div class="col-md-6 col-lg-4">
                <div class="ax-prop-card">
                    <div class="ax-prop-img-box">
                        <img src="{{ $img('prop2') }}" alt="Modern Apartment in Downtown">
                        <span class="ax-prop-badge">For Sale</span>
                        <button class="ax-prop-heart" title="Save Property"><i class="bi bi-heart"></i></button>
                    </div>
                    <div class="ax-prop-body">
                        <div class="ax-prop-loc"><i class="bi bi-geo-alt-fill"></i> New York, NY</div>
                        <h4 class="ax-prop-title">Modern Apartment in Downtown</h4>
                        <div class="ax-prop-price">$1,250,000</div>
                        <div class="ax-prop-meta">
                            <span><i class="bi bi-door-closed"></i> 3 Beds</span>
                            <span><i class="bi bi-water"></i> 2 Baths</span>
                            <span><i class="bi bi-aspect-ratio"></i> 1,600 sqft</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Property 3 -->
            <div class="col-md-6 col-lg-4">
                <div class="ax-prop-card">
                    <div class="ax-prop-img-box">
                        <img src="{{ $img('prop3') }}" alt="Elegant City Apartment">
                        <span class="ax-prop-badge">For Rent</span>
                        <button class="ax-prop-heart" title="Save Property"><i class="bi bi-heart"></i></button>
                    </div>
                    <div class="ax-prop-body">
                        <div class="ax-prop-loc"><i class="bi bi-geo-alt-fill"></i> Downtown, Austin</div>
                        <h4 class="ax-prop-title">Elegant City Apartment</h4>
                        <div class="ax-prop-price">$4,200 <span>/month</span></div>
                        <div class="ax-prop-meta">
                            <span><i class="bi bi-door-closed"></i> 2 Beds</span>
                            <span><i class="bi bi-water"></i> 2 Baths</span>
                            <span><i class="bi bi-aspect-ratio"></i> 1,200 sqft</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ================= 4. WHY CHOOSE ANTIXOR (DIAGONAL SPLIT BANNER) ================= --}}
<section class="ax-why-banner" id="about">
    <!-- Right Background Media -->
    <div class="ax-why-bg-media">
        <img src="{{ $img('skyline') }}" alt="Panoramic Skyline View">
    </div>

    <!-- Left Angled Dark Green Overlay -->
    <div class="ax-why-angled-overlay"></div>

    <!-- Content Container -->
    <div class="container position-relative" style="z-index: 5;">
        <div class="row">
            <div class="col-lg-5 py-5 my-lg-4">
                <span class="ax-eyebrow">WHY CHOOSE ANTIXOR</span>
                <h2 class="ax-title ax-title-white mb-3" style="font-size: 36px; max-width: 480px;">
                    More Than Just Properties — It's a Better Tomorrow
                </h2>
                <p style="color: rgba(255,255,255,0.78); font-size: 14.5px; line-height: 1.7; margin-bottom: 28px; max-width: 420px;">
                    We help you find the ideal property that aligns with your lifestyle, investment goals, and long-term vision with trusted expertise at every step.
                </p>
                <a href="#services" class="ax-btn-gold">Learn More <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>

    <!-- Floating Stats Box (Bottom-Right Overlapping Skyline) -->
    <div class="ax-why-stats-bar">
        <div class="ax-stat-col">
            <strong>10K+</strong>
            <span>Happy Clients</span>
        </div>
        <div class="ax-stat-div"></div>
        <div class="ax-stat-col">
            <strong>500+</strong>
            <span>Properties Listed</span>
        </div>
        <div class="ax-stat-div"></div>
        <div class="ax-stat-col">
            <strong>98%</strong>
            <span>Satisfaction Rate</span>
        </div>
    </div>
</section>

{{-- ================= 5. COMPREHENSIVE REAL ESTATE SOLUTIONS ================= --}}
<section class="ax-services-section" id="services">
    <div class="container">
        <!-- Section Header -->
        <div class="d-flex justify-content-between align-items-end flex-wrap gap-3 mb-4 pb-2">
            <div>
                <span class="ax-eyebrow ax-eyebrow-dark">OUR SERVICES</span>
                <h2 class="ax-title mb-0">Comprehensive Real Estate Solutions</h2>
            </div>
            <a href="#services" class="ax-link-arrow">
                View All Services <i class="bi bi-arrow-right"></i>
            </a>
        </div>

        <!-- 4 Service Cards -->
        <div class="row g-4">
            <div class="col-sm-6 col-lg-3">
                <div class="ax-service-card">
                    <div class="ax-serv-icon">
                        <i class="bi bi-house-door"></i>
                    </div>
                    <h4 class="ax-serv-title">Residential Sales</h4>
                    <p class="ax-serv-desc">Find your perfect home with ease and verified confidence.</p>
                    <button class="ax-serv-arrow-btn" aria-label="Learn More"><i class="bi bi-arrow-right"></i></button>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3">
                <div class="ax-service-card">
                    <div class="ax-serv-icon">
                        <i class="bi bi-building"></i>
                    </div>
                    <h4 class="ax-serv-title">Commercial Leasing</h4>
                    <p class="ax-serv-desc">Grow your business in premier commercial and retail spaces.</p>
                    <button class="ax-serv-arrow-btn" aria-label="Learn More"><i class="bi bi-arrow-right"></i></button>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3">
                <div class="ax-service-card">
                    <div class="ax-serv-icon">
                        <i class="bi bi-graph-up-arrow"></i>
                    </div>
                    <h4 class="ax-serv-title">Property Investment</h4>
                    <p class="ax-serv-desc">Build generational wealth with high-yield acquisitions.</p>
                    <button class="ax-serv-arrow-btn" aria-label="Learn More"><i class="bi bi-arrow-right"></i></button>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3">
                <div class="ax-service-card">
                    <div class="ax-serv-icon">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <h4 class="ax-serv-title">Property Management</h4>
                    <p class="ax-serv-desc">Hassle-free tenant and asset care for total peace of mind.</p>
                    <button class="ax-serv-arrow-btn" aria-label="Learn More"><i class="bi bi-arrow-right"></i></button>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ================= 6. POPULAR LOCATIONS ================= --}}
<section class="ax-locations-section">
    <div class="container">
        <!-- Section Header -->
        <div class="d-flex justify-content-between align-items-end flex-wrap gap-3 mb-4 pb-2">
            <div>
                <span class="ax-eyebrow ax-eyebrow-dark">TOP DESTINATIONS</span>
                <h2 class="ax-title mb-1">Popular Locations</h2>
                <p class="text-muted small mb-0">Explore the most sought-after locations for your next property.</p>
            </div>
            <a href="#properties" class="ax-link-arrow">
                View All Locations <i class="bi bi-arrow-right"></i>
            </a>
        </div>

        <!-- 4-Column Locations Grid with Text Below Image -->
        <div class="row g-4">
            <div class="col-6 col-lg-3">
                <div class="ax-loc-box">
                    <a href="#properties" class="ax-loc-card">
                        <img src="{{ $img('loc1') }}" alt="Dubai Properties">
                    </a>
                    <div class="ax-loc-info">
                        <div class="ax-loc-name">Dubai</div>
                        <div class="ax-loc-count">150+ Properties</div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-lg-3">
                <div class="ax-loc-box">
                    <a href="#properties" class="ax-loc-card">
                        <img src="{{ $img('loc2') }}" alt="London Properties">
                    </a>
                    <div class="ax-loc-info">
                        <div class="ax-loc-name">London</div>
                        <div class="ax-loc-count">220+ Properties</div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-lg-3">
                <div class="ax-loc-box">
                    <a href="#properties" class="ax-loc-card">
                        <img src="{{ $img('loc3') }}" alt="New York Properties">
                    </a>
                    <div class="ax-loc-info">
                        <div class="ax-loc-name">New York</div>
                        <div class="ax-loc-count">310+ Properties</div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-lg-3">
                <div class="ax-loc-box">
                    <a href="#properties" class="ax-loc-card">
                        <img src="{{ $img('loc4') }}" alt="Singapore Properties">
                    </a>
                    <div class="ax-loc-info">
                        <div class="ax-loc-name">Singapore</div>
                        <div class="ax-loc-count">180+ Properties</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ================= 7. SMART INVESTMENTS BANNER ================= --}}
<section class="ax-invest-section">
    <div class="container position-relative">
        <div class="row align-items-center g-5">
            <!-- Left Info -->
            <div class="col-lg-4">
                <span class="ax-eyebrow">INVESTMENT OPPORTUNITIES</span>
                <h2 class="ax-title ax-title-white mb-3">
                    Smart Investments For a Brighter Future
                </h2>
                <p style="color: rgba(255,255,255,0.72); font-size: 14.5px; line-height: 1.7; margin-bottom: 28px;">
                    Explore high-return investment opportunities in prime international locations around the world.
                </p>
                <a href="#contact" class="ax-btn-gold">Explore Investments <i class="bi bi-arrow-right"></i></a>
            </div>

            <!-- Center Modern Architectural Building -->
            <div class="col-lg-5">
                <div class="ax-invest-img">
                    <img src="{{ $img('building') }}" alt="Luxury Architectural Building at Dusk">
                </div>
            </div>

            <!-- Right 3 Stacked Glassmorphism Metric Cards -->
            <div class="col-lg-3">
                <div class="ax-invest-stat-box">
                    <div class="ax-stat-num">8-15%</div>
                    <div class="ax-stat-desc">Average ROI</div>
                </div>

                <div class="ax-invest-stat-box">
                    <div class="ax-stat-num">100+</div>
                    <div class="ax-stat-desc">Investment Projects</div>
                </div>

                <div class="ax-invest-stat-box">
                    <div class="ax-stat-num">Global</div>
                    <div class="ax-stat-desc">Markets</div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ================= 8. WHAT OUR CLIENTS SAY (TESTIMONIALS) ================= --}}
<section class="ax-testimonials-section" id="testimonials">
    <div class="container">
        <!-- Section Header -->
        <div class="d-flex justify-content-between align-items-end flex-wrap gap-3 mb-4 pb-2">
            <div>
                <span class="ax-eyebrow ax-eyebrow-dark">TESTIMONIALS</span>
                <h2 class="ax-title mb-1">What Our Clients Say</h2>
                <p class="text-muted small mb-0">Real stories from people who found their dream properties with Antixor.</p>
            </div>
            <a href="#testimonials" class="ax-link-arrow">
                View All Reviews <i class="bi bi-arrow-right"></i>
            </a>
        </div>

        <!-- 3-Column Reviews matching Reference Visual Hierarchy -->
        <div class="row g-4">
            <!-- Review 1 -->
            <div class="col-md-4">
                <div class="ax-test-card">
                    <img src="{{ $img('client1') }}" alt="David Miller" class="ax-test-avatar">
                    <p class="ax-test-quote">
                        "Antixor made the entire process seamless and stress-free. Their team understood our requirements and found us the perfect home."
                    </p>
                    <h5 class="ax-test-name">David Miller</h5>
                    <div class="ax-test-role">Property Buyer</div>
                    <div class="ax-test-stars">
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                    </div>
                </div>
            </div>

            <!-- Review 2 -->
            <div class="col-md-4">
                <div class="ax-test-card">
                    <img src="{{ $img('client2') }}" alt="Sarah Jenkins" class="ax-test-avatar">
                    <p class="ax-test-quote">
                        "The team listened to our exact requirements and presented properties that felt custom-made for our family. Truly highly recommended!"
                    </p>
                    <h5 class="ax-test-name">Sarah Jenkins</h5>
                    <div class="ax-test-role">Homeowner</div>
                    <div class="ax-test-stars">
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                    </div>
                </div>
            </div>

            <!-- Review 3 -->
            <div class="col-md-4">
                <div class="ax-test-card">
                    <img src="{{ $img('client3') }}" alt="Marcus Vance" class="ax-test-avatar">
                    <p class="ax-test-quote">
                        "Outstanding return on investment and clear transactional advisory. Antixor is my number one trusted real estate firm."
                    </p>
                    <h5 class="ax-test-name">Marcus Vance</h5>
                    <div class="ax-test-role">Commercial Investor</div>
                    <div class="ax-test-stars">
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ================= 9. CTA VIDEO BANNER ================= --}}
<section class="ax-cta-section" id="contact">
    <img src="{{ $img('cta') }}" alt="Luxury Pool Villa at Dusk" class="ax-cta-bg-img">
    <div class="ax-cta-overlay"></div>
    <div class="container position-relative z-2">
        <div class="row align-items-center g-4">
            <div class="col-lg-8">
                <span class="ax-eyebrow">READY TO GET STARTED?</span>
                <h2 class="ax-title ax-title-white mb-2" style="font-size: 38px;">
                    Let's Find Your Perfect Property
                </h2>
                <p style="color: rgba(255,255,255,0.78); font-size: 15px; line-height: 1.6; margin-bottom: 26px; max-width: 540px;">
                    Whether you're buying, renting, or investing — our experienced team is here to guide your journey.
                </p>
                <a href="#contact" class="ax-btn-gold">Contact Us <i class="bi bi-arrow-right"></i></a>
            </div>
            <div class="col-lg-4 text-center">
                <div class="ax-play-btn-pulse" title="Watch Brand Film">
                    <i class="bi bi-play-fill"></i>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ================= 10. BLOG SECTION ================= --}}
<section class="ax-blog-section" id="blog">
    <div class="container">
        <!-- Section Header -->
        <div class="d-flex justify-content-between align-items-end flex-wrap gap-3 mb-4 pb-2">
            <div>
                <span class="ax-eyebrow ax-eyebrow-dark">LATEST BLOG</span>
                <h2 class="ax-title mb-1">Insights &amp; Tips</h2>
                <p class="text-muted small mb-0">Stay up to date with the latest real estate trends and expert advice.</p>
            </div>
            <a href="#blog" class="ax-link-arrow">
                View All Articles <i class="bi bi-arrow-right"></i>
            </a>
        </div>

        <!-- 3-Column Blog Cards -->
        <div class="row g-4">
            <!-- Article 1 -->
            <div class="col-md-4">
                <a href="#blog" class="ax-blog-card">
                    <img src="{{ $img('blog1') }}" alt="Real Estate Investment Tips" class="ax-blog-img">
                    <div class="ax-blog-body">
                        <div class="ax-blog-meta-row">
                            <span class="ax-blog-date">Aug 15, 2025</span>
                            <span class="ax-blog-pill">Investment</span>
                        </div>
                        <h4 class="ax-blog-title">Real Estate Investment Tips for 2025</h4>
                    </div>
                </a>
            </div>

            <!-- Article 2 -->
            <div class="col-md-4">
                <a href="#blog" class="ax-blog-card">
                    <img src="{{ $img('blog2') }}" alt="Choosing the Right Location" class="ax-blog-img">
                    <div class="ax-blog-body">
                        <div class="ax-blog-meta-row">
                            <span class="ax-blog-date">Aug 12, 2025</span>
                            <span class="ax-blog-pill">Home Buying</span>
                        </div>
                        <h4 class="ax-blog-title">How to Choose the Right Location for Your Home</h4>
                    </div>
                </a>
            </div>

            <!-- Article 3 -->
            <div class="col-md-4">
                <a href="#blog" class="ax-blog-card">
                    <img src="{{ $img('blog3') }}" alt="Future of Real Estate" class="ax-blog-img">
                    <div class="ax-blog-body">
                        <div class="ax-blog-meta-row">
                            <span class="ax-blog-date">Aug 08, 2025</span>
                            <span class="ax-blog-pill">Market Trends</span>
                        </div>
                        <h4 class="ax-blog-title">The Future of Real Estate: Trends to Watch</h4>
                    </div>
                </a>
            </div>
        </div>
    </div>
</section>

{{-- ================= 11. FOOTER ================= --}}
<footer class="ax-footer">
    <div class="container">
        <!-- Top Row -->
        <div class="ax-footer-top d-flex align-items-center justify-content-between flex-wrap gap-4">
            <div class="ax-brand">
                <svg width="34" height="34" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M6 34V14L14 8V34H6Z" stroke="#DFB45B" stroke-width="2.5" stroke-linejoin="round"/>
                    <path d="M14 34V6L26 6V34" stroke="#DFB45B" stroke-width="2.5" stroke-linejoin="round"/>
                    <path d="M26 34V14L34 20V34H26Z" stroke="#DFB45B" stroke-width="2.5" stroke-linejoin="round"/>
                    <path d="M20 12V22" stroke="#DFB45B" stroke-width="2" stroke-linecap="round"/>
                    <path d="M4 34H36" stroke="#DFB45B" stroke-width="2.5" stroke-linecap="round"/>
                </svg>
                <div>
                    <div class="ax-brand-name">ANTIXOR</div>
                    <div class="ax-brand-tag">REAL ESTATE</div>
                </div>
            </div>

            <div class="d-flex align-items-center gap-4 flex-wrap">
                <span class="text-white fw-bold" style="font-size: 15px;">Your Trusted Partner in Real Estate</span>
                <a href="#contact" class="ax-btn-gold">Get Started <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>

        <!-- Middle Row -->
        <div class="row g-4 mb-5">
            <div class="col-6 col-lg-3">
                <div class="ax-footer-title">Quick Links</div>
                <ul class="ax-footer-links">
                    <li><a href="{{ $homeUrl }}">Home</a></li>
                    <li><a href="#properties">Properties</a></li>
                    <li><a href="#about">About</a></li>
                    <li><a href="#services">Services</a></li>
                    <li><a href="#blog">Blog</a></li>
                    <li><a href="#contact">Contact</a></li>
                </ul>
            </div>

            <div class="col-6 col-lg-4">
                <div class="ax-footer-title">Contact Info</div>
                <ul class="ax-footer-links ax-footer-contact">
                    <li><i class="bi bi-telephone"></i> +1 (555) 234-5678</li>
                    <li><i class="bi bi-envelope"></i> info@antixor.com</li>
                    <li><i class="bi bi-geo-alt"></i> 742 Evergreen Terrace, Beverly Hills, CA 90210</li>
                </ul>
            </div>

            <div class="col-lg-5">
                <div class="ax-footer-title">Follow Us</div>
                <p class="small text-white-50 mb-3" style="max-width: 380px;">
                    Join our global community for property releases, prime investment updates, and market intelligence.
                </p>
                <div class="ax-social-links">
                    <a href="#" class="ax-social-btn" title="X (Twitter)"><i class="bi bi-twitter-x"></i></a>
                    <a href="#" class="ax-social-btn" title="Facebook"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="ax-social-btn" title="LinkedIn"><i class="bi bi-linkedin"></i></a>
                    <a href="#" class="ax-social-btn" title="Instagram"><i class="bi bi-instagram"></i></a>
                    <a href="#" class="ax-social-btn" title="YouTube"><i class="bi bi-youtube"></i></a>
                </div>
            </div>
        </div>

        <!-- Bottom Row -->
        <div class="ax-footer-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                &copy; {{ date('Y') }} Antixor Real Estate. All rights reserved.
            </div>
            <div>
                <a href="#">Privacy Policy</a>
                <a href="#">Terms &amp; Conditions</a>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
