@extends('admin.layouts.master')

@section('title', 'Activity Log - ' . ($globalSiteName ?? 'SiteBuilder'))

@section('admin_content')
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <h3 style="font-size: 24px; font-weight: 800; color: #0B2545; margin: 0 0 4px 0;">Activity Log</h3>
            <p style="font-size: 13px; color: #64748B; margin: 0;">Every recorded action across your websites, newest first.</p>
        </div>
    </div>

    <div class="sb-table-card">
        <div style="overflow-x: auto;">
            <table class="sb-data-table">
                <thead>
                    <tr>
                        <th>Action</th>
                        <th>Details</th>
                        <th>By</th>
                        <th>When</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($activities as $act)
                        <tr>
                            <td>
                                <span class="d-inline-flex align-items-center gap-2">
                                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle flex-shrink-0" style="width: 30px; height: 30px; background: {{ $act->bg_color }}; color: {{ $act->text_color }}; font-size: 13px;">
                                        <i class="bi {{ $act->icon }}"></i>
                                    </span>
                                    <strong>{{ $act->title }}</strong>
                                </span>
                            </td>
                            <td>{{ $act->subtitle }}</td>
                            <td>{{ $act->user?->name ?? 'System' }}</td>
                            <td title="{{ $act->created_at?->format('d M Y, h:i A') }}">{{ $act->created_at?->diffForHumans() ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">No activity recorded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="sb-table-footer">
            <div>Showing {{ $activities->firstItem() ?? 0 }} to {{ $activities->lastItem() ?? 0 }} of {{ number_format($activities->total()) }} activities</div>
            @include('admin.partials.sb-pagination', ['paginator' => $activities])
        </div>
    </div>
@endsection
