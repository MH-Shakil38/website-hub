<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard - SiteBuilder')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --sb-primary: #EC134A;
            --sb-primary-soft: #FFEBF0;
            --sb-dark: #0F172A;
            --sb-text: #334155;
            --sb-muted: #64748B;
            --sb-light: #94A3B8;
            --sb-bg: #F8FAFC;
            --sb-border: #EEF2F6;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--sb-bg);
            color: var(--sb-dark);
            margin: 0;
            -webkit-font-smoothing: antialiased;
        }

        .sb-layout {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sb-sidebar {
            width: 256px;
            flex-shrink: 0;
            background: #FFFFFF;
            border-right: 1px solid var(--sb-border);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 22px 16px 20px;
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
        }

        .sb-brand {
            display: flex;
            align-items: center;
            gap: 11px;
            padding: 0 10px 22px;
            text-decoration: none;
        }

        .sb-brand span {
            font-size: 20px;
            font-weight: 800;
            color: var(--sb-dark);
            letter-spacing: -0.02em;
        }

        .sb-nav {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 3px;
        }

        .sb-nav-link {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 14px;
            border-radius: 10px;
            font-size: 13.5px;
            font-weight: 500;
            color: #334155;
            text-decoration: none;
            transition: all 0.15s ease;
        }

        .sb-nav-link .link-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .sb-nav-link i.nav-icon {
            font-size: 16px;
            color: #64748B;
            width: 18px;
            text-align: center;
            transition: color 0.15s;
        }

        .sb-nav-link:hover {
            background: #F8FAFC;
            color: var(--sb-dark);
        }

        .sb-nav-link.active {
            background: var(--sb-primary-soft);
            color: var(--sb-primary);
            font-weight: 600;
        }

        .sb-nav-link.active i.nav-icon {
            color: var(--sb-primary);
        }

        .sb-section-label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #94A3B8;
            padding: 18px 14px 8px;
        }

        .sb-chevron {
            font-size: 11px;
            color: #94A3B8;
        }

        /* Upgrade to Pro Card */
        .sb-pro-card {
            margin-top: 22px;
            background: #FFF1F4;
            border-radius: 14px;
            padding: 18px 16px;
        }

        .sb-pro-header {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13.5px;
            font-weight: 700;
            color: var(--sb-primary);
            margin-bottom: 6px;
        }

        .sb-pro-card p {
            font-size: 12px;
            color: #64748B;
            line-height: 1.45;
            margin-bottom: 14px;
        }

        .btn-sb-pro {
            width: 100%;
            height: 38px;
            background: var(--sb-primary);
            color: #FFFFFF;
            border: none;
            border-radius: 9px;
            font-size: 12.5px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            text-decoration: none;
            box-shadow: 0 4px 12px rgba(236, 19, 74, 0.22);
            transition: all 0.15s;
        }

        .btn-sb-pro:hover {
            background: #D40E40;
            color: #FFFFFF;
        }

        /* Main Area */
        .sb-main {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        /* Topbar */
        .sb-topbar {
            height: 72px;
            background: #FFFFFF;
            border-bottom: 1px solid var(--sb-border);
            padding: 0 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 20;
        }

        .sb-search-box {
            position: relative;
            width: 340px;
        }

        .sb-search-box i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94A3B8;
            font-size: 14px;
        }

        .sb-search-box input {
            width: 100%;
            height: 40px;
            background: #F1F5F9;
            border: 1px solid transparent;
            border-radius: 10px;
            padding: 0 16px 0 38px;
            font-size: 13.5px;
            color: var(--sb-dark);
            outline: none;
            transition: all 0.15s;
        }

        .sb-search-box input::placeholder {
            color: #94A3B8;
        }

        .sb-search-box input:focus {
            background: #FFFFFF;
            border-color: #CBD5E1;
        }

        .sb-topbar-right {
            display: flex;
            align-items: center;
            gap: 24px;
        }

        .sb-notif-btn {
            position: relative;
            background: none;
            border: none;
            color: #475569;
            font-size: 19px;
            cursor: pointer;
            padding: 4px;
            display: flex;
            align-items: center;
        }

        .sb-notif-badge {
            position: absolute;
            top: -1px;
            right: -3px;
            width: 17px;
            height: 17px;
            border-radius: 50%;
            background: var(--sb-primary);
            color: #FFFFFF;
            font-size: 10px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #FFFFFF;
        }

        .sb-user-dropdown {
            display: flex;
            align-items: center;
            gap: 11px;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
        }

        .sb-user-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            object-fit: cover;
            border: 1px solid #E2E8F0;
        }

        .sb-user-info h6 {
            font-size: 13.5px;
            font-weight: 700;
            color: var(--sb-dark);
            margin: 0;
            line-height: 1.2;
        }

        .sb-user-info span {
            font-size: 11.5px;
            color: #64748B;
            display: block;
        }

        .sb-content {
            padding: 28px 32px 40px;
            flex: 1;
        }

        /* Shared Table & Filter UI Styles (Image 8 Pattern) */
        .sb-summary-strip {
            background: #FFFFFF;
            border: 1px solid var(--sb-border);
            border-radius: 14px;
            padding: 22px 28px;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
            margin-bottom: 28px;
            box-shadow: 0 2px 6px rgba(15, 23, 42, 0.02);
        }

        .sb-summary-item {
            display: flex;
            align-items: center;
            gap: 16px;
            border-right: 1px solid #E2E8F0;
            padding-right: 20px;
        }

        .sb-summary-item:last-child {
            border-right: none;
            padding-right: 0;
        }

        .sb-summary-icon {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: #EFF6FF;
            color: #1D4ED8;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
        }

        .sb-summary-label {
            font-size: 12.5px;
            color: #64748B;
            margin-bottom: 3px;
        }

        .sb-summary-value {
            font-size: 19px;
            font-weight: 800;
            color: #0F172A;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .sb-table-card {
            background: #FFFFFF;
            border: 1px solid var(--sb-border);
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 2px 6px rgba(15, 23, 42, 0.02);
        }

        .sb-data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .sb-data-table thead th {
            background: #F8FAFC;
            padding: 14px 18px;
            font-size: 12px;
            font-weight: 700;
            color: #1E293B;
            text-align: left;
            border-bottom: 1px solid #E2E8F0;
            white-space: nowrap;
        }

        .sb-data-table thead th i {
            font-size: 11px;
            color: #94A3B8;
            margin-left: 4px;
        }

        .sb-data-table tbody td {
            padding: 15px 18px;
            font-size: 13px;
            color: #334155;
            border-bottom: 1px solid #F1F5F9;
            vertical-align: middle;
        }

        .sb-data-table tbody tr:hover {
            background: #FAFBFD;
        }

        .sb-badge-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11.5px;
            font-weight: 600;
        }

        .sb-badge-blue {
            background: #DBEAFE;
            color: #2563EB;
        }

        .sb-badge-green {
            background: #DCFCE7;
            color: #16A34A;
        }

        .sb-badge-pink {
            background: #FFE4EC;
            color: #EC134A;
        }

        .sb-badge-purple {
            background: #F3E8FF;
            color: #9333EA;
        }

        .sb-badge-amber {
            background: #FEF3C7;
            color: #D97706;
        }

        .sb-table-footer {
            padding: 16px 22px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #FFFFFF;
            font-size: 13px;
            color: #64748B;
        }

        .sb-pagination {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .sb-page-btn {
            min-width: 32px;
            height: 32px;
            padding: 0 8px;
            border-radius: 7px;
            border: 1px solid #E2E8F0;
            background: #FFFFFF;
            color: #334155;
            font-size: 12.5px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: all 0.15s;
        }

        .sb-page-btn:hover {
            background: #F8FAFC;
            color: #0F172A;
        }

        .sb-page-btn.active {
            background: #1D4ED8;
            border-color: #1D4ED8;
            color: #FFFFFF;
        }
    </style>
    @stack('module_styles')
</head>
<body>
    <div class="sb-layout">
        <!-- Global Sidebar -->
        @include('admin.partials.sidebar')

        <!-- Main Content Area -->
        <div class="sb-main">
            @php
                $currentUser = auth()->user() ?? \App\Models\User::with('role')->first();
                $notifLogs = \App\Models\ActivityLog::latest('id')->take(3)->get();
            @endphp
            <!-- Top Header Bar -->
            <header class="sb-topbar">
                <form action="{{ route('admin.transactions') }}" method="GET" class="sb-search-box">
                    <i class="bi bi-search"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search anything...">
                </form>

                <div class="sb-topbar-right">
                    <div class="dropdown">
                        <button type="button" class="sb-notif-btn" data-bs-toggle="dropdown" aria-expanded="false" title="Notifications">
                            <i class="bi bi-bell"></i>
                            <span class="sb-notif-badge">{{ $notifLogs->count() ?: 3 }}</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2 p-2" style="border-radius: 12px; width: 290px;">
                            <li class="px-2 py-1 fw-bold small text-dark border-bottom mb-1">Recent Notifications</li>
                            @foreach($notifLogs as $nLog)
                                <li>
                                    <a class="dropdown-item rounded py-2 d-flex align-items-center gap-2" href="{{ route('admin.dashboard') }}">
                                        <span class="d-inline-flex align-items-center justify-content-center rounded-circle flex-shrink-0" style="width: 30px; height: 30px; background: {{ $nLog->bg_color }}; color: {{ $nLog->text_color }}; font-size: 13px;">
                                            <i class="bi {{ $nLog->icon }}"></i>
                                        </span>
                                        <div class="text-truncate">
                                            <div class="fw-semibold" style="font-size: 12.5px; color: #0F172A;">{{ $nLog->title }}</div>
                                            <div class="text-muted text-truncate" style="font-size: 11px;">{{ $nLog->subtitle }}</div>
                                        </div>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="dropdown">
                        <a href="#" class="sb-user-dropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="{{ $currentUser?->avatar_url ?? 'https://ui-avatars.com/api/?name=John+Doe&background=0F172A&color=fff&size=80' }}" alt="{{ $currentUser?->name ?? 'John Doe' }}" class="sb-user-avatar">
                            <div class="sb-user-info">
                                <h6>{{ $currentUser?->name ?? 'John Doe' }}</h6>
                                <span>{{ $currentUser?->role?->name ?? 'Administrator' }}</span>
                            </div>
                            <i class="bi bi-chevron-down" style="font-size: 12px; color: #64748B; margin-left: 2px;"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2" style="border-radius: 12px; font-size: 13.5px; min-width: 225px;">
                            <li class="px-3 py-2 border-bottom small text-muted">
                                Active Module: <strong class="text-dark">{{ config('app.active_module') }}</strong>
                            </li>
                            <li><a class="dropdown-item py-2" href="{{ route('admin.settings') }}"><i class="bi bi-person-gear me-2"></i>My Profile &amp; Password</a></li>
                            <li><a class="dropdown-item py-2" href="{{ route('admin.users') }}"><i class="bi bi-shield-lock me-2"></i>Users, Roles &amp; Permissions</a></li>
                            <li><a class="dropdown-item py-2" href="{{ url('/') }}" target="_blank"><i class="bi bi-box-arrow-up-right me-2"></i>Visit Frontend ({{ config('app.active_module') }})</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li class="px-3 py-1 text-uppercase text-muted" style="font-size: 10.5px; font-weight: 700;">Auth Pages Preview</li>
                            <li><a class="dropdown-item py-1" href="{{ route('login') }}"><i class="bi bi-person-lock me-2"></i>Login UI</a></li>
                            <li><a class="dropdown-item py-1" href="{{ route('password.request') }}"><i class="bi bi-key me-2"></i>Forgot Password UI</a></li>
                            <li><a class="dropdown-item py-1" href="{{ route('password.reset') }}"><i class="bi bi-shield-check me-2"></i>Reset Password UI</a></li>
                            <li><a class="dropdown-item py-1" href="{{ route('verification.notice') }}"><i class="bi bi-envelope-check me-2"></i>Verify Email UI</a></li>
                            <li><a class="dropdown-item py-1" href="{{ route('two-factor.login') }}"><i class="bi bi-phone-flip me-2"></i>Two-Factor (2FA) UI</a></li>
                            <li><a class="dropdown-item py-1" href="{{ route('password.confirm') }}"><i class="bi bi-check2-circle me-2"></i>Confirm Password UI</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item py-2 text-danger" href="{{ route('logout') }}"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                        </ul>
                    </div>
                </div>
            </header>

            <main class="sb-content">
                @if(session('success'))
                    <div class="alert alert-success border-0 shadow-sm mb-4" style="border-radius: 10px;">{{ session('success') }}</div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger border-0 shadow-sm mb-4" style="border-radius: 10px;">{{ $errors->first() }}</div>
                @endif
                @yield('admin_content')
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('module_scripts')
</body>
</html>
