<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $globalSiteName ?? 'SiteBuilder' }} — All Active Themes Showcase</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #F8FAFC;
            color: #0F172A;
            margin: 0;
        }
        .hub-navbar {
            background: #FFFFFF;
            border-bottom: 1px solid #E2E8F0;
            padding: 16px 0;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .hub-hero {
            background: linear-gradient(135deg, #0F172A 0%, #1E293B 100%);
            color: #FFFFFF;
            padding: 72px 0 64px;
            position: relative;
            overflow: hidden;
        }
        .hub-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(236, 19, 74, 0.18);
            border: 1px solid rgba(236, 19, 74, 0.4);
            color: #FF85A1;
            padding: 6px 16px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 18px;
        }
        .theme-card {
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 18px;
            overflow: hidden;
            transition: all 0.25s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
            box-shadow: 0 4px 16px rgba(15, 23, 42, 0.04);
        }
        .theme-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 18px 36px rgba(15, 23, 42, 0.1);
            border-color: #CBD5E1;
        }
        .theme-thumb-wrap {
            position: relative;
            height: 220px;
            overflow: hidden;
            background: #0F172A;
        }
        .theme-thumb-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.35s ease;
        }
        .theme-card:hover .theme-thumb-wrap img {
            transform: scale(1.05);
        }
        .theme-pill {
            position: absolute;
            top: 14px;
            left: 14px;
            background: rgba(15, 23, 42, 0.82);
            backdrop-filter: blur(6px);
            color: #FFFFFF;
            font-size: 12px;
            font-weight: 600;
            padding: 5px 12px;
            border-radius: 999px;
        }
        .theme-body {
            padding: 24px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .btn-launch {
            background: #EC134A;
            color: #FFFFFF;
            font-weight: 600;
            font-size: 14px;
            padding: 11px 18px;
            border-radius: 10px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: background 0.18s;
        }
        .btn-launch:hover {
            background: #D00F40;
            color: #FFFFFF;
        }
    </style>
</head>
<body>
    <!-- Top Navbar -->
    <header class="hub-navbar">
        <div class="container d-flex align-items-center justify-content-between">
            <a href="{{ url('/') }}" class="d-flex align-items-center gap-2 text-decoration-none">
                @if(!empty($globalSiteLogo))
                    <img src="{{ $globalSiteLogo }}" alt="{{ $globalSiteName ?? 'SiteBuilder' }}" style="width: 36px; height: 36px; object-fit: contain; border-radius: 8px;">
                @else
                    <svg width="36" height="36" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M24 4L6 14V20L24 30L42 20V14L24 4Z" fill="#EC134A"/>
                        <path d="M24 10L11 17L24 24L37 17L24 10Z" fill="#FF85A1" opacity="0.35"/>
                        <path d="M6 23L24 33L42 23V28L24 38L6 28V23Z" fill="#D90F43"/>
                        <path d="M6 31L24 41L42 31V36L24 46L6 36V31Z" fill="#B80B38"/>
                    </svg>
                @endif
                <div>
                    <span class="fw-bold fs-5 text-dark d-block lh-1">{{ $globalSiteName ?? 'SiteBuilder' }}</span>
                    <small class="text-muted" style="font-size: 11px;">{{ $globalSiteTagline ?? 'Website Management' }}</small>
                </div>
            </a>

            <div class="d-flex align-items-center gap-3">
                @foreach($activeSites as $navSite)
                    <a href="{{ url('/' . $navSite->slug) }}" class="text-decoration-none text-secondary fw-semibold small d-none d-md-inline">
                        {{ $navSite->module }}
                    </a>
                @endforeach
                <a href="{{ route('admin.websites') }}" class="btn btn-sm btn-dark px-3 py-2 rounded-3 fw-semibold">
                    <i class="bi bi-sliders me-1"></i> Manage Websites
                </a>
            </div>
        </div>
    </header>

    <!-- Hero Banner -->
    <section class="hub-hero text-center">
        <div class="container" style="max-width: 780px;">
            <div class="hub-badge">
                <i class="bi bi-layers-fill"></i>
                <span>Multi-Module Theme Showcase ({{ $activeSites->count() }} Active Themes)</span>
            </div>
            <h1 class="fw-bolder mb-3" style="font-size: 40px; letter-spacing: -0.02em;">
                Explore Our Active Industry Websites
            </h1>
            <p class="text-white-50 mb-0" style="font-size: 16px; line-height: 1.6;">
                Multiple modules are currently enabled in <strong>Manage Websites</strong>. Click any active website below to browse its standalone frontend, or keep only <strong>1 module active</strong> to run it directly on the root domain (<code>/</code>).
            </p>
        </div>
    </section>

    <!-- Active Themes Grid -->
    <section class="py-5">
        <div class="container">
            @if($activeSites->isEmpty())
                <div class="text-center bg-white border rounded-4 p-5 shadow-sm mx-auto" style="max-width: 560px;">
                    <i class="bi bi-power text-danger" style="font-size: 48px;"></i>
                    <h4 class="fw-bold mt-3">All Modules Are Currently Turned Off</h4>
                    <p class="text-muted small mb-4">Go to the Admin Panel &rarr; Websites and activate at least one module to launch your frontend.</p>
                    <a href="{{ route('admin.websites') }}" class="btn-launch">
                        <i class="bi bi-globe2"></i> Go to Manage Websites
                    </a>
                </div>
            @else
                <div class="row g-4">
                    @foreach($activeSites as $site)
                        <div class="col-md-6 col-lg-4">
                            <div class="theme-card">
                                <div class="theme-thumb-wrap">
                                    <img src="{{ $site->thumbnail }}" alt="{{ $site->name }}">
                                    <span class="theme-pill">{{ $site->module }}</span>
                                </div>
                                <div class="theme-body">
                                    <div>
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <h4 class="fw-bold mb-0" style="font-size: 20px;">{{ $site->name }}</h4>
                                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1">Active</span>
                                        </div>
                                        <p class="text-muted small mb-3">
                                            {{ $site->description ?? 'Standalone industry website module with dedicated frontend layout and admin CRUD.' }}
                                        </p>
                                        <div class="mb-4 p-2 rounded-3 bg-light border d-flex align-items-center justify-content-between small">
                                            <span class="text-muted">Route Slug:</span>
                                            <code class="fw-bold text-dark">/{{ $site->slug }}</code>
                                        </div>
                                    </div>

                                    <div class="d-flex gap-2">
                                        <a href="{{ url('/' . $site->slug) }}" class="btn-launch flex-grow-1">
                                            <span>Launch Live Demo</span>
                                            <i class="bi bi-arrow-right"></i>
                                        </a>
                                        @if($site->admin_route && Route::has($site->admin_route))
                                            <a href="{{ route($site->admin_route) }}" class="btn btn-outline-secondary rounded-3 px-3 d-inline-flex align-items-center" title="Open Module Admin">
                                                <i class="bi bi-gear"></i>
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

    <footer class="py-4 border-top bg-white text-center text-muted small mt-5">
        <div class="container">
            &copy; {{ date('Y') }} {{ $globalSiteName ?? 'SiteBuilder' }} — {{ $globalSiteTagline ?? 'Website Management' }}. All rights reserved.
        </div>
    </footer>
</body>
</html>
