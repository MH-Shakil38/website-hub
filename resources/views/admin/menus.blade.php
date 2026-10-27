@extends('admin.layouts.master')

@section('title', 'Menu Builder (' . $modMeta['label'] . ') - ' . ($globalSiteName ?? 'SiteBuilder'))

@section('admin_content')
    @include('admin.partials.module-switcher')

    <!-- Top 3-Column Summary Strip (Screenshot 8 Pattern) -->
    <div class="sb-summary-strip">
        <div class="sb-summary-item">
            <div class="sb-summary-icon">
                <i class="bi bi-layers"></i>
            </div>
            <div>
                <div class="sb-summary-label">Total Menu Links ({{ $modMeta['prefix'] }}menus)</div>
                <div class="sb-summary-value">{{ $menus->total() }} Items</div>
            </div>
        </div>

        <div class="sb-summary-item">
            <div class="sb-summary-icon">
                <i class="bi bi-Segmented-nav"></i>
            </div>
            <div>
                <div class="sb-summary-label">Header Navbar Menus</div>
                <div class="sb-summary-value">{{ $headerCount }} Links</div>
            </div>
        </div>

        <div class="sb-summary-item">
            <div class="sb-summary-icon">
                <i class="bi bi-layout-text-window-reverse"></i>
            </div>
            <div>
                <div class="sb-summary-label">Footer Menus</div>
                <div class="sb-summary-value">{{ $footerCount }} Links</div>
            </div>
        </div>
    </div>

    <!-- Filter Bar Header -->
    <div style="display: flex; align-items: flex-end; justify-content: space-between; gap: 16px; margin-bottom: 20px; flex-wrap: wrap;">
        <div>
            <h3 style="font-size: 24px; font-weight: 800; color: #0B2545; margin: 0 0 4px 0;">{{ $modMeta['label'] }} — Menu Builder</h3>
            <p style="font-size: 13px; color: #64748B; margin: 0;">Manage standalone header &amp; footer navigation menus stored in <code>{{ $modMeta['prefix'] }}menus</code>.</p>
        </div>

        <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
            <form action="{{ route('admin.menu-builder') }}" method="GET" style="position: relative; width: 250px;">
                <input type="hidden" name="cms_module" value="{{ $modKey }}">
                <i class="bi bi-search" style="position: absolute; left: 13px; top: 50%; transform: translateY(-50%); color: #64748B; font-size: 14px;"></i>
                <input type="text" name="search" value="{{ $search }}" placeholder="Search Menu Title or URL..." style="width: 100%; height: 42px; background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 9px; padding: 0 14px 0 36px; font-size: 13px; color: #0F172A; outline: none;">
            </form>

            <button type="button" data-bs-toggle="modal" data-bs-target="#addMenuModal" style="height: 42px; background: #D8125B; color: #FFFFFF; border: none; border-radius: 9px; padding: 0 18px; font-size: 13px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; cursor: pointer; box-shadow: 0 4px 10px rgba(216, 18, 91, 0.2);">
                <i class="bi bi-plus-lg"></i>
                <span>Add Menu Item</span>
            </button>
        </div>
    </div>

    <!-- Menus Table Card -->
    <div class="sb-table-card">
        <div style="overflow-x: auto;">
            <table class="sb-data-table">
                <thead>
                    <tr>
                        <th>Order <i class="bi bi-chevron-expand"></i></th>
                        <th>Menu Label <i class="bi bi-chevron-expand"></i></th>
                        <th>Destination URL <i class="bi bi-chevron-expand"></i></th>
                        <th>Location <i class="bi bi-chevron-expand"></i></th>
                        <th>Target <i class="bi bi-chevron-expand"></i></th>
                        <th>Module Table <i class="bi bi-chevron-expand"></i></th>
                        <th>Actions <i class="bi bi-chevron-expand"></i></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($menus as $item)
                        <tr>
                            <td>#{{ $item->sort_order }}</td>
                            <td class="fw-bold text-dark">{{ $item->title }}</td>
                            <td><code>{{ $item->url }}</code></td>
                            <td>
                                <span class="sb-badge-pill {{ $item->location === 'header' ? 'sb-badge-blue' : 'sb-badge-purple' }}">
                                    {{ ucfirst($item->location) }}
                                </span>
                            </td>
                            <td>{{ $item->target === '_blank' ? 'New Tab' : 'Same Window' }}</td>
                            <td><span class="sb-badge-pill sb-badge-{{ $modMeta['badge'] }}">{{ $modMeta['prefix'] }}menus</span></td>
                            <td>
                                <form action="{{ route('admin.menu-builder.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Delete menu item?')">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="cms_module" value="{{ $modKey }}">
                                    <button type="submit" class="sb-page-btn text-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No menu items found in <code>{{ $modMeta['prefix'] }}menus</code>.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="sb-table-footer">
            <div>Showing {{ $menus->firstItem() ?? 0 }} to {{ $menus->lastItem() ?? 0 }} of {{ $menus->total() }} menus in {{ $modMeta['prefix'] }}menus</div>
            <div class="sb-pagination">
                <a href="{{ $menus->previousPageUrl() ?? '#' }}" class="sb-page-btn"><i class="bi bi-chevron-left"></i></a>
                <a href="#" class="sb-page-btn active">1</a>
                <a href="{{ $menus->nextPageUrl() ?? '#' }}" class="sb-page-btn"><i class="bi bi-chevron-right"></i></a>
            </div>
        </div>
    </div>

    <!-- Add Menu Modal -->
    <div class="modal fade" id="addMenuModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('admin.menu-builder.store') }}" method="POST" class="modal-content border-0 shadow" style="border-radius: 16px;">
                @csrf
                <input type="hidden" name="cms_module" value="{{ $modKey }}">
                <div class="modal-header border-bottom px-4 py-3">
                    <h5 class="modal-title fw-bold">Add Menu Item ({{ $modMeta['label'] }})</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Menu Label</label>
                        <input type="text" name="title" class="form-control" placeholder="e.g. Portfolio / Services" style="height: 44px; border-radius: 10px;" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">URL / Path</label>
                        <input type="text" name="url" class="form-control" placeholder="e.g. /portfolio or /contact" style="height: 44px; border-radius: 10px;" required>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Location</label>
                            <select name="location" class="form-select" style="height: 44px; border-radius: 10px;">
                                <option value="header">Header Navbar</option>
                                <option value="footer">Footer</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Target</label>
                            <select name="target" class="form-select" style="height: 44px; border-radius: 10px;">
                                <option value="_self">Same Window</option>
                                <option value="_blank">New Tab</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Sort Order</label>
                            <input type="number" name="sort_order" value="1" class="form-control" style="height: 44px; border-radius: 10px;">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top px-4 py-3">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius: 9px;">Cancel</button>
                    <button type="submit" class="btn text-white fw-semibold" style="background: #EC134A; border-radius: 9px;">Save to {{ $modMeta['prefix'] }}menus</button>
                </div>
            </form>
        </div>
    </div>
@endsection
