@php
    /** @var \App\Models\User|null $authUser */
    $authUser = auth()->user() ?? \App\Models\User::first();
    $currentModKey = \App\Support\ModuleCmsResolver::currentModule();
    $currentModMeta = \App\Support\ModuleCmsResolver::moduleMeta($currentModKey);
    $activeTenant = function_exists('current_tenant') ? current_tenant() : (app()->bound('tenant') ? app('tenant') : null);
    $accessibleTenants = $authUser
        ? $authUser->accessibleTenantsQuery()->orderByDesc('is_demo')->orderBy('id')->get()
        : collect();
    $activeSitesForCms = isset($activeModuleWebsites)
        ? $activeModuleWebsites->whereNotNull('module_name')
        : \App\Models\Website::activeModules();
@endphp

<div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 12px; padding: 12px 18px; margin-bottom: 18px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
    <div style="display: flex; align-items: center; gap: 10px;">
        <span style="width: 36px; height: 36px; border-radius: 9px; background: #FFE4EC; color: #EC134A; display: inline-flex; align-items: center; justify-content: center; font-size: 16px;">
            <i class="bi bi-hdd-network-fill"></i>
        </span>
        <div>
            <div style="font-size: 13.5px; font-weight: 700; color: #0F172A;">
                @if($activeTenant)
                    Active Website: <span style="color: #EC134A;">{{ $activeTenant->name }}</span>
                    <span class="badge bg-light text-dark border ms-1" style="font-size: 11px;">{{ $currentModMeta['label'] }}</span>
                    <code style="background: #DCFCE7; color: #166534; padding: 2px 6px; border-radius: 5px; font-size: 11px; margin-left: 4px;" title="Isolated MySQL Database">
                        <i class="bi bi-database-check me-1"></i>{{ $activeTenant->db_name }}
                    </code>
                @else
                    Isolated Module Workspace: <span style="color: #EC134A;">{{ $currentModMeta['label'] }}</span>
                    <code style="background: #F1F5F9; color: #334155; padding: 2px 6px; border-radius: 5px; font-size: 11.5px; margin-left: 4px;">{{ $currentModMeta['prefix'] }}*</code>
                @endif
            </div>
            <div style="font-size: 11.5px; color: #64748B;">
                @if($activeTenant)
                    Subdomain: <code>{{ $activeTenant->subdomain }}.{{ config('app.central_domains')[0] ?? 'localhost' }}</code> • Tables: <code>{{ $currentModMeta['prefix'] }}*</code> • Uploads: <code>Modules/{{ $currentModKey }}/Public/uploads/t{{ $activeTenant->id }}/</code>
                @else
                    All data &amp; uploaded media on this page are stored exclusively inside <code>Modules/{{ $currentModKey }}/</code>
                @endif
            </div>
        </div>
    </div>

    <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
        @if($accessibleTenants->count() > 1)
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-dark dropdown-toggle fw-semibold" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="border-radius: 8px; font-size: 12.5px;">
                    <i class="bi bi-arrow-left-right me-1"></i> Switch Website ({{ $accessibleTenants->count() }})
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 p-2" style="border-radius: 12px; min-width: 280px; max-height: 340px; overflow-y: auto;">
                    <li class="px-2 py-1 small fw-bold text-muted border-bottom mb-1">Select Website Workspace</li>
                    @foreach($accessibleTenants as $tItem)
                        @php
                            $isCurrentTenant = $activeTenant && (int) $activeTenant->id === (int) $tItem->id;
                        @endphp
                        <li>
                            <a class="dropdown-item rounded py-2 d-flex align-items-center justify-content-between {{ $isCurrentTenant ? 'bg-danger-subtle text-danger fw-bold' : '' }}"
                               href="{{ route('admin.tenants.switch', ['tenant' => $tItem->id, 'redirect_to' => request()->fullUrl()]) }}">
                                <div class="text-truncate me-2">
                                    <div style="font-size: 12.5px;">{{ $tItem->name }}</div>
                                    <div class="text-muted" style="font-size: 10.5px;">{{ $tItem->theme_module }} • {{ $tItem->db_name }}</div>
                                </div>
                                @if($tItem->is_demo)
                                    <span class="badge bg-secondary-subtle text-secondary" style="font-size: 10px;">DEMO</span>
                                @else
                                    <span class="badge bg-success-subtle text-success" style="font-size: 10px;">CLIENT</span>
                                @endif
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if((!$authUser || $authUser->isSuperAdmin()) && $activeSitesForCms->count() > 1)
            <div style="display: flex; align-items: center; gap: 5px; flex-wrap: wrap;">
                @foreach($activeSitesForCms as $modSite)
                    @php
                        $isCurrent = $modSite->module_name === $currentModKey;
                    @endphp
                    <a href="{{ request()->fullUrlWithQuery(['cms_module' => $modSite->module_name]) }}"
                       style="text-decoration: none; padding: 5px 11px; border-radius: 8px; font-size: 12px; font-weight: 600; border: 1px solid {{ $isCurrent ? '#EC134A' : '#E2E8F0' }}; background: {{ $isCurrent ? '#EC134A' : '#F8FAFC' }}; color: {{ $isCurrent ? '#FFFFFF' : '#334155' }}; transition: all 0.15s;">
                        {{ $modSite->module }}
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</div>
