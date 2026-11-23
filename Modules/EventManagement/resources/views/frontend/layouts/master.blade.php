<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $s['brand_name'] ?? 'Eventify' }} — {{ $s['hero_title_line1'] ?? 'YEARLY BUSINESS' }} {{ $s['hero_title_highlight'] ?? 'CONFERENCES' }} {{ $s['hero_title_edition'] ?? '“ 13 ”' }}</title>
    <meta name="description" content="{{ $s['about_description'] ?? 'Growth Through World Class And Conferences.' }}">

    <!-- Google Fonts: Plus Jakarta Sans & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3 & Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            --ev-dark: #03222A;
            --ev-dark-deep: #021A21;
            --ev-dark-band: #082932;
            --ev-card: #13333D;
            --ev-card-hover: #173C47;
            --ev-pill-bg: #0D353F;
            --ev-lime: #BAF92C;
            --ev-lime-soft: #CEF958;
            --ev-lime-border: #9AE62B;
            --ev-text: #FFFFFF;
            --ev-muted: #A9C2C8;
        }

        * {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
            background-color: var(--ev-dark);
            color: var(--ev-text);
            margin: 0;
            width: 100%;
            overflow-x: hidden;
        }

        /* =====================================================================
           UNIFIED FULL-WINDOW PROPORTIONAL CONTAINER (91.6% WIDTH = 4.2% LEFT/RIGHT)
           Matches the exact screenshot proportions across all monitor resolutions!
           ===================================================================== */
        .ev-container {
            width: 91.6%;
            max-width: 100%;
            margin: 0 auto;
            position: relative;
        }

        /* =====================================================================
           1. HERO SECTION + FLOATING WHITE HEADER CARD AT THE TOP
           ===================================================================== */
        .ev-hero {
            position: relative;
            width: 100%;
            min-height: 52.6vw;
            background: radial-gradient(circle at 12% 28%, #053843 0%, #012F39 50%, #012F38 100%);
            padding-top: 0;
            padding-bottom: 4.5vw;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        }

        /* Right Side Speaker Image Spanning Top-to-Bottom of Hero (Exact 47.6% width x 52.6vw height) */
        .ev-hero-right-bg {
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            width: 47.6%;
            z-index: 1;
            overflow: hidden;
            cursor: pointer;
            background: linear-gradient(180deg, #5A3114 0%, #9E6334 55%, #C68B59 100%);
        }

        .ev-hero-right-bg img {
            position: absolute;
            left: 0;
            bottom: 0;
            width: 100%;
            height: 88.8%;
            object-fit: fill;
            display: block;
        }

        /* Floating White Header Card (Topbar + Navbar inside .ev-container) */
        .ev-header-box {
            position: relative;
            z-index: 15;
            background: #FFFFFF;
            border-radius: 0 0 8px 8px;
            border-left: 4px solid var(--ev-lime);
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.22);
        }

        .ev-topbar-row {
            padding: 0.65vw 2vw;
            border-bottom: 1px solid #EAECEF;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 8px;
            font-size: clamp(11.5px, 0.95vw, 15.5px);
            color: #1E293B;
            font-weight: 600;
        }

        .ev-topbar-left {
            display: flex;
            align-items: center;
            gap: 7px;
        }

        .ev-topbar-left .bag-icon {
            color: #76B816;
            font-size: clamp(12px, 1.05vw, 16px);
        }

        .ev-topbar-left a {
            color: #0F172A;
            font-weight: 800;
            text-decoration: underline;
        }

        .ev-topbar-right {
            display: flex;
            align-items: center;
            gap: 1.8vw;
            color: #0F172A;
            font-weight: 700;
        }

        .ev-Navbar-row {
            padding: 1.05vw 2vw;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .ev-brand {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: #04222A;
            font-weight: 800;
            font-size: clamp(20px, 1.75vw, 28px);
            letter-spacing: -0.02em;
        }

        .ev-brand svg {
            width: clamp(26px, 2.2vw, 34px);
            height: clamp(26px, 2.2vw, 34px);
        }

        .ev-nav-menu {
            display: flex;
            align-items: center;
            gap: 2.1vw;
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .ev-nav-menu li a {
            color: #0F172A;
            text-decoration: none;
            font-size: clamp(13px, 1.1vw, 17px);
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            transition: color 0.18s;
        }

        .ev-nav-menu li a:hover {
            color: #4D7C0F;
        }

        .ev-nav-menu li a i {
            font-size: 0.75em;
            color: #475569;
        }

        .ev-nav-icons {
            display: flex;
            align-items: center;
            gap: 1.3vw;
        }

        .ev-nav-icons button,
        .ev-nav-icons a {
            background: none;
            border: none;
            color: #0F172A;
            font-size: clamp(15px, 1.25vw, 20px);
            padding: 0;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            position: relative;
            cursor: pointer;
        }

        .ev-nav-divider {
            width: 1.5px;
            height: 20px;
            background: #94A3B8;
            margin: 0 2px;
        }

        .ev-cart-badge {
            position: absolute;
            top: -7px;
            right: -9px;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: var(--ev-lime);
            color: #04222A;
            font-size: 10px;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        /* Hero Text Content (Left side inside .ev-container, aligned with Eventify logo) */
        .ev-hero-body {
            position: relative;
            z-index: 5;
            padding-top: 5.2vw;
            padding-left: 1vw;
            max-width: 52%;
        }

        .ev-hero-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            font-size: clamp(11.5px, 1.05vw, 16.5px);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            color: #E8F4F7;
            margin-bottom: 1.3vw;
        }

        .ev-lime-dot {
            width: clamp(8px, 0.65vw, 11px);
            height: clamp(8px, 0.65vw, 11px);
            border-radius: 50%;
            background: var(--ev-lime);
            display: inline-block;
            flex-shrink: 0;
        }

        .ev-hero-title {
            font-size: clamp(30px, 4.15vw, 66px);
            font-weight: 800;
            line-height: 1.22;
            color: #FFFFFF;
            text-transform: uppercase;
            margin-bottom: 2.3vw;
            letter-spacing: -0.01em;
        }

        /* CONFERENCES Outline Pill with Lime Border */
        .ev-conf-outline-box {
            display: inline-block;
            border: 1.8px solid var(--ev-lime-border);
            background: rgba(255, 255, 255, 0.06);
            border-radius: 11px;
            padding: 0.1vw 1.15vw 0.25vw;
            margin-top: 0.45vw;
            margin-right: 0.45vw;
        }

        /* 4-Column Countdown with 4 Individual Lime Bottom Lines */
        .ev-hero-countdown {
            display: flex;
            align-items: center;
            gap: 1.35vw;
            margin-bottom: 2.6vw;
        }

        .ev-cd-col {
            position: relative;
            width: clamp(72px, 6.8vw, 114px);
            text-align: center;
            padding-bottom: 0.8vw;
            border-bottom: 2.5px solid var(--ev-lime-border);
        }

        .ev-cd-col:not(:last-child)::after {
            content: "";
            position: absolute;
            right: -0.68vw;
            top: 15%;
            height: 62%;
            width: 1px;
            background: rgba(255, 255, 255, 0.16);
        }

        .ev-cd-num {
            font-size: clamp(22px, 2.25vw, 36px);
            font-weight: 800;
            color: #FFFFFF;
            line-height: 1.05;
        }

        .ev-cd-label {
            font-size: clamp(10.5px, 0.95vw, 14.5px);
            color: #D5E6EB;
            font-weight: 600;
            margin-top: 4px;
        }

        /* Signature Pill + Right Chevron Circle CTA Button */
        .ev-btn-group {
            display: inline-flex;
            align-items: center;
            text-decoration: none;
            gap: 2px;
            cursor: pointer;
            border: none;
            background: transparent;
            padding: 0;
            transition: transform 0.2s ease;
        }

        .ev-btn-group:hover {
            transform: translateY(-2px);
        }

        .ev-btn-pill {
            background: var(--ev-lime-soft);
            color: #04222A;
            font-weight: 800;
            font-size: clamp(11.5px, 1vw, 15.5px);
            letter-spacing: 0.02em;
            padding: clamp(10px, 0.85vw, 14px) clamp(20px, 1.8vw, 28px);
            border-radius: 999px;
            line-height: 1.2;
            display: inline-block;
            transition: background 0.2s;
        }

        .ev-btn-circle {
            width: clamp(34px, 2.9vw, 46px);
            height: clamp(34px, 2.9vw, 46px);
            border-radius: 50%;
            background: var(--ev-lime);
            color: #04222A;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: clamp(12px, 1.05vw, 16px);
            font-weight: 900;
            transition: background 0.2s, transform 0.2s;
        }

        .ev-btn-group.ev-btn-dark .ev-btn-pill,
        .ev-btn-group.ev-btn-dark .ev-btn-circle {
            background: #04222A;
            color: #FFFFFF;
        }

        /* Bottom-Left White Double Wave SVG */
        .ev-hero-wave {
            position: absolute;
            bottom: -1px;
            left: 0;
            width: 22vw;
            min-width: 210px;
            pointer-events: none;
            z-index: 4;
        }

        /* Bottom-Right Stacked White Circle Arrows (> and <) */
        .ev-hero-slider-btns {
            position: absolute;
            right: 2.2vw;
            bottom: -1.4vw;
            z-index: 20;
            display: flex;
            flex-direction: column;
            gap: 0.55vw;
        }

        .ev-slider-arrow {
            width: clamp(36px, 2.9vw, 46px);
            height: clamp(36px, 2.9vw, 46px);
            border-radius: 50%;
            background: #FFFFFF;
            color: #04222A;
            border: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: clamp(13px, 1.1vw, 17px);
            font-weight: 900;
            box-shadow: 0 8px 22px rgba(0, 0, 0, 0.28);
            cursor: pointer;
            transition: all 0.2s;
        }

        .ev-slider-arrow:hover {
            background: var(--ev-lime);
            transform: scale(1.06);
        }

        /* =====================================================================
           SECTION EYEBROW PILL WITH GREEN DOT
           ===================================================================== */
        .ev-eyebrow-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--ev-pill-bg);
            color: #E2F1F5;
            font-size: clamp(10.5px, 0.9vw, 13.5px);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 6px 16px;
            border-radius: 999px;
            margin-bottom: 14px;
            border: 1px solid rgba(255, 255, 255, 0.06);
        }

        .ev-section-title {
            font-size: clamp(28px, 3.1vw, 46px);
            font-weight: 800;
            color: #FFFFFF;
            line-height: 1.22;
            letter-spacing: -0.02em;
            margin-bottom: 18px;
        }

        /* =====================================================================
           2. ABOUT OUR BUSINESS CONFERENCE (3-IMAGE COLLAGE + 2 STAT PILLS)
           ===================================================================== */
        .ev-about-section {
            padding: 6.5vw 0 5.5vw;
            background: var(--ev-dark);
            position: relative;
        }

        .ev-collage-wrap {
            position: relative;
            padding: 8px 20px 8px 0;
        }

        /* Concentric teal target circles top-left behind tall photo */
        .ev-target-rings {
            position: absolute;
            top: -12px;
            left: -28px;
            width: clamp(110px, 10vw, 155px);
            height: clamp(110px, 10vw, 155px);
            border-radius: 50%;
            background: radial-gradient(circle, #186272 0%, #186272 26%, #0B3E4B 27%, #0B3E4B 52%, rgba(11, 62, 75, 0.45) 53%, rgba(11, 62, 75, 0.45) 72%, transparent 73%);
            pointer-events: none;
            z-index: 1;
        }

        .ev-collage-img-tall {
            width: 100%;
            aspect-ratio: 97 / 144;
            object-fit: cover;
            border-radius: 10px;
            box-shadow: 0 18px 40px rgba(0, 0, 0, 0.35);
            position: relative;
            z-index: 2;
        }

        .ev-collage-img-sm {
            width: 100%;
            aspect-ratio: 93 / 86;
            object-fit: cover;
            border-radius: 10px;
            box-shadow: 0 18px 40px rgba(0, 0, 0, 0.35);
            position: relative;
            z-index: 2;
        }

        /* Two Separate Rounded Stat Pills (30+ Speakers | 10+ Sponsors) */
        .ev-about-stat-pills {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
            margin: 26px 0 32px;
        }

        .ev-stat-badge-pill {
            background: #0A2E37;
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 999px;
            padding: 12px 26px;
            display: inline-flex;
            align-items: center;
            gap: 11px;
            font-size: clamp(13.5px, 1.1vw, 17px);
            font-weight: 700;
            color: #FFFFFF;
        }

        .ev-stat-badge-pill i {
            font-size: 1.15em;
            color: #FFFFFF;
        }

        /* =====================================================================
           3. SPONSORS BAND (Join 6,000+ Companies Already Growing)
           ===================================================================== */
        .ev-sponsors-band {
            padding: 4vw 0 4.5vw;
            background: var(--ev-dark-band);
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .ev-sponsor-card {
            background: #133640;
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 10px;
            height: clamp(74px, 6.2vw, 96px);
            padding: 16px 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            color: #FFFFFF;
            font-weight: 800;
            font-size: clamp(16px, 1.35vw, 21px);
            text-decoration: none;
            transition: all 0.22s ease;
        }

        .ev-sponsor-card:hover {
            background: #18424E;
            border-color: rgba(186, 249, 44, 0.35);
            color: var(--ev-lime);
            transform: translateY(-3px);
        }

        /* =====================================================================
           4. OUR EVENT SPEAKERS (CIRCULAR DOUBLE-RING PORTRAITS)
           ===================================================================== */
        .ev-speakers-section {
            padding: 5.5vw 0 6vw;
            background: var(--ev-dark);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .ev-speaker-card {
            text-align: center;
            transition: transform 0.25s ease;
        }

        .ev-speaker-card:hover {
            transform: translateY(-6px);
        }

        .ev-speaker-ring {
            width: 88%;
            aspect-ratio: 1 / 1;
            margin: 0 auto 18px;
            border-radius: 50%;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .ev-speaker-ring img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
            display: block;
        }

        .ev-speaker-name {
            font-size: clamp(16px, 1.35vw, 21px);
            font-weight: 800;
            color: #FFFFFF;
            margin-bottom: 4px;
        }

        .ev-speaker-role {
            font-size: clamp(12.5px, 1vw, 15px);
            color: #CBDCE0;
            font-weight: 500;
        }

        /* =====================================================================
           5. OUR EVENTS SCHEDULE PLAN (3 TABS + 01 DEC CIRCLE & VIEW DETAILS ->)
           ===================================================================== */
        .ev-schedule-section {
            padding: 5.5vw 0 6vw;
            background: var(--ev-dark-band);
        }

        .ev-day-tabs {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
            gap: 16px;
            margin-bottom: 48px;
        }

        .ev-day-tab-btn {
            background: #133640;
            border: 1px solid rgba(255, 255, 255, 0.08);
            color: #FFFFFF;
            border-radius: 10px;
            padding: 12px 22px;
            display: inline-flex;
            align-items: center;
            gap: 14px;
            text-align: left;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .ev-day-tab-btn .tab-icon {
            font-size: 22px;
            line-height: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .ev-day-tab-btn .day-title {
            font-size: clamp(13px, 1.05vw, 16px);
            font-weight: 800;
            display: block;
            line-height: 1.2;
        }

        .ev-day-tab-btn .day-date {
            font-size: clamp(11px, 0.9vw, 13.5px);
            opacity: 0.85;
            font-weight: 600;
            display: block;
        }

        .ev-day-tab-btn.active {
            background: var(--ev-lime);
            color: #04222A;
            border-color: var(--ev-lime);
            box-shadow: 0 10px 25px rgba(186, 249, 44, 0.18);
        }

        /* Schedule Timeline Row with 01 Dec Left Badge + Connector Line */
        .ev-schedule-row {
            display: flex;
            align-items: center;
            margin-bottom: 28px;
            position: relative;
        }

        .ev-schedule-date-col {
            display: flex;
            align-items: center;
            flex-shrink: 0;
        }

        .ev-schedule-date-circle {
            width: clamp(56px, 4.6vw, 72px);
            height: clamp(56px, 4.6vw, 72px);
            border-radius: 50%;
            background: var(--ev-lime);
            color: #04222A;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            line-height: 1.1;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.25);
        }

        .ev-schedule-date-circle .num {
            font-size: clamp(14px, 1.15vw, 18px);
            font-weight: 800;
        }

        .ev-schedule-date-circle .mon {
            font-size: clamp(11.5px, 0.95vw, 14px);
            font-weight: 700;
        }

        .ev-schedule-connector {
            width: clamp(20px, 1.8vw, 32px);
            height: 1.5px;
            background: rgba(255, 255, 255, 0.18);
        }

        .ev-schedule-card {
            flex-grow: 1;
            background: #13333D;
            border: 1px solid rgba(255, 255, 255, 0.07);
            border-radius: 16px;
            padding: clamp(20px, 2vw, 32px);
            transition: all 0.25s ease;
        }

        .ev-schedule-card:hover {
            border-color: rgba(186, 249, 44, 0.35);
            background: #163A45;
        }

        .ev-schedule-thumb {
            width: 100%;
            aspect-ratio: 126 / 76;
            object-fit: cover;
            border-radius: 10px;
            display: block;
        }

        .ev-schedule-meta {
            font-size: clamp(12px, 1vw, 15px);
            color: #E2F1F5;
            font-weight: 700;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }

        .ev-schedule-title {
            font-size: clamp(20px, 1.85vw, 28px);
            font-weight: 800;
            color: #FFFFFF;
            margin-bottom: 20px;
            letter-spacing: -0.01em;
        }

        .ev-schedule-speakers {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 32px;
            margin-bottom: 12px;
        }

        .ev-mini-speaker {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .ev-mini-speaker img {
            width: clamp(44px, 3.6vw, 56px);
            height: clamp(44px, 3.6vw, 56px);
            border-radius: 50%;
            object-fit: cover;
            background: #FFFFFF;
        }

        .ev-mini-speaker .sp-name {
            font-size: clamp(13px, 1.05vw, 16px);
            font-weight: 800;
            color: #FFFFFF;
            line-height: 1.2;
        }

        .ev-mini-speaker .sp-role {
            font-size: clamp(11.5px, 0.9vw, 14px);
            color: #B4CBD0;
        }

        .ev-view-details-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--ev-lime);
            font-size: clamp(13.5px, 1.1vw, 16.5px);
            font-weight: 800;
            text-decoration: none;
            background: none;
            border: none;
            padding: 0;
            cursor: pointer;
        }

        .ev-view-details-link span.txt {
            text-decoration: underline;
            text-underline-offset: 3px;
        }

        .ev-view-details-link:hover {
            color: #FFFFFF;
        }

        /* =====================================================================
           6. RECENT MEMORIES, PRICING, VENUE, BOTTOM BANNER & FOOTER
           ===================================================================== */
        .ev-memories-section {
            padding: 5.5vw 0;
            background: var(--ev-dark);
        }

        .ev-memory-card {
            border-radius: 12px;
            overflow: hidden;
            aspect-ratio: 92 / 50;
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 16px 36px rgba(0, 0, 0, 0.3);
        }

        .ev-memory-card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.45s ease;
        }

        .ev-memory-card:hover img {
            transform: scale(1.05);
        }

        .ev-pricing-section {
            padding: 5.5vw 0;
            background: var(--ev-dark-band);
        }

        .ev-price-card {
            background: var(--ev-card);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 14px;
            padding: 40px 30px;
            text-align: center;
            transition: all 0.25s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .ev-price-card.highlighted {
            background: var(--ev-lime);
            color: #04222A;
            border-color: var(--ev-lime);
            transform: scale(1.04);
            box-shadow: 0 22px 48px rgba(186, 249, 44, 0.2);
        }

        .ev-price-tier {
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 10px;
        }

        .ev-price-value {
            font-size: clamp(38px, 3.2vw, 52px);
            font-weight: 800;
            line-height: 1.05;
            margin-bottom: 22px;
        }

        .ev-price-divider {
            height: 2px;
            background: rgba(255, 255, 255, 0.14);
            margin: 0 auto 20px;
            width: 85%;
        }

        .ev-price-card.highlighted .ev-price-divider {
            background: rgba(4, 34, 42, 0.25);
        }

        .ev-price-sub {
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin-bottom: 24px;
        }

        .ev-venue-section {
            padding: 5.5vw 0 6vw;
            background: var(--ev-dark);
        }

        .ev-map-frame {
            border-radius: 14px;
            overflow: hidden;
            height: 100%;
            min-height: 310px;
            border: 1px solid rgba(255, 255, 255, 0.12);
        }

        .ev-map-frame img {
            width: 100%;
            height: 100%;
            min-height: 310px;
            object-fit: cover;
            display: block;
        }

        .ev-venue-contact-card {
            background: var(--ev-card);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            padding: 20px 24px;
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 16px;
        }

        .ev-venue-contact-card:last-child {
            margin-bottom: 0;
        }

        .ev-venue-icon {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.07);
            color: var(--ev-lime);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
        }

        .ev-venue-label {
            font-size: 11.5px;
            font-weight: 800;
            text-transform: uppercase;
            color: var(--ev-muted);
            letter-spacing: 0.05em;
            margin-bottom: 2px;
        }

        .ev-venue-val {
            font-size: 15.5px;
            font-weight: 700;
            color: #FFFFFF;
        }

        .ev-lime-banner {
            background: var(--ev-lime);
            color: #04222A;
            border-radius: 16px;
            padding: 36px 44px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.35);
        }

        .ev-white-cd-box {
            background: #FFFFFF;
            color: #04222A;
            border-radius: 10px;
            padding: 14px 26px;
            min-width: 105px;
            text-align: center;
        }

        .ev-white-cd-box .num {
            font-size: 26px;
            font-weight: 800;
            line-height: 1.1;
        }

        .ev-white-cd-box .lbl {
            font-size: 11.5px;
            font-weight: 600;
            color: #475569;
        }

        .ev-footer {
            background: var(--ev-dark-deep);
            padding: 72px 0 28px;
            color: var(--ev-muted);
            font-size: 14px;
        }

        .ev-footer-title {
            color: #FFFFFF;
            font-size: 17px;
            font-weight: 800;
            margin-bottom: 20px;
        }

        .ev-footer-links {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .ev-footer-links li {
            margin-bottom: 10px;
        }

        .ev-footer-links a {
            color: var(--ev-muted);
            text-decoration: none;
        }

        .ev-footer-links a:hover {
            color: var(--ev-lime);
        }

        .ev-footer-socials {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 20px;
        }

        .ev-footer-socials a {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.08);
            color: #FFFFFF;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            font-size: 14px;
        }

        .ev-footer-gallery-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
        }

        .ev-footer-gallery-grid img {
            width: 100%;
            aspect-ratio: 1 / 1;
            object-fit: cover;
            border-radius: 6px;
        }

        @media (max-width: 991px) {
            .ev-hero {
                min-height: auto;
                padding-bottom: 40px;
            }
            .ev-hero-body {
                max-width: 100%;
                padding-top: 36px;
            }
            .ev-hero-right-bg {
                position: relative;
                width: 100%;
                height: 420px;
                margin-top: 28px;
            }
            .ev-hero-right-bg img {
                object-fit: cover;
            }
            .ev-schedule-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }
            .ev-schedule-connector {
                display: none;
            }
            .ev-price-card.highlighted {
                transform: none;
            }
        }
    </style>
</head>
<body>

    @yield('content')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
