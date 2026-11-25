@extends('admin.layouts.master')

@section('title', 'Support Tickets & Help Desk - SiteBuilder')

@section('admin_content')
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color: #0F172A;">Support Tickets &amp; Help Desk</h4>
            <p class="text-muted small mb-0">
                Open a ticket for technical assistance, custom domain setup, or billing inquiries.
            </p>
        </div>
        <button type="button" class="btn btn-danger fw-semibold px-3 py-2" data-bs-toggle="modal" data-bs-target="#createTicketModal" style="background: #EC134A; border: none; border-radius: 10px; font-size: 13px;">
            <i class="bi bi-plus-lg me-1"></i> Open Support Ticket
        </button>
    </div>

    <div class="row g-3">
        @forelse($tickets as $tkt)
            <div class="col-12">
                <div class="card border-0 shadow-sm" style="border-radius: 14px;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3 pb-2 border-bottom">
                            <div>
                                <span class="badge bg-dark me-1">{{ $tkt->ticket_number }}</span>
                                <span class="fw-bold text-dark fs-6">{{ $tkt->subject }}</span>
                                <span class="badge bg-light text-dark border ms-1 text-uppercase" style="font-size: 10px;">{{ $tkt->category }}</span>
                                <span class="badge {{ $tkt->priority === 'urgent' ? 'bg-danger' : 'bg-secondary-subtle text-secondary' }} ms-1 text-uppercase" style="font-size: 10px;">{{ $tkt->priority }}</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                @if($tkt->status === 'open')
                                    <span class="badge bg-warning-subtle text-warning">OPEN</span>
                                @elseif($tkt->status === 'answered')
                                    <span class="badge bg-success-subtle text-success">ANSWERED</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary">CLOSED</span>
                                @endif

                                @if($tkt->status !== 'closed')
                                    <form action="{{ route('admin.support.tickets.close', $tkt->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size: 11px;">Close Ticket</button>
                                    </form>
                                @endif
                            </div>
                        </div>

                        <!-- Replies Thread -->
                        <div class="mb-3">
                            @foreach($tkt->replies as $rep)
                                <div class="p-3 rounded-3 mb-2 {{ $rep->is_staff_reply ? 'bg-primary-subtle border border-primary-subtle' : 'bg-light border' }}" style="font-size: 13px;">
                                    <div class="d-flex justify-content-between small mb-1">
                                        <span class="fw-bold">
                                            {{ $rep->user?->name ?? 'User' }}
                                            @if($rep->is_staff_reply)
                                                <span class="badge bg-primary ms-1" style="font-size: 9.5px;">SUPPORT STAFF</span>
                                            @endif
                                        </span>
                                        <span class="text-muted" style="font-size: 11px;">{{ $rep->created_at?->diffForHumans() }}</span>
                                    </div>
                                    <div class="text-dark">{{ $rep->message }}</div>
                                </div>
                            @endforeach
                        </div>

                        @if($tkt->status !== 'closed')
                            <form action="{{ route('admin.support.tickets.reply', $tkt->id) }}" method="POST" class="d-flex gap-2">
                                @csrf
                                <input type="text" name="message" class="form-control form-control-sm" placeholder="Write a reply..." required>
                                <button type="submit" class="btn btn-sm btn-dark fw-semibold px-3">Reply</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card border-0 shadow-sm p-5 text-center text-muted" style="border-radius: 14px;">
                    No support tickets found. Click "Open Support Ticket" if you need assistance.
                </div>
            </div>
        @endforelse
    </div>

    <!-- Modal: Create Support Ticket -->
    <div class="modal fade" id="createTicketModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('admin.support.tickets.store') }}" method="POST" class="modal-content border-0 shadow" style="border-radius: 16px;">
                @csrf
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold">Open New Support Ticket</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Subject</label>
                        <input type="text" name="subject" class="form-control" placeholder="e.g. Help connecting my custom domain" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold">Department</label>
                            <select name="category" class="form-select">
                                <option value="technical">Technical Support</option>
                                <option value="domain">Custom Domain &amp; SSL</option>
                                <option value="billing">Billing &amp; Subscription</option>
                                <option value="reseller">Reseller Program</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold">Priority</label>
                            <select name="priority" class="form-select">
                                <option value="normal">Normal</option>
                                <option value="high">High</option>
                                <option value="urgent">Urgent</option>
                                <option value="low">Low</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Related Website (Optional)</label>
                        <select name="tenant_id" class="form-select">
                            <option value="">General Account Inquiry</option>
                            @foreach($accessibleTenants as $tOpt)
                                <option value="{{ $tOpt->id }}">{{ $tOpt->name }} ({{ $tOpt->db_name }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-bold">Message</label>
                        <textarea name="message" rows="3" class="form-control no-rte" placeholder="Describe your issue or question..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger fw-semibold" style="background: #EC134A; border: none;">Submit Ticket</button>
                </div>
            </form>
        </div>
    </div>
@endsection
