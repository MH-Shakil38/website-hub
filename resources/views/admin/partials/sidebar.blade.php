@php
    $activeWebsitesList = isset($activeModuleWebsites)
        ? $activeModuleWebsites
        : \App\Models\Website::where('is_active', true)->orderBy('id')->get();
    $currentCmsMod = \App\Support\ModuleCmsResolver::currentModule();
    $currentTabParam = request()->query('tab', 'hero');
    $isMultiModule = $activeWebsitesList->count() > 1;
@endphp
<aside class="sb-sidebar">
    <div>
        <!-- Brand Logo -->
        <a href="{{ route('admin.dashboard') }}" class="sb-brand">
            @if(!empty($globalSiteLogo))
                <img src="{{ $globalSiteLogo }}" alt="{{ $globalSiteName ?? 'SiteBuilder' }}" style="width: 34px; height: 34px; object-fit: contain; border-radius: 7px;">
            @else
                <svg width="34" height="34" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M24 4L6 14V20L24 30L42 20V14L24 4Z" fill="#EC134A"/>
                    <path d="M24 10L11 17L24 24L37 17L24 10Z" fill="#FF85A1" opacity="0.35"/>
                    <path d="M6 23L24 33L42 23V28L24 38L6 28V23Z" fill="#D90F43"/>
                    <path d="M6 31L24 41L42 31V36L24 46L6 36V31Z" fill="#B80B38"/>
                </svg>
            @endif
            <span>{{ $globalSiteName ?? 'SiteBuilder' }}</span>
        </a>

        <!-- Root Global Menu Items (ONLY Dashboard & Websites) -->
        <ul class="sb-nav">
            <li>
                <a href="{{ route('admin.dashboard') }}" class="sb-nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <span class="link-left">
                        <i class="bi bi-grid-fill nav-icon"></i>
                        <span>Dashboard</span>
                    </span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.websites') }}" class="sb-nav-link {{ request()->routeIs('admin.websites') ? 'active' : '' }}">
                    <span class="link-left">
                        <i class="bi bi-globe2 nav-icon"></i>
                        <span>Websites</span>
                    </span>
                </a>
            </li>
        </ul>

        @if($isMultiModule)
            <div class="sb-section-label" style="padding-bottom: 4px;">ACTIVE MODULES</div>
        @endif

        <!-- MODULES LIST: If 1 module active, stays open directly; if 2+ modules active, collapsible under module name -->
        @forelse($activeWebsitesList as $activeSite)
            @php
                $modName = $activeSite->module_name;
                $modLower = $modName ? strtolower($modName) : 'mod_' . $activeSite->id;
                $modMenus = $modName ? config(strtolower($modName) . '.admin_menus', []) : [];

                // Module icon
                $modIcon = match($modLower) {
                    'interior' => 'bi bi-house-door-fill',
                    'pharmacy' => 'bi bi-capsule-pill',
                    'realestate' => 'bi bi-buildings-fill',
                    default => 'bi bi-box-fill',
                };

                // Check if the current page belongs to this module
                $isCurrentModuleRoute = (
                    ($modName && request()->routeIs("admin." . strtolower($modName) . ".*")) ||
                    ($currentCmsMod === $modName && request()->routeIs('admin.pages', 'admin.menu-builder', 'admin.slider', 'admin.media-library', 'admin.themes', 'admin.users', 'admin.settings', 'admin.video-gallery'))
                );

                // If only 1 module active, always open; if multi-module, open the active module (or the first one if neither matches)
                $isExpanded = !$isMultiModule || $isCurrentModuleRoute || ($loop->first && !$activeWebsitesList->contains(function ($w) use ($currentCmsMod) {
                    return ($w->module_name && request()->routeIs("admin." . strtolower($w->module_name) . ".*")) ||
                           ($currentCmsMod === $w->module_name && request()->routeIs('admin.pages', 'admin.menu-builder', 'admin.slider', 'admin.media-library', 'admin.themes', 'admin.users', 'admin.settings', 'admin.video-gallery'));
                }));
            @endphp

            @if($isMultiModule)
                <!-- 2+ Active Modules: Collapsible Accordion per Module -->
                <div class="sb-module-group mb-2">
                    <button type="button"
                            class="sb-module-toggle-btn {{ $isExpanded ? '' : 'collapsed' }} {{ $isCurrentModuleRoute ? 'active-module' : '' }}"
                            data-bs-toggle="collapse"
                            data-bs-target="#collapse-mod-{{ $modLower }}"
                            aria-expanded="{{ $isExpanded ? 'true' : 'false' }}"
                            aria-controls="collapse-mod-{{ $modLower }}">
                        <span class="d-flex align-items-center gap-2">
                            <i class="{{ $modIcon }} {{ $isCurrentModuleRoute ? 'text-danger' : 'text-secondary' }}" style="font-size: 13px;"></i>
                            <span class="sb-module-title">{{ strtoupper($activeSite->module) }}</span>
                        </span>
                        <i class="bi bi-chevron-down sb-chevron"></i>
                    </button>

                    <div class="collapse {{ $isExpanded ? 'show' : '' }}" id="collapse-mod-{{ $modLower }}">
                        <ul class="sb-nav mt-1 ps-2" style="border-left: 2px solid #E2E8F0; margin-left: 14px;">
                            @if(!empty($modMenus))
                                @foreach($modMenus as $m)
                                    @if(Route::has($m['route']))
                                        @php
                                            $menuUrl = route($m['route'], $m['params'] ?? []);
                                            $routeMatches = request()->routeIs($m['active'] ?? '') && ($currentCmsMod === $modName || str_starts_with($m['active'] ?? '', "admin.{$modLower}."));
                                            if (!empty($m['tab_match'])) {
                                                $isMenuActive = $routeMatches && ($currentTabParam === $m['tab_match']);
                                            } else {
                                                $isMenuActive = $routeMatches;
                                            }
                                        @endphp
                                        <li>
                                            <a href="{{ $menuUrl }}" class="sb-nav-link {{ $isMenuActive ? 'active' : '' }}" style="padding: 8px 12px; font-size: 13px;">
                                                <span class="link-left">
                                                    <i class="{{ $m['icon'] ?? 'bi bi-circle' }} nav-icon" style="font-size: 14px;"></i>
                                                    <span>{{ $m['title'] }}</span>
                                                </span>
                                            </a>
                                        </li>
                                    @endif
                                @endforeach
                            @else
                                @php
                                    $targetRoute = ($activeSite->admin_route && Route::has($activeSite->admin_route))
                                        ? route($activeSite->admin_route)
                                        : route('admin.websites', ['search' => $activeSite->module]);
                                @endphp
                                <li>
                                    <a href="{{ $targetRoute }}" class="sb-nav-link" style="padding: 8px 12px; font-size: 13px;">
                                        <span class="link-left">
                                            <i class="bi bi-arrow-right-circle nav-icon"></i>
                                            <span>Manage {{ $activeSite->module }}</span>
                                        </span>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            @else
                <!-- Single Active Module: Always Open Directly -->
                <div class="sb-section-label">{{ strtoupper($activeSite->module) }}</div>
                <ul class="sb-nav">
                    @if(!empty($modMenus))
                        @foreach($modMenus as $m)
                            @if(Route::has($m['route']))
                                @php
                                    $menuUrl = route($m['route'], $m['params'] ?? []);
                                    $routeMatches = request()->routeIs($m['active'] ?? '') && ($currentCmsMod === $modName || str_starts_with($m['active'] ?? '', "admin.{$modLower}."));
                                    if (!empty($m['tab_match'])) {
                                        $isMenuActive = $routeMatches && ($currentTabParam === $m['tab_match']);
                                    } else {
                                        $isMenuActive = $routeMatches;
                                    }
                                @endphp
                                <li>
                                    <a href="{{ $menuUrl }}" class="sb-nav-link {{ $isMenuActive ? 'active' : '' }}">
                                        <span class="link-left">
                                            <i class="{{ $m['icon'] ?? 'bi bi-circle' }} nav-icon"></i>
                                            <span>{{ $m['title'] }}</span>
                                        </span>
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    @else
                        @php
                            $targetRoute = ($activeSite->admin_route && Route::has($activeSite->admin_route))
                                ? route($activeSite->admin_route)
                                : route('admin.websites', ['search' => $activeSite->module]);
                        @endphp
                        <li>
                            <a href="{{ $targetRoute }}" class="sb-nav-link">
                                <span class="link-left">
                                    <i class="bi bi-box nav-icon"></i>
                                    <span>{{ $activeSite->module }}</span>
                                </span>
                            </a>
                        </li>
                    @endif
                </ul>
            @endif
        @empty
            <div class="sb-section-label">MODULES</div>
            <ul class="sb-nav">
                <li class="px-3 py-2 small text-muted">
                    No active modules. <a href="{{ route('admin.websites') }}" class="text-danger fw-semibold text-decoration-none">Activate one</a>
                </li>
            </ul>
        @endforelse
    </div>

    <!-- Bottom Upgrade to Pro Card -->
    <div class="sb-pro-card">
        <div class="sb-pro-header">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="#EC134A" xmlns="http://www.w3.org/2000/svg">
                <path d="M2 19H22V21H2V19ZM2 7L7 12L12 4L17 12L22 7L20 17H4L2 7Z"/>
            </svg>
            <span>Upgrade to Pro</span>
        </div>
        <p>Unlock more features and industry modules.</p>
        <a href="{{ route('admin.settings') }}" class="btn-sb-pro">
            <span>View Plans</span>
            <i class="bi bi-arrow-right"></i>
        </a>
    </div>
</aside>

<style>
    .sb-module-toggle-btn {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 9px 12px;
        background: #F8FAFC;
        border: 1px solid #EEF2F6;
        border-radius: 9px;
        font-size: 11.5px;
        font-weight: 700;
        letter-spacing: 0.04em;
        color: #0F172A;
        text-transform: uppercase;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .sb-module-toggle-btn:hover {
        background: #F1F5F9;
        border-color: #CBD5E1;
    }
    .sb-module-toggle-btn.active-module {
        background: #FFF1F5;
        border-color: #FFE4EC;
        color: #EC134A;
    }
    .sb-module-toggle-btn .sb-chevron {
        font-size: 11px;
        color: #64748B;
        transition: transform 0.22s ease;
    }
    .sb-module-toggle-btn.active-module .sb-chevron {
        color: #EC134A;
    }
    .sb-module-toggle-btn:not(.collapsed) .sb-chevron {
        transform: rotate(180deg);
    }
</style>
