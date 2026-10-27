@extends('admin.layouts.master')

@section('title', 'Themes (' . $modMeta['label'] . ') - ' . ($globalSiteName ?? 'SiteBuilder'))

@section('admin_content')
    @include('admin.partials.module-switcher')

    <!-- Top 3-Column Summary Strip (Screenshot 8 Pattern) -->
    <div class="sb-summary-strip">
        <div class="sb-summary-item">
            <div class="sb-summary-icon">
                <i class="bi bi-palette"></i>
            </div>
            <div>
                <div class="sb-summary-label">Total Themes ({{ $modMeta['prefix'] }}themes)</div>
                <div class="sb-summary-value">{{ $themes->total() }} Presets</div>
            </div>
        </div>

        <div class="sb-summary-item">
            <div class="sb-summary-icon">
                <i class="bi bi-brush"></i>
            </div>
            <div>
                <div class="sb-summary-label">Active Module Theme</div>
                <div class="sb-summary-value">{{ $activeTheme?->name ?? 'Default Theme' }}</div>
            </div>
        </div>

        <div class="sb-summary-item">
            <div class="sb-summary-icon">
                <i class="bi bi-fonts"></i>
            </div>
            <div>
                <div class="sb-summary-label">Typography &amp; Palette</div>
                <div class="sb-summary-value" style="font-size: 14px;">
                    {{ $activeTheme?->font_family ?? 'Inter' }} &bull;
                    <span style="display: inline-block; width: 13px; height: 13px; border-radius: 3px; background: {{ $activeTheme?->primary_color ?? '#EC134A' }}; vertical-align: middle;"></span>
                    {{ $activeTheme?->primary_color ?? '#EC134A' }}
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Bar Header -->
    <div style="display: flex; align-items: flex-end; justify-content: space-between; gap: 16px; margin-bottom: 20px; flex-wrap: wrap;">
        <div>
            <h3 style="font-size: 24px; font-weight: 800; color: #0B2545; margin: 0 0 4px 0;">{{ $modMeta['label'] }} — Standalone Themes</h3>
            <p style="font-size: 13px; color: #64748B; margin: 0;">Configure color schemes, fonts, and layout presets stored exclusively in <code>{{ $modMeta['prefix'] }}themes</code>.</p>
        </div>

        <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
            <form action="{{ route('admin.themes') }}" method="GET" style="position: relative; width: 250px;">
                <input type="hidden" name="cms_module" value="{{ $modKey }}">
                <i class="bi bi-search" style="position: absolute; left: 13px; top: 50%; transform: translateY(-50%); color: #64748B; font-size: 14px;"></i>
                <input type="text" name="search" value="{{ $search }}" placeholder="Search Theme Preset..." style="width: 100%; height: 42px; background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 9px; padding: 0 14px 0 36px; font-size: 13px; color: #0F172A; outline: none;">
            </form>

            <button type="button" data-bs-toggle="modal" data-bs-target="#addThemeModal" style="height: 42px; background: #D8125B; color: #FFFFFF; border: none; border-radius: 9px; padding: 0 18px; font-size: 13px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; cursor: pointer; box-shadow: 0 4px 10px rgba(216, 18, 91, 0.2);">
                <i class="bi bi-plus-lg"></i>
                <span>Create Theme Preset</span>
            </button>
        </div>
    </div>

    <!-- Themes Table Card -->
    <div class="sb-table-card">
        <div style="overflow-x: auto;">
            <table class="sb-data-table">
                <thead>
                    <tr>
                        <th>Theme Name <i class="bi bi-chevron-expand"></i></th>
                        <th>Layout Style <i class="bi bi-chevron-expand"></i></th>
                        <th>Primary / Secondary Color <i class="bi bi-chevron-expand"></i></th>
                        <th>Font Family <i class="bi bi-chevron-expand"></i></th>
                        <th>Module Table <i class="bi bi-chevron-expand"></i></th>
                        <th>State <i class="bi bi-chevron-expand"></i></th>
                        <th>Actions <i class="bi bi-chevron-expand"></i></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($themes as $th)
                        <tr>
                            <td class="fw-bold text-dark">{{ $th->name }}</td>
                            <td>{{ $th->layout_style }}</td>
                            <td>
                                <span class="d-inline-flex align-items-center gap-1 me-2">
                                    <span style="width: 14px; height: 14px; border-radius: 4px; background: {{ $th->primary_color }}; display: inline-block;"></span>
                                    <code>{{ $th->primary_color }}</code>
                                </span>
                                <span class="d-inline-flex align-items-center gap-1">
                                    <span style="width: 14px; height: 14px; border-radius: 4px; background: {{ $th->secondary_color }}; display: inline-block;"></span>
                                    <code>{{ $th->secondary_color }}</code>
                                </span>
                            </td>
                            <td>{{ $th->font_family }}</td>
                            <td><span class="sb-badge-pill sb-badge-{{ $modMeta['badge'] }}">{{ $modMeta['prefix'] }}themes</span></td>
                            <td>
                                @if($th->is_active)
                                    <span class="sb-badge-pill sb-badge-green">Active Preset</span>
                                @else
                                    <span class="sb-badge-pill sb-badge-amber">Standby</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    @if(!$th->is_active)
                                        <form action="{{ route('admin.themes.activate', $th->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="cms_module" value="{{ $modKey }}">
                                            <button type="submit" class="sb-page-btn text-success" style="width: auto; padding: 0 10px; font-size: 12px;">Activate</button>
                                        </form>
                                    @endif
                                    <form action="{{ route('admin.themes.destroy', $th->id) }}" method="POST" onsubmit="return confirm('Delete this theme?')">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="cms_module" value="{{ $modKey }}">
                                        <button type="submit" class="sb-page-btn text-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No themes in <code>{{ $modMeta['prefix'] }}themes</code>.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="sb-table-footer">
            <div>Showing {{ $themes->firstItem() ?? 0 }} to {{ $themes->lastItem() ?? 0 }} of {{ $themes->total() }} themes in {{ $modMeta['prefix'] }}themes</div>
            <div class="sb-pagination">
                <a href="{{ $themes->previousPageUrl() ?? '#' }}" class="sb-page-btn"><i class="bi bi-chevron-left"></i></a>
                <a href="#" class="sb-page-btn active">1</a>
                <a href="{{ $themes->nextPageUrl() ?? '#' }}" class="sb-page-btn"><i class="bi bi-chevron-right"></i></a>
            </div>
        </div>
    </div>

    <!-- Add Theme Modal -->
    <div class="modal fade" id="addThemeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('admin.themes.store') }}" method="POST" class="modal-content border-0 shadow" style="border-radius: 16px;">
                @csrf
                <input type="hidden" name="cms_module" value="{{ $modKey }}">
                <div class="modal-header border-bottom px-4 py-3">
                    <h5 class="modal-title fw-bold">Create Theme Preset ({{ $modMeta['label'] }})</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Theme Name</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Royal Gold Edition" style="height: 44px; border-radius: 10px;" required>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Primary Accent Color</label>
                            <input type="color" name="primary_color" value="#EC134A" class="form-control form-control-color w-100" style="height: 44px; border-radius: 10px;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Secondary Dark Color</label>
                            <input type="color" name="secondary_color" value="#0F172A" class="form-control form-control-color w-100" style="height: 44px; border-radius: 10px;">
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Font Family</label>
                            <select name="font_family" class="form-select" style="height: 44px; border-radius: 10px;">
                                <option value="Inter">Inter</option>
                                <option value="Playfair Display">Playfair Display</option>
                                <option value="Plus Jakarta Sans">Plus Jakarta Sans</option>
                                <option value="Poppins">Poppins</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Layout Style</label>
                            <input type="text" name="layout_style" value="Modern Grid" class="form-control" style="height: 44px; border-radius: 10px;" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top px-4 py-3">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius: 9px;">Cancel</button>
                    <button type="submit" class="btn text-white fw-semibold" style="background: #EC134A; border-radius: 9px;">Save Theme Preset</button>
                </div>
            </form>
        </div>
    </div>
@endsection
