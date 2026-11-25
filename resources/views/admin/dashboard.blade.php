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
            <span>{{ $dateRangeLabel }}</span>
        </button>
    </div>

    <!-- 4 KPI Stat Cards (real totals, growth this month and 6-month trend from the database) -->
    @php
        $kpiCards = [
            ['key' => 'websites', 'label' => 'Total Websites', 'icon' => 'bi-globe2', 'bg' => '#FFE4EC', 'color' => '#EC134A', 'one' => 'website', 'many' => 'websites', 'value' => $totalWebsites ?? 0],
            ['key' => 'pages', 'label' => 'Total Pages', 'icon' => 'bi-file-earmark-text', 'bg' => '#DBEAFE', 'color' => '#3B82F6', 'one' => 'page', 'many' => 'pages', 'value' => $totalPages ?? 0],
            ['key' => 'media', 'label' => 'Media Files', 'icon' => 'bi-image', 'bg' => '#DCFCE7', 'color' => '#10B981', 'one' => 'file', 'many' => 'files', 'value' => $mediaFilesCount ?? 0],
            ['key' => 'users', 'label' => 'Total Users', 'icon' => 'bi-person', 'bg' => '#F3E8FF', 'color' => '#A855F7', 'one' => 'user', 'many' => 'users', 'value' => $totalUsers ?? 0],
        ];
    @endphp
    <div class="kpi-grid">
        @foreach($kpiCards as $i => $card)
            @php
                $k = $kpis[$card['key']] ?? ['new_this_month' => 0, 'trend' => 0, 'trend_up' => false, 'spark' => []];
                $sp = \App\Support\DashboardStats::sparkPath($k['spark']);
                $newCount = $k['new_this_month'];
            @endphp
            <div class="kpi-card">
                <div class="kpi-top">
                    <div class="kpi-icon" style="background: {{ $card['bg'] }}; color: {{ $card['color'] }};">
                        <i class="bi {{ $card['icon'] }}"></i>
                    </div>
                    <div class="kpi-meta">
                        <span>{{ $card['label'] }}</span>
                        <h3>{{ $card['value'] }}</h3>
                    </div>
                </div>
                <div class="kpi-bottom">
                    <div>
                        @if($k['trend_up'])
                            <div class="kpi-trend-up" title="Growth this month"><i class="bi bi-arrow-up"></i> {{ rtrim(rtrim(number_format($k['trend'], 1), '0'), '.') }}%</div>
                        @else
                            <div class="kpi-trend-up" style="color: #94A3B8;" title="No change this month"><i class="bi bi-dash"></i> 0%</div>
                        @endif
                        <p class="kpi-sub">+{{ number_format($newCount) }} new {{ $newCount === 1 ? $card['one'] : $card['many'] }} this month</p>
                    </div>
                    <svg width="86" height="42" viewBox="0 0 86 42" fill="none" aria-label="{{ $card['label'] }} over the last 6 months">
                        <defs>
                            <linearGradient id="sp{{ $i + 1 }}" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="{{ $card['color'] }}" stop-opacity="0.25"/>
                                <stop offset="100%" stop-color="{{ $card['color'] }}" stop-opacity="0.0"/>
                            </linearGradient>
                        </defs>
                        <path d="{{ $sp['area'] }}" fill="url(#sp{{ $i + 1 }})"/>
                        <path d="{{ $sp['line'] }}" stroke="{{ $card['color'] }}" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
            </div>
        @endforeach
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
                    <button type="button" class="time-tab" data-range="day" onclick="setTrafficTab(this)">Day</button>
                    <button type="button" class="time-tab active" data-range="week" onclick="setTrafficTab(this)">Week</button>
                    <button type="button" class="time-tab" data-range="month" onclick="setTrafficTab(this)">Month</button>
                    <button type="button" class="time-tab" data-range="year" onclick="setTrafficTab(this)">Year</button>
                </div>
            </div>

            <div class="chart-legend">
                <span><i class="legend-dot" style="background: #EC134A;"></i>Visitors</span>
                <span><i class="legend-dot" style="background: #3B82F6;"></i>Page Views</span>
            </div>

            <!-- Visitors & page views from core_site_visits (drawn by the script below) -->
            <svg id="trafficChart" viewBox="0 0 680 215" width="100%" height="215" fill="none" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Website traffic chart"></svg>
            <p id="trafficEmpty" class="text-muted small text-center mb-0" style="display: none; margin-top: -120px; position: relative;">No visits recorded in this period yet.</p>
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
                    <circle cx="90" cy="90" r="68" fill="none" stroke="#10B981" stroke-width="22" stroke-dasharray="{{ $pageDonut['published']['dash'] ?? '0 427' }}" stroke-dashoffset="{{ $pageDonut['published']['offset'] ?? 0 }}"/>
                    <!-- Draft (Amber) -->
                    <circle cx="90" cy="90" r="68" fill="none" stroke="#F59E0B" stroke-width="22" stroke-dasharray="{{ $pageDonut['draft']['dash'] ?? '0 427' }}" stroke-dashoffset="{{ $pageDonut['draft']['offset'] ?? 0 }}"/>
                    <!-- Private (Purple) -->
                    <circle cx="90" cy="90" r="68" fill="none" stroke="#A855F7" stroke-width="22" stroke-dasharray="{{ $pageDonut['private']['dash'] ?? '0 427' }}" stroke-dashoffset="{{ $pageDonut['private']['offset'] ?? 0 }}"/>
                    <!-- Scheduled (Blue) -->
                    <circle cx="90" cy="90" r="68" fill="none" stroke="#3B82F6" stroke-width="22" stroke-dasharray="{{ $pageDonut['scheduled']['dash'] ?? '0 427' }}" stroke-dashoffset="{{ $pageDonut['scheduled']['offset'] ?? 0 }}"/>

                    <text x="90" y="88" text-anchor="middle" font-family="Inter, sans-serif" font-size="28" font-weight="800" fill="#0F172A">{{ $totalPages ?? 0 }}</text>
                    <text x="90" y="108" text-anchor="middle" font-family="Inter, sans-serif" font-size="11.5" font-weight="500" fill="#64748B">Total Pages</text>
                </svg>

                <!-- Donut Legend -->
                <div class="donut-legend">
                    <div class="donut-legend-row">
                        <span><i class="legend-dot" style="background: #10B981;"></i>Published</span>
                        <strong>{{ $publishedPages ?? 0 }}</strong>
                    </div>
                    <div class="donut-legend-row">
                        <span><i class="legend-dot" style="background: #F59E0B;"></i>Draft</span>
                        <strong>{{ $draftPages ?? 0 }}</strong>
                    </div>
                    <div class="donut-legend-row">
                        <span><i class="legend-dot" style="background: #A855F7;"></i>Private</span>
                        <strong>{{ $privatePages ?? 0 }}</strong>
                    </div>
                    <div class="donut-legend-row">
                        <span><i class="legend-dot" style="background: #3B82F6;"></i>Scheduled</span>
                        <strong>{{ $scheduledPages ?? 0 }}</strong>
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
                            <td>{{ $site->updated_at ? $site->updated_at->format('M d, Y') : '—' }}</td>
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
    const TRAFFIC = @json($traffic ?? []);

    function niceMax(v) {
        if (v <= 4) return 4;
        const pow = Math.pow(10, Math.floor(Math.log10(v)));
        const steps = [1, 2, 2.5, 5, 10];
        for (const s of steps) { if (s * pow >= v) return s * pow; }
        return 10 * pow;
    }

    function fmtTick(v) {
        if (v >= 1000000) return (v / 1000000).toFixed(v % 1000000 ? 1 : 0) + 'M';
        if (v >= 1000) return (v / 1000).toFixed(v % 1000 ? 1 : 0) + 'K';
        return String(Math.round(v));
    }

    function drawTraffic(range) {
        const svg = document.getElementById('trafficChart');
        const data = TRAFFIC[range];
        if (!svg || !data) return;

        const L = 40, R = 665, T = 20, B = 180;
        const n = data.labels.length;
        const max = niceMax(Math.max(1, ...data.visitors, ...data.views));
        const x = i => n > 1 ? L + (R - L) * i / (n - 1) : (L + R) / 2;
        const y = v => B - (B - T) * v / max;
        const ns = 'http://www.w3.org/2000/svg';
        const el = (tag, attrs, text) => {
            const e = document.createElementNS(ns, tag);
            Object.entries(attrs).forEach(([k, v]) => e.setAttribute(k, v));
            if (text !== undefined) e.textContent = text;
            return e;
        };
        const smooth = vals => vals.map((v, i) => {
            if (i === 0) return `M${x(0)} ${y(v)}`;
            const cx = (x(i - 1) + x(i)) / 2;
            return `C${cx} ${y(vals[i - 1])}, ${cx} ${y(v)}, ${x(i)} ${y(v)}`;
        }).join(' ');

        svg.innerHTML = '';
        const defs = el('defs', {});
        [['blueGrad', '#3B82F6'], ['redGrad', '#EC134A']].forEach(([id, c]) => {
            const g = el('linearGradient', { id, x1: 0, y1: 0, x2: 0, y2: 1 });
            g.appendChild(el('stop', { offset: '0%', 'stop-color': c, 'stop-opacity': 0.18 }));
            g.appendChild(el('stop', { offset: '100%', 'stop-color': c, 'stop-opacity': 0 }));
            defs.appendChild(g);
        });
        svg.appendChild(defs);

        for (let k = 0; k <= 4; k++) {
            const v = max * k / 4, yy = y(v);
            svg.appendChild(el('line', { x1: L, y1: yy, x2: R, y2: yy, stroke: '#F1F5F9', 'stroke-width': 1 }));
            svg.appendChild(el('text', { x: L - 6, y: yy + 4, 'text-anchor': 'end', fill: '#94A3B8', 'font-size': 11, 'font-family': 'Inter, sans-serif' }, fmtTick(v)));
        }

        [['views', '#3B82F6', 'blueGrad'], ['visitors', '#EC134A', 'redGrad']].forEach(([key, color, grad]) => {
            const line = smooth(data[key]);
            svg.appendChild(el('path', { d: `${line} L${x(n - 1)} ${B} L${x(0)} ${B} Z`, fill: `url(#${grad})` }));
            svg.appendChild(el('path', { d: line, stroke: color, 'stroke-width': 2.5, fill: 'none', 'stroke-linecap': 'round' }));
        });

        const every = Math.max(1, Math.ceil(n / 11));
        data.labels.forEach((lab, i) => {
            if (i % every !== 0 && i !== n - 1) return;
            svg.appendChild(el('text', { x: x(i), y: 202, 'text-anchor': 'middle', fill: '#94A3B8', 'font-size': 11, 'font-family': 'Inter, sans-serif' }, lab));
            const tip = el('circle', { cx: x(i), cy: y(data.views[i]), r: 3, fill: '#3B82F6' });
            tip.appendChild(el('title', {}, `${lab}: ${data.visitors[i]} visitors, ${data.views[i]} page views`));
            svg.appendChild(tip);
        });

        const empty = document.getElementById('trafficEmpty');
        if (empty) empty.style.display = data.views.some(v => v > 0) ? 'none' : 'block';
    }

    function setTrafficTab(btn) {
        document.querySelectorAll('#trafficTabs .time-tab').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        drawTraffic(btn.dataset.range || 'week');
    }

    drawTraffic('week');
</script>
@endpush
