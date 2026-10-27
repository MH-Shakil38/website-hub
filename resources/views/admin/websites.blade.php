@extends('admin.layouts.master')

@section('title', 'Websites - ' . ($globalSiteName ?? 'SiteBuilder'))

@section('admin_content')
    @php
        $activeModCount = isset($activeModuleWebsites) ? $activeModuleWebsites->count() : \App\Models\Website::activeModules()->count();
        $singleActiveSite = $activeModCount === 1 ? ($activeModuleWebsites->first() ?? \App\Models\Website::activeModules()->first()) : null;
    @endphp

    <!-- Top 3-Column Summary Strip (Screenshot 8 Pattern) -->
    <div class="sb-summary-strip">
        <div class="sb-summary-item">
            <div class="sb-summary-icon">
                <i class="bi bi-globe2"></i>
            </div>
            <div>
                <div class="sb-summary-label">Total Websites</div>
                <div class="sb-summary-value">{{ $websites->total() }} Websites</div>
            </div>
        </div>

        <div class="sb-summary-item">
            <div class="sb-summary-icon">
                <i class="bi bi-check2-circle"></i>
            </div>
            <div>
                <div class="sb-summary-label">Frontend Routing Mode</div>
                <div class="sb-summary-value">
                    @if($activeModCount === 1 && $singleActiveSite)
                        Single: {{ $singleActiveSite->module }} (/)
                    @elseif($activeModCount > 1)
                        Multi-Theme Hub ({{ $activeModCount }} Active)
                    @else
                        No Active Module
                    @endif
                </div>
            </div>
        </div>

        <div class="sb-summary-item">
            <div class="sb-summary-icon">
                <i class="bi bi-box-arrow-up-right"></i>
            </div>
            <div>
                <div class="sb-summary-label">Live Root URL</div>
                <div class="sb-summary-value">
                    <a href="{{ url('/') }}" target="_blank" style="color: #EC134A; text-decoration: none; font-size: 16px;">
                        {{ url('/') }} <i class="bi bi-arrow-up-right" style="font-size: 13px;"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Bar Header -->
    <div style="display: flex; align-items: flex-end; justify-content: space-between; gap: 16px; margin-bottom: 20px; flex-wrap: wrap;">
        <div>
            <h3 style="font-size: 24px; font-weight: 800; color: #0B2545; margin: 0 0 4px 0;">Managed Websites</h3>
            <p style="font-size: 13px; color: #64748B; margin: 0;">
                Toggle modules <strong>Active / Off</strong>. If <strong>1 module</strong> is Active, it runs directly on <code>/</code>. If <strong>multiple modules</strong> are Active, <code>/</code> shows the All Themes Showcase and modules run on <code>/interior-design</code>, <code>/real-estate</code>, etc.
            </p>
        </div>

        <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
            <form action="{{ route('admin.websites') }}" method="GET" style="position: relative; width: 265px;">
                <i class="bi bi-search" style="position: absolute; left: 13px; top: 50%; transform: translateY(-50%); color: #64748B; font-size: 14px;"></i>
                <input type="text" name="search" value="{{ $search }}" placeholder="Search by Website, Domain or Module..." style="width: 100%; height: 42px; background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 9px; padding: 0 14px 0 36px; font-size: 13px; color: #0F172A; outline: none;">
            </form>

            <button type="button" data-bs-toggle="modal" data-bs-target="#addWebsiteModal" style="height: 42px; background: #D8125B; color: #FFFFFF; border: none; border-radius: 9px; padding: 0 18px; font-size: 13px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; cursor: pointer; box-shadow: 0 4px 10px rgba(216, 18, 91, 0.2);">
                <i class="bi bi-plus-lg"></i>
                <span>Add Website</span>
            </button>
        </div>
    </div>

    <!-- Websites Table Card -->
    <div class="sb-table-card">
        <div style="overflow-x: auto;">
            <table class="sb-data-table">
                <thead>
                    <tr>
                        <th>Website <i class="bi bi-chevron-expand"></i></th>
                        <th>Domain / Live URL <i class="bi bi-chevron-expand"></i></th>
                        <th>Module <i class="bi bi-chevron-expand"></i></th>
                        <th>Module State <i class="bi bi-chevron-expand"></i></th>
                        <th>Last Updated <i class="bi bi-chevron-expand"></i></th>
                        <th>Actions <i class="bi bi-chevron-expand"></i></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($websites as $site)
                        @php
                            $livePath = ($site->is_active && $site->module_name)
                                ? ($activeModCount === 1 ? '/' : '/' . $site->slug)
                                : null;
                        @endphp
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <img src="{{ $site->thumbnail }}" alt="{{ $site->name }}" style="width: 44px; height: 32px; border-radius: 6px; object-fit: cover;">
                                    <div>
                                        <span class="fw-bold text-dark d-block">{{ $site->name }}</span>
                                        <span class="text-muted" style="font-size: 11.5px;">{{ $site->domain }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($livePath)
                                    <a href="{{ url($livePath) }}" target="_blank" class="text-decoration-none fw-semibold" style="color: #EC134A; font-size: 13px;">
                                        <code>{{ $livePath }}</code> <i class="bi bi-box-arrow-up-right ms-1" style="font-size: 11px;"></i>
                                    </a>
                                @else
                                    <span class="text-muted small">Offline (Turned Off)</span>
                                @endif
                            </td>
                            <td><span class="sb-badge-pill sb-badge-{{ $site->module_badge ?? 'pink' }}">{{ $site->module }}</span></td>
                            <td>
                                <form action="{{ route('admin.websites.toggle', $site->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="border-0 bg-transparent p-0" style="cursor: pointer;" title="Click to toggle Active / Off">
                                        @if($site->is_active)
                                            <span class="sb-badge-pill sb-badge-green d-inline-flex align-items-center gap-1">
                                                <i class="bi bi-toggle-on" style="font-size: 14px;"></i> Active
                                            </span>
                                        @else
                                            <span class="sb-badge-pill sb-badge-amber d-inline-flex align-items-center gap-1">
                                                <i class="bi bi-toggle-off" style="font-size: 14px;"></i> Turned Off
                                            </span>
                                        @endif
                                    </button>
                                </form>
                            </td>
                            <td>{{ $site->updated_at ? $site->updated_at->format('M d, Y') : 'Mar 31, 2025' }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <form action="{{ route('admin.websites.toggle', $site->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="sb-page-btn {{ $site->is_active ? 'text-success' : 'text-secondary' }}" title="{{ $site->is_active ? 'Turn Off Module' : 'Activate Module' }}" style="width: auto; padding: 0 10px; font-size: 12px; gap: 5px;">
                                            <i class="bi {{ $site->is_active ? 'bi-power' : 'bi-play-fill' }}"></i>
                                            <span>{{ $site->is_active ? 'Turn Off' : 'Activate' }}</span>
                                        </button>
                                    </form>

                                    @if($livePath)
                                        <a href="{{ url($livePath) }}" target="_blank" class="sb-page-btn text-primary" title="Visit Live Frontend">
                                            <i class="bi bi-box-arrow-up-right"></i>
                                        </a>
                                    @endif

                                    @if($site->module_name)
                                        <form action="{{ route('admin.websites.reset', $site->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Reset {{ $site->name }} ({{ $site->module }}) back to 100% original default design & images?')">
                                            @csrf
                                            <button type="submit" class="sb-page-btn text-warning" title="Reset to Default Template" style="width: auto; padding: 0 10px; font-size: 12px; gap: 4px;">
                                                <i class="bi bi-arrow-counterclockwise"></i>
                                                <span>Reset Default</span>
                                            </button>
                                        </form>
                                    @endif

                                    <form action="{{ route('admin.websites.destroy', $site->id) }}" method="POST" onsubmit="return confirm('Delete {{ $site->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="sb-page-btn text-danger" title="Delete"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="sb-table-footer">
            <div>Showing {{ $websites->firstItem() ?? 0 }} to {{ $websites->lastItem() ?? 0 }} of {{ $websites->total() }} websites</div>
            <div class="sb-pagination">
                <a href="{{ $websites->previousPageUrl() ?? '#' }}" class="sb-page-btn"><i class="bi bi-chevron-left"></i></a>
                <a href="#" class="sb-page-btn active">1</a>
                <a href="{{ $websites->nextPageUrl() ?? '#' }}" class="sb-page-btn"><i class="bi bi-chevron-right"></i></a>
            </div>
        </div>
    </div>

    <!-- Add Website Modal -->
    <div class="modal fade" id="addWebsiteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('admin.websites.store') }}" method="POST" class="modal-content border-0 shadow" style="border-radius: 16px;">
                @csrf
                <div class="modal-header border-bottom px-4 py-3">
                    <h5 class="modal-title fw-bold">Add New Website</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Website Name</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Urban Estates" style="height: 44px; border-radius: 10px;" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Domain</label>
                        <input type="text" name="domain" class="form-control" placeholder="e.g. urbanestates.com" style="height: 44px; border-radius: 10px;" required>
                    </div>
                    <div class="row g-3 mb-0">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Module</label>
                            <select name="module" class="form-select" style="height: 44px; border-radius: 10px;">
                                <option value="Real Estate">Real Estate</option>
                                <option value="Interior Design">Interior Design</option>
                                <option value="Pharmacy">Pharmacy</option>
                                <option value="Booking">Booking</option>
                                <option value="Software">Software</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Initial State</label>
                            <select name="status" class="form-select" style="height: 44px; border-radius: 10px;">
                                <option value="Published">Active (Published)</option>
                                <option value="Draft">Turned Off (Draft)</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top px-4 py-3">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius: 9px;">Cancel</button>
                    <button type="submit" class="btn text-white fw-semibold" style="background: #EC134A; border-radius: 9px;">Create Website</button>
                </div>
            </form>
        </div>
    </div>
@endsection
