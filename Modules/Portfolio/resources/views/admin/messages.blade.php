@extends('admin.layouts.master')

@section('title', 'Contact Messages & Subscribers — Developer Portfolio')

@section('admin_content')
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    <h4 class="fw-bold mb-0">Contact Form Inquiries ({{ $unreadCount }} Unread)</h4>
                    <small class="text-muted">Submitted from public portfolio contact form into <code>port_messages</code></small>
                </div>
            </div>

            <div class="sb-table-card">
                <table class="sb-data-table">
                    <thead>
                        <tr>
                            <th>Sender</th>
                            <th>Subject &amp; Message</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($messages as $m)
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark">{{ $m->name }}</div>
                                    <small class="text-muted d-block">{{ $m->email }}</small>
                                    @if($m->phone)
                                        <small class="text-muted">{{ $m->phone }}</small>
                                    @endif
                                </td>
                                <td style="max-width: 340px;">
                                    <div class="fw-semibold text-dark">{{ $m->subject }}</div>
                                    <small class="text-muted">{{ $m->message }}</small>
                                </td>
                                <td>
                                    <form action="{{ route('admin.portfolio.messages.status', $m->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <select name="status" class="form-select form-select-sm" onchange="this.form.submit()" style="width: 110px;">
                                            <option value="Unread" {{ $m->status === 'Unread' ? 'selected' : '' }}>Unread</option>
                                            <option value="Read" {{ $m->status === 'Read' ? 'selected' : '' }}>Read</option>
                                            <option value="Replied" {{ $m->status === 'Replied' ? 'selected' : '' }}>Replied</option>
                                        </select>
                                    </form>
                                </td>
                                <td>
                                    <form action="{{ route('admin.portfolio.messages.destroy', $m->id) }}" method="POST">
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

        <div class="col-lg-4">
            <div class="mb-3">
                <h4 class="fw-bold mb-0">Newsletter Subscribers</h4>
                <small class="text-muted">Stored in <code>port_subscribers</code></small>
            </div>
            <div class="sb-table-card">
                <table class="sb-data-table">
                    <thead>
                        <tr>
                            <th>Subscriber Email</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($subscribers as $sub)
                            <tr>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $sub->email }}</div>
                                    <span class="sb-badge-pill sb-badge-green">{{ $sub->status }}</span>
                                </td>
                                <td>
                                    <form action="{{ route('admin.portfolio.subscribers.destroy', $sub->id) }}" method="POST">
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
@endsection
