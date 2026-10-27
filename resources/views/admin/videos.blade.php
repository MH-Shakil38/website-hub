@extends('admin.layouts.master')

@section('title', 'Video Gallery (' . $modMeta['label'] . ') - ' . ($globalSiteName ?? 'SiteBuilder'))

@section('admin_content')
    @include('admin.partials.module-switcher')

    <!-- Top 3-Column Summary Strip (Screenshot 8 Pattern) -->
    <div class="sb-summary-strip">
        <div class="sb-summary-item">
            <div class="sb-summary-icon">
                <i class="bi bi-play-btn"></i>
            </div>
            <div>
                <div class="sb-summary-label">Custom Videos ({{ $modMeta['prefix'] }}videos)</div>
                <div class="sb-summary-value">
                    {{ $videos->total() > 0 ? $videos->total() . ' Custom Videos' : count($defaultVideos) . ' Default Videos Active' }}
                </div>
            </div>
        </div>

        <div class="sb-summary-item">
            <div class="sb-summary-icon">
                <i class="bi bi-youtube"></i>
            </div>
            <div>
                <div class="sb-summary-label">Supported Formats</div>
                <div class="sb-summary-value" style="font-size: 14px;">YouTube Link or Direct MP4 Upload</div>
            </div>
        </div>

        <div class="sb-summary-item">
            <div class="sb-summary-icon">
                <i class="bi bi-folder-check"></i>
            </div>
            <div>
                <div class="sb-summary-label">Isolated Video Storage</div>
                <div class="sb-summary-value" style="font-size: 13.5px;">Modules/{{ $modKey }}/Public/uploads/videos</div>
            </div>
        </div>
    </div>

    <!-- Filter Bar Header -->
    <div style="display: flex; align-items: flex-end; justify-content: space-between; gap: 16px; margin-bottom: 20px; flex-wrap: wrap;">
        <div>
            <h3 style="font-size: 24px; font-weight: 800; color: #0B2545; margin: 0 0 4px 0;">{{ $modMeta['label'] }} — Video Gallery</h3>
            <p style="font-size: 13px; color: #64748B; margin: 0;">
                Paste any YouTube link OR upload an MP4 video directly. If no custom video is added, the 4 default walkthrough videos stay active automatically!
            </p>
        </div>

        <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
            <form action="{{ route('admin.video-gallery') }}" method="GET" style="position: relative; width: 250px;">
                <input type="hidden" name="cms_module" value="{{ $modKey }}">
                <i class="bi bi-search" style="position: absolute; left: 13px; top: 50%; transform: translateY(-50%); color: #64748B; font-size: 14px;"></i>
                <input type="text" name="search" value="{{ $search }}" placeholder="Search Video Title..." style="width: 100%; height: 42px; background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 9px; padding: 0 14px 0 36px; font-size: 13px; color: #0F172A; outline: none;">
            </form>

            <button type="button" data-bs-toggle="modal" data-bs-target="#addVideoModal" style="height: 42px; background: #D8125B; color: #FFFFFF; border: none; border-radius: 9px; padding: 0 18px; font-size: 13px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; cursor: pointer; box-shadow: 0 4px 10px rgba(216, 18, 91, 0.2);">
                <i class="bi bi-plus-lg"></i>
                <span>Add New Video</span>
            </button>
        </div>
    </div>

    <!-- Videos Table Card -->
    <div class="sb-table-card">
        <div style="overflow-x: auto;">
            <table class="sb-data-table">
                <thead>
                    <tr>
                        <th>Thumbnail <i class="bi bi-chevron-expand"></i></th>
                        <th>Video Title <i class="bi bi-chevron-expand"></i></th>
                        <th>Category <i class="bi bi-chevron-expand"></i></th>
                        <th>Duration <i class="bi bi-chevron-expand"></i></th>
                        <th>Source / URL <i class="bi bi-chevron-expand"></i></th>
                        <th>State <i class="bi bi-chevron-expand"></i></th>
                        <th>Actions <i class="bi bi-chevron-expand"></i></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($videos as $vid)
                        <tr>
                            <td>
                                <img src="{{ $vid->thumbnail }}" alt="{{ $vid->title }}" style="width: 64px; height: 42px; border-radius: 7px; object-fit: cover;">
                            </td>
                            <td class="fw-bold text-dark">{{ $vid->title }}</td>
                            <td><span class="sb-badge-pill sb-badge-purple">{{ ucfirst(str_replace('_', ' ', $vid->category)) }}</span></td>
                            <td><code>{{ $vid->duration }}</code></td>
                            <td>
                                <a href="{{ $vid->video_url }}" target="_blank" class="text-decoration-none small" style="color: #EC134A;">
                                    {{ \Illuminate\Support\Str::limit($vid->video_url, 38) }} <i class="bi bi-box-arrow-up-right"></i>
                                </a>
                            </td>
                            <td><span class="sb-badge-pill sb-badge-green">Custom Active</span></td>
                            <td>
                                <form action="{{ route('admin.video-gallery.destroy', $vid->id) }}" method="POST" onsubmit="return confirm('Delete this video?')">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="cms_module" value="{{ $modKey }}">
                                    <button type="submit" class="sb-page-btn text-danger" title="Delete"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        @foreach($defaultVideos as $dVid)
                            <tr>
                                <td>
                                    <img src="{{ $dVid['thumbnail'] }}" alt="{{ $dVid['title'] }}" style="width: 64px; height: 42px; border-radius: 7px; object-fit: cover;">
                                </td>
                                <td class="fw-bold text-dark">{{ $dVid['title'] }}</td>
                                <td><span class="sb-badge-pill sb-badge-blue">{{ ucfirst(str_replace('_', ' ', $dVid['category'])) }}</span></td>
                                <td><code>{{ $dVid['duration'] }}</code></td>
                                <td><code class="small text-muted">{{ $dVid['video_url'] }}</code></td>
                                <td><span class="sb-badge-pill sb-badge-amber">Default Fallback</span></td>
                                <td><span class="text-muted small">Built-in Default</span></td>
                            </tr>
                        @endforeach
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Video Modal -->
    <div class="modal fade" id="addVideoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('admin.video-gallery.store') }}" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow" style="border-radius: 16px;">
                @csrf
                <input type="hidden" name="cms_module" value="{{ $modKey }}">
                <div class="modal-header border-bottom px-4 py-3">
                    <h5 class="modal-title fw-bold">Add Video to Gallery ({{ $modMeta['label'] }})</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Video Title</label>
                        <input type="text" name="title" class="form-control" placeholder="e.g. Khulshi Residence Full Walkthrough" style="height: 44px; border-radius: 10px;" required>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-7">
                            <label class="form-label fw-semibold small">Category Filter</label>
                            <select name="category" class="form-select" style="height: 44px; border-radius: 10px;">
                                <option value="walkthrough">Walkthrough</option>
                                <option value="3d_tour">3D Tour</option>
                                <option value="client_story">Client Story</option>
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-semibold small">Duration Label</label>
                            <input type="text" name="duration" value="02:45" class="form-control" placeholder="02:45" style="height: 44px; border-radius: 10px;">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Option A: Paste YouTube Video Link</label>
                        <input type="text" name="video_url" class="form-control" placeholder="https://www.youtube.com/watch?v=..." style="height: 44px; border-radius: 10px;">
                        <small class="text-muted" style="font-size: 11px;">Normal YouTube watch links or short links work automatically!</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">OR Option B: Upload MP4 Video File</label>
                        <input type="file" name="video_file" accept="video/mp4,video/webm" class="form-control" style="height: 44px; padding-top: 9px; border-radius: 10px;">
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-semibold small">Cover Thumbnail Image (Optional — uses default if left blank)</label>
                        <input type="file" name="thumbnail_file" accept="image/*" class="form-control" style="height: 44px; padding-top: 9px; border-radius: 10px;">
                    </div>
                </div>
                <div class="modal-footer border-top px-4 py-3">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius: 9px;">Cancel</button>
                    <button type="submit" class="btn text-white fw-semibold" style="background: #EC134A; border-radius: 9px;">Save Video</button>
                </div>
            </form>
        </div>
    </div>
@endsection
