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
            width: 268px;
            flex-shrink: 0;
            background: #FFFFFF;
            border-right: 1px solid var(--sb-border);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 20px 14px 18px;
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
            padding: 9px 12px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 500;
            color: #334155;
            text-decoration: none;
            transition: all 0.15s ease;
            white-space: nowrap;
            overflow: hidden;
        }

        .sb-nav-link .link-left {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
            flex: 1;
            overflow: hidden;
        }

        .sb-nav-link .link-left > span {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sb-nav-link i.nav-icon {
            font-size: 15px;
            color: #64748B;
            width: 18px;
            flex-shrink: 0;
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

        .modal-content {
            background-color: #FFFFFF !important;
            display: flex !important;
            flex-direction: column !important;
            width: 100% !important;
            border-radius: 16px;
        }

        /* ==================================================================
           GLOBAL FULL-FEATURED RICH TEXT EDITOR (.sb-rte-wrap)
           ================================================================== */
        .sb-rte-wrap {
            border: 1px solid #CBD5E1;
            border-radius: 12px;
            background: #FFFFFF;
            overflow: hidden;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .sb-rte-wrap:focus-within {
            border-color: #2563EB;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
        }

        .sb-rte-wrap.sb-rte-fullscreen {
            position: fixed !important;
            inset: 16px !important;
            z-index: 99999 !important;
            display: flex !important;
            flex-direction: column !important;
            box-shadow: 0 25px 60px rgba(15, 23, 42, 0.35) !important;
        }

        .sb-rte-toolbar {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 4px;
            padding: 8px 10px;
            background: #F8FAFC;
            border-bottom: 1px solid #E2E8F0;
            user-select: none;
        }

        .sb-rte-group {
            display: inline-flex;
            align-items: center;
            gap: 2px;
            padding-right: 6px;
            margin-right: 2px;
            border-right: 1px solid #E2E8F0;
        }

        .sb-rte-group:last-child {
            border-right: none;
            padding-right: 0;
        }

        .sb-rte-btn {
            width: 30px;
            height: 30px;
            border: 1px solid transparent;
            border-radius: 6px;
            background: transparent;
            color: #334155;
            font-size: 13.5px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            padding: 0;
            transition: all 0.15s;
        }

        .sb-rte-btn:hover {
            background: #E2E8F0;
            color: #0F172A;
        }

        .sb-rte-btn.active {
            background: #DBEAFE;
            color: #1D4ED8;
            border-color: #BFDBFE;
        }

        .sb-rte-select {
            height: 30px;
            border: 1px solid #CBD5E1;
            border-radius: 6px;
            background: #FFFFFF;
            color: #1E293B;
            font-size: 12px;
            font-weight: 600;
            padding: 0 8px;
            outline: none;
            cursor: pointer;
        }

        .sb-rte-color-label {
            position: relative;
            width: 30px;
            height: 30px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 13px;
            color: #334155;
        }

        .sb-rte-color-label:hover {
            background: #E2E8F0;
        }

        .sb-rte-color-input {
            position: absolute;
            opacity: 0;
            width: 100%;
            height: 100%;
            left: 0;
            top: 0;
            cursor: pointer;
        }

        .sb-rte-editor {
            min-height: 210px;
            max-height: 420px;
            overflow-y: auto;
            padding: 14px 16px;
            font-size: 14px;
            line-height: 1.7;
            color: #0F172A;
            outline: none;
        }

        .sb-rte-wrap.sb-rte-fullscreen .sb-rte-editor {
            flex: 1;
            max-height: none;
        }

        .sb-rte-editor h1, .sb-rte-editor h2, .sb-rte-editor h3, .sb-rte-editor h4 {
            font-weight: 800;
            color: #0F172A;
            margin-top: 12px;
            margin-bottom: 8px;
        }

        .sb-rte-editor blockquote {
            border-left: 4px solid #2563EB;
            background: #F8FAFC;
            padding: 10px 16px;
            margin: 10px 0;
            border-radius: 0 8px 8px 0;
            font-style: italic;
            color: #334155;
        }

        .sb-rte-editor pre {
            background: #0F172A;
            color: #38BDF8;
            padding: 12px 16px;
            border-radius: 8px;
            font-family: monospace;
            font-size: 13px;
            overflow-x: auto;
        }

        .sb-rte-editor table {
            width: 100%;
            border-collapse: collapse;
            margin: 12px 0;
        }

        .sb-rte-editor table td, .sb-rte-editor table th {
            border: 1px solid #CBD5E1;
            padding: 8px 12px;
        }

        .sb-rte-editor img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            margin: 8px 0;
        }

        .sb-rte-source {
            display: none;
            width: 100%;
            min-height: 210px;
            border: none;
            padding: 14px 16px;
            font-family: monospace;
            font-size: 13px;
            background: #0F172A;
            color: #E2E8F0;
            outline: none;
            resize: vertical;
        }

        .sb-rte-statusbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 6px 12px;
            background: #F8FAFC;
            border-top: 1px solid #E2E8F0;
            font-size: 11px;
            color: #64748B;
        }
    </style>
    @include('admin.partials.form-system')
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
                $headerTenant = function_exists('current_tenant') ? current_tenant() : (app()->bound('tenant') ? app('tenant') : null);
                $headerAccessibleTenants = $currentUser
                    ? $currentUser->accessibleTenantsQuery()->orderByDesc('is_demo')->orderBy('id')->get()
                    : collect();
                $impersonatorUser = session()->has('impersonator_id')
                    ? \App\Models\User::find(session('impersonator_id'))
                    : null;
            @endphp

            @if($impersonatorUser)
                <div class="px-4 py-2 d-flex align-items-center justify-content-between" style="background: #0F172A; color: #FFFFFF; font-size: 12.5px;">
                    <div>
                        <i class="bi bi-person-badge-fill text-warning me-2"></i>
                        Impersonating <strong>{{ $currentUser?->name }}</strong>
                        <span class="badge bg-warning text-dark ms-1">{{ strtoupper($currentUser?->panelType() ?? 'CLIENT') }} PANEL</span>
                        @if($headerTenant)
                            <span class="ms-2 text-white-50">• Active DB: <code class="text-info">{{ $headerTenant->db_name }}</code></span>
                        @endif
                    </div>
                    <form action="{{ route('admin.impersonate.leave') }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-warning fw-bold py-0 px-3" style="font-size: 11.5px; border-radius: 6px;">
                            <i class="bi bi-box-arrow-left me-1"></i> Return to {{ $impersonatorUser->name }}
                        </button>
                    </form>
                </div>
            @endif

            <!-- Top Header Bar -->
            <header class="sb-topbar">
                <div class="d-flex align-items-center gap-3">
                    <form action="{{ route('admin.transactions') }}" method="GET" class="sb-search-box">
                        <i class="bi bi-search"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search anything...">
                    </form>

                    @if($headerAccessibleTenants->isNotEmpty())
                        <div class="dropdown d-none d-md-block">
                            <button class="btn btn-sm btn-light border d-flex align-items-center gap-2 px-3 py-1" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="border-radius: 9px; font-size: 12.5px;">
                                <i class="bi bi-hdd-network text-danger"></i>
                                <span class="fw-semibold text-dark">{{ $headerTenant ? $headerTenant->name : 'Select Website' }}</span>
                                @if($headerTenant)
                                    <code class="small px-1 rounded" style="background: #DCFCE7; color: #166534; font-size: 10.5px;">{{ $headerTenant->db_name }}</code>
                                @endif
                                <i class="bi bi-chevron-down text-muted" style="font-size: 10px;"></i>
                            </button>
                            <ul class="dropdown-menu shadow-sm border-0 mt-2 p-2" style="border-radius: 12px; min-width: 280px; max-height: 360px; overflow-y: auto;">
                                <li class="px-2 py-1 small fw-bold text-muted border-bottom mb-1">Switch Active Website (Tenant DB)</li>
                                @foreach($headerAccessibleTenants as $hTenant)
                                    <li>
                                        <a class="dropdown-item rounded py-2 d-flex align-items-center justify-content-between {{ ($headerTenant && $headerTenant->id === $hTenant->id) ? 'bg-danger-subtle text-danger fw-bold' : '' }}"
                                           href="{{ route('admin.tenants.switch', ['tenant' => $hTenant->id, 'redirect_to' => request()->fullUrl()]) }}">
                                            <div class="text-truncate me-2">
                                                <div style="font-size: 12.5px;">{{ $hTenant->name }}</div>
                                                <div class="text-muted" style="font-size: 10.5px;">{{ $hTenant->theme_module }} • {{ $hTenant->db_name }}</div>
                                            </div>
                                            <span class="badge {{ $hTenant->is_demo ? 'bg-secondary-subtle text-secondary' : 'bg-success-subtle text-success' }}" style="font-size: 10px;">
                                                {{ $hTenant->is_demo ? 'DEMO' : strtoupper($hTenant->status) }}
                                            </span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>

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
                            <li><a class="dropdown-item py-1" href="{{ route('admin.settings') }}"><i class="bi bi-shield-check me-2"></i>Reset Password UI</a></li>
                            <li><a class="dropdown-item py-1" href="{{ route('verification.notice') }}"><i class="bi bi-envelope-check me-2"></i>Verify Email UI</a></li>
                            <li><a class="dropdown-item py-1" href="{{ route('two-factor.login') }}"><i class="bi bi-phone-flip me-2"></i>Two-Factor (2FA) UI</a></li>
                            <li><a class="dropdown-item py-1" href="{{ route('password.confirm') }}"><i class="bi bi-check2-circle me-2"></i>Confirm Password UI</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><form method="POST" action="{{ route('logout') }}" class="m-0">@csrf<button type="submit" class="dropdown-item py-2 text-danger"><i class="bi bi-box-arrow-right me-2"></i>Logout</button></form></li>
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
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            function initRichTextEditor(textarea) {
                if (textarea.dataset.rteInitialized === 'true' || textarea.classList.contains('no-rte')) return;
                textarea.dataset.rteInitialized = 'true';

                const wrapper = document.createElement('div');
                wrapper.className = 'sb-rte-wrap';

                const isCompact = textarea.classList.contains('sb-rte-compact') || (textarea.rows && parseInt(textarea.rows, 10) <= 2);

                // Build Full-Featured Summernote-Style Toolbar
                const toolbar = document.createElement('div');
                toolbar.className = 'sb-rte-toolbar';
                toolbar.innerHTML = `
                    <!-- Group 1: Undo / Redo -->
                    <div class="sb-rte-group">
                        <button type="button" class="sb-rte-btn" data-cmd="undo" title="Undo (Ctrl+Z)"><i class="bi bi-arrow-counterclockwise"></i></button>
                        <button type="button" class="sb-rte-btn" data-cmd="redo" title="Redo (Ctrl+Y)"><i class="bi bi-arrow-clockwise"></i></button>
                    </div>

                    <!-- Group 2: Format Block & Font Size -->
                    <div class="sb-rte-group">
                        <select class="sb-rte-select" data-cmd-select="formatBlock" title="Paragraph / Heading Format">
                            <option value="P">Paragraph</option>
                            <option value="H1">Heading 1 (H1)</option>
                            <option value="H2">Heading 2 (H2)</option>
                            <option value="H3">Heading 3 (H3)</option>
                            <option value="H4">Heading 4 (H4)</option>
                            <option value="BLOCKQUOTE">Blockquote</option>
                            <option value="PRE">Code Block</option>
                        </select>
                        <select class="sb-rte-select" data-cmd-select="fontSize" title="Font Size">
                            <option value="3">Normal Size</option>
                            <option value="1">Small (12px)</option>
                            <option value="2">Compact (13px)</option>
                            <option value="4">Medium (18px)</option>
                            <option value="5">Large (24px)</option>
                            <option value="6">X-Large (32px)</option>
                        </select>
                    </div>

                    <!-- Group 3: Bold, Italic, Underline, Strike, Sub/Sup -->
                    <div class="sb-rte-group">
                        <button type="button" class="sb-rte-btn" data-cmd="bold" title="Bold (Ctrl+B)"><i class="bi bi-type-bold"></i></button>
                        <button type="button" class="sb-rte-btn" data-cmd="italic" title="Italic (Ctrl+I)"><i class="bi bi-type-italic"></i></button>
                        <button type="button" class="sb-rte-btn" data-cmd="underline" title="Underline (Ctrl+U)"><i class="bi bi-type-underline"></i></button>
                        <button type="button" class="sb-rte-btn" data-cmd="strikeThrough" title="Strikethrough"><i class="bi bi-type-strikethrough"></i></button>
                        <button type="button" class="sb-rte-btn" data-cmd="subscript" title="Subscript"><i class="bi bi-subscript"></i></button>
                        <button type="button" class="sb-rte-btn" data-cmd="superscript" title="Superscript"><i class="bi bi-superscript"></i></button>
                    </div>

                    <!-- Group 4: Text Color & Background Highlight -->
                    <div class="sb-rte-group">
                        <label class="sb-rte-color-label" title="Text Color">
                            <i class="bi bi-palette"></i>
                            <input type="color" class="sb-rte-color-input" data-cmd-color="foreColor" value="#1FA84F">
                        </label>
                        <label class="sb-rte-color-label" title="Highlight Background Color">
                            <i class="bi bi-highlighter"></i>
                            <input type="color" class="sb-rte-color-input" data-cmd-color="hiliteColor" value="#FEF08A">
                        </label>
                    </div>

                    <!-- Group 5: Alignment -->
                    <div class="sb-rte-group">
                        <button type="button" class="sb-rte-btn" data-cmd="justifyLeft" title="Align Left"><i class="bi bi-text-left"></i></button>
                        <button type="button" class="sb-rte-btn" data-cmd="justifyCenter" title="Align Center"><i class="bi bi-text-center"></i></button>
                        <button type="button" class="sb-rte-btn" data-cmd="justifyRight" title="Align Right"><i class="bi bi-text-right"></i></button>
                        <button type="button" class="sb-rte-btn" data-cmd="justifyFull" title="Justify Full"><i class="bi bi-justify"></i></button>
                    </div>

                    <!-- Group 6: Lists & Indent -->
                    <div class="sb-rte-group">
                        <button type="button" class="sb-rte-btn" data-cmd="insertUnorderedList" title="Bullet List"><i class="bi bi-list-ul"></i></button>
                        <button type="button" class="sb-rte-btn" data-cmd="insertOrderedList" title="Numbered List"><i class="bi bi-list-ol"></i></button>
                        <button type="button" class="sb-rte-btn" data-custom="checklist" title="Insert Feature Checklist"><i class="bi bi-check2-square"></i></button>
                        <button type="button" class="sb-rte-btn" data-cmd="outdent" title="Outdent"><i class="bi bi-text-indent-right"></i></button>
                        <button type="button" class="sb-rte-btn" data-cmd="indent" title="Indent"><i class="bi bi-text-indent-left"></i></button>
                    </div>

                    <!-- Group 7: Links, Images, Tables, Callout, Divider -->
                    <div class="sb-rte-group">
                        <button type="button" class="sb-rte-btn" data-custom="link" title="Insert Hyperlink"><i class="bi bi-link-45deg"></i></button>
                        <button type="button" class="sb-rte-btn" data-cmd="unlink" title="Remove Hyperlink"><i class="bi bi-link"></i></button>
                        <button type="button" class="sb-rte-btn" data-custom="imageUrl" title="Insert Image by URL"><i class="bi bi-image"></i></button>
                        <label class="sb-rte-color-label" title="Upload Image from Computer">
                            <i class="bi bi-cloud-arrow-up"></i>
                            <input type="file" accept="image/*" class="sb-rte-color-input" data-custom-upload="image">
                        </label>
                        <button type="button" class="sb-rte-btn" data-custom="table" title="Insert 3x3 Table"><i class="bi bi-table"></i></button>
                        <button type="button" class="sb-rte-btn" data-custom="callout" title="Insert Highlight Callout Box"><i class="bi bi-card-text"></i></button>
                        <button type="button" class="sb-rte-btn" data-cmd="insertHorizontalRule" title="Insert Horizontal Divider"><i class="bi bi-hr"></i></button>
                    </div>

                    <!-- Group 8: Clear Format, Source Code & Fullscreen -->
                    <div class="sb-rte-group">
                        <button type="button" class="sb-rte-btn" data-cmd="removeFormat" title="Clear Formatting"><i class="bi bi-eraser"></i></button>
                        <button type="button" class="sb-rte-btn" data-custom="source" title="Toggle HTML Source Code"><i class="bi bi-code-slash"></i></button>
                        <button type="button" class="sb-rte-btn" data-custom="fullscreen" title="Toggle Fullscreen Editor"><i class="bi bi-arrows-fullscreen"></i></button>
                    </div>
                `;

                // Editable Visual Area
                const editor = document.createElement('div');
                editor.className = 'sb-rte-editor';
                editor.contentEditable = 'true';
                if (isCompact) {
                    editor.style.minHeight = '125px';
                    editor.style.maxHeight = '260px';
                }
                if (textarea.getAttribute('dir') === 'rtl' || /_ar\b/.test(textarea.name || '')) {
                    editor.setAttribute('dir', 'rtl');
                    editor.style.textAlign = 'right';
                }
                if (/_bn\b/.test(textarea.name || '')) {
                    editor.style.fontFamily = "'SolaimanLipi', 'Solaiman Lipi', 'Hind Siliguri', 'Inter', sans-serif";
                }

                // Convert plain text newlines to paragraphs if not already HTML
                const initialVal = textarea.value.trim();
                if (initialVal !== '' && !/<[a-z][\s\S]*>/i.test(initialVal)) {
                    editor.innerHTML = initialVal.split(/\n\n+/).map(p => `<p>${p.replace(/\n/g, '<br>')}</p>`).join('');
                } else {
                    editor.innerHTML = initialVal || '<p><br></p>';
                }

                // Bottom Status Bar (Word & Character Count)
                const statusbar = document.createElement('div');
                statusbar.className = 'sb-rte-statusbar';
                statusbar.innerHTML = `
                    <span><i class="bi bi-stars text-primary me-1"></i> Summernote Pro Rich Text Editor (Visual &amp; HTML)</span>
                    <span class="sb-rte-counts">0 words • 0 chars</span>
                `;
                const countEl = statusbar.querySelector('.sb-rte-counts');

                // Remove required from hidden textarea to avoid browser 'not focusable' error, validate on submit if needed
                if (textarea.hasAttribute('required')) {
                    textarea.removeAttribute('required');
                    textarea.dataset.wasRequired = 'true';
                }

                // Hide original textarea and use it as the synced form field (+ HTML source view)
                textarea.classList.add('sb-rte-source');
                textarea.parentNode.insertBefore(wrapper, textarea);
                wrapper.appendChild(toolbar);
                wrapper.appendChild(editor);
                wrapper.appendChild(textarea);
                wrapper.appendChild(statusbar);

                let isSourceMode = false;

                function syncToTextarea() {
                    if (!isSourceMode) {
                        textarea.value = editor.innerHTML;
                    }
                    const textOnly = editor.innerText.trim();
                    const words = textOnly ? textOnly.split(/\s+/).length : 0;
                    countEl.textContent = `${words} words • ${textOnly.length} chars`;
                }

                syncToTextarea();
                editor.addEventListener('input', syncToTextarea);
                editor.addEventListener('blur', syncToTextarea);
                textarea.addEventListener('input', function () {
                    if (isSourceMode) {
                        editor.innerHTML = textarea.value;
                    }
                });

                // Ensure form submission always has latest synced HTML
                const parentForm = textarea.closest('form');
                if (parentForm) {
                    parentForm.addEventListener('submit', function () {
                        if (isSourceMode) {
                            editor.innerHTML = textarea.value;
                        } else {
                            textarea.value = editor.innerHTML;
                        }
                    });
                }

                // Standard execCommand buttons
                toolbar.querySelectorAll('[data-cmd]').forEach(function (btn) {
                    btn.addEventListener('mousedown', function (e) {
                        e.preventDefault();
                        const cmd = this.getAttribute('data-cmd');
                        editor.focus();
                        document.execCommand(cmd, false, null);
                        syncToTextarea();
                    });
                });

                // Select dropdown commands (formatBlock, fontSize)
                toolbar.querySelectorAll('[data-cmd-select]').forEach(function (sel) {
                    sel.addEventListener('change', function () {
                        const cmd = this.getAttribute('data-cmd-select');
                        const val = this.value;
                        editor.focus();
                        if (cmd === 'formatBlock') {
                            document.execCommand(cmd, false, '<' + val + '>');
                        } else {
                            document.execCommand(cmd, false, val);
                        }
                        syncToTextarea();
                    });
                });

                // Color pickers
                toolbar.querySelectorAll('[data-cmd-color]').forEach(function (input) {
                    input.addEventListener('input', function () {
                        const cmd = this.getAttribute('data-cmd-color');
                        editor.focus();
                        document.execCommand(cmd, false, this.value);
                        syncToTextarea();
                    });
                });

                // Local Image Upload -> Base64 inline insert
                const fileInput = toolbar.querySelector('[data-custom-upload="image"]');
                if (fileInput) {
                    fileInput.addEventListener('change', function () {
                        const file = this.files && this.files[0];
                        if (!file) return;
                        const reader = new FileReader();
                        reader.onload = function (ev) {
                            editor.focus();
                            document.execCommand('insertHTML', false, `<img src="${ev.target.result}" alt="${file.name}" style="max-width:100%; border-radius:10px; margin:10px 0;">`);
                            syncToTextarea();
                        };
                        reader.readAsDataURL(file);
                        this.value = '';
                    });
                }

                // Custom Actions (Link, Image URL, Table, Checklist, Callout, Source, Fullscreen)
                toolbar.querySelectorAll('[data-custom]').forEach(function (btn) {
                    btn.addEventListener('click', function (e) {
                        e.preventDefault();
                        const action = this.getAttribute('data-custom');

                        if (action === 'link') {
                            const url = prompt('Enter URL (e.g. https://example.com):', 'https://');
                            if (url && url !== 'https://') {
                                editor.focus();
                                document.execCommand('createLink', false, url);
                                syncToTextarea();
                            }
                        } else if (action === 'imageUrl') {
                            const url = prompt('Enter Image URL:', 'https://');
                            if (url && url !== 'https://') {
                                editor.focus();
                                document.execCommand('insertHTML', false, `<img src="${url}" alt="Content Image" style="max-width:100%; border-radius:10px; margin:10px 0;">`);
                                syncToTextarea();
                            }
                        } else if (action === 'table') {
                            editor.focus();
                            const tableHtml = `
                                <table>
                                    <thead>
                                        <tr><th>Day / Schedule</th><th>Location &amp; Activity</th><th>Included Services</th></tr>
                                    </thead>
                                    <tbody>
                                        <tr><td>Day 1–5</td><td>Makkah Hotel Check-in &amp; Umrah Tawaf</td><td>Hotel, Transport &amp; Mutawif Guide</td></tr>
                                        <tr><td>Day 6–10</td><td>Madinah Masjid An-Nabawi &amp; Ziyarah</td><td>Full Board Halal Meals &amp; AC Bus</td></tr>
                                    </tbody>
                                </table><p><br></p>`;
                            document.execCommand('insertHTML', false, tableHtml);
                            syncToTextarea();
                        } else if (action === 'checklist') {
                            editor.focus();
                            const listHtml = `
                                <ul style="list-style:none; padding-left:0;">
                                    <li>✅ Direct Return Flight &amp; Express Visa Processing</li>
                                    <li>✅ Star Hotel Walking Distance to Haramain</li>
                                    <li>✅ 3 Times Buffet Meals &amp; Guided Ziyarah Tours</li>
                                </ul><p><br></p>`;
                            document.execCommand('insertHTML', false, listHtml);
                            syncToTextarea();
                        } else if (action === 'callout') {
                            editor.focus();
                            const calloutHtml = `
                                <blockquote>
                                    <strong>💡 Important Package Highlight:</strong> Experienced religious scholars (Shariah guides) accompany the group throughout Makkah and Madinah.
                                </blockquote><p><br></p>`;
                            document.execCommand('insertHTML', false, calloutHtml);
                            syncToTextarea();
                        } else if (action === 'source') {
                            isSourceMode = !isSourceMode;
                            this.classList.toggle('active', isSourceMode);
                            if (isSourceMode) {
                                textarea.value = editor.innerHTML;
                                editor.style.display = 'none';
                                textarea.style.display = 'block';
                                textarea.focus();
                            } else {
                                editor.innerHTML = textarea.value;
                                textarea.style.display = 'none';
                                editor.style.display = 'block';
                                editor.focus();
                            }
                        } else if (action === 'fullscreen') {
                            wrapper.classList.toggle('sb-rte-fullscreen');
                            this.classList.toggle('active', wrapper.classList.contains('sb-rte-fullscreen'));
                        }
                    });
                });
            }

            const rteSelector = [
                'textarea.sb-rich-editor',
                'textarea[name="description"]',
                'textarea[name^="description_"]',
                'textarea[name="short_description"]',
                'textarea[name^="short_description_"]',
                'textarea[name="content"]',
                'textarea[name^="content_"]',
                'textarea[name="details"]',
                'textarea[name^="details_"]',
                'textarea[name="review_text"]',
                'textarea[name^="review_text_"]',
                'textarea[name*="[hero_subtitle]"]',
                'textarea[name*="[services_subtitle]"]',
                'textarea[name*="[pricing_subtitle]"]',
                'textarea[name*="[offers_subtitle]"]'
            ].join(', ');

            document.querySelectorAll(rteSelector).forEach(initRichTextEditor);
        });
    </script>
    @stack('module_scripts')
</body>
</html>
