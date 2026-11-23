@extends('admin.layouts.master')

@section('title', 'Per-Tenant Database Backups & One-Click Restore - SiteBuilder')

@section('admin_content')
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color: #0F172A;">Per-Tenant Database Backups &amp; 1-Click Restore</h4>
            <p class="text-muted small mb-0">
                Create instant snapshots of any website's isolated MySQL database (<code>hub_t_XXXX</code>) and restore a single client's database without affecting other tenants.
            </p>
        </div>
        <button type="button" class="btn btn-danger fw-semibold px-3 py-2" data-bs-toggle="modal" data-bs-target="#createBackupModal" style="background: #EC134A; border: none; border-radius: 10px; font-size: 13px;">
            <i class="bi bi-cloud-arrow-down-fill me-1"></i> Create New DB Snapshot
        </button>
    </div>

    <!-- Filter Bar -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px;">
        <div class="card-body p-3">
            <form action="{{ route('admin.backups.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-md-9">
                    <select name="tenant_id" class="form-select form-select-sm">
                        <option value="">All Accessible Website Databases ({{ $accessibleTenants->count() }})</option>
                        @foreach($accessibleTenants as $tOpt)
                            <option value="{{ $tOpt->id }}" {{ (string) $selectedTenantId === (string) $tOpt->id ? 'selected' : '' }}>
                                {{ $tOpt->name }} ({{ $tOpt->theme_module }} • DB: {{ $tOpt->db_name }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-dark w-100">Filter Backups</button>
                    <a href="{{ route('admin.backups.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Backups Table -->
    <div class="card border-0 shadow-sm" style="border-radius: 14px;">
        <div class="table-responsive">
            <table class="table align-middle mb-0" style="font-size: 13px;">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Backup ID</th>
                        <th>Website &amp; Theme</th>
                        <th>Isolated Database</th>
                        <th>Snapshot File &amp; Size</th>
                        <th>Created At</th>
                        <th class="text-end pe-4">1-Click Restore / Download</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($backups as $b)
                        <tr>
                            <td class="ps-4 fw-bold">#{{ $b->id }}</td>
                            <td>
                                <div class="fw-bold text-dark">{{ $b->tenant?->name ?? 'Deleted Tenant' }}</div>
                                <div class="text-muted" style="font-size: 11px;">{{ $b->tenant?->theme_module }} • {{ $b->notes }}</div>
                            </td>
                            <td>
                                <code class="px-2 py-1 rounded" style="background: #DCFCE7; color: #166534; font-weight: 600;">
                                    {{ $b->db_name }}
                                </code>
                            </td>
                            <td>
                                <code>{{ basename($b->file_path) }}</code>
                                <div class="text-muted" style="font-size: 11px;">{{ number_format($b->size_bytes / 1024, 1) }} KB</div>
                            </td>
                            <td class="text-muted small">{{ $b->created_at?->format('M d, Y H:i:s') }}</td>
                            <td class="text-end pe-4">
                                <div class="d-inline-flex align-items-center gap-1">
                                    <form action="{{ route('admin.backups.restore', $b->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Restore database {{ $b->db_name }} to this snapshot?');">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success fw-semibold" style="font-size: 11.5px; border-radius: 7px;">
                                            <i class="bi bi-arrow-counterclockwise me-1"></i> Restore DB
                                        </button>
                                    </form>
                                    <a href="{{ route('admin.backups.download', $b->id) }}" class="btn btn-sm btn-outline-dark" style="font-size: 11.5px; border-radius: 7px;">
                                        <i class="bi bi-download"></i>
                                    </a>
                                    <form action="{{ route('admin.backups.destroy', $b->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this backup snapshot?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" style="font-size: 11.5px; border-radius: 7px;">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No database snapshots created yet. Click "Create New DB Snapshot" above.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($backups->hasPages())
            <div class="card-footer bg-white border-0 px-4 py-3">
                {{ $backups->links() }}
            </div>
        @endif
    </div>

    <!-- Modal: Create New Backup -->
    <div class="modal fade" id="createBackupModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('admin.backups.store') }}" method="POST" class="modal-content border-0 shadow" style="border-radius: 16px;">
                @csrf
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold">Create Tenant Database Snapshot</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Select Target Website Database</label>
                        <select name="tenant_id" class="form-select" required>
                            @foreach($accessibleTenants as $tOpt)
                                <option value="{{ $tOpt->id }}">{{ $tOpt->name }} ({{ $tOpt->theme_module }} • {{ $tOpt->db_name }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-bold">Snapshot Label / Notes (Optional)</label>
                        <input type="text" name="notes" class="form-control" placeholder="e.g. Before homepage redesign">
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger fw-semibold" style="background: #EC134A; border: none;">
                        <i class="bi bi-cloud-arrow-down-fill me-1"></i> Create Snapshot Now
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
