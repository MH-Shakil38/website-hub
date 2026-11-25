@extends('admin.layouts.master')

@section('title', 'Kid Enrollments & Subscribers - WonderLearn Admin')

@section('admin_content')
<div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
    <div>
        <h3 class="fw-bolder mb-1" style="color: #0B2545;">Kid Explorer Enrollments & Subscribers</h3>
        <p class="text-muted small mb-0">Manage children registered via "Join Now" (`wl_enrollments`) and parent newsletter subscribers (`wl_subscribers`).</p>
    </div>
    <button type="button" class="btn btn-primary rounded-3 fw-semibold" data-bs-toggle="modal" data-bs-target="#addEnrollmentModal">
        <i class="bi bi-plus-lg me-1"></i> Register Kid Explorer
    </button>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="bg-white border rounded-4 p-4 shadow-sm">
            <h5 class="fw-bold mb-3">Registered Kid Explorers ({{ $enrollments->count() }})</h5>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr class="text-muted small">
                            <th>Child Name & Age</th>
                            <th>Parent Name</th>
                            <th>Email & Phone</th>
                            <th>Favorite Topic</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($enrollments as $enr)
                            <tr>
                                <td class="fw-bold">{{ $enr->child_name }} <span class="badge bg-info-subtle text-primary rounded-pill">Age {{ $enr->child_age }}</span></td>
                                <td>{{ $enr->parent_name }}</td>
                                <td class="small">
                                    <div>{{ $enr->email }}</div>
                                    <div class="text-muted">{{ $enr->phone }}</div>
                                </td>
                                <td><span class="badge bg-warning-subtle text-dark rounded-pill">{{ $enr->favorite_topic }}</span></td>
                                <td><span class="badge bg-success-subtle text-success rounded-pill">{{ $enr->status }}</span></td>
                                <td>
                                    <form action="{{ route('admin.wonderlearn.enrollments.destroy', $enr->id) }}" method="POST" onsubmit="return confirm('Remove enrollment?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-3"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="bg-white border rounded-4 p-4 shadow-sm">
            <h5 class="fw-bold mb-3">Newsletter Subscribers ({{ $subscribers->count() }})</h5>
            <ul class="list-group list-group-flush">
                @forelse($subscribers as $sub)
                    <li class="list-group-item px-0 d-flex align-items-center justify-content-between">
                        <div>
                            <div class="fw-semibold small">{{ $sub->email }}</div>
                            <span class="badge bg-success-subtle text-success rounded-pill" style="font-size: 10px;">{{ $sub->status }}</span>
                        </div>
                        <form action="{{ route('admin.wonderlearn.subscribers.destroy', $sub->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-3"><i class="bi bi-x-lg"></i></button>
                        </form>
                    </li>
                @empty
                    <li class="list-group-item px-0 text-muted small">No subscribers yet.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>

<!-- Add Enrollment Modal -->
<div class="modal fade" id="addEnrollmentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('admin.wonderlearn.enrollments.store') }}" method="POST" class="modal-content border-0 rounded-4 shadow">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Register Kid Explorer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-bold">Parent Name *</label>
                        <input type="text" name="parent_name" class="form-control" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold">Child Name *</label>
                        <input type="text" name="child_name" class="form-control" required>
                    </div>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-4">
                        <label class="form-label small fw-bold">Child Age *</label>
                        <input type="number" name="child_age" value="7" min="3" max="14" class="form-control" required>
                    </div>
                    <div class="col-8">
                        <label class="form-label small fw-bold">Favorite Topic</label>
                        <input type="text" name="favorite_topic" value="Math Magic" class="form-control">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Parent Email *</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-2">
                    <label class="form-label small fw-bold">Phone</label>
                    <input type="text" name="phone" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary rounded-3 fw-semibold">Save Explorer</button>
            </div>
        </form>
    </div>
</div>
@endsection
