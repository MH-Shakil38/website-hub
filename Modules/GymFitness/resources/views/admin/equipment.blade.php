@extends('admin.layouts.master')

@section('title', 'Gym Equipment & Maintenance — FitPulse Gym Admin')

@section('admin_content')
    <div class="bg-white border rounded-4 p-4 mb-4 shadow-sm d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <h3 style="font-size: 22px; font-weight: 800; color: #0F172A; margin: 0;">Gym Equipment &amp; Maintenance Log</h3>
                <span class="sb-badge-pill sb-badge-green">gym_equipments</span>
            </div>
            <p class="text-muted small mb-0">Manage gym machines, free weights, quantities, operational status, and maintenance schedules.</p>
        </div>

        <button type="button" class="btn btn-success btn-sm fw-bold px-3 py-2 rounded-3" data-bs-toggle="modal" data-bs-target="#addEquipModal">
            <i class="bi bi-plus-lg me-1"></i> Add Equipment
        </button>
    </div>

    <div class="sb-table-card">
        <div class="table-responsive">
            <table class="sb-data-table">
                <thead>
                    <tr>
                        <th>Equipment (EN / BN)</th>
                        <th>Category &amp; Brand</th>
                        <th>Quantity</th>
                        <th>Maintenance Schedule</th>
                        <th>Condition Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($equipments as $eq)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <img src="{{ $eq->image }}" alt="{{ $eq->name }}" style="width:64px;height:46px;object-fit:cover;border-radius:8px;border:1px solid #E2E8F0;">
                                    <div>
                                        <div class="fw-bold text-dark">{{ $eq->name }}</div>
                                        <div class="text-muted small">{{ $eq->name_bn }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fw-semibold small">{{ $eq->category }}</div>
                                <div class="text-muted small">{{ $eq->brand }}</div>
                            </td>
                            <td><span class="sb-badge-pill sb-badge-blue">{{ $eq->quantity }} Units</span></td>
                            <td>
                                <div class="small">Last: {{ $eq->last_maintenance_date?->format('M d, Y') ?? 'N/A' }}</div>
                                <div class="text-primary small fw-semibold">Next: {{ $eq->next_maintenance_date?->format('M d, Y') ?? 'N/A' }}</div>
                            </td>
                            <td>
                                <span class="sb-badge-pill {{ $eq->condition_status === 'Operational' ? 'sb-badge-green' : 'sb-badge-amber' }}">
                                    {{ $eq->condition_status }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <button type="button" class="sb-page-btn" data-bs-toggle="modal" data-bs-target="#editEq{{ $eq->id }}"><i class="bi bi-pencil"></i></button>
                                    <form action="{{ route('admin.gymfitness.equipment.destroy', $eq->id) }}" method="POST" onsubmit="return confirm('Delete equipment {{ $eq->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="sb-page-btn text-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @foreach($equipments as $eq)
        <div class="modal fade" id="editEq{{ $eq->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content bg-white border-0 shadow" style="border-radius:16px;">
                    <form action="{{ route('admin.gymfitness.equipment.update', $eq->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="modal-header border-bottom px-4 py-3">
                            <h5 class="modal-title fw-bold">Edit Equipment: {{ $eq->name }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Name (EN) *</label>
                                    <input type="text" name="name" value="{{ $eq->name }}" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Name (BN)</label>
                                    <input type="text" name="name_bn" value="{{ $eq->name_bn }}" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Category</label>
                                    <input type="text" name="category" value="{{ $eq->category }}" class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Brand</label>
                                    <input type="text" name="brand" value="{{ $eq->brand }}" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Quantity</label>
                                    <input type="number" name="quantity" value="{{ $eq->quantity }}" class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Condition Status</label>
                                    <select name="condition_status" class="form-select">
                                        @foreach(['Operational', 'Under Maintenance', 'Repair Needed'] as $cs)
                                            <option value="{{ $cs }}" {{ $eq->condition_status === $cs ? 'selected' : '' }}>{{ $cs }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Last Maintenance</label>
                                    <input type="date" name="last_maintenance_date" value="{{ $eq->last_maintenance_date?->format('Y-m-d') }}" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Next Maintenance</label>
                                    <input type="date" name="next_maintenance_date" value="{{ $eq->next_maintenance_date?->format('Y-m-d') }}" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Equipment Image</label>
                                    <input type="file" name="image_file" class="form-control" accept="image/*">
                                </div>
                                <div class="col-md-6 d-flex align-items-end">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="eqAct{{ $eq->id }}" {{ $eq->is_active ? 'checked' : '' }}>
                                        <label class="form-check-label small fw-bold" for="eqAct{{ $eq->id }}">Show on Website</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-top px-4 py-3">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary fw-bold px-4">Update Equipment</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    {{-- Add Equipment Modal --}}
    <div class="modal fade" id="addEquipModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content bg-white border-0 shadow" style="border-radius:16px;">
                <form action="{{ route('admin.gymfitness.equipment.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header border-bottom px-4 py-3">
                        <h5 class="modal-title fw-bold">Add Gym Equipment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Name (EN) *</label>
                                <input type="text" name="name" class="form-control" required placeholder="e.g. Cable Crossover">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Name (BN)</label>
                                <input type="text" name="name_bn" class="form-control" placeholder="ক্যাবল ক্রসওভার">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Category *</label>
                                <select name="category" class="form-select" required>
                                    <option value="Strength">Strength</option>
                                    <option value="Cardio">Cardio</option>
                                    <option value="Free Weights">Free Weights</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Brand</label>
                                <input type="text" name="brand" value="Hammer Strength" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Quantity *</label>
                                <input type="number" name="quantity" value="4" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Condition Status</label>
                                <select name="condition_status" class="form-select">
                                    <option value="Operational">Operational</option>
                                    <option value="Under Maintenance">Under Maintenance</option>
                                    <option value="Repair Needed">Repair Needed</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Equipment Photo</label>
                                <input type="file" name="image_file" class="form-control" accept="image/*">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top px-4 py-3">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success fw-bold px-4">Save Equipment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
