@extends('admin.layouts.master')

@section('title', 'Pages (' . $modMeta['label'] . ') - ' . ($globalSiteName ?? 'SiteBuilder'))

@section('admin_content')
    @include('admin.partials.module-switcher')

    <!-- Top 3-Column Summary Strip (Screenshot 8 Pattern) -->
    <div class="sb-summary-strip">
        <div class="sb-summary-item">
            <div class="sb-summary-icon">
                <i class="bi bi-file-earmark-text"></i>
            </div>
            <div>
                <div class="sb-summary-label">Total Pages ({{ $modMeta['prefix'] }}pages)</div>
                <div class="sb-summary-value">{{ $pages->total() }} Pages</div>
            </div>
        </div>

        <div class="sb-summary-item">
            <div class="sb-summary-icon">
                <i class="bi bi-check-circle"></i>
            </div>
            <div>
                <div class="sb-summary-label">Published Pages</div>
                <div class="sb-summary-value">{{ $publishedCount }} Published</div>
            </div>
        </div>

        <div class="sb-summary-item">
            <div class="sb-summary-icon">
                <i class="bi bi-pencil-square"></i>
            </div>
            <div>
                <div class="sb-summary-label">Draft / Scheduled</div>
                <div class="sb-summary-value">{{ $draftOrScheduledCount }} Pages</div>
            </div>
        </div>
    </div>

    <!-- Filter Bar Header -->
    <div style="display: flex; align-items: flex-end; justify-content: space-between; gap: 16px; margin-bottom: 20px; flex-wrap: wrap;">
        <div>
            <h3 style="font-size: 24px; font-weight: 800; color: #0B2545; margin: 0 0 4px 0;">{{ $modMeta['label'] }} — Pages</h3>
            <p style="font-size: 13px; color: #64748B; margin: 0;">Standalone pages stored exclusively in <code>{{ $modMeta['prefix'] }}pages</code> for the {{ $modMeta['label'] }} module.</p>
        </div>

        <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
            <form action="{{ route('admin.pages') }}" method="GET" style="position: relative; width: 250px;">
                <input type="hidden" name="cms_module" value="{{ $modKey }}">
                <i class="bi bi-search" style="position: absolute; left: 13px; top: 50%; transform: translateY(-50%); color: #64748B; font-size: 14px;"></i>
                <input type="text" name="search" value="{{ $search }}" placeholder="Search by Page Title or Slug..." style="width: 100%; height: 42px; background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 9px; padding: 0 14px 0 36px; font-size: 13px; color: #0F172A; outline: none;">
            </form>

            <button type="button" data-bs-toggle="modal" data-bs-target="#addPageModal" style="height: 42px; background: #D8125B; color: #FFFFFF; border: none; border-radius: 9px; padding: 0 18px; font-size: 13px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; cursor: pointer; box-shadow: 0 4px 10px rgba(216, 18, 91, 0.2);">
                <i class="bi bi-plus-lg"></i>
                <span>Create Page</span>
            </button>
        </div>
    </div>

    <!-- Pages Table Card -->
    <div class="sb-table-card">
        <div style="overflow-x: auto;">
            <table class="sb-data-table">
                <thead>
                    <tr>
                        <th>ID <i class="bi bi-chevron-expand"></i></th>
                        <th>Page Title <i class="bi bi-chevron-expand"></i></th>
                        <th>Slug <i class="bi bi-chevron-expand"></i></th>
                        <th>Module Table <i class="bi bi-chevron-expand"></i></th>
                        <th>Views <i class="bi bi-chevron-expand"></i></th>
                        <th>Status <i class="bi bi-chevron-expand"></i></th>
                        <th>Actions <i class="bi bi-chevron-expand"></i></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pages as $pg)
                        @php
                            $statusBadge = match($pg->status) {
                                'Published' => 'sb-badge-green',
                                'Draft' => 'sb-badge-amber',
                                'Private' => 'sb-badge-purple',
                                default => 'sb-badge-blue',
                            };
                        @endphp
                        <tr>
                            <td>#{{ $pg->id }}</td>
                            <td class="fw-bold text-dark">{{ $pg->title }}</td>
                            <td><code>/{{ $pg->slug }}</code></td>
                            <td><span class="sb-badge-pill sb-badge-{{ $modMeta['badge'] }}">{{ $modMeta['prefix'] }}pages</span></td>
                            <td>{{ number_format($pg->views) }}</td>
                            <td><span class="sb-badge-pill {{ $statusBadge }}">{{ $pg->status }}</span></td>
                            <td>
                                <form action="{{ route('admin.pages.destroy', $pg->id) }}" method="POST" onsubmit="return confirm('Delete this page?')">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="cms_module" value="{{ $modKey }}">
                                    <button type="submit" class="sb-page-btn text-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No pages found in <code>{{ $modMeta['prefix'] }}pages</code>. Click "Create Page" to add one.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="sb-table-footer">
            <div>Showing {{ $pages->firstItem() ?? 0 }} to {{ $pages->lastItem() ?? 0 }} of {{ $pages->total() }} pages in {{ $modMeta['prefix'] }}pages</div>
            <div class="sb-pagination">
                <a href="{{ $pages->previousPageUrl() ?? '#' }}" class="sb-page-btn"><i class="bi bi-chevron-left"></i></a>
                @for($p = 1; $p <= min(5, max(1, $pages->lastPage())); $p++)
                    <a href="{{ $pages->url($p) }}" class="sb-page-btn {{ $pages->currentPage() === $p ? 'active' : '' }}">{{ $p }}</a>
                @endfor
                <a href="{{ $pages->nextPageUrl() ?? '#' }}" class="sb-page-btn"><i class="bi bi-chevron-right"></i></a>
            </div>
        </div>
    </div>

    <!-- Create Page Modal -->
    <div class="modal fade" id="addPageModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('admin.pages.store') }}" method="POST" class="modal-content border-0 shadow" style="border-radius: 16px;">
                @csrf
                <input type="hidden" name="cms_module" value="{{ $modKey }}">
                <div class="modal-header border-bottom px-4 py-3">
                    <h5 class="modal-title fw-bold">Create Page ({{ $modMeta['label'] }})</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Page Title</label>
                        <input type="text" name="title" class="form-control" placeholder="e.g. About Us" style="height: 44px; border-radius: 10px;" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Page Content / Summary</label>
                        <textarea name="content" rows="3" class="form-control" placeholder="Write standalone page content..." style="border-radius: 10px;"></textarea>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-semibold small">Status</label>
                        <select name="status" class="form-select" style="height: 44px; border-radius: 10px;">
                            <option value="Published">Published</option>
                            <option value="Draft">Draft</option>
                            <option value="Private">Private</option>
                            <option value="Scheduled">Scheduled</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-top px-4 py-3">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius: 9px;">Cancel</button>
                    <button type="submit" class="btn text-white fw-semibold" style="background: #EC134A; border-radius: 9px;">Save to {{ $modMeta['prefix'] }}pages</button>
                </div>
            </form>
        </div>
    </div>
@endsection
