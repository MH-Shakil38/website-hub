@extends('admin.layouts.master')

@section('title', 'Users (' . $modMeta['label'] . ') - ' . ($globalSiteName ?? 'SiteBuilder'))

@section('admin_content')
    @include('admin.partials.module-switcher')

    <!-- Top 3-Column Summary Strip (Screenshot 8 Pattern) -->
    <div class="sb-summary-strip">
        <div class="sb-summary-item">
            <div class="sb-summary-icon">
                <i class="bi bi-people"></i>
            </div>
            <div>
                <div class="sb-summary-label">Module Users ({{ $modMeta['prefix'] }}users)</div>
                <div class="sb-summary-value">
                    <span>{{ $moduleUsers->total() }} Users</span>
                </div>
            </div>
        </div>

        <div class="sb-summary-item">
            <div class="sb-summary-icon">
                <i class="bi bi-shield-lock"></i>
            </div>
            <div>
                <div class="sb-summary-label">Available Roles</div>
                <div class="sb-summary-value">{{ $roles->count() }} Roles</div>
                <div style="font-size: 11px; color: #64748B; margin-top: 1px;">{{ $roles->pluck('name')->join(', ') }}</div>
            </div>
        </div>

        <div class="sb-summary-item">
            <div class="sb-summary-icon">
                <i class="bi bi-key"></i>
            </div>
            <div>
                <div class="sb-summary-label">System Permissions</div>
                <div class="sb-summary-value">
                    <span>{{ $permissions->count() }} Permissions</span>
                    <i class="bi bi-gear" style="font-size: 14px; color: #64748B; cursor: pointer;" data-bs-toggle="modal" data-bs-target="#rolesPermissionsModal" title="Manage Roles & Permissions"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Bar Header -->
    <div style="display: flex; align-items: flex-end; justify-content: space-between; gap: 16px; margin-bottom: 20px; flex-wrap: wrap;">
        <div>
            <h3 style="font-size: 24px; font-weight: 800; color: #0B2545; margin: 0 0 4px 0;">{{ $modMeta['label'] }} — Users &amp; Staff</h3>
            <p style="font-size: 13px; color: #64748B; margin: 0;">Manage dedicated users &amp; staff stored in <code>{{ $modMeta['prefix'] }}users</code> for the {{ $modMeta['label'] }} module.</p>
        </div>

        <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
            <!-- Role Filter Dropdown -->
            <div class="dropdown">
                <button type="button" data-bs-toggle="dropdown" style="height: 42px; background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 9px; padding: 0 14px; font-size: 13px; font-weight: 500; color: #1E293B; display: inline-flex; align-items: center; gap: 10px;">
                    <i class="bi bi-shield-check" style="color: #475569;"></i>
                    <span>{{ $roleName ?: 'All Roles' }}</span>
                    <i class="bi bi-chevron-down" style="font-size: 11px; color: #64748B;"></i>
                </button>
                <ul class="dropdown-menu shadow-sm border-0" style="font-size: 13px; border-radius: 10px;">
                    <li><a class="dropdown-item" href="{{ route('admin.users', ['cms_module' => $modKey]) }}">All Roles</a></li>
                    @foreach($roles as $r)
                        <li><a class="dropdown-item" href="{{ route('admin.users', ['cms_module' => $modKey, 'role_name' => $r->name]) }}">{{ $r->name }}</a></li>
                    @endforeach
                </ul>
            </div>

            <!-- Search Input -->
            <form action="{{ route('admin.users') }}" method="GET" style="position: relative; width: 250px;">
                <input type="hidden" name="cms_module" value="{{ $modKey }}">
                <i class="bi bi-search" style="position: absolute; left: 13px; top: 50%; transform: translateY(-50%); color: #64748B; font-size: 14px;"></i>
                <input type="text" name="search" value="{{ $search }}" placeholder="Search by Name, Email or Phone..." style="width: 100%; height: 42px; background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 9px; padding: 0 14px 0 36px; font-size: 13px; color: #0F172A; outline: none;">
            </form>

            <!-- Manage Roles Button -->
            <button type="button" data-bs-toggle="modal" data-bs-target="#rolesPermissionsModal" style="height: 42px; background: #FFFFFF; color: #0F172A; border: 1px solid #CBD5E1; border-radius: 9px; padding: 0 16px; font-size: 13px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; cursor: pointer;">
                <i class="bi bi-key"></i>
                <span>Roles &amp; Permissions</span>
            </button>

            <!-- Add User Button -->
            <button type="button" data-bs-toggle="modal" data-bs-target="#addUserModal" style="height: 42px; background: #D8125B; color: #FFFFFF; border: none; border-radius: 9px; padding: 0 18px; font-size: 13px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; cursor: pointer; box-shadow: 0 4px 10px rgba(216, 18, 91, 0.2);">
                <i class="bi bi-person-plus" style="font-size: 15px;"></i>
                <span>Add Module User</span>
            </button>
        </div>
    </div>

    <!-- Users Data Table Card -->
    <div class="sb-table-card">
        <div style="overflow-x: auto;">
            <table class="sb-data-table">
                <thead>
                    <tr>
                        <th>Joined Date <i class="bi bi-chevron-expand"></i></th>
                        <th>User Name <i class="bi bi-chevron-expand"></i></th>
                        <th>Email Address <i class="bi bi-chevron-expand"></i></th>
                        <th>Phone <i class="bi bi-chevron-expand"></i></th>
                        <th>Assigned Role <i class="bi bi-chevron-expand"></i></th>
                        <th>Module Table <i class="bi bi-chevron-expand"></i></th>
                        <th>Status <i class="bi bi-chevron-expand"></i></th>
                        <th>Actions <i class="bi bi-chevron-expand"></i></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($moduleUsers as $u)
                        @php
                            $roleBadge = match(strtolower($u->role_name)) {
                                'administrator' => 'sb-badge-pink',
                                'manager' => 'sb-badge-purple',
                                'editor' => 'sb-badge-blue',
                                default => 'sb-badge-amber',
                            };
                        @endphp
                        <tr>
                            <td>{{ $u->created_at ? $u->created_at->format('d M Y') : '25 Oct 2026' }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($u->name) }}&background=FFE4EC&color=EC134A&bold=true" alt="{{ $u->name }}" style="width: 30px; height: 30px; border-radius: 50%; object-fit: cover;">
                                    <span class="fw-bold text-dark">{{ $u->name }}</span>
                                </div>
                            </td>
                            <td>{{ $u->email }}</td>
                            <td>{{ $u->phone ?? '01818-****521' }}</td>
                            <td><span class="sb-badge-pill {{ $roleBadge }}">{{ $u->role_name }}</span></td>
                            <td><span class="sb-badge-pill sb-badge-{{ $modMeta['badge'] }}">{{ $modMeta['prefix'] }}users</span></td>
                            <td>
                                <span class="sb-badge-pill {{ $u->status === 'Active' ? 'sb-badge-green' : 'sb-badge-amber' }}">
                                    {{ $u->status }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <button type="button" class="sb-page-btn" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $u->id }}" title="Edit User">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <form action="{{ route('admin.users.destroy', $u->id) }}" method="POST" onsubmit="return confirm('Delete user {{ $u->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="cms_module" value="{{ $modKey }}">
                                        <button type="submit" class="sb-page-btn text-danger" title="Delete User">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        <!-- Edit User Modal -->
                        <div class="modal fade" id="editUserModal{{ $u->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <form action="{{ route('admin.users.update', $u->id) }}" method="POST" class="modal-content border-0 shadow" style="border-radius: 16px;">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="cms_module" value="{{ $modKey }}">
                                    <div class="modal-header border-bottom px-4 py-3">
                                        <h5 class="modal-title fw-bold">Edit User — {{ $u->name }} ({{ $modMeta['label'] }})</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body p-4">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold small">Full Name</label>
                                            <input type="text" name="name" value="{{ $u->name }}" class="form-control" style="border-radius: 10px; height: 44px;" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold small">Email Address</label>
                                            <input type="email" name="email" value="{{ $u->email }}" class="form-control" style="border-radius: 10px; height: 44px;" required>
                                        </div>
                                        <div class="row g-3 mb-3">
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold small">Phone Number</label>
                                                <input type="text" name="phone" value="{{ $u->phone }}" class="form-control" style="border-radius: 10px; height: 44px;">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold small">Status</label>
                                                <select name="status" class="form-select" style="border-radius: 10px; height: 44px;">
                                                    <option value="Active" @selected($u->status === 'Active')>Active</option>
                                                    <option value="Inactive" @selected($u->status === 'Inactive')>Inactive</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="mb-0">
                                            <label class="form-label fw-semibold small">Assign Role</label>
                                            <select name="role_name" class="form-select" style="border-radius: 10px; height: 44px;" required>
                                                @foreach($roles as $r)
                                                    <option value="{{ $r->name }}" @selected($u->role_name === $r->name)>{{ $r->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-top px-4 py-3">
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius: 9px;">Cancel</button>
                                        <button type="submit" class="btn text-white fw-semibold" style="background: #EC134A; border-radius: 9px;">Save Changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">No users in <code>{{ $modMeta['prefix'] }}users</code>.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="sb-table-footer">
            <div>Showing {{ $moduleUsers->firstItem() ?? 0 }} to {{ $moduleUsers->lastItem() ?? 0 }} of {{ $moduleUsers->total() }} users in {{ $modMeta['prefix'] }}users</div>
            <div class="sb-pagination">
                <a href="{{ $moduleUsers->previousPageUrl() ?? '#' }}" class="sb-page-btn"><i class="bi bi-chevron-left"></i></a>
                <a href="#" class="sb-page-btn active">1</a>
                <a href="{{ $moduleUsers->nextPageUrl() ?? '#' }}" class="sb-page-btn"><i class="bi bi-chevron-right"></i></a>
            </div>
        </div>
    </div>

    <!-- Add New Module User Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('admin.users.store') }}" method="POST" class="modal-content border-0 shadow" style="border-radius: 16px;">
                @csrf
                <input type="hidden" name="cms_module" value="{{ $modKey }}">
                <div class="modal-header border-bottom px-4 py-3">
                    <h5 class="modal-title fw-bold">Add User ({{ $modMeta['label'] }})</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Full Name</label>
                        <input type="text" name="name" class="form-control" placeholder="Enter full name" style="border-radius: 10px; height: 44px;" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Email Address</label>
                        <input type="email" name="email" class="form-control" placeholder="user@example.com" style="border-radius: 10px; height: 44px;" required>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Phone Number</label>
                            <input type="text" name="phone" class="form-control" placeholder="01812-XXXXXX" style="border-radius: 10px; height: 44px;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Status</label>
                            <select name="status" class="form-select" style="border-radius: 10px; height: 44px;">
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-semibold small">Assign Role</label>
                        <select name="role_name" class="form-select" style="border-radius: 10px; height: 44px;" required>
                            @foreach($roles as $r)
                                <option value="{{ $r->name }}">{{ $r->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-top px-4 py-3">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius: 9px;">Cancel</button>
                    <button type="submit" class="btn text-white fw-semibold" style="background: #EC134A; border-radius: 9px;">Save to {{ $modMeta['prefix'] }}users</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Roles & Permissions Matrix Modal -->
    <div class="modal fade" id="rolesPermissionsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow" style="border-radius: 16px;">
                <div class="modal-header border-bottom px-4 py-3">
                    <div>
                        <h5 class="modal-title fw-bold mb-0">Roles &amp; Permissions Management</h5>
                        <small class="text-muted">Assign permissions dynamically to each role or create a new role.</small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <form action="{{ route('admin.roles.store') }}" method="POST" class="p-3 mb-4 rounded-3" style="background: #FFF5F7; border: 1px solid #FFE4EC;">
                        @csrf
                        <h6 class="fw-bold mb-2" style="color: #0F172A; font-size: 14px;"><i class="bi bi-plus-circle me-1" style="color: #EC134A;"></i>Create New Role</h6>
                        <div class="row g-2 align-items-center">
                            <div class="col-md-4">
                                <input type="text" name="name" class="form-control form-control-sm" placeholder="Role Name" style="height: 38px; border-radius: 8px;" required>
                            </div>
                            <div class="col-md-5">
                                <input type="text" name="description" class="form-control form-control-sm" placeholder="Short description..." style="height: 38px; border-radius: 8px;">
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-sm w-100 text-white fw-semibold" style="height: 38px; background: #EC134A; border-radius: 8px;">Add Role</button>
                            </div>
                        </div>
                    </form>

                    @foreach($roles as $role)
                        <form action="{{ route('admin.roles.permissions', $role->id) }}" method="POST" class="border rounded-3 p-3 mb-3">
                            @csrf
                            @method('PUT')
                            <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                <div>
                                    <span class="fw-bold text-dark">{{ $role->name }}</span>
                                    <div class="small text-muted">{{ $role->description }}</div>
                                </div>
                                <button type="submit" class="btn btn-sm text-white fw-semibold px-3" style="background: #EC134A; border-radius: 8px;">
                                    Update Permissions
                                </button>
                            </div>
                            <div class="row g-2 mt-1">
                                @foreach($permissions as $perm)
                                    <div class="col-md-4">
                                        <label class="d-flex align-items-center gap-2 small" style="cursor: pointer;">
                                            <input type="checkbox" name="permissions[]" value="{{ $perm->id }}"
                                                @checked($role->permissions->contains('id', $perm->id))
                                                style="accent-color: #EC134A; width: 16px; height: 16px;">
                                            <span>{{ $perm->name }}</span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </form>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
