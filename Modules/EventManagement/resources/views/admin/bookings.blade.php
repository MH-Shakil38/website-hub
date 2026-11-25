@extends('admin.layouts.master')

@section('title', 'Ticket Bookings & Attendees - Eventify Admin')

@section('admin_content')
<div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
    <div>
        <h3 class="fw-bolder mb-1" style="color: #071E24;">Conference Ticket Bookings (em_ticket_bookings)</h3>
        <p class="text-muted small mb-0">Manage online ticket reservations, seat quantities, and attendee passes.</p>
    </div>
    <button type="button" class="btn btn-primary rounded-3 fw-semibold" data-bs-toggle="modal" data-bs-target="#addBookingModal">
        <i class="bi bi-plus-lg me-1"></i> Issue Manual Ticket Pass
    </button>
</div>

<div class="bg-white border rounded-4 p-4 shadow-sm">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr class="text-muted small">
                    <th>Pass ID</th>
                    <th>Attendee</th>
                    <th>Company</th>
                    <th>Ticket Plan</th>
                    <th>Seats</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th class="text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bookings as $bk)
                    <tr>
                        <td><code class="fw-bold text-dark">{{ $bk->ticket_code }}</code></td>
                        <td>
                            <div class="fw-bold">{{ $bk->attendee_name }}</div>
                            <small class="text-muted">{{ $bk->email }} {{ $bk->phone ? '• ' . $bk->phone : '' }}</small>
                        </td>
                        <td>{{ $bk->company ?: '—' }}</td>
                        <td><span class="badge bg-dark rounded-pill px-3">{{ $bk->plan_name }}</span></td>
                        <td class="fw-bold">{{ $bk->quantity }}</td>
                        <td class="fw-bold text-success">${{ number_format((float)$bk->total_amount, 2) }}</td>
                        <td><span class="badge bg-success-subtle text-success rounded-pill">{{ $bk->status }}</span></td>
                        <td class="text-end">
                            <form action="{{ route('admin.eventmanagement.bookings.destroy', $bk->id) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Delete this ticket booking?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-3">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="addBookingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('admin.eventmanagement.bookings.store') }}" method="POST" class="modal-content border-0 rounded-4 shadow">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Issue Conference Ticket Pass</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Attendee Name *</label>
                        <input type="text" name="attendee_name" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Email *</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Phone</label>
                        <input type="text" name="phone" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Company</label>
                        <input type="text" name="company" class="form-control">
                    </div>
                    <div class="col-md-7">
                        <label class="form-label small fw-bold">Ticket Plan *</label>
                        <select name="plan_name" class="form-select">
                            @foreach($plans as $pl)
                                <option value="{{ $pl->name }}">{{ $pl->name }} (${{ number_format((float)$pl->price, 0) }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label small fw-bold">Seats *</label>
                        <input type="number" name="quantity" value="1" min="1" class="form-control" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary rounded-3 fw-semibold">Issue Ticket Pass</button>
            </div>
        </form>
    </div>
</div>
@endsection
