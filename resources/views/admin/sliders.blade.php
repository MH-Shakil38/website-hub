@extends('admin.layouts.master')

@section('title', 'Sliders (' . $modMeta['label'] . ') - ' . ($globalSiteName ?? 'SiteBuilder'))

@section('admin_content')
    @include('admin.partials.module-switcher')

    <!-- Top 3-Column Summary Strip (Screenshot 8 Pattern) -->
    <div class="sb-summary-strip">
        <div class="sb-summary-item">
            <div class="sb-summary-icon">
                <i class="bi bi-card-image"></i>
            </div>
            <div>
                <div class="sb-summary-label">Total Sliders ({{ $modMeta['prefix'] }}sliders)</div>
                <div class="sb-summary-value">{{ $sliders->total() }} Banners</div>
            </div>
        </div>

        <div class="sb-summary-item">
            <div class="sb-summary-icon">
                <i class="bi bi-check2-circle"></i>
            </div>
            <div>
                <div class="sb-summary-label">Active Hero Banners</div>
                <div class="sb-summary-value">{{ $activeSlidersCount }} Active</div>
            </div>
        </div>

        <div class="sb-summary-item">
            <div class="sb-summary-icon">
                <i class="bi bi-folder2-open"></i>
            </div>
            <div>
                <div class="sb-summary-label">Module Upload Path</div>
                <div class="sb-summary-value" style="font-size: 13.5px;">Modules/{{ $modKey }}/Public/uploads/sliders</div>
            </div>
        </div>
    </div>

    <!-- Filter Bar Header -->
    <div style="display: flex; align-items: flex-end; justify-content: space-between; gap: 16px; margin-bottom: 20px; flex-wrap: wrap;">
        <div>
            <h3 style="font-size: 24px; font-weight: 800; color: #0B2545; margin: 0 0 4px 0;">{{ $modMeta['label'] }} — Hero Sliders</h3>
            <p style="font-size: 13px; color: #64748B; margin: 0;">Manage hero banners stored in <code>{{ $modMeta['prefix'] }}sliders</code> and <code>Modules/{{ $modKey }}/Public/uploads/sliders</code>.</p>
        </div>

        <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
            <form action="{{ route('admin.slider') }}" method="GET" style="position: relative; width: 250px;">
                <input type="hidden" name="cms_module" value="{{ $modKey }}">
                <i class="bi bi-search" style="position: absolute; left: 13px; top: 50%; transform: translateY(-50%); color: #64748B; font-size: 14px;"></i>
                <input type="text" name="search" value="{{ $search }}" placeholder="Search Slider Title..." style="width: 100%; height: 42px; background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 9px; padding: 0 14px 0 36px; font-size: 13px; color: #0F172A; outline: none;">
            </form>

            <button type="button" data-bs-toggle="modal" data-bs-target="#addSliderModal" style="height: 42px; background: #D8125B; color: #FFFFFF; border: none; border-radius: 9px; padding: 0 18px; font-size: 13px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; cursor: pointer; box-shadow: 0 4px 10px rgba(216, 18, 91, 0.2);">
                <i class="bi bi-plus-lg"></i>
                <span>Add Hero Slider</span>
            </button>
        </div>
    </div>

    <!-- Sliders Table Card -->
    <div class="sb-table-card">
        <div style="overflow-x: auto;">
            <table class="sb-data-table">
                <thead>
                    <tr>
                        <th>Banner <i class="bi bi-chevron-expand"></i></th>
                        <th>Heading &amp; Subtitle <i class="bi bi-chevron-expand"></i></th>
                        <th>CTA Button <i class="bi bi-chevron-expand"></i></th>
                        <th>Order <i class="bi bi-chevron-expand"></i></th>
                        <th>Module Table <i class="bi bi-chevron-expand"></i></th>
                        <th>Status <i class="bi bi-chevron-expand"></i></th>
                        <th>Actions <i class="bi bi-chevron-expand"></i></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sliders as $sl)
                        <tr>
                            <td>
                                <img src="{{ $sl->image }}" alt="{{ $sl->title }}" style="width: 64px; height: 40px; border-radius: 7px; object-fit: cover;">
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $sl->title }}</div>
                                <div class="text-muted small">{{ $sl->subtitle }}</div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $sl->button_text }} ({{ $sl->button_url }})</span>
                            </td>
                            <td>#{{ $sl->sort_order }}</td>
                            <td><span class="sb-badge-pill sb-badge-{{ $modMeta['badge'] }}">{{ $modMeta['prefix'] }}sliders</span></td>
                            <td><span class="sb-badge-pill sb-badge-green">Active</span></td>
                            <td>
                                <form action="{{ route('admin.slider.destroy', $sl->id) }}" method="POST" onsubmit="return confirm('Delete this slider?')">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="cms_module" value="{{ $modKey }}">
                                    <button type="submit" class="sb-page-btn text-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No sliders found in <code>{{ $modMeta['prefix'] }}sliders</code>.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="sb-table-footer">
            <div>Showing {{ $sliders->firstItem() ?? 0 }} to {{ $sliders->lastItem() ?? 0 }} of {{ $sliders->total() }} sliders in {{ $modMeta['prefix'] }}sliders</div>
            <div class="sb-pagination">
                <a href="{{ $sliders->previousPageUrl() ?? '#' }}" class="sb-page-btn"><i class="bi bi-chevron-left"></i></a>
                <a href="#" class="sb-page-btn active">1</a>
                <a href="{{ $sliders->nextPageUrl() ?? '#' }}" class="sb-page-btn"><i class="bi bi-chevron-right"></i></a>
            </div>
        </div>
    </div>

    <!-- Add Slider Modal -->
    <div class="modal fade" id="addSliderModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('admin.slider.store') }}" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow" style="border-radius: 16px;">
                @csrf
                <input type="hidden" name="cms_module" value="{{ $modKey }}">
                <div class="modal-header border-bottom px-4 py-3">
                    <h5 class="modal-title fw-bold">Add Hero Slider ({{ $modMeta['label'] }})</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Hero Heading</label>
                        <input type="text" name="title" class="form-control" placeholder="e.g. Bespoke Luxury Interior Architecture" style="height: 44px; border-radius: 10px;" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Subtitle</label>
                        <input type="text" name="subtitle" class="form-control" placeholder="Short tagline for the hero banner" style="height: 44px; border-radius: 10px;">
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Button Text</label>
                            <input type="text" name="button_text" value="Explore Now" class="form-control" style="height: 44px; border-radius: 10px;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Button Link</label>
                            <input type="text" name="button_url" value="/contact" class="form-control" style="height: 44px; border-radius: 10px;">
                        </div>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-semibold small">Upload Banner Image (Saved in <code>Modules/{{ $modKey }}/Public/uploads/sliders</code>)</label>
                        <input type="file" name="image_file" class="form-control" accept="image/*" style="height: 44px; padding-top: 9px; border-radius: 10px;">
                    </div>
                </div>
                <div class="modal-footer border-top px-4 py-3">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius: 9px;">Cancel</button>
                    <button type="submit" class="btn text-white fw-semibold" style="background: #EC134A; border-radius: 9px;">Save Slider</button>
                </div>
            </form>
        </div>
    </div>
@endsection
