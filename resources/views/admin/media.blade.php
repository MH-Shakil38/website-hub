@extends('admin.layouts.master')

@section('title', 'Media Library (' . $modMeta['label'] . ') - ' . ($globalSiteName ?? 'SiteBuilder'))

@section('admin_content')
    @include('admin.partials.module-switcher')

    <!-- Top 3-Column Summary Strip (Screenshot 8 Pattern) -->
    <div class="sb-summary-strip">
        <div class="sb-summary-item">
            <div class="sb-summary-icon">
                <i class="bi bi-images"></i>
            </div>
            <div>
                <div class="sb-summary-label">Total Media ({{ $modMeta['prefix'] }}media)</div>
                <div class="sb-summary-value">{{ $mediaItems->total() }} Files</div>
            </div>
        </div>

        <div class="sb-summary-item">
            <div class="sb-summary-icon">
                <i class="bi bi-hdd-stack"></i>
            </div>
            <div>
                <div class="sb-summary-label">Isolated Module Folder</div>
                <div class="sb-summary-value" style="font-size: 13.5px;">Modules/{{ $modKey }}/Public/uploads/media</div>
            </div>
        </div>

        <div class="sb-summary-item">
            <div class="sb-summary-icon">
                <i class="bi bi-shield-check"></i>
            </div>
            <div>
                <div class="sb-summary-label">Storage Mode</div>
                <div class="sb-summary-value">100% Module Self-Contained</div>
            </div>
        </div>
    </div>

    <!-- Filter Bar Header -->
    <div style="display: flex; align-items: flex-end; justify-content: space-between; gap: 16px; margin-bottom: 20px; flex-wrap: wrap;">
        <div>
            <h3 style="font-size: 24px; font-weight: 800; color: #0B2545; margin: 0 0 4px 0;">{{ $modMeta['label'] }} — Media Library</h3>
            <p style="font-size: 13px; color: #64748B; margin: 0;">Every file uploaded here is stored directly inside <code>Modules/{{ $modKey }}/Public/uploads/media</code> and recorded in <code>{{ $modMeta['prefix'] }}media</code>.</p>
        </div>

        <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
            <form action="{{ route('admin.media-library') }}" method="GET" style="position: relative; width: 250px;">
                <input type="hidden" name="cms_module" value="{{ $modKey }}">
                <i class="bi bi-search" style="position: absolute; left: 13px; top: 50%; transform: translateY(-50%); color: #64748B; font-size: 14px;"></i>
                <input type="text" name="search" value="{{ $search }}" placeholder="Search Media Title..." style="width: 100%; height: 42px; background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 9px; padding: 0 14px 0 36px; font-size: 13px; color: #0F172A; outline: none;">
            </form>

            <button type="button" data-bs-toggle="modal" data-bs-target="#uploadMediaModal" style="height: 42px; background: #D8125B; color: #FFFFFF; border: none; border-radius: 9px; padding: 0 18px; font-size: 13px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; cursor: pointer; box-shadow: 0 4px 10px rgba(216, 18, 91, 0.2);">
                <i class="bi bi-cloud-arrow-up"></i>
                <span>Upload Media</span>
            </button>
        </div>
    </div>

    <!-- Media Table Card -->
    <div class="sb-table-card">
        <div style="overflow-x: auto;">
            <table class="sb-data-table">
                <thead>
                    <tr>
                        <th>Preview <i class="bi bi-chevron-expand"></i></th>
                        <th>Title &amp; Alt Text <i class="bi bi-chevron-expand"></i></th>
                        <th>Room / Category <i class="bi bi-chevron-expand"></i></th>
                        <th>MIME Type <i class="bi bi-chevron-expand"></i></th>
                        <th>File Size <i class="bi bi-chevron-expand"></i></th>
                        <th>Module Table <i class="bi bi-chevron-expand"></i></th>
                        <th>Uploaded Date <i class="bi bi-chevron-expand"></i></th>
                        <th>Actions <i class="bi bi-chevron-expand"></i></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mediaItems as $m)
                        <tr>
                            <td>
                                <img src="{{ $m->file_path }}" alt="{{ $m->alt_text }}" style="width: 56px; height: 40px; border-radius: 6px; object-fit: cover;">
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $m->title }}</div>
                                <div class="text-muted small">{{ $m->alt_text }}</div>
                            </td>
                            <td><span class="sb-badge-pill sb-badge-blue text-uppercase">{{ $m->category ?? 'living' }}</span></td>
                            <td><code>{{ $m->file_type }}</code></td>
                            <td>{{ $m->file_size }}</td>
                            <td><span class="sb-badge-pill sb-badge-{{ $modMeta['badge'] }}">{{ $modMeta['prefix'] }}media</span></td>
                            <td>{{ $m->created_at ? $m->created_at->format('M d, Y') : 'Oct 25, 2026' }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <a href="{{ $m->file_path }}" target="_blank" class="sb-page-btn text-primary" title="View Full Image"><i class="bi bi-box-arrow-up-right"></i></a>
                                    <form action="{{ route('admin.media-library.destroy', $m->id) }}" method="POST" onsubmit="return confirm('Delete this media file?')">
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
                            <td colspan="8" class="text-center py-4 text-muted">No media files in <code>{{ $modMeta['prefix'] }}media</code>. (Frontend Photo Gallery automatically uses default photos until you upload here)</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="sb-table-footer">
            <div>Showing {{ $mediaItems->firstItem() ?? 0 }} to {{ $mediaItems->lastItem() ?? 0 }} of {{ $mediaItems->total() }} files in {{ $modMeta['prefix'] }}media</div>
            <div class="sb-pagination">
                <a href="{{ $mediaItems->previousPageUrl() ?? '#' }}" class="sb-page-btn"><i class="bi bi-chevron-left"></i></a>
                <a href="#" class="sb-page-btn active">1</a>
                <a href="{{ $mediaItems->nextPageUrl() ?? '#' }}" class="sb-page-btn"><i class="bi bi-chevron-right"></i></a>
            </div>
        </div>
    </div>

    <!-- Upload Media Modal -->
    <div class="modal fade" id="uploadMediaModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('admin.media-library.store') }}" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow" style="border-radius: 16px;">
                @csrf
                <input type="hidden" name="cms_module" value="{{ $modKey }}">
                <div class="modal-header border-bottom px-4 py-3">
                    <h5 class="modal-title fw-bold">Upload Media ({{ $modMeta['label'] }})</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Media Title</label>
                        <input type="text" name="title" class="form-control" placeholder="e.g. Living Room Showcase" style="height: 44px; border-radius: 10px;" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Room / Gallery Category</label>
                        <select name="category" class="form-select" style="height: 44px; border-radius: 10px;">
                            <option value="living">Living Room (living)</option>
                            <option value="kitchen">Kitchen (kitchen)</option>
                            <option value="bedroom">Bedroom (bedroom)</option>
                            <option value="bath">Bathroom (bath)</option>
                            <option value="dining">Dining &amp; Nook (dining)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Subtitle / Alt Text</label>
                        <input type="text" name="alt_text" class="form-control" placeholder="e.g. Living · Austin, TX" style="height: 44px; border-radius: 10px;">
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-semibold small">Select File (Saves to <code>Modules/{{ $modKey }}/Public/uploads/media</code>)</label>
                        <input type="file" name="media_file" class="form-control" style="height: 44px; padding-top: 9px; border-radius: 10px;">
                    </div>
                </div>
                <div class="modal-footer border-top px-4 py-3">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius: 9px;">Cancel</button>
                    <button type="submit" class="btn text-white fw-semibold" style="background: #EC134A; border-radius: 9px;">Upload to {{ $modKey }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection
