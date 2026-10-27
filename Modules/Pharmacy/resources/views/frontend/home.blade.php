@php
    $activeCount = isset($activeModuleWebsites) ? $activeModuleWebsites->count() : 1;
    $basePrefix = $activeCount === 1 ? '' : '/pharmacy';
    $homeUrl = $basePrefix === '' ? url('/') : url($basePrefix);
    // Static theme images stored inside Modules/Pharmacy/Public/uploads/theme/
    $img = fn (string $file) => route('module.media.serve', ['module' => 'Pharmacy', 'folder' => 'theme', 'filename' => $file . '.jpg']);
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nexora Pharma — Better Health, Brighter Tomorrow</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --nx-green: #1E5B3A;
            --nx-green-dark: #154A2E;
            --nx-green-mid: #2E7D52;
            --nx-mint: #EEF5EF;
            --nx-mint-2: #E3EFE5;
            --nx-mint-3: #D5E8D9;
            --nx-dark: #12241A;
            --nx-text: #4A5A50;
            --nx-muted: #7A8A80;
            --nx-border: #E2EAE4;
        }
        * { box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; color: var(--nx-text); background: #fff; font-size: 14px; }
        h1, h2, h3, h4, h5, h6, .nx-heading { font-family: 'Poppins', sans-serif; color: var(--nx-dark); }
        a { text-decoration: none; }
        .container { max-width: 1200px; }

        /* Eyebrow & headings */
        .nx-eyebrow { font-size: 11px; font-weight: 600; letter-spacing: .14em; text-transform: uppercase; color: var(--nx-green); margin-bottom: 8px; display: block; }
        .nx-title { font-size: 34px; font-weight: 600; line-height: 1.18; margin-bottom: 14px; }
        .nx-lead { font-size: 13.5px; line-height: 1.7; color: var(--nx-text); }

        /* Buttons */
        .nx-btn { display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; border-radius: 6px; font-size: 13px; font-weight: 500; transition: all .2s ease; border: 1px solid transparent; }
        .nx-btn-primary { background: var(--nx-green); color: #fff; }
        .nx-btn-primary:hover { background: var(--nx-green-dark); color: #fff; }
        .nx-btn-outline { background: #fff; color: var(--nx-dark); border-color: #C9D6CD; }
        .nx-btn-outline:hover { border-color: var(--nx-green); color: var(--nx-green); }
        .nx-link { font-size: 12px; font-weight: 600; color: var(--nx-green); letter-spacing: .04em; }
        .nx-link:hover { color: var(--nx-green-dark); }

        /* Header */
        .nx-header { background: #fff; border-bottom: 1px solid var(--nx-border); position: sticky; top: 0; z-index: 100; }
        .nx-header .container { display: flex; align-items: center; justify-content: space-between; height: 64px; }
        .nx-logo { display: flex; align-items: center; gap: 10px; }
        .nx-logo-name { font-family: 'Poppins', sans-serif; font-weight: 700; font-size: 17px; color: var(--nx-green); line-height: 1; }
        .nx-logo-tag { font-size: 10px; color: var(--nx-muted); margin-top: 3px; }
        .nx-nav { display: flex; align-items: center; gap: 26px; list-style: none; margin: 0; padding: 0; }
        .nx-nav a { font-size: 13px; font-weight: 500; color: var(--nx-dark); padding: 22px 0; position: relative; }
        .nx-nav a.active::after, .nx-nav a:hover::after { content: ''; position: absolute; left: 0; right: 0; bottom: 14px; height: 2px; background: var(--nx-green); }
        .nx-header-actions { display: flex; align-items: center; gap: 16px; }
        .nx-btn-quote { background: var(--nx-mint-2); color: var(--nx-green); font-weight: 600; font-size: 12.5px; padding: 9px 18px; border-radius: 6px; }
        .nx-btn-quote:hover { background: var(--nx-mint-3); color: var(--nx-green-dark); }
        .nx-search { color: var(--nx-dark); font-size: 16px; }

        /* Hero */
        .nx-hero { position: relative; background: #EAF3EC url('{{ $img('hero') }}') right center / cover no-repeat; min-height: 480px; display: flex; align-items: center; }
        .nx-hero::before { content: ''; position: absolute; inset: 0; background: linear-gradient(90deg, rgba(234,243,236,.96) 0%, rgba(234,243,236,.9) 32%, rgba(234,243,236,.45) 48%, rgba(234,243,236,0) 60%); }
        .nx-hero .container { position: relative; z-index: 2; }
        .nx-hero h1 { font-size: 46px; font-weight: 600; line-height: 1.12; margin-bottom: 16px; }
        .nx-hero h1 span { color: var(--nx-green); display: block; }
        .nx-hero p { font-size: 14px; line-height: 1.7; max-width: 400px; margin-bottom: 26px; }

        /* Features strip */
        .nx-features { background: #fff; border-bottom: 1px solid var(--nx-border); }
        .nx-feature { text-align: center; padding: 30px 18px; position: relative; }
        .nx-feature + .nx-feature::before { content: ''; position: absolute; left: 0; top: 28px; bottom: 28px; width: 1px; background: var(--nx-border); }
        .nx-feature i { font-size: 30px; color: var(--nx-green); display: block; margin-bottom: 10px; }
        .nx-feature h6 { font-size: 14px; font-weight: 600; margin-bottom: 4px; }
        .nx-feature p { font-size: 12px; color: var(--nx-muted); margin: 0; line-height: 1.5; }

        /* About */
        .nx-section { padding: 70px 0; }
        .nx-about-img { width: 100%; height: 280px; object-fit: cover; border-radius: 4px; }
        .nx-stat { padding: 14px 0; border-bottom: 1px solid var(--nx-border); }
        .nx-stat:last-child { border-bottom: none; }
        .nx-stat strong { font-family: 'Poppins', sans-serif; font-size: 30px; font-weight: 600; color: var(--nx-dark); display: block; line-height: 1.1; }
        .nx-stat span { font-size: 12px; color: var(--nx-text); }
        .nx-stats-wrap { border-left: 1px solid var(--nx-border); padding-left: 28px; }

        /* Therapeutic areas */
        .nx-bg-mint { background: var(--nx-mint); }
        .nx-area-card { background: #fff; border: 1px solid var(--nx-border); border-radius: 8px; padding: 18px 10px; text-align: center; height: 100%; transition: all .2s ease; }
        .nx-area-card:hover { border-color: var(--nx-green); box-shadow: 0 8px 22px rgba(30,91,58,.08); transform: translateY(-2px); }
        .nx-area-icon { width: 46px; height: 46px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 20px; margin-bottom: 10px; }
        .nx-area-card h6 { font-size: 12.5px; font-weight: 500; margin: 0; color: var(--nx-dark); font-family: 'Inter', sans-serif; }

        /* Values */
        .nx-values { background: linear-gradient(180deg, #F4F9F5 0%, #EEF5EF 100%); padding: 60px 0; }
        .nx-value { text-align: center; padding: 0 10px; }
        .nx-value-icon { width: 54px; height: 54px; border-radius: 50%; background: #fff; border: 1px solid var(--nx-border); display: inline-flex; align-items: center; justify-content: center; color: var(--nx-green); font-size: 22px; margin-bottom: 10px; }
        .nx-value h6 { font-size: 13.5px; font-weight: 600; margin-bottom: 2px; }
        .nx-value p { font-size: 11.5px; color: var(--nx-muted); margin: 0; }

        /* Marquee */
        .nx-marquee-wrap { background: #fff; border-bottom: 1px solid var(--nx-border); padding: 36px 0; overflow: hidden; }
        .nx-marquee-label { text-align: center; font-size: 11px; font-weight: 600; letter-spacing: .16em; text-transform: uppercase; color: var(--nx-muted); margin-bottom: 22px; }
        .nx-marquee-mask {
            position: relative;
            width: 100%;
            overflow: hidden;
            -webkit-mask-image: linear-gradient(to right, transparent 0%, #000 8%, #000 92%, transparent 100%);
            mask-image: linear-gradient(to right, transparent 0%, #000 8%, #000 92%, transparent 100%);
        }
        .nx-marquee-track {
            display: flex;
            align-items: center;
            gap: 24px;
            width: max-content;
            animation: nxMarqueeScroll 26s linear infinite;
        }
        .nx-marquee-mask:hover .nx-marquee-track { animation-play-state: paused; }
        @keyframes nxMarqueeScroll {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }
        .nx-brand-card {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            padding: 12px 22px;
            background: #fff;
            border: 1px solid var(--nx-border);
            border-radius: 999px;
            box-shadow: 0 2px 8px rgba(18,36,26,.03);
            white-space: nowrap;
            transition: all .25s ease;
            cursor: default;
        }
        .nx-brand-card:hover {
            border-color: var(--nx-green);
            background: var(--nx-mint);
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(30,91,58,.08);
        }
        .nx-brand-icon {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--nx-mint);
            color: var(--nx-green);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            flex-shrink: 0;
            transition: all .25s ease;
        }
        .nx-brand-card:hover .nx-brand-icon { background: var(--nx-green); color: #fff; }
        .nx-brand-name { font-family: 'Poppins', sans-serif; font-size: 12.5px; font-weight: 600; color: var(--nx-dark); }
        .nx-brand-tag { font-size: 10px; color: var(--nx-muted); display: block; line-height: 1; margin-top: 2px; }

        /* Medicine Gallery */
        .nx-filter-tabs { display: flex; flex-wrap: wrap; gap: 8px; justify-content: center; margin-bottom: 34px; }
        .nx-filter-btn {
            background: #fff;
            border: 1px solid var(--nx-border);
            padding: 8px 18px;
            border-radius: 999px;
            font-size: 12.5px;
            font-weight: 500;
            color: var(--nx-dark);
            cursor: pointer;
            transition: all .2s ease;
        }
        .nx-filter-btn:hover, .nx-filter-btn.active {
            background: var(--nx-green);
            color: #fff;
            border-color: var(--nx-green);
        }
        .nx-med-card {
            background: #fff;
            border: 1px solid var(--nx-border);
            border-radius: 10px;
            overflow: hidden;
            height: 100%;
            display: flex;
            flex-direction: column;
            transition: all .25s ease;
            position: relative;
        }
        .nx-med-card:hover {
            border-color: var(--nx-green);
            transform: translateY(-4px);
            box-shadow: 0 12px 28px rgba(18,36,26,.08);
        }
        .nx-med-thumb {
            width: 100%;
            height: 190px;
            object-fit: cover;
            background: #F8FAF9;
            transition: transform .4s ease;
        }
        .nx-med-card:hover .nx-med-thumb { transform: scale(1.04); }
        .nx-med-img-box { position: relative; overflow: hidden; background: #F8FAF9; }
        .nx-med-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(18,36,26,.85);
            color: #fff;
            font-size: 10px;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 999px;
            letter-spacing: .04em;
        }
        .nx-med-body { padding: 18px; display: flex; flex-direction: column; flex-grow: 1; }
        .nx-med-cat {
            display: inline-block;
            font-size: 10.5px;
            font-weight: 600;
            letter-spacing: .06em;
            text-transform: uppercase;
            padding: 3px 8px;
            border-radius: 4px;
            margin-bottom: 8px;
            width: fit-content;
        }
        .nx-med-title { font-size: 15px; font-weight: 600; color: var(--nx-dark); margin-bottom: 4px; }
        .nx-med-generic { font-size: 12px; color: var(--nx-muted); font-style: italic; margin-bottom: 10px; }
        .nx-med-meta { font-size: 11.5px; color: var(--nx-text); display: flex; align-items: center; gap: 6px; margin-bottom: 14px; }
        .nx-med-meta i { color: var(--nx-green); }
        .nx-med-footer { margin-top: auto; padding-top: 12px; border-top: 1px solid #F1F5F2; display: flex; align-items: center; justify-content: space-between; }

        /* Image Gallery */
        .nx-gallery-item { text-align: center; margin-bottom: 24px; }
        .nx-blob { position: relative; width: 100%; aspect-ratio: 1.12 / 1; overflow: hidden; margin-bottom: 14px; transition: transform .35s ease; box-shadow: 0 8px 24px rgba(18,36,26,.06); }
        .nx-blob img { width: 100%; height: 100%; object-fit: cover; transition: transform .5s ease; }
        .nx-gallery-item:hover .nx-blob img { transform: scale(1.06); }
        .nx-blob-1 { border-radius: 46% 54% 42% 58% / 52% 40% 60% 48%; }
        .nx-blob-2 { border-radius: 58% 42% 55% 45% / 45% 55% 45% 55%; }
        .nx-blob-3 { border-radius: 40% 60% 48% 52% / 58% 46% 54% 42%; }
        .nx-blob-4 { border-radius: 55% 45% 60% 40% / 42% 58% 42% 58%; }
        .nx-gallery-item h6 { font-size: 14.5px; font-weight: 600; line-height: 1.35; margin-bottom: 4px; }

        /* Video Gallery */
        .nx-video-card {
            background: #fff;
            border: 1px solid var(--nx-border);
            border-radius: 12px;
            overflow: hidden;
            height: 100%;
            transition: all .25s ease;
            display: flex;
            flex-direction: column;
        }
        .nx-video-card:hover {
            border-color: var(--nx-green);
            transform: translateY(-4px);
            box-shadow: 0 14px 30px rgba(18,36,26,.09);
        }
        .nx-video-poster {
            position: relative;
            width: 100%;
            aspect-ratio: 16 / 10;
            overflow: hidden;
            background: #000;
            cursor: pointer;
        }
        .nx-video-poster img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: .92;
            transition: transform .4s ease, opacity .4s ease;
        }
        .nx-video-card:hover .nx-video-poster img { transform: scale(1.05); opacity: 1; }
        .nx-video-duration {
            position: absolute;
            bottom: 10px;
            right: 10px;
            background: rgba(0,0,0,.75);
            color: #fff;
            font-size: 11px;
            font-weight: 600;
            padding: 3px 8px;
            border-radius: 4px;
            letter-spacing: .02em;
        }
        .nx-video-play-btn {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 54px;
            height: 54px;
            border-radius: 50%;
            background: rgba(255,255,255,.95);
            color: var(--nx-green);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            box-shadow: 0 8px 24px rgba(0,0,0,.25);
            transition: all .3s ease;
        }
        .nx-video-card:hover .nx-video-play-btn {
            background: var(--nx-green);
            color: #fff;
            transform: translate(-50%, -50%) scale(1.1);
        }
        .nx-video-body { padding: 16px 18px; }
        .nx-video-cat { font-size: 10.5px; font-weight: 600; letter-spacing: .08em; text-transform: uppercase; color: var(--nx-green); margin-bottom: 4px; }
        .nx-video-title { font-size: 14.5px; font-weight: 600; line-height: 1.35; color: var(--nx-dark); margin-bottom: 6px; }
        .nx-video-desc { font-size: 12px; color: var(--nx-muted); margin: 0; line-height: 1.5; }

        /* R&D banner */
        .nx-rnd { background: var(--nx-mint-2); border-radius: 6px; overflow: hidden; position: relative; }
        .nx-rnd-text { padding: 40px 40px; position: relative; z-index: 2; }
        .nx-rnd-img-wrap { position: relative; height: 100%; min-height: 290px; }
        .nx-rnd-img-wrap::before { content: ''; position: absolute; inset: 0; background: linear-gradient(90deg, var(--nx-mint-2) 0%, rgba(227,239,229,0) 30%); z-index: 1; }
        .nx-rnd-img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; }

        /* FAQ */
        .nx-faq-item { border: 1px solid var(--nx-border); border-radius: 4px; margin-bottom: 10px; background: #fff; }
        .nx-faq-btn { width: 100%; display: flex; justify-content: space-between; align-items: center; background: none; border: none; padding: 14px 18px; font-size: 13px; font-weight: 600; color: var(--nx-dark); text-align: left; font-family: 'Poppins', sans-serif; }
        .nx-faq-btn i { font-size: 16px; color: var(--nx-dark); transition: transform .25s ease; }
        .nx-faq-btn:not(.collapsed) i { transform: rotate(45deg); color: var(--nx-green); }
        .nx-faq-body { padding: 0 18px 14px; font-size: 12.5px; line-height: 1.7; color: var(--nx-text); }

        /* News */
        .nx-news { display: flex; gap: 14px; border: 1px solid var(--nx-border); border-radius: 4px; overflow: hidden; background: #fff; height: 100%; transition: box-shadow .2s ease; }
        .nx-news:hover { box-shadow: 0 10px 24px rgba(18,36,26,.07); }
        .nx-news img { width: 42%; object-fit: cover; min-height: 104px; }
        .nx-news-body { padding: 14px 14px 14px 0; display: flex; flex-direction: column; justify-content: center; }
        .nx-news-cat { font-size: 10px; font-weight: 600; letter-spacing: .1em; color: var(--nx-green); text-transform: uppercase; margin-bottom: 4px; }
        .nx-news h6 { font-size: 13px; font-weight: 600; line-height: 1.4; margin-bottom: 6px; }
        .nx-news-date { font-size: 11px; color: var(--nx-muted); }

        /* Contact */
        .nx-contact-info li { display: flex; align-items: center; gap: 12px; font-size: 13px; color: var(--nx-dark); margin-bottom: 14px; }
        .nx-contact-info li i { width: 32px; height: 32px; border-radius: 50%; background: #fff; border: 1px solid var(--nx-border); display: inline-flex; align-items: center; justify-content: center; color: var(--nx-green); font-size: 14px; flex-shrink: 0; }
        .nx-input { width: 100%; border: 1px solid #D5E0D8; border-radius: 4px; padding: 10px 14px; font-size: 13px; background: #fff; color: var(--nx-dark); outline: none; transition: border-color .2s ease; }
        .nx-input:focus { border-color: var(--nx-green); }
        .nx-input::placeholder { color: var(--nx-muted); }

        /* Footer */
        .nx-footer { background: var(--nx-green-dark); color: rgba(255,255,255,.75); padding: 46px 0 0; }
        .nx-footer h6 { color: #fff; font-size: 13.5px; font-weight: 600; margin-bottom: 14px; }
        .nx-footer ul { list-style: none; padding: 0; margin: 0; }
        .nx-footer ul li { margin-bottom: 8px; }
        .nx-footer ul a { color: rgba(255,255,255,.72); font-size: 12.5px; }
        .nx-footer ul a:hover { color: #fff; }
        .nx-footer p { font-size: 12.5px; line-height: 1.7; }
        .nx-newsletter { display: flex; background: rgba(255,255,255,.08); border: 1px solid rgba(255,255,255,.18); border-radius: 4px; overflow: hidden; }
        .nx-newsletter input { flex: 1; background: transparent; border: none; padding: 10px 14px; color: #fff; font-size: 12.5px; outline: none; }
        .nx-newsletter input::placeholder { color: rgba(255,255,255,.55); }
        .nx-newsletter button { background: #fff; border: none; color: var(--nx-green); width: 42px; font-size: 15px; }
        .nx-footer-bottom { border-top: 1px solid rgba(255,255,255,.12); margin-top: 36px; padding: 16px 0; font-size: 12px; }
        .nx-social a { color: #fff; font-size: 14px; margin-left: 14px; opacity: .85; }
        .nx-social a:hover { opacity: 1; }

        @media (max-width: 991px) {
            .nx-nav { display: none; }
            .nx-hero h1 { font-size: 36px; }
            .nx-stats-wrap { border-left: none; padding-left: 0; margin-top: 20px; }
            .nx-feature + .nx-feature::before { display: none; }
        }
    </style>
</head>
<body>

{{-- ================= HEADER ================= --}}
<header class="nx-header">
    <div class="container">
        <a href="{{ $homeUrl }}" class="nx-logo">
            <svg width="34" height="34" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M20 3C11 9 7 17 9 26c4-7 9-11 15-13-6 5-10 11-11 19 9-2 16-9 17-19 1-4 0-7-1-10z" fill="#2E7D52"/>
                <path d="M20 3c6 4 10 10 10 17 0 6-3 11-7 14 2-6 2-13-3-31z" fill="#1E5B3A"/>
            </svg>
            <div>
                <div class="nx-logo-name">Nexora Pharma</div>
                <div class="nx-logo-tag">Better Health, Brighter Tomorrow</div>
            </div>
        </a>

        <ul class="nx-nav">
            <li><a href="{{ $homeUrl }}" class="active">Home</a></li>
            <li><a href="#about">About</a></li>
            <li><a href="#products">Therapeutic Areas</a></li>
            <li><a href="#medicines">Medicine Gallery</a></li>
            <li><a href="#image-gallery">Photo Gallery</a></li>
            <li><a href="#video-gallery">Video Gallery</a></li>
            <li><a href="#rnd">R&amp;D</a></li>
            <li><a href="#news">Insights</a></li>
            <li><a href="#faq">FAQ</a></li>
            <li><a href="#contact">Contact</a></li>
        </ul>

        <div class="nx-header-actions">
            <a href="#contact" class="nx-btn-quote">Request Quote</a>
            <a href="#" class="nx-search"><i class="bi bi-search"></i></a>
        </div>
    </div>
</header>

{{-- ================= HERO ================= --}}
<section class="nx-hero">
    <div class="container">
        <div class="row">
            <div class="col-lg-6">
                <span class="nx-eyebrow">Innovative Pharmaceutical Solutions</span>
                <h1>Better Medicines. <span>Healthier Tomorrows.</span></h1>
                <p>We develop high-quality, affordable medicines that make a difference to people's lives — across communities and generations.</p>
                <a href="#products" class="nx-btn nx-btn-primary">Explore Our Products <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>
</section>

{{-- ================= FEATURES STRIP ================= --}}
<section class="nx-features">
    <div class="container">
        <div class="row g-0">
            <div class="col-6 col-lg-3 nx-feature">
                <i class="bi bi-shield-check"></i>
                <h6>Trusted Quality</h6>
                <p>Global standards, uncompromising<br>quality at every step.</p>
            </div>
            <div class="col-6 col-lg-3 nx-feature">
                <i class="bi bi-lightbulb"></i>
                <h6>Innovative Research</h6>
                <p>Advancing science for<br>better health outcomes.</p>
            </div>
            <div class="col-6 col-lg-3 nx-feature">
                <i class="bi bi-people"></i>
                <h6>Patient Focused</h6>
                <p>Because every life<br>matters.</p>
            </div>
            <div class="col-6 col-lg-3 nx-feature">
                <i class="bi bi-globe-americas"></i>
                <h6>Sustainable Future</h6>
                <p>Healthier people,<br>healthier planet.</p>
            </div>
        </div>
    </div>
</section>

{{-- ================= COMPANY BRAND LOGO MARQUEE ================= --}}
<section class="nx-marquee-wrap">
    <div class="container">
        <div class="nx-marquee-label">Trusted by Leading Global Healthcare Organizations &amp; Certified Global Partners</div>
    </div>
    <div class="nx-marquee-mask">
        <div class="nx-marquee-track">
            @php
                $brandLogos = [
                    ['name' => 'WHO-GMP Certified', 'tag' => 'Global Manufacturing', 'icon' => 'bi-patch-check-fill'],
                    ['name' => 'Novartis Alliance', 'tag' => 'Distribution Partner', 'icon' => 'bi-globe2'],
                    ['name' => 'US-FDA Compliant', 'tag' => 'Quality Standards', 'icon' => 'bi-shield-fill-check'],
                    ['name' => 'CarePoint Hospitals', 'tag' => 'Clinical Network', 'icon' => 'bi-hospital-fill'],
                    ['name' => 'AstraCare Health', 'tag' => 'Therapeutic Research', 'icon' => 'bi-heart-pulse-fill'],
                    ['name' => 'Sanofi Global', 'tag' => 'Formulation Alliance', 'icon' => 'bi-capsule'],
                    ['name' => 'BioPharma International', 'tag' => 'Biotech Innovation', 'icon' => 'bi-virus'],
                    ['name' => 'Apex LifeSciences', 'tag' => 'Vaccines & Generics', 'icon' => 'bi-activity'],
                    ['name' => 'MedLife Systems', 'tag' => 'Healthcare Supply', 'icon' => 'bi-bandaid-fill'],
                    ['name' => 'GeneSys Labs', 'tag' => 'Precision Diagnostics', 'icon' => 'bi-diagram-3-fill'],
                ];
                $marqueeList = array_merge($brandLogos, $brandLogos);
            @endphp
            @foreach($marqueeList as $brand)
                <div class="nx-brand-card">
                    <span class="nx-brand-icon"><i class="bi {{ $brand['icon'] }}"></i></span>
                    <div>
                        <div class="nx-brand-name">{{ $brand['name'] }}</div>
                        <span class="nx-brand-tag">{{ $brand['tag'] }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ================= ABOUT ================= --}}
<section class="nx-section" id="about">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-5">
                <img src="{{ $img('about') }}" alt="Nexora Pharma Medicines" class="nx-about-img">
            </div>
            <div class="col-lg-4">
                <span class="nx-eyebrow">About Us</span>
                <h2 class="nx-title">Committed to Global Healthcare</h2>
                <p class="nx-lead mb-4">At Nexora Pharma, we are dedicated to developing safe, effective and affordable medicines that improve lives. With a strong foundation in reliability, innovation, and quality, we serve patients and healthcare professionals across the world.</p>
                <a href="#" class="nx-btn nx-btn-outline">Learn More <i class="bi bi-arrow-right"></i></a>
            </div>
            <div class="col-lg-3">
                <div class="nx-stats-wrap">
                    <div class="nx-stat">
                        <strong>20+</strong>
                        <span>Years of Healthcare<br>Commitment</span>
                    </div>
                    <div class="nx-stat">
                        <strong>500+</strong>
                        <span>Products in Portfolio</span>
                    </div>
                    <div class="nx-stat">
                        <strong>60+</strong>
                        <span>Countries Served</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ================= THERAPEUTIC AREAS ================= --}}
<section class="nx-section" id="products">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-4">
                <span class="nx-eyebrow">Our Products</span>
                <h2 class="nx-title">Therapeutic Areas</h2>
                <p class="nx-lead mb-4">A diverse portfolio of high-quality medicines across key therapeutic areas, designed to meet evolving healthcare needs.</p>
                <a href="#" class="nx-btn nx-btn-outline">View All Products <i class="bi bi-arrow-right"></i></a>
            </div>
            <div class="col-lg-8">
                @php
                    $areas = [
                        ['Cardiovascular', 'bi-heart-pulse', '#FDECEC', '#E35D5D'],
                        ['Diabetes', 'bi-droplet', '#EAF2FD', '#4A86D9'],
                        ['Respiratory', 'bi-lungs', '#E8F6F1', '#2E9C78'],
                        ['Gastrointestinal', 'bi-capsule', '#FFF3E6', '#E08A2E'],
                        ['Anti-Infectives', 'bi-virus', '#EAF6EA', '#3E9A4B'],
                        ['Pain Management', 'bi-bandaid', '#FFF5E5', '#D99A2B'],
                        ['Neurology', 'bi-activity', '#F1ECFB', '#7E5BC8'],
                        ['Women Health', 'bi-gender-female', '#FCEBF4', '#D45A9A'],
                    ];
                @endphp
                <div class="row g-3">
                    @foreach($areas as [$name, $icon, $bg, $color])
                        <div class="col-6 col-md-3">
                            <div class="nx-area-card">
                                <span class="nx-area-icon" style="background: {{ $bg }}; color: {{ $color }};"><i class="bi {{ $icon }}"></i></span>
                                <h6>{{ $name }}</h6>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ================= MEDICINE GALLERY ================= --}}
<section class="nx-section nx-bg-mint" id="medicines">
    <div class="container">
        <div class="text-center mb-4">
            <span class="nx-eyebrow">Medicine Gallery</span>
            <h2 class="nx-title mb-2">Formulations &amp; Medicine Portfolio</h2>
            <p class="nx-lead mx-auto" style="max-width: 620px;">
                Explore our certified pharmaceutical portfolio crafted with stringent precision across essential therapeutic areas.
            </p>
        </div>

        {{-- Filter Categories --}}
        <div class="nx-filter-tabs">
            <button class="nx-filter-btn active" data-filter="all">All Medicines</button>
            <button class="nx-filter-btn" data-filter="cardio">Cardiovascular</button>
            <button class="nx-filter-btn" data-filter="diabetes">Diabetes</button>
            <button class="nx-filter-btn" data-filter="respiratory">Respiratory</button>
            <button class="nx-filter-btn" data-filter="anti-infectives">Antibiotics</button>
            <button class="nx-filter-btn" data-filter="gastro">Gastrointestinal</button>
            <button class="nx-filter-btn" data-filter="pain">Pain Relief</button>
            <button class="nx-filter-btn" data-filter="neuro">Neurology</button>
            <button class="nx-filter-btn" data-filter="women">Women Health</button>
        </div>

        @php
            $medicinesList = [
                [
                    'name' => 'Nexorapril 10mg',
                    'generic' => 'Lisinopril USP (ACE Inhibitor)',
                    'category' => 'Cardiovascular',
                    'filter' => 'cardio',
                    'cat_color' => '#E35D5D',
                    'cat_bg' => '#FDECEC',
                    'badge' => 'Rx Only',
                    'pack' => '30 Film-Coated Tablets',
                    'img' => 'm1',
                    'desc' => 'Indicated for hypertension, heart failure management and myocardial infarction recovery.'
                ],
                [
                    'name' => 'GlucoMet-XR 500mg',
                    'generic' => 'Metformin HCl Extended Release',
                    'category' => 'Diabetes',
                    'filter' => 'diabetes',
                    'cat_color' => '#4A86D9',
                    'cat_bg' => '#EAF2FD',
                    'badge' => 'Rx Only',
                    'pack' => '60 Extended-Release Tablets',
                    'img' => 'm2',
                    'desc' => 'High-compliance formulation to optimize glycaemic control in adults with type 2 diabetes mellitus.'
                ],
                [
                    'name' => 'RespiNex Metered Inhaler',
                    'generic' => 'Salbutamol Sulfate 100mcg',
                    'category' => 'Respiratory',
                    'filter' => 'respiratory',
                    'cat_color' => '#2E9C78',
                    'cat_bg' => '#E8F6F1',
                    'badge' => 'CFC-Free',
                    'pack' => '200 Metered Inhalations',
                    'img' => 'm3',
                    'desc' => 'Rapid relief of acute bronchospasm and exercise-induced asthma with ergonomic actuator.'
                ],
                [
                    'name' => 'GastroNex-DSR',
                    'generic' => 'Rabeprazole Sodium + Domperidone',
                    'category' => 'Gastrointestinal',
                    'filter' => 'gastro',
                    'cat_color' => '#E08A2E',
                    'cat_bg' => '#FFF3E6',
                    'badge' => 'Sustained Release',
                    'pack' => '10x10 Hard Gelatin Capsules',
                    'img' => 'm4',
                    'desc' => 'Fast-acting acid suppression and prokinetic control for severe GERD, peptic ulcers and reflux.'
                ],
                [
                    'name' => 'AmoxiNex-Clav 625mg',
                    'generic' => 'Amoxicillin + Potassium Clavulanate',
                    'category' => 'Antibiotics',
                    'filter' => 'anti-infectives',
                    'cat_color' => '#3E9A4B',
                    'cat_bg' => '#EAF6EA',
                    'badge' => 'Broad Spectrum',
                    'pack' => '14 Film-Coated Tablets',
                    'img' => 'm5',
                    'desc' => 'Potent dual-action antibiotic therapy indicated for respiratory, ENT, and soft tissue infections.'
                ],
                [
                    'name' => 'PainRelief Plus 100ml',
                    'generic' => 'Paracetamol + Ibuprofen Suspension',
                    'category' => 'Pain Relief',
                    'filter' => 'pain',
                    'cat_color' => '#D99A2B',
                    'cat_bg' => '#FFF5E5',
                    'badge' => 'Pediatric / Adult',
                    'pack' => '100ml Oral Liquid + Dropper',
                    'img' => 'm6',
                    'desc' => 'Quick antipyretic and analgesic action for post-surgical fever, toothache and muscular soreness.'
                ],
                [
                    'name' => 'NeuroCalm-OD',
                    'generic' => 'Methylcobalamin + Pregabalin 75mg',
                    'category' => 'Neurology',
                    'filter' => 'neuro',
                    'cat_color' => '#7E5BC8',
                    'cat_bg' => '#F1ECFB',
                    'badge' => 'Neuropathic',
                    'pack' => '30 Sustained Release Tablets',
                    'img' => 'm7',
                    'desc' => 'Regenerates nerve sheaths and effectively suppresses diabetic neuropathy and nerve pain.'
                ],
                [
                    'name' => 'VitaFem MultiCare',
                    'generic' => 'Iron, Folic Acid & Multivitamins',
                    'category' => 'Women Health',
                    'filter' => 'women',
                    'cat_color' => '#D45A9A',
                    'cat_bg' => '#FCEBF4',
                    'badge' => 'OTC Health',
                    'pack' => '60 Softgel Capsules',
                    'img' => 'm8',
                    'desc' => 'Fortified essential micronutrients, red blood cell synthesis, maternal and daily wellness support.'
                ],
            ];
        @endphp

        <div class="row g-4" id="medicineGrid">
            @foreach($medicinesList as $med)
                <div class="col-sm-6 col-lg-3 nx-med-col" data-cat="{{ $med['filter'] }}">
                    <div class="nx-med-card">
                        <div class="nx-med-img-box">
                            <img src="{{ $img($med['img']) }}" alt="{{ $med['name'] }}" class="nx-med-thumb">
                            <span class="nx-med-badge">{{ $med['badge'] }}</span>
                        </div>
                        <div class="nx-med-body">
                            <span class="nx-med-cat" style="background: {{ $med['cat_bg'] }}; color: {{ $med['cat_color'] }};">
                                {{ $med['category'] }}
                            </span>
                            <div class="nx-med-title">{{ $med['name'] }}</div>
                            <div class="nx-med-generic">{{ $med['generic'] }}</div>
                            <div class="nx-med-meta">
                                <i class="bi bi-box-seam"></i>
                                <span>{{ $med['pack'] }}</span>
                            </div>
                            <p style="font-size: 11.5px; color: #64748B; line-height: 1.5; margin-bottom: 12px;">
                                {{ $med['desc'] }}
                            </p>
                            <div class="nx-med-footer">
                                <span class="badge bg-light text-dark border" style="font-weight: 500; font-size: 10px;">WHO-GMP</span>
                                <button type="button" class="btn btn-sm btn-outline-success py-1 px-2" style="font-size: 11px; font-weight: 600;" onclick="openMedModal('{{ addslashes($med['name']) }}', '{{ addslashes($med['generic']) }}', '{{ addslashes($med['category']) }}', '{{ addslashes($med['pack']) }}', '{{ addslashes($med['desc']) }}', '{{ $img($med['img']) }}')">
                                    Leaflet <i class="bi bi-arrow-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
<section class="nx-values">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-lg-4">
                <span class="nx-eyebrow">Our Values</span>
                <h2 class="nx-title mb-2">What Drives Us</h2>
                <p class="nx-lead mb-0">Our values guide our actions. Shape our culture and help us create a healthier future for all.</p>
            </div>
            <div class="col-lg-8">
                <div class="row g-4">
                    <div class="col-6 col-md-3 nx-value">
                        <span class="nx-value-icon"><i class="bi bi-shield-check"></i></span>
                        <h6>Integrity</h6>
                        <p>We do what's right.</p>
                    </div>
                    <div class="col-6 col-md-3 nx-value">
                        <span class="nx-value-icon"><i class="bi bi-people"></i></span>
                        <h6>Collaboration</h6>
                        <p>Better together.</p>
                    </div>
                    <div class="col-6 col-md-3 nx-value">
                        <span class="nx-value-icon"><i class="bi bi-lightbulb"></i></span>
                        <h6>Innovation</h6>
                        <p>For a better future.</p>
                    </div>
                    <div class="col-6 col-md-3 nx-value">
                        <span class="nx-value-icon"><i class="bi bi-globe-americas"></i></span>
                        <h6>Responsibility</h6>
                        <p>For people and planet.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ================= IMAGE GALLERY ================= --}}
<section class="nx-section" id="image-gallery">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end flex-wrap gap-3 mb-4">
            <div>
                <span class="nx-eyebrow">Image Gallery</span>
                <h2 class="nx-title mb-0">Visual Journey &amp; Facility Explorations<br>Cleanrooms, Plants &amp; Health Missions</h2>
            </div>
            <a href="#contact" class="nx-link">REQUEST FACILITY TOUR <i class="bi bi-arrow-right"></i></a>
        </div>

        @php
            $photoGallery = [
                ['title' => 'R&D Facility Tour', 'desc' => 'High-precision spectrometry and optical microscopy cleanroom.', 'photo' => 'g1', 'tag' => 'R&D LAB'],
                ['title' => 'Modern Manufacturing', 'desc' => 'High-throughput automated tablet blistering and packaging.', 'photo' => 'g2', 'tag' => 'PRODUCTION'],
                ['title' => 'Community Health Camps', 'desc' => 'Healthcare access, free diagnostics and medicine outreach.', 'photo' => 'g3', 'tag' => 'CSR OUTREACH'],
                ['title' => 'Natural Botanical Extractions', 'desc' => 'Standardized herbal extracts under green extraction protocol.', 'photo' => 'g4', 'tag' => 'BOTANICALS'],
            ];
        @endphp
        <div class="row g-4">
            @foreach($photoGallery as $i => $item)
                <div class="col-6 col-lg-3">
                    <div class="nx-gallery-item">
                        <div class="nx-blob nx-blob-{{ $i + 1 }}">
                            <img src="{{ $img($item['photo']) }}" alt="{{ $item['title'] }}">
                        </div>
                        <h6>{{ $item['title'] }}</h6>
                        <p class="text-muted small mb-0" style="font-size: 11.5px; line-height: 1.5; padding: 0 4px;">{{ $item['desc'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ================= VIDEO GALLERY ================= --}}
<section class="nx-section" style="background: linear-gradient(180deg, #F4F8F5 0%, #EEF5EF 100%);" id="video-gallery">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end flex-wrap gap-3 mb-4">
            <div>
                <span class="nx-eyebrow">Video Gallery</span>
                <h2 class="nx-title mb-0">Watch Our Stories &amp; Innovations<br>Documentaries &amp; Live Walkthroughs</h2>
            </div>
            <span class="badge bg-white text-success border px-3 py-2 fw-semibold" style="font-size: 11.5px;">
                <i class="bi bi-camera-video me-1"></i> 4 Verified Documentaries
            </span>
        </div>

        @php
            $videoGallery = [
                [
                    'title' => 'Innovation in Lab',
                    'subtitle' => 'Behind the Scenes in Formulation R&D',
                    'desc' => 'Watch our senior research scientists pipette micro-assays in our ISO-Class 5 sterile laboratory.',
                    'photo' => 'g5',
                    'duration' => '03:45',
                    'tag' => 'RESEARCH LAB',
                ],
                [
                    'title' => 'Quality Inspection Process',
                    'subtitle' => 'Zero-Defect Inspection Line',
                    'desc' => 'Automated optical detection and high-speed packaging verification for every sterile medicine vial.',
                    'photo' => 'g6',
                    'duration' => '02:30',
                    'tag' => 'QUALITY ASSURANCE',
                ],
                [
                    'title' => 'Patient Recovery Stories',
                    'subtitle' => 'Real Journeys, Real Impact',
                    'desc' => 'Patients share how accessible Nexora cardiovascular and diabetes therapies transformed their lives.',
                    'photo' => 'g7',
                    'duration' => '04:15',
                    'tag' => 'PATIENT STORIES',
                ],
                [
                    'title' => 'Our Sustainable Campus',
                    'subtitle' => 'Carbon-Neutral Green Facility',
                    'desc' => 'Take a guided walkthrough of our eco-friendly campus featuring zero-liquid discharge and solar power.',
                    'photo' => 'g8',
                    'duration' => '03:10',
                    'tag' => 'CAMPUS TOUR',
                ],
            ];
        @endphp
        <div class="row g-4">
            @foreach($videoGallery as $v)
                <div class="col-md-6 col-lg-3">
                    <div class="nx-video-card">
                        <div class="nx-video-poster" onclick="openVideoModal('{{ addslashes($v['title']) }}', '{{ addslashes($v['subtitle']) }}', '{{ $img($v['photo']) }}')">
                            <img src="{{ $img($v['photo']) }}" alt="{{ $v['title'] }}">
                            <span class="nx-video-play-btn"><i class="bi bi-play-fill"></i></span>
                            <span class="nx-video-duration"><i class="bi bi-clock me-1"></i>{{ $v['duration'] }}</span>
                        </div>
                        <div class="nx-video-body">
                            <div class="nx-video-cat">{{ $v['tag'] }}</div>
                            <div class="nx-video-title">{{ $v['title'] }}</div>
                            <p class="nx-video-desc">{{ $v['desc'] }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ================= R&D BANNER ================= --}}
<section class="pb-5" id="rnd">
    <div class="container">
        <div class="nx-rnd">
            <div class="row g-0 align-items-stretch">
                <div class="col-lg-5 d-flex align-items-center">
                    <div class="nx-rnd-text">
                        <span class="nx-eyebrow">Research &amp; Development</span>
                        <h2 class="nx-title">Turning Science into Better Health</h2>
                        <p class="nx-lead mb-4">Our research teams work tirelessly to bring forward innovative therapies and to grow lives across the world.</p>
                        <a href="#" class="nx-btn nx-btn-primary">Discover Our R&amp;D <i class="bi bi-arrow-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="nx-rnd-img-wrap">
                        <img src="{{ $img('rnd') }}" alt="Research & Development" class="nx-rnd-img">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ================= FAQ ================= --}}
<section class="nx-section pt-4" id="faq">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-4">
                <span class="nx-eyebrow">FAQ Section</span>
                <h2 class="nx-title">Frequently Asked Questions</h2>
                <p class="nx-lead mb-4">Have any questions in mind? Read our FAQs or contact us.</p>
                <a href="#contact" class="nx-btn nx-btn-outline">Learn More <i class="bi bi-arrow-right"></i></a>
            </div>
            <div class="col-lg-8">
                @php
                    $faqs = [
                        ['What therapeutic areas does Nexora Pharma focus on?', 'We focus on cardiovascular, diabetes, respiratory, gastrointestinal, anti-infectives, pain management, neurology and women\'s health.'],
                        ['Are Nexora Pharma products globally certified?', 'Yes. Our manufacturing facilities comply with WHO-GMP and international regulatory standards.'],
                        ['How does Nexora Pharma ensure product quality?', 'Every batch passes through rigorous multi-stage quality control and inspection before release.'],
                        ['What are your R&D focus areas?', 'Our R&D focuses on novel formulations, affordable generics and botanical-based therapies.'],
                        ['Do you offer partnership or collaboration opportunities?', 'Absolutely. We welcome partnerships with distributors, research institutes and healthcare providers.'],
                        ['Where can I find Nexora Pharma product data sheets?', 'Product data sheets are available on request through our contact form or from your regional representative.'],
                    ];
                @endphp
                <div id="nxFaq">
                    @foreach($faqs as $i => [$q, $a])
                        <div class="nx-faq-item">
                            <button class="nx-faq-btn collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#nxFaq{{ $i }}" aria-expanded="false">
                                <span>{{ $q }}</span>
                                <i class="bi bi-plus-lg"></i>
                            </button>
                            <div id="nxFaq{{ $i }}" class="collapse" data-bs-parent="#nxFaq">
                                <div class="nx-faq-body">{{ $a }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ================= NEWS ================= --}}
<section class="pb-5" id="news">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <span class="nx-eyebrow">Latest Insights</span>
                <h2 class="nx-title mb-0">News &amp; Updates</h2>
            </div>
            <a href="#" class="nx-link">View All News <i class="bi bi-arrow-right"></i></a>
        </div>
        @php
            $news = [
                ['Research', 'New Breakthrough in Diabetic Care', 'May 12, 2026', 'n1'],
                ['Company', 'Nexora Pharma Expands Global Reach', 'April 28, 2026', 'n2'],
                ['Health & Lifestyle', 'Building a Healthier Tomorrow', 'April 15, 2026', 'n3'],
            ];
        @endphp
        <div class="row g-4">
            @foreach($news as [$cat, $title, $date, $photo])
                <div class="col-md-4">
                    <a href="#" class="nx-news">
                        <img src="{{ $img($photo) }}" alt="{{ $title }}">
                        <div class="nx-news-body">
                            <div class="nx-news-cat">{{ $cat }}</div>
                            <h6>{{ $title }}</h6>
                            <div class="nx-news-date">{{ $date }}</div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ================= CONTACT ================= --}}
<section class="nx-section nx-bg-mint" id="contact">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-5">
                <span class="nx-eyebrow">Contact Us</span>
                <h2 class="nx-title">Get in Touch</h2>
                <p class="nx-lead mb-4">Our values guide our actions. Shape our culture actions and healthier data centres.</p>
                <ul class="list-unstyled nx-contact-info mb-0">
                    <li><i class="bi bi-geo-alt"></i> 2026 Nexora Pharma,<br>Burnet, NY 10018</li>
                    <li><i class="bi bi-telephone"></i> +1 (123) 456-7759</li>
                    <li><i class="bi bi-envelope"></i> email@nexorapharma.io</li>
                </ul>
            </div>
            <div class="col-lg-7">
                <form onsubmit="event.preventDefault();">
                    <div class="row g-3">
                        <div class="col-md-6"><input type="text" class="nx-input" placeholder="Name"></div>
                        <div class="col-md-6"><input type="email" class="nx-input" placeholder="Email"></div>
                        <div class="col-12">
                            <select class="nx-input">
                                <option>Subject</option>
                                <option>Product Inquiry</option>
                                <option>Partnership</option>
                                <option>General Question</option>
                            </select>
                        </div>
                        <div class="col-12"><textarea class="nx-input" rows="5" placeholder="Message"></textarea></div>
                        <div class="col-12">
                            <button type="submit" class="nx-btn nx-btn-primary w-100 justify-content-center">Send Message</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

{{-- ================= FOOTER ================= --}}
<footer class="nx-footer">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <svg width="34" height="34" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M20 3C11 9 7 17 9 26c4-7 9-11 15-13-6 5-10 11-11 19 9-2 16-9 17-19 1-4 0-7-1-10z" fill="#8FD1A8"/>
                        <path d="M20 3c6 4 10 10 10 17 0 6-3 11-7 14 2-6 2-13-3-31z" fill="#FFFFFF"/>
                    </svg>
                    <div>
                        <div class="nx-logo-name text-white">Nexora Pharma</div>
                        <div class="nx-logo-tag" style="color: rgba(255,255,255,.65);">Better Health, Brighter Tomorrow</div>
                    </div>
                </div>
                <p class="mb-0" style="max-width: 280px;">Developing safe, effective and affordable medicines for a healthier world.</p>
            </div>
            <div class="col-6 col-lg-2">
                <h6>Quick Links</h6>
                <ul>
                    <li><a href="{{ $homeUrl }}">Home</a></li>
                    <li><a href="#about">About Us</a></li>
                    <li><a href="#products">Products</a></li>
                    <li><a href="#products">Therapeutic Areas</a></li>
                </ul>
            </div>
            <div class="col-6 col-lg-2">
                <h6>Company</h6>
                <ul>
                    <li><a href="#news">Insights</a></li>
                    <li><a href="#">Careers</a></li>
                    <li><a href="#news">General Insights</a></li>
                    <li><a href="#contact">Contact</a></li>
                </ul>
            </div>
            <div class="col-lg-4">
                <h6>Stay Connected</h6>
                <p class="mb-3">Subscribe to our newsletter for the latest updates.</p>
                <form class="nx-newsletter" onsubmit="event.preventDefault();">
                    <input type="email" placeholder="Enter your email">
                    <button type="submit"><i class="bi bi-arrow-right"></i></button>
                </form>
            </div>
        </div>
        <div class="nx-footer-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
            <span>&copy; {{ date('Y') }} Nexora Pharma. All rights reserved.</span>
            <div class="nx-social">
                <a href="#"><i class="bi bi-linkedin"></i></a>
                <a href="#"><i class="bi bi-twitter-x"></i></a>
                <a href="#"><i class="bi bi-facebook"></i></a>
                <a href="#"><i class="bi bi-youtube"></i></a>
            </div>
        </div>
    </div>
</footer>

{{-- ================= MEDICINE LEAFLET MODAL ================= --}}
<div class="modal fade" id="medLeafletModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">
            <div class="modal-header border-bottom py-3 px-4" style="background: var(--nx-mint);">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-success px-2 py-1" style="font-size: 11px;">PRESCRIPTION DRUG</span>
                    <h5 class="modal-title fw-bold mb-0" id="medModalTitle" style="color: var(--nx-dark);">Medicine Details</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-4 align-items-center">
                    <div class="col-md-5 text-center">
                        <img id="medModalImg" src="" alt="Medicine" class="img-fluid rounded border" style="max-height: 250px; object-fit: cover; width: 100%;">
                        <div class="mt-2 text-muted small"><i class="bi bi-shield-check text-success me-1"></i> WHO-GMP Certified Product</div>
                    </div>
                    <div class="col-md-7">
                        <span class="badge bg-light text-success border mb-2" id="medModalCategory">Category</span>
                        <h4 class="fw-bold mb-1" id="medModalName">Nexorapril 10mg</h4>
                        <div class="text-muted fst-italic mb-3" id="medModalGeneric">Generic Composition</div>

                        <div class="p-3 rounded mb-3" style="background: #F8FAF9; border: 1px solid var(--nx-border);">
                            <div class="small fw-semibold text-dark mb-1"><i class="bi bi-box-seam me-1 text-success"></i> Packaging / Presentation:</div>
                            <div class="small text-muted mb-2" id="medModalPack">30 Film-Coated Tablets</div>
                            <div class="small fw-semibold text-dark mb-1"><i class="bi bi-info-circle me-1 text-success"></i> Clinical Indication:</div>
                            <div class="small text-muted" id="medModalDesc">Description goes here.</div>
                        </div>

                        <div class="d-flex gap-2">
                            <a href="#contact" class="nx-btn nx-btn-primary" data-bs-dismiss="modal">Inquire for Bulk Order <i class="bi bi-arrow-right"></i></a>
                            <button type="button" class="nx-btn nx-btn-outline" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ================= VIDEO PLAYER MODAL ================= --}}
<div class="modal fade" id="videoPlayerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg text-white" style="border-radius: 14px; background: #0B1611; overflow: hidden;">
            <div class="modal-header border-0 py-3 px-4" style="background: #12241A;">
                <div>
                    <span class="badge bg-success-subtle text-success px-2 py-1 mb-1" style="font-size: 10.5px;">DOCUMENTARY PREVIEW</span>
                    <h5 class="modal-title fw-bold mb-0 text-white" id="videoModalTitle">Video Title</h5>
                    <small class="text-white-50" id="videoModalSubtitle">Subtitle</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 position-relative" style="background: #000; min-height: 380px;">
                <img id="videoModalPoster" src="" alt="Video Poster" style="width: 100%; height: 380px; object-fit: cover; opacity: 0.85;">
                <div class="position-absolute top-50 start-50 translate-middle text-center">
                    <div style="width: 72px; height: 72px; border-radius: 50%; background: var(--nx-green); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 32px; box-shadow: 0 0 30px rgba(30,91,58,.8); margin: 0 auto 14px; cursor: pointer;">
                        <i class="bi bi-play-fill" style="margin-left: 4px;"></i>
                    </div>
                    <div class="fw-semibold text-white">Full HD Video Stream</div>
                    <small class="text-white-50">High-Definition Pharma Cleanroom &amp; Clinical Walkthrough</small>
                </div>
            </div>
            <div class="modal-footer border-0 py-3 px-4 d-flex justify-content-between" style="background: #12241A;">
                <span class="small text-white-50"><i class="bi bi-check-circle-fill text-success me-1"></i> Authorized Nexora Pharma Media Production</span>
                <button type="button" class="btn btn-sm btn-outline-light" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Medicine Gallery Live Filter
    document.querySelectorAll('.nx-filter-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.nx-filter-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            const filter = this.getAttribute('data-filter');
            const items = document.querySelectorAll('#medicineGrid .nx-med-col');

            items.forEach(item => {
                if (filter === 'all' || item.getAttribute('data-cat') === filter) {
                    item.style.display = 'block';
                    item.style.opacity = '1';
                } else {
                    item.style.display = 'none';
                    item.style.opacity = '0';
                }
            });
        });
    });

    // Medicine Leaflet Modal
    function openMedModal(name, generic, cat, pack, desc, img) {
        document.getElementById('medModalName').innerText = name;
        document.getElementById('medModalGeneric').innerText = generic;
        document.getElementById('medModalCategory').innerText = cat;
        document.getElementById('medModalPack').innerText = pack;
        document.getElementById('medModalDesc').innerText = desc;
        document.getElementById('medModalImg').src = img;
        const modal = new bootstrap.Modal(document.getElementById('medLeafletModal'));
        modal.show();
    }

    // Video Player Modal
    function openVideoModal(title, subtitle, poster) {
        document.getElementById('videoModalTitle').innerText = title;
        document.getElementById('videoModalSubtitle').innerText = subtitle;
        document.getElementById('videoModalPoster').src = poster;
        const modal = new bootstrap.Modal(document.getElementById('videoPlayerModal'));
        modal.show();
    }
</script>
</body>
</html>
