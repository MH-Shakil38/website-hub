@php
    $currentModKey = \App\Support\ModuleCmsResolver::currentModule();
    $currentModMeta = \App\Support\ModuleCmsResolver::moduleMeta($currentModKey);
    $activeSitesForCms = isset($activeModuleWebsites)
        ? $activeModuleWebsites->whereNotNull('module_name')
        : \App\Models\Website::activeModules();
@endphp

<div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 12px; padding: 12px 18px; margin-bottom: 18px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
    <div style="display: flex; align-items: center; gap: 10px;">
        <span style="width: 34px; height: 34px; border-radius: 9px; background: #FFE4EC; color: #EC134A; display: inline-flex; align-items: center; justify-content: center; font-size: 16px;">
            <i class="bi bi-boxes"></i>
        </span>
        <div>
            <div style="font-size: 13.5px; font-weight: 700; color: #0F172A;">
                Isolated Module Workspace: <span style="color: #EC134A;">{{ $currentModMeta['label'] }}</span>
                <code style="background: #F1F5F9; color: #334155; padding: 2px 6px; border-radius: 5px; font-size: 11.5px; margin-left: 4px;">{{ $currentModMeta['prefix'] }}*</code>
            </div>
            <div style="font-size: 11.5px; color: #64748B;">
                All data &amp; uploaded media on this page are stored exclusively inside <code>Modules/{{ $currentModKey }}/</code>
            </div>
        </div>
    </div>

    @if($activeSitesForCms->count() > 1)
        <div style="display: flex; align-items: center; gap: 6px; flex-wrap: wrap;">
            <span style="font-size: 12px; font-weight: 600; color: #64748B; margin-right: 4px;">Switch Module:</span>
            @foreach($activeSitesForCms as $modSite)
                @php
                    $isCurrent = $modSite->module_name === $currentModKey;
                @endphp
                <a href="{{ request()->fullUrlWithQuery(['cms_module' => $modSite->module_name]) }}"
                   style="text-decoration: none; padding: 6px 13px; border-radius: 8px; font-size: 12.5px; font-weight: 600; border: 1px solid {{ $isCurrent ? '#EC134A' : '#E2E8F0' }}; background: {{ $isCurrent ? '#EC134A' : '#F8FAFC' }}; color: {{ $isCurrent ? '#FFFFFF' : '#334155' }}; transition: all 0.15s;">
                    {{ $modSite->module }}
                </a>
            @endforeach
        </div>
    @else
        <div>
            <span class="sb-badge-pill sb-badge-green">
                <i class="bi bi-check-circle-fill me-1"></i> Single Active Module Mode
            </span>
        </div>
    @endif
</div>
