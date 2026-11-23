<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $s['brand_name'] ?? 'WonderLearn' }} — {{ $s['brand_tagline'] ?? 'Learn • Discover • Grow' }}</title>
    <meta name="description" content="{{ $s['hero_subtitle'] ?? 'A magical world where learning feels like an adventure!' }}">

    <!-- Google Fonts: Fredoka (Rounded Kid Display) & Nunito (Friendly Body) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@500;600;700&family=Nunito:wght@500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3 & Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            --wl-blue: {{ $activeTheme->primary_color ?? '#1554B4' }};
            --wl-blue-bright: #1D63C8;
            --wl-green: #169B56;
            --wl-footer-green: {{ $activeTheme->secondary_color ?? '#14884B' }};
            --wl-yellow: #FACC15;
            --wl-yellow-hover: #F59E0B;
            --wl-navy: #1E293B;
            --wl-sky-light: #E6F6FE;
            --wl-sky-mid: #D3EFFD;
        }

        * {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Nunito', sans-serif;
            background: #FFFFFF;
            color: var(--wl-navy);
            margin: 0;
            width: 100%;
            overflow-x: hidden;
        }

        h1, h2, h3, h4, .wl-display {
            font-family: 'Fredoka', sans-serif;
        }

        /* Full-Window Edge-to-Edge Wrapper */
        .wl-page-canvas {
            width: 100%;
            max-width: 100%;
            margin: 0;
            background: #FFFFFF;
            position: relative;
            overflow: hidden;
        }

        /* =====================================================================
           1. TOP CLOUD NAVBAR (Full Window)
           ===================================================================== */
        .wl-navbar {
            background: #FFFFFF;
            width: 100%;
            padding: 14px 4vw 12px;
            position: relative;
            z-index: 50;
        }

        .wl-navbar::after {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            bottom: -11px;
            height: 14px;
            background-image: radial-gradient(circle at 18px 0, #FFFFFF 15px, transparent 16px);
            background-size: 36px 14px;
            background-repeat: repeat-x;
            filter: drop-shadow(0 3px 3px rgba(14, 116, 144, 0.08));
            z-index: 5;
            pointer-events: none;
        }

        .wl-brand {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }

        .wl-brand-icon {
            width: 48px;
            height: 40px;
            object-fit: contain;
        }

        .wl-brand-title {
            font-family: 'Fredoka', sans-serif;
            font-size: 25px;
            font-weight: 700;
            line-height: 1;
            letter-spacing: -0.01em;
        }

        .wl-brand-title .w-blue {
            color: #1A62C6;
        }

        .wl-brand-title .w-orange {
            color: #F59E0B;
        }

        .wl-brand-sub {
            display: block;
            font-size: 10.5px;
            font-weight: 800;
            color: #1E3A8A;
            letter-spacing: 0.04em;
            margin-top: 2px;
        }

        .wl-nav-links {
            display: flex;
            align-items: center;
            gap: 28px;
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .wl-nav-links a {
            text-decoration: none;
            color: #1E293B;
            font-weight: 800;
            font-size: 14.5px;
            transition: color 0.18s;
        }

        .wl-nav-links a:hover,
        .wl-nav-links a.active {
            color: var(--wl-blue-bright);
        }

        .wl-nav-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .wl-btn-search {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #E8F2FF;
            border: 1px solid #CFE3FF;
            color: #1D63C8;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .wl-btn-search:hover {
            background: #D8E8FF;
            transform: scale(1.05);
        }

        .wl-btn-login {
            background: linear-gradient(180deg, #FDE047 0%, #FACC15 100%);
            color: #1E293B;
            font-weight: 800;
            font-size: 13.5px;
            padding: 7px 20px;
            border-radius: 999px;
            border: 1px solid #F59E0B;
            box-shadow: 0 3px 0 #D97706;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            cursor: pointer;
            transition: all 0.18s;
        }

        .wl-btn-login:hover {
            transform: translateY(-1px);
            background: #FACC15;
            color: #0F172A;
        }

        .wl-btn-join {
            background: linear-gradient(180deg, #22C55E 0%, #159947 100%);
            color: #FFFFFF;
            font-weight: 800;
            font-size: 13.5px;
            padding: 7px 18px;
            border-radius: 999px;
            border: 1px solid #15803D;
            box-shadow: 0 3px 0 #166534;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            transition: all 0.18s;
        }

        .wl-btn-join:hover {
            transform: translateY(-1px);
            color: #FFFFFF;
            background: #16A34A;
        }

        /* =====================================================================
           2. HERO SECTION (1:1 Same-to-Same Treehouse World)
           ===================================================================== */
        .wl-hero {
            position: relative;
            width: 100%;
            aspect-ratio: 576 / 253;
            background-size: 100% 100%;
            background-position: center top;
            background-repeat: no-repeat;
            overflow: hidden;
        }

        .wl-hero-center-overlay {
            position: absolute;
            top: 9.5%;
            left: 50.8%;
            transform: translateX(-50%);
            text-align: center;
            width: 38%;
            z-index: 10;
        }

        .wl-hero-title-1 {
            font-family: 'Fredoka', sans-serif;
            font-size: clamp(24px, 3.85vw, 46px);
            font-weight: 700;
            color: #1554B4;
            line-height: 1.05;
            margin: 0;
            text-shadow: 0 2px 0 rgba(255, 255, 255, 0.9), 0 4px 14px rgba(255, 255, 255, 0.85);
        }

        .wl-hero-title-2 {
            font-family: 'Fredoka', sans-serif;
            font-size: clamp(23px, 3.65vw, 44px);
            font-weight: 700;
            color: #179454;
            line-height: 1.08;
            margin: 0 0 6px 0;
            text-shadow: 0 2px 0 rgba(255, 255, 255, 0.9), 0 4px 14px rgba(255, 255, 255, 0.85);
        }

        .wl-hero-sub {
            font-size: clamp(11px, 1.25vw, 15px);
            font-weight: 800;
            color: #1E293B;
            line-height: 1.32;
            margin: 0 auto 10px;
            max-width: 290px;
            text-shadow: 0 1px 6px rgba(255, 255, 255, 0.95);
        }

        .wl-btn-explore {
            background: linear-gradient(180deg, #FDE047 0%, #FACC15 100%);
            color: #1E293B;
            font-weight: 900;
            font-size: clamp(11px, 1.15vw, 14px);
            padding: 8px 20px;
            border-radius: 999px;
            border: 1.5px solid #F59E0B;
            box-shadow: 0 4px 0 #D97706, 0 8px 16px rgba(15, 23, 42, 0.14);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
        }

        .wl-btn-explore:hover {
            transform: translateY(-2px);
            color: #0F172A;
            background: #FACC15;
        }

        /* =====================================================================
           3. FLOATING 4-BADGE TRUST BAR (100% Full Window Content)
           ===================================================================== */
        .wl-trust-wrap {
            position: relative;
            z-index: 25;
            margin-top: -32px;
            padding: 0 4vw;
            width: 100%;
        }

        .wl-trust-bar {
            width: 100%;
            max-width: 100%;
            margin: 0 auto;
            background: #FFFFFF;
            border-radius: 999px;
            padding: 15px 3vw;
            box-shadow: 0 12px 32px rgba(14, 116, 144, 0.14);
            border: 1.5px solid #E2F2FE;
            display: flex;
            align-items: center;
            justify-content: space-around;
            flex-wrap: wrap;
            gap: 12px;
        }

        .wl-trust-item {
            display: flex;
            align-items: center;
            justify-content: center;
            flex: 1;
            gap: 10px;
            font-size: 16px;
            font-weight: 800;
            color: #1E293B;
            padding: 2px 14px;
            position: relative;
        }

        .wl-trust-item:not(:last-child)::after {
            content: "";
            position: absolute;
            right: 0;
            top: 15%;
            height: 70%;
            width: 1.5px;
            background: #E2E8F0;
        }

        .wl-trust-icon {
            width: 32px;
            height: 32px;
            border-radius: 9px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 17px;
        }

        /* =====================================================================
           4. EXPLORE BY TOPIC SECTION (100% Full Window Content)
           ===================================================================== */
        .wl-topics-section {
            position: relative;
            width: 100%;
            background: linear-gradient(180deg, #E3F5FE 0%, #EFF9FE 100%);
            padding: 56px 2.5vw 46px;
            margin-top: -26px;
            overflow: hidden;
        }

        .wl-section-title {
            font-family: 'Fredoka', sans-serif;
            font-size: clamp(28px, 2.4vw, 36px);
            font-weight: 700;
            color: #1554B4;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            margin: 0;
        }

        .wl-sparkle {
            color: #FACC15;
            font-size: 24px;
            filter: drop-shadow(0 2px 3px rgba(245, 158, 11, 0.25));
        }

        .wl-topics-stage {
            position: relative;
            width: 100%;
            max-width: 100%;
            margin: 24px 0 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        .wl-mascot-bunny {
            width: clamp(145px, 12vw, 220px);
            height: auto;
            object-fit: contain;
            flex-shrink: 0;
            margin-top: -24px;
            filter: drop-shadow(0 8px 14px rgba(14, 116, 144, 0.12));
        }

        .wl-mascot-signpost {
            width: clamp(145px, 12vw, 220px);
            height: auto;
            object-fit: contain;
            flex-shrink: 0;
            margin-top: -30px;
            filter: drop-shadow(0 8px 14px rgba(14, 116, 144, 0.12));
        }

        .wl-topics-grid {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 18px;
            flex-grow: 1;
            width: 100%;
            max-width: 100%;
            margin: 0;
        }

        .wl-topic-card {
            background: #FFFFFF;
            border-radius: 20px;
            padding: 8px;
            box-shadow: 0 8px 22px rgba(14, 116, 144, 0.09);
            text-decoration: none;
            transition: all 0.22s ease;
            cursor: pointer;
            display: block;
        }

        .wl-topic-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 14px 28px rgba(14, 116, 144, 0.16);
        }

        .wl-topic-inner {
            border-radius: 15px;
            padding: 22px 12px 18px;
            text-align: center;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            border: 1.5px solid transparent;
        }

        .wl-topic-img {
            width: 104px;
            height: 88px;
            object-fit: contain;
            margin-bottom: 12px;
            border-radius: 8px;
        }

        .wl-topic-name {
            font-family: 'Fredoka', sans-serif;
            font-size: 18px;
            font-weight: 700;
            color: #1E293B;
            margin: 0;
        }

        /* =====================================================================
           5. MAGICAL STORIES SECTION (100% Full Window Content)
           ===================================================================== */
        .wl-stories-section {
            background: #FFFFFF;
            width: 100%;
            padding: 44px 4vw 48px;
            position: relative;
        }

        .wl-section-header-row {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 26px;
            width: 100%;
            max-width: 100%;
        }

        .wl-btn-view-all {
            position: absolute;
            right: 0;
            background: #FFFFFF;
            border: 1.5px solid #CBD5E1;
            color: #334155;
            font-weight: 800;
            font-size: 14px;
            padding: 8px 20px;
            border-radius: 999px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.18s;
            cursor: pointer;
        }

        .wl-btn-view-all:hover {
            border-color: var(--wl-blue-bright);
            color: var(--wl-blue-bright);
            background: #F0F7FF;
        }

        .wl-carousel-stage {
            position: relative;
            width: 100%;
            max-width: 100%;
            margin: 0;
        }

        .wl-nav-arrow {
            position: absolute;
            top: 44%;
            transform: translateY(-50%);
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: #1A66CC;
            color: #FFFFFF;
            border: none;
            box-shadow: 0 4px 12px rgba(26, 102, 204, 0.35);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            cursor: pointer;
            z-index: 10;
            transition: all 0.18s;
        }

        .wl-nav-arrow:hover {
            background: #1554B4;
            transform: translateY(-50%) scale(1.08);
        }

        .wl-nav-arrow.prev {
            left: -22px;
        }

        .wl-nav-arrow.next {
            right: -22px;
        }

        .wl-stories-grid {
            width: 100%;
            max-width: 100%;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 24px;
        }

        .wl-story-card {
            background: #FFFFFF;
            border: 1.5px solid #E2E8F0;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 8px 22px rgba(15, 23, 42, 0.05);
            transition: all 0.22s ease;
            cursor: pointer;
            display: flex;
            flex-direction: column;
        }

        .wl-story-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 14px 28px rgba(21, 84, 180, 0.13);
            border-color: #BAE6FD;
        }

        .wl-story-thumb {
            width: 100%;
            aspect-ratio: 108 / 69;
            object-fit: cover;
            display: block;
        }

        .wl-story-body {
            padding: 16px 18px 18px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .wl-story-title {
            font-family: 'Fredoka', sans-serif;
            font-size: 18.5px;
            font-weight: 700;
            color: #1E293B;
            line-height: 1.25;
            margin: 0 0 12px 0;
        }

        .wl-story-tag {
            display: inline-block;
            font-size: 12.5px;
            font-weight: 800;
            padding: 4px 16px;
            border-radius: 999px;
            align-self: flex-start;
        }

        /* =====================================================================
           6. PLAY & LEARN GAMES SECTION (100% Full Window Content)
           ===================================================================== */
        .wl-games-section {
            background: linear-gradient(180deg, #E2F5FE 0%, #EBF8FF 100%);
            width: 100%;
            padding: 44px 4vw 52px;
            position: relative;
        }

        .wl-games-grid {
            width: 100%;
            max-width: 100%;
            margin: 0;
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 24px;
        }

        .wl-game-card {
            background: #FFFFFF;
            border-radius: 22px;
            padding: 8px 8px 24px;
            box-shadow: 0 8px 22px rgba(14, 116, 144, 0.09);
            position: relative;
            cursor: pointer;
            transition: all 0.22s ease;
            text-align: center;
        }

        .wl-game-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 14px 28px rgba(14, 116, 144, 0.18);
        }

        .wl-game-img {
            width: 100%;
            aspect-ratio: 82 / 54;
            object-fit: cover;
            border-radius: 15px;
            display: block;
        }

        .wl-game-play-btn {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: linear-gradient(180deg, #4ADE80 0%, #16A34A 100%);
            color: #FFFFFF;
            border: 3px solid #FFFFFF;
            box-shadow: 0 4px 10px rgba(22, 163, 74, 0.35);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            position: absolute;
            bottom: 4px;
            left: 50%;
            transform: translateX(-50%);
            transition: transform 0.2s;
        }

        .wl-game-card:hover .wl-game-play-btn {
            transform: translateX(-50%) scale(1.12);
        }

        /* =====================================================================
           7. FEATURED ADVENTURES BANNER CAROUSEL (100% Full Window Content)
           ===================================================================== */
        .wl-adventures-section {
            background: #FFFFFF;
            width: 100%;
            padding: 44px 4vw 36px;
            position: relative;
        }

        .wl-adventure-banner-wrap {
            position: relative;
            width: 100%;
            max-width: 100%;
            margin: 22px 0 0;
        }

        .wl-adventure-card {
            position: relative;
            width: 100%;
            aspect-ratio: 470 / 72;
            border-radius: 26px;
            overflow: hidden;
            background-size: 100% 100%;
            background-position: center;
            box-shadow: 0 12px 30px rgba(14, 116, 144, 0.13);
            border: 2px solid #BAE6FD;
        }

        .wl-adventure-content {
            position: absolute;
            top: 50%;
            left: 52.5%;
            transform: translateY(-50%);
            width: 35%;
            text-align: left;
            z-index: 5;
        }

        .wl-adventure-title {
            font-family: 'Fredoka', sans-serif;
            font-size: clamp(20px, 2.4vw, 34px);
            font-weight: 700;
            color: #1554B4;
            margin: 0 0 8px 0;
            line-height: 1.15;
        }

        .wl-adventure-desc {
            font-size: clamp(12px, 1.2vw, 17px);
            font-weight: 700;
            color: #1E293B;
            line-height: 1.4;
            margin: 0 0 14px 0;
        }

        .wl-btn-adventure {
            background: linear-gradient(180deg, #FDE047 0%, #FACC15 100%);
            color: #1E293B;
            font-weight: 900;
            font-size: clamp(12px, 1.15vw, 15px);
            padding: 9px 24px;
            border-radius: 999px;
            border: 1px solid #F59E0B;
            box-shadow: 0 3px 0 #D97706;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
        }

        .wl-adv-arrow {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: #FFFFFF;
            border: 1.5px solid #CBD5E1;
            color: #1E293B;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 4px 10px rgba(15, 23, 42, 0.08);
            z-index: 10;
        }

        .wl-adv-arrow.prev {
            left: -22px;
        }

        .wl-adv-arrow.next {
            right: -22px;
        }

        .wl-dots {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 14px;
        }

        .wl-dot {
            width: 9px;
            height: 9px;
            border-radius: 50%;
            background: #CBD5E1;
            border: none;
            padding: 0;
            cursor: pointer;
        }

        .wl-dot.active {
            background: #22C55E;
            width: 11px;
            height: 11px;
        }

        /* =====================================================================
           8. LOVED BY PARENTS TESTIMONIALS SECTION (100% Full Window Content)
           ===================================================================== */
        .wl-parents-section {
            background: linear-gradient(180deg, #E6F6FE 0%, #D8F1FD 100%);
            width: 100%;
            padding: 40px 4vw 38px;
            position: relative;
        }

        .wl-parents-stage {
            position: relative;
            width: 100%;
            max-width: 100%;
            margin: 22px 0 0;
        }

        .wl-butterfly-deco {
            position: absolute;
            top: -42px;
            right: 8px;
            width: 62px;
            height: auto;
            pointer-events: none;
            z-index: 10;
        }

        .wl-parents-grid {
            width: 100%;
            max-width: 100%;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
        }

        .wl-parent-card {
            background: #FFFFFF;
            border-radius: 22px;
            padding: 22px 26px;
            box-shadow: 0 8px 22px rgba(14, 116, 144, 0.08);
            display: flex;
            align-items: flex-start;
            gap: 16px;
            border: 1.5px solid #FFFFFF;
        }

        .wl-parent-avatar {
            width: 66px;
            height: 66px;
            border-radius: 50%;
            object-fit: cover;
            flex-shrink: 0;
            border: 2px solid #E0F2FE;
        }

        .wl-parent-quote {
            font-size: 15px;
            font-weight: 700;
            color: #1E293B;
            line-height: 1.45;
            margin: 0 0 12px 0;
        }

        .wl-parent-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
        }

        .wl-parent-name {
            font-weight: 900;
            font-size: 15px;
            color: #0F172A;
        }

        .wl-stars {
            color: #FACC15;
            font-size: 15px;
            letter-spacing: 1px;
        }

        /* =====================================================================
           9. LEARNING ON THE GO! APP PROMO BANNER (100% Full Window Content)
           ===================================================================== */
        .wl-app-section {
            background: linear-gradient(180deg, #CDECFB 0%, #B9E3FA 100%);
            width: 100%;
            padding: 28px 4vw 0;
            position: relative;
            overflow: hidden;
        }

        .wl-app-inner {
            width: 100%;
            max-width: 100%;
            margin: 0;
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 24px;
        }

        .wl-app-mascot-left {
            width: clamp(220px, 20vw, 320px);
            height: auto;
            object-fit: contain;
            display: block;
        }

        .wl-app-mascot-right {
            width: clamp(240px, 22vw, 350px);
            height: auto;
            object-fit: contain;
            display: block;
        }

        .wl-app-center {
            text-align: center;
            padding-bottom: 34px;
            flex-grow: 1;
        }

        .wl-app-title {
            font-family: 'Fredoka', sans-serif;
            font-size: clamp(26px, 2.5vw, 38px);
            font-weight: 700;
            color: #1554B4;
            margin: 0 0 8px 0;
        }

        .wl-app-sub {
            font-size: 16px;
            font-weight: 700;
            color: #1E293B;
            max-width: 480px;
            margin: 0 auto 16px;
            line-height: 1.4;
        }

        .wl-store-badges {
            display: inline-flex;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .wl-store-btn {
            background: #0F172A;
            color: #FFFFFF;
            border-radius: 10px;
            padding: 9px 20px;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            border: 1px solid #334155;
            transition: transform 0.18s;
        }

        .wl-store-btn:hover {
            transform: translateY(-2px);
            color: #FFFFFF;
            background: #1E293B;
        }

        .wl-store-btn i {
            font-size: 24px;
        }

        .wl-store-text small {
            display: block;
            font-size: 10px;
            line-height: 1;
            opacity: 0.85;
            text-align: left;
        }

        .wl-store-text strong {
            display: block;
            font-size: 15.5px;
            font-weight: 800;
            line-height: 1.15;
            text-align: left;
        }

        /* =====================================================================
           10. EMERALD GREEN CURVED HILL FOOTER (100% Full Window Content)
           ===================================================================== */
        .wl-footer-wave {
            display: block;
            width: 100%;
            height: 48px;
            background: #B9E3FA;
            margin-bottom: -1px;
        }

        .wl-footer {
            background: var(--wl-footer-green);
            color: #FFFFFF;
            width: 100%;
            padding: 28px 4vw 50px;
            position: relative;
        }

        .wl-footer-grid {
            width: 100%;
            max-width: 100%;
            margin: 0;
            display: grid;
            grid-template-columns: 1.4fr 0.9fr 0.9fr 1fr 1.6fr;
            gap: 36px;
        }

        .wl-footer-brand-title {
            font-family: 'Fredoka', sans-serif;
            font-size: 22px;
            font-weight: 700;
            color: #FFFFFF;
            line-height: 1;
        }

        .wl-footer-brand-sub {
            font-size: 10px;
            font-weight: 700;
            color: #A7F3D0;
            display: block;
            margin-top: 2px;
        }

        .wl-footer-tagline {
            font-size: 12.5px;
            color: #E2F8EC;
            margin-top: 12px;
            line-height: 1.45;
            font-weight: 600;
        }

        .wl-footer-col h5 {
            font-family: 'Fredoka', sans-serif;
            font-size: 14.5px;
            font-weight: 600;
            color: #FDE047;
            margin: 0 0 10px 0;
        }

        .wl-footer-links {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .wl-footer-links li {
            margin-bottom: 5px;
        }

        .wl-footer-links a {
            color: #ECFDF5;
            text-decoration: none;
            font-size: 12.5px;
            font-weight: 700;
            transition: color 0.15s;
        }

        .wl-footer-links a:hover {
            color: #FDE047;
        }

        .wl-newsletter-form {
            display: flex;
            background: #FFFFFF;
            border-radius: 999px;
            padding: 3px;
            margin-top: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
        }

        .wl-newsletter-form input {
            border: none;
            background: transparent;
            padding: 6px 14px;
            font-size: 12.5px;
            color: #1E293B;
            flex-grow: 1;
            outline: none;
            min-width: 0;
        }

        .wl-newsletter-form button {
            background: #FACC15;
            color: #1E293B;
            font-weight: 900;
            font-size: 12.5px;
            border: none;
            border-radius: 999px;
            padding: 6px 18px;
            cursor: pointer;
            transition: background 0.18s;
        }

        .wl-newsletter-form button:hover {
            background: #F59E0B;
        }

        .wl-social-row {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 14px;
        }

        .wl-social-btn {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #FFFFFF;
            font-size: 14px;
            text-decoration: none;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.18);
            transition: transform 0.18s;
        }

        .wl-social-btn:hover {
            transform: translateY(-2px);
            color: #FFFFFF;
        }

        /* Responsive Breakpoints */
        @media (max-width: 991px) {
            .wl-topics-grid {
                grid-template-columns: repeat(3, 1fr);
            }
            .wl-mascot-bunny, .wl-mascot-signpost {
                display: none;
            }
            .wl-stories-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .wl-games-grid {
                grid-template-columns: repeat(3, 1fr);
            }
            .wl-parents-grid {
                grid-template-columns: 1fr;
            }
            .wl-footer-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .wl-nav-arrow.prev, .wl-adv-arrow.prev {
                left: -14px;
            }
            .wl-nav-arrow.next, .wl-adv-arrow.next {
                right: -14px;
            }
        }

        @media (max-width: 767px) {
            .wl-navbar {
                padding: 12px 16px;
            }
            .wl-nav-links {
                display: none;
            }
            .wl-hero {
                aspect-ratio: auto;
                min-height: 320px;
                background-size: cover;
            }
            .wl-hero-center-overlay {
                width: 82%;
                top: 12%;
            }
            .wl-topics-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .wl-stories-section, .wl-games-section, .wl-adventures-section, .wl-parents-section, .wl-app-section, .wl-footer {
                padding-left: 18px;
                padding-right: 18px;
            }
            .wl-games-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .wl-adventure-card {
                aspect-ratio: auto;
                min-height: 210px;
                background-size: cover;
            }
            .wl-adventure-content {
                position: relative;
                left: auto;
                top: auto;
                transform: none;
                width: 100%;
                background: rgba(255, 255, 255, 0.88);
                padding: 16px;
                border-radius: 14px;
                margin: 16px;
            }
            .wl-app-inner {
                flex-direction: column;
                align-items: center;
            }
            .wl-footer-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    @yield('content')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
