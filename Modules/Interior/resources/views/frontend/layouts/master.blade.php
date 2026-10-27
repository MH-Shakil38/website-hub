@php
    $activeCount = isset($activeModuleWebsites) ? $activeModuleWebsites->count() : 1;
    $basePrefix = $activeCount === 1 ? '' : '/interior-design';
    $homeUrl = $basePrefix === '' ? url('/') : url($basePrefix);

    $modSettings = \Modules\Interior\App\Models\ModuleSetting::pluck('value', 'key')->toArray();
    $modTheme = \Modules\Interior\App\Models\ModuleTheme::where('is_active', true)->first();
    $headerMenus = \Modules\Interior\App\Models\ModuleMenu::where('location', 'header')->where('is_active', true)->orderBy('sort_order')->get();
    $primaryColor = $modTheme?->primary_color ?? '#EC134A';
    $secondaryColor = $modTheme?->secondary_color ?? '#0F172A';
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $modSettings['site_title'] ?? 'Interior Design Studio' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body style="font-family: '{{ $modTheme?->font_family ?? 'Inter' }}', sans-serif;">
    <nav class="navbar navbar-expand-lg navbar-dark" style="background: {{ $secondaryColor }};">
        <div class="container">
            <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="{{ $homeUrl }}">
                @if(!empty($modSettings['module_logo']))
                    <img src="{{ $modSettings['module_logo'] }}" alt="Logo" style="height: 30px; border-radius: 5px;">
                @endif
                <span>{{ $modSettings['site_title'] ?? 'Interior Studio' }}</span>
            </a>
            <div class="navbar-nav ms-auto align-items-center gap-2">
                @forelse($headerMenus as $m)
                    @php
                        $linkUrl = $m->url === '/' ? $homeUrl : url($basePrefix . '/' . ltrim($m->url, '/'));
                    @endphp
                    <a class="nav-link" href="{{ $linkUrl }}" target="{{ $m->target }}">{{ $m->title }}</a>
                @empty
                    <a class="nav-link" href="{{ $homeUrl }}">Home</a>
                    <a class="nav-link" href="{{ url($basePrefix . '/portfolio') }}">Portfolio</a>
                    <a class="nav-link" href="{{ url($basePrefix . '/contact') }}">Contact</a>
                @endforelse
                @if($activeCount > 1)
                    <a class="btn btn-sm btn-outline-light ms-2" href="{{ url('/') }}"><i class="bi bi-grid-3x3-gap me-1"></i>All Themes</a>
                @endif
                <a class="nav-link text-warning" href="{{ route('admin.dashboard') }}">Admin</a>
            </div>
        </div>
    </nav>

    <main class="container py-5">
        @yield('frontend_content')
    </main>

    <footer class="bg-light border-top py-3 text-center text-muted small">
        {{ $modSettings['footer_copyright'] ?? ('© ' . date('Y') . ' Interior Design Module (Standalone Frontend)') }}
    </footer>
</body>
</html>
