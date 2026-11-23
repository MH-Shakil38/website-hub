@extends('admin.layouts.master')

@section('admin_content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">IVF Center Consultation Requests</h4>
        <p class="text-muted small mb-0">Patient bookings and inquiries submitted from the website</p>
    </div>
    <span class="badge bg-primary px-3 py-2 fs-6">{{ $consultations->total() }} Total Requests</span>
</div>

<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">#</th>
                        <th>Patient Name</th>
                        <th>Contact Info</th>
                        <th>Service Interest</th>
                        <th>Preferred Date</th>
                        <th>Date Submitted</th>
                        <th class="text-end pe-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($consultations as $item)
                        <tr>
                            <td class="ps-4 fw-bold text-muted">{{ $item->id }}</td>
                            <td>
                                <div class="fw-bold text-dark">{{ $item->full_name }}</div>
                                @if($item->message)
                                    <small class="text-muted text-truncate d-inline-block" style="max-width: 260px;" title="{{ $item->message }}">
                                        "{{ $item->message }}"
                                    </small>
                                @endif
                            </td>
                            <td>
                                <div><i class="bi bi-envelope text-primary me-1"></i> {{ $item->email }}</div>
                                @if($item->phone)
                                    <div class="small text-muted"><i class="bi bi-telephone me-1"></i> {{ $item->phone }}</div>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle px-2 py-1">
                                    {{ $item->service_interest ?: 'General Consultation' }}
                                </span>
                            </td>
                            <td>
                                {{ $item->preferred_date ? \Carbon\Carbon::parse($item->preferred_date)->format('M d, Y') : 'Flexible' }}
                            </td>
                            <td class="text-muted small">
                                {{ $item->created_at->format('M d, Y h:i A') }}
                            </td>
                            <td class="text-end pe-4">
                                <form action="{{ route('admin.ivfcenter.consultations.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this consultation record?');" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                                No consultation requests submitted yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($consultations->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            {{ $consultations->links() }}
        </div>
    @endif
</div>
@endsection
