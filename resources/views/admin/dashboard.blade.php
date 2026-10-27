@extends('admin.layouts.master')

@section('title', 'Dashboard - SiteBuilder')

@push('module_styles')
<style>
    .dash-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        margin-bottom: 24px;
    }

    .dash-header h2 {
        font-size: 26px;
        font-weight: 800;
        color: #0F172A;
        letter-spacing: -0.02em;
        margin: 0 0 4px 0;
    }

    .dash-header p {
        font-size: 14px;
        color: #64748B;
        margin: 0;
    }

    .date-picker-btn {
        background: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 10px;
        padding: 9px 16px;
        font-size: 13px;
        font-weight: 500;
        color: #334155;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.03);
    }

    /* 4 KPI Cards Grid */
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-bottom: 24px;
    }

    .kpi-card {
        background: #FFFFFF;
        border: 1px solid #EEF2F6;
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 2px 6px rgba(15, 23, 42, 0.02);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .kpi-top {
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 14px;
    }

    .kpi-icon {
        width: 46px;
        height: 46px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }

    .kpi-meta span {
        display: block;
        font-size: 12.5px;
        font-weight: 500;
        color: #64748B;
        margin-bottom: 2px;
    }

    .kpi-meta h3 {
        font-size: 26px;
        font-weight: 800;
        color: #0F172A;
        margin: 0;
        line-height: 1.15;
    }

    .kpi-bottom {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
    }

    .kpi-trend-up {
        display: inline-flex;
        align-items: center;
        gap: 3px;
        font-size: 13px;
        font-weight: 700;
        color: #10B981;
        margin-bottom: 4px;
    }

    .kpi-sub {
        font-size: 12px;
        color: #94A3B8;
        margin: 0;
    }

    /* Middle Charts Grid */
    .charts-grid {
        display: grid;
        grid-template-columns: 1.85fr 1fr;
        gap: 20px;
        margin-bottom: 24px;
    }

    .sb-panel {
        background: #FFFFFF;
        border: 1px solid #EEF2F6;
        border-radius: 16px;
        padding: 22px 24px;
        box-shadow: 0 2px 6px rgba(15, 23, 42, 0.02);
    }

    .sb-panel-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        margin-bottom: 14px;
    }

    .sb-panel-title h4 {
        font-size: 17px;
        font-weight: 800;
        color: #0F172A;
        margin: 0 0 3px 0;
    }

    .sb-panel-title p {
        font-size: 12.5px;
        color: #64748B;
        margin: 0;
    }

    .time-tabs {
        display: inline-flex;
        background: #F8FAFC;
        border: 1px solid #E2E8F0;
        border-radius: 9px;
        padding: 3px;
        gap: 2px;
    }

    .time-tab {
        border: none;
        background: transparent;
        padding: 5px 12px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        color: #64748B;
        cursor: pointer;
    }

    .time-tab.active {
        background: #FFEBF0;
        color: #EC134A;
    }

    .chart-legend {
        display: flex;
        justify-content: flex-end;
        gap: 18px;
        margin-bottom: 8px;
        font-size: 12px;
        font-weight: 500;
        color: #475569;
    }

    .legend-dot {
        display: inline-block;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        margin-right: 6px;
    }

    /* Donut Chart Layout */
    .donut-wrap {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
        margin-top: 22px;
    }

    .donut-legend {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .donut-legend-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 13px;
        color: #475569;
    }

    .donut-legend-row strong {
        font-weight: 700;
        color: #0F172A;
    }

    /* Bottom Grid */
    .bottom-grid {
        display: grid;
        grid-template-columns: 1.45fr 1fr;
        gap: 20px;
    }

    .view-all-link {
        font-size: 13px;
        font-weight: 600;
        color: #EC134A;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .view-all-link:hover {
        text-decoration: underline;
        color: #D40E40;
    }

    .recent-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 8px;
    }

    .recent-table thead th {
        background: #F8FAFC;
        padding: 10px 14px;
        font-size: 12px;
        font-weight: 600;
        color: #64748B;
        text-align: left;
        border-radius: 6px;
    }

    .recent-table tbody td {
        padding: 12px 14px;
        border-bottom: 1px solid #F1F5F9;
        font-size: 13px;
        vertical-align: middle;
    }

    .recent-table tbody tr:last-child td {
        border-bottom: none;
    }

    .site-item {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .site-thumb {
        width: 44px;
        height: 32px;
        border-radius: 6px;
        object-fit: cover;
        flex-shrink: 0;
        border: 1px solid #E2E8F0;
    }

    .site-info h6 {
        font-size: 13.5px;
        font-weight: 700;
        color: #0F172A;
        margin: 0;
    }

    .site-info span {
        font-size: 11.5px;
        color: #94A3B8;
    }

    .btn-dots {
        width: 30px;
        height: 30px;
        border-radius: 8px;
        border: 1px solid #E2E8F0;
        background: #FFFFFF;
        color: #64748B;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }

    /* Activity List */
    .activity-list {
        display: flex;
        flex-direction: column;
        gap: 18px;
        margin-top: 14px;
    }

    .activity-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    .activity-left {
        display: flex;
        align-items: center;
        gap: 13px;
    }

    .activity-icon {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
        flex-shrink: 0;
    }

    .activity-text h6 {
        font-size: 13.5px;
        font-weight: 700;
        color: #0F172A;
        margin: 0 0 2px 0;
    }

    .activity-text span {
        font-size: 12px;
        color: #64748B;
    }

    .activity-time {
        font-size: 12px;
        color: #94A3B8;
        white-space: nowrap;
    }

    @media (max-width: 1200px) {
        .kpi-grid { grid-template-columns: repeat(2, 1fr); }
        .charts-grid, .bottom-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('admin_content')
    @php
        $userFirstName = explode(' ', auth()->user()?->name ?? 'John')[0];
    @endphp
    <!-- Dashboard Header -->
    <div class="dash-header">
        <div>
            <h2>Dashboard</h2>
            <p>Welcome back, {{ $userFirstName }}! Here's what's happening with your website today.</p>
        </div>
        <button type="button" class="date-picker-btn">
            <i class="bi bi-calendar4-week" style="color: #64748B;"></i>
            <span>{{ $dateRangeLabel ?? 'Mar 1, 2025 - Mar 31, 2025' }}</span>
            <i class="bi bi-chevron-down" style="font-size: 11px; color: #64748B;"></i>
        </button>
    </div>

    <!-- 4 KPI Stat Cards (Dynamic Counts from Database) -->
    <div class="kpi-grid">
        <!-- Card 1: Total Websites -->
        <div class="kpi-card">
            <div class="kpi-top">
                <div class="kpi-icon" style="background: #FFE4EC; color: #EC134A;">
                    <i class="bi bi-globe2"></i>
                </div>
                <div class="kpi-meta">
                    <span>Total Websites</span>
                    <h3>{{ $totalWebsites ?? 5 }}</h3>
                </div>
            </div>
            <div class="kpi-bottom">
                <div>
                    <div class="kpi-trend-up"><i class="bi bi-arrow-up"></i> 25%</div>
                    <p class="kpi-sub">+1 new website this month</p>
                </div>
                <svg width="86" height="42" viewBox="0 0 86 42" fill="none">
                    <defs>
                        <linearGradient id="sp1" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#EC134A" stop-opacity="0.25"/>
                            <stop offset="100%" stop-color="#EC134A" stop-opacity="0.0"/>
                        </linearGradient>
                    </defs>
                    <path d="M2 35 Q16 32, 26 22 T52 20 T84 8 L84 40 L2 40 Z" fill="url(#sp1)"/>
                    <path d="M2 35 Q16 32, 26 22 T52 20 T84 8" stroke="#EC134A" stroke-width="2" fill="none" stroke-linecap="round"/>
                </svg>
            </div>
        </div>

        <!-- Card 2: Total Pages -->
        <div class="kpi-card">
            <div class="kpi-top">
                <div class="kpi-icon" style="background: #DBEAFE; color: #3B82F6;">
                    <i class="bi bi-file-earmark-text"></i>
                </div>
                <div class="kpi-meta">
                    <span>Total Pages</span>
                    <h3>{{ $totalPages ?? 48 }}</h3>
                </div>
            </div>
            <div class="kpi-bottom">
                <div>
                    <div class="kpi-trend-up"><i class="bi bi-arrow-up"></i> 12%</div>
                    <p class="kpi-sub">+5 new pages this month</p>
                </div>
                <svg width="86" height="42" viewBox="0 0 86 42" fill="none">
                    <defs>
                        <linearGradient id="sp2" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#3B82F6" stop-opacity="0.25"/>
                            <stop offset="100%" stop-color="#3B82F6" stop-opacity="0.0"/>
                        </linearGradient>
                    </defs>
                    <path d="M2 36 Q18 33, 28 24 T55 21 T84 9 L84 40 L2 40 Z" fill="url(#sp2)"/>
                    <path d="M2 36 Q18 33, 28 24 T55 21 T84 9" stroke="#3B82F6" stroke-width="2" fill="none" stroke-linecap="round"/>
                </svg>
            </div>
        </div>

        <!-- Card 3: Media Files -->
        <div class="kpi-card">
            <div class="kpi-top">
                <div class="kpi-icon" style="background: #DCFCE7; color: #10B981;">
                    <i class="bi bi-image"></i>
                </div>
                <div class="kpi-meta">
                    <span>Media Files</span>
                    <h3>{{ $mediaFilesCount ?? '1,284' }}</h3>
                </div>
            </div>
            <div class="kpi-bottom">
                <div>
                    <div class="kpi-trend-up"><i class="bi bi-arrow-up"></i> 18%</div>
                    <p class="kpi-sub">+196 new files this month</p>
                </div>
                <svg width="86" height="42" viewBox="0 0 86 42" fill="none">
                    <defs>
                        <linearGradient id="sp3" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#10B981" stop-opacity="0.25"/>
                            <stop offset="100%" stop-color="#10B981" stop-opacity="0.0"/>
                        </linearGradient>
                    </defs>
                    <path d="M2 36 Q18 32, 30 23 T56 19 T84 8 L84 40 L2 40 Z" fill="url(#sp3)"/>
                    <path d="M2 36 Q18 32, 30 23 T56 19 T84 8" stroke="#10B981" stroke-width="2" fill="none" stroke-linecap="round"/>
                </svg>
            </div>
        </div>

        <!-- Card 4: Total Users -->
        <div class="kpi-card">
            <div class="kpi-top">
                <div class="kpi-icon" style="background: #F3E8FF; color: #A855F7;">
                    <i class="bi bi-person"></i>
                </div>
                <div class="kpi-meta">
                    <span>Total Users</span>
                    <h3>{{ $totalUsers ?? 12 }}</h3>
                </div>
            </div>
            <div class="kpi-bottom">
                <div>
                    <div class="kpi-trend-up"><i class="bi bi-arrow-up"></i> 8%</div>
                    <p class="kpi-sub">+1 new user this month</p>
                </div>
                <svg width="86" height="42" viewBox="0 0 86 42" fill="none">
                    <defs>
                        <linearGradient id="sp4" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#A855F7" stop-opacity="0.25"/>
                            <stop offset="100%" stop-color="#A855F7" stop-opacity="0.0"/>
                        </linearGradient>
                    </defs>
                    <path d="M2 36 Q18 32, 28 24 T55 20 T84 9 L84 40 L2 40 Z" fill="url(#sp4)"/>
                    <path d="M2 36 Q18 32, 28 24 T55 20 T84 9" stroke="#A855F7" stroke-width="2" fill="none" stroke-linecap="round"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Middle Row: Website Traffic & Page Overview -->
    <div class="charts-grid">
        <!-- Website Traffic Chart Panel -->
        <div class="sb-panel">
            <div class="sb-panel-header">
                <div class="sb-panel-title">
                    <h4>Website Traffic</h4>
                    <p>Overview of your website visitors and activity.</p>
                </div>
                <div class="time-tabs" id="trafficTabs">
                    <button type="button" class="time-tab" onclick="setTrafficTab(this)">Day</button>
                    <button type="button" class="time-tab active" onclick="setTrafficTab(this)">Week</button>
                    <button type="button" class="time-tab" onclick="setTrafficTab(this)">Month</button>
                    <button type="button" class="time-tab" onclick="setTrafficTab(this)">Year</button>
                </div>
            </div>

            <div class="chart-legend">
                <span><i class="legend-dot" style="background: #EC134A;"></i>Visitors</span>
                <span><i class="legend-dot" style="background: #3B82F6;"></i>Page Views</span>
            </div>

            <!-- Dual Area Spline SVG Chart -->
            <svg viewBox="0 0 680 215" width="100%" height="215" fill="none" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <linearGradient id="blueGrad" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" stop-color="#3B82F6" stop-opacity="0.16"/>
                        <stop offset="100%" stop-color="#3B82F6" stop-opacity="0.0"/>
                    </linearGradient>
                    <linearGradient id="redGrad" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" stop-color="#EC134A" stop-opacity="0.22"/>
                        <stop offset="100%" stop-color="#EC134A" stop-opacity="0.0"/>
                    </linearGradient>
                </defs>

                <!-- Horizontal Grid Lines & Y-Axis Labels -->
                <g stroke="#F1F5F9" stroke-width="1">
                    <line x1="36" y1="20" x2="665" y2="20"/>
                    <line x1="36" y1="60" x2="665" y2="60"/>
                    <line x1="36" y1="100" x2="665" y2="100"/>
                    <line x1="36" y1="140" x2="665" y2="140"/>
                    <line x1="36" y1="180" x2="665" y2="180"/>
                </g>

                <g fill="#94A3B8" font-family="Inter, sans-serif" font-size="11">
                    <text x="4" y="24">4K</text>
                    <text x="4" y="64">3K</text>
                    <text x="4" y="104">2K</text>
                    <text x="4" y="144">1K</text>
                    <text x="10" y="184">0</text>
                </g>

                <!-- Blue Area & Line (Page Views) -->
                <path d="M36 146 C75 142, 95 102, 130 105 C165 108, 190 132, 225 120 C260 108, 285 82, 325 86 C365 90, 390 112, 430 92 C470 72, 495 58, 535 66 C575 74, 615 55, 665 28 L665 180 L36 180 Z" fill="url(#blueGrad)"/>
                <path d="M36 146 C75 142, 95 102, 130 105 C165 108, 190 132, 225 120 C260 108, 285 82, 325 86 C365 90, 390 112, 430 92 C470 72, 495 58, 535 66 C575 74, 615 55, 665 28" stroke="#3B82F6" stroke-width="2.2" fill="none"/>

                <!-- Red Area & Line (Visitors) -->
                <path d="M36 162 C75 158, 95 126, 130 128 C165 130, 190 148, 225 140 C260 132, 285 118, 325 122 C365 126, 390 138, 430 122 C470 106, 495 96, 535 104 C575 112, 615 82, 665 92 L665 180 L36 180 Z" fill="url(#redGrad)"/>
                <path d="M36 162 C75 158, 95 126, 130 128 C165 130, 190 148, 225 140 C260 132, 285 118, 325 122 C365 126, 390 138, 430 122 C470 106, 495 96, 535 104 C575 112, 615 82, 665 92" stroke="#EC134A" stroke-width="2.2" fill="none"/>

                <!-- X-Axis Labels -->
                <g fill="#94A3B8" font-family="Inter, sans-serif" font-size="11" text-anchor="middle">
                    <text x="48" y="202">Mar 1</text>
                    <text x="108" y="202">Mar 4</text>
                    <text x="168" y="202">Mar 7</text>
                    <text x="228" y="202">Mar 10</text>
                    <text x="288" y="202">Mar 13</text>
                    <text x="348" y="202">Mar 16</text>
                    <text x="408" y="202">Mar 19</text>
                    <text x="468" y="202">Mar 22</text>
                    <text x="528" y="202">Mar 25</text>
                    <text x="588" y="202">Mar 28</text>
                    <text x="648" y="202">Mar 31</text>
                </g>
            </svg>
        </div>

        <!-- Page Overview Donut Panel -->
        <div class="sb-panel">
            <div class="sb-panel-header">
                <div class="sb-panel-title">
                    <h4>Page Overview</h4>
                </div>
                <div class="dropdown">
                    <button type="button" class="date-picker-btn" data-bs-toggle="dropdown" style="padding: 5px 12px; font-size: 12px;">
                        <span>{{ isset($selectedWebsiteId) && $selectedWebsiteId ? ($allWebsites->firstWhere('id', $selectedWebsiteId)?->name ?? 'All Websites') : 'All Websites' }}</span>
                        <i class="bi bi-chevron-down" style="font-size: 10px;"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" style="font-size: 12.5px; border-radius: 10px;">
                        <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">All Websites</a></li>
                        @foreach($allWebsites ?? [] as $w)
                            <li><a class="dropdown-item" href="{{ route('admin.dashboard', ['website_id' => $w->id]) }}">{{ $w->name }}</a></li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="donut-wrap">
                <!-- SVG Donut Chart -->
                <svg width="175" height="175" viewBox="0 0 180 180">
                    <!-- Published (Green) -->
                    <circle cx="90" cy="90" r="68" fill="none" stroke="#10B981" stroke-width="22" stroke-dasharray="280 427" stroke-dashoffset="100"/>
                    <!-- Draft (Amber) -->
                    <circle cx="90" cy="90" r="68" fill="none" stroke="#F59E0B" stroke-width="22" stroke-dasharray="72 427" stroke-dashoffset="-182"/>
                    <!-- Private (Purple) -->
                    <circle cx="90" cy="90" r="68" fill="none" stroke="#A855F7" stroke-width="22" stroke-dasharray="44 427" stroke-dashoffset="-256"/>
                    <!-- Scheduled (Blue) -->
                    <circle cx="90" cy="90" r="68" fill="none" stroke="#3B82F6" stroke-width="22" stroke-dasharray="27 427" stroke-dashoffset="-302"/>

                    <text x="90" y="88" text-anchor="middle" font-family="Inter, sans-serif" font-size="28" font-weight="800" fill="#0F172A">{{ $totalPages ?? 48 }}</text>
                    <text x="90" y="108" text-anchor="middle" font-family="Inter, sans-serif" font-size="11.5" font-weight="500" fill="#64748B">Total Pages</text>
                </svg>

                <!-- Donut Legend -->
                <div class="donut-legend">
                    <div class="donut-legend-row">
                        <span><i class="legend-dot" style="background: #10B981;"></i>Published</span>
                        <strong>{{ $publishedPages ?? 32 }}</strong>
                    </div>
                    <div class="donut-legend-row">
                        <span><i class="legend-dot" style="background: #F59E0B;"></i>Draft</span>
                        <strong>{{ $draftPages ?? 8 }}</strong>
                    </div>
                    <div class="donut-legend-row">
                        <span><i class="legend-dot" style="background: #A855F7;"></i>Private</span>
                        <strong>{{ $privatePages ?? 5 }}</strong>
                    </div>
                    <div class="donut-legend-row">
                        <span><i class="legend-dot" style="background: #3B82F6;"></i>Scheduled</span>
                        <strong>{{ $scheduledPages ?? 3 }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Row: Recent Websites & Recent Activities -->
    <div class="bottom-grid">
        <!-- Recent Websites Table (Dynamic from core_websites) -->
        <div class="sb-panel">
            <div class="sb-panel-header">
                <div class="sb-panel-title">
                    <h4>Recent Websites</h4>
                    <p>Your latest websites and their status.</p>
                </div>
                <a href="{{ route('admin.websites') }}" class="view-all-link">
                    <span>View all</span>
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>

            <table class="recent-table">
                <thead>
                    <tr>
                        <th>Website</th>
                        <th>Module</th>
                        <th>Status</th>
                        <th>Last Updated</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentWebsites ?? [] as $site)
                        @php
                            $statusBadge = match($site->status) {
                                'Published' => 'sb-badge-green',
                                'Draft' => 'sb-badge-amber',
                                'Private' => 'sb-badge-purple',
                                default => 'sb-badge-blue',
                            };
                        @endphp
                        <tr>
                            <td>
                                <div class="site-item">
                                    <img src="{{ $site->thumbnail }}" alt="{{ $site->name }}" class="site-thumb">
                                    <div class="site-info">
                                        <h6>{{ $site->name }}</h6>
                                        <span>{{ $site->domain }}</span>
                                    </div>
                                </div>
                            </td>
                            <td><span class="sb-badge-pill sb-badge-{{ $site->module_badge ?? 'pink' }}">{{ $site->module }}</span></td>
                            <td><span class="sb-badge-pill {{ $statusBadge }}">{{ $site->status }}</span></td>
                            <td>{{ $site->updated_at ? $site->updated_at->format('M d, Y') : 'Mar 31, 2025' }}</td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn-dots" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" style="font-size: 12.5px; border-radius: 10px;">
                                        <li>
                                            <form action="{{ route('admin.websites.toggle', $site->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="dropdown-item fw-semibold {{ $site->is_active ? 'text-warning' : 'text-success' }}">
                                                    <i class="bi {{ $site->is_active ? 'bi-toggle-off' : 'bi-toggle-on' }} me-2"></i>{{ $site->is_active ? 'Turn Off Module' : 'Activate Module' }}
                                                </button>
                                            </form>
                                        </li>
                                        <li><a class="dropdown-item" href="{{ route('admin.websites', ['search' => $site->name]) }}"><i class="bi bi-eye me-2"></i>Manage Website</a></li>
                                        <li>
                                            <form action="{{ route('admin.websites.destroy', $site->id) }}" method="POST" onsubmit="return confirm('Delete this website?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger"><i class="bi bi-trash me-2"></i>Delete</button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Recent Activities Panel (Dynamic from core_activity_logs) -->
        <div class="sb-panel">
            <div class="sb-panel-header">
                <div class="sb-panel-title">
                    <h4>Recent Activities</h4>
                    <p>Latest actions on your websites.</p>
                </div>
                <a href="{{ route('admin.transactions') }}" class="view-all-link">
                    <span>View all</span>
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>

            <div class="activity-list">
                @foreach($recentActivities ?? [] as $act)
                    <div class="activity-item">
                        <div class="activity-left">
                            <div class="activity-icon" style="background: {{ $act->bg_color }}; color: {{ $act->text_color }};">
                                <i class="bi {{ $act->icon }}"></i>
                            </div>
                            <div class="activity-text">
                                <h6>{{ $act->title }}</h6>
                                <span>{{ $act->subtitle }}</span>
                            </div>
                        </div>
                        <span class="activity-time">{{ $act->time_label ?: $act->created_at->diffForHumans() }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection

@push('module_scripts')
<script>
    function setTrafficTab(btn) {
        document.querySelectorAll('#trafficTabs .time-tab').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
    }
</script>
@endpush
