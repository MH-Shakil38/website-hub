@extends('admin.layouts.master')

@section('title', 'Package Bookings & Newsletter Subscribers — Admin')

@section('admin_content')
    <div class="bg-white border rounded-4 p-4 mb-4 shadow-sm d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <h3 style="font-size: 22px; font-weight: 800; color: #0F172A; margin: 0;">Package Booking Inquiries &amp; Subscribers</h3>
                <span class="sb-badge-pill sb-badge-green">hajj_inquiries</span>
                @if($pendingCount > 0)
                    <span class="badge bg-danger rounded-pill">{{ $pendingCount }} Pending</span>
                @endif
            </div>
            <p class="text-muted small mb-0">Manage pilgrim booking requests from the website pricing/package cards and newsletter subscribers in <code>hajj_inquiries</code> &amp; <code>hajj_subscribers</code>.</p>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="sb-table-card">
                <div class="p-3 border-bottom fw-bold">Pilgrim Package Booking Requests</div>
                <div class="table-responsive">
                    <table class="sb-data-table">
                        <thead>
                            <tr>
                                <th>Pilgrim</th>
                                <th>Selected Package</th>
                                <th>Pilgrims / Season</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($inquiries as $inq)
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $inq->name }}</div>
                                        <div class="text-muted small">{{ $inq->phone }} {{ $inq->email ? '• ' . $inq->email : '' }}</div>
                                        @if($inq->message)
                                            <div class="small text-secondary mt-1">"{{ $inq->message }}"</div>
                                        @endif
                                    </td>
                                    <td><span class="fw-semibold text-success">{{ $inq->package_name }}</span></td>
                                    <td class="small">{{ $inq->pilgrim_count }} Persons<br><span class="text-muted">{{ $inq->travel_date }}</span></td>
                                    <td>
                                        <form action="{{ route('admin.hajjumrah.inquiries.status', $inq->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()" style="width: 120px;">
                                                <option value="Pending" {{ $inq->status === 'Pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="Contacted" {{ $inq->status === 'Contacted' ? 'selected' : '' }}>Contacted</option>
                                                <option value="Booked" {{ $inq->status === 'Booked' ? 'selected' : '' }}>Booked</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td>
                                        <form action="{{ route('admin.hajjumrah.inquiries.destroy', $inq->id) }}" method="POST" onsubmit="return confirm('Delete inquiry?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="sb-page-btn text-danger"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-3">{{ $inquiries->links() }}</div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="sb-table-card">
                <div class="p-3 border-bottom fw-bold">Newsletter Subscribers ({{ $subscribers->count() }})</div>
                <div class="table-responsive">
                    <table class="sb-data-table">
                        <thead>
                            <tr>
                                <th>Email</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($subscribers as $sub)
                                <tr>
                                    <td class="small fw-semibold">{{ $sub->email }}</td>
                                    <td>
                                        <form action="{{ route('admin.hajjumrah.subscribers.destroy', $sub->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="sb-page-btn text-danger"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
