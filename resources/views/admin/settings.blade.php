@extends('admin.layouts.master')

@section('title', 'Settings (' . $modMeta['label'] . ') - ' . ($globalSiteName ?? 'SiteBuilder'))

@section('admin_content')
    @include('admin.partials.module-switcher')

    <!-- Top 3-Column Summary Strip (Screenshot 8 Pattern) -->
    <div class="sb-summary-strip">
        <div class="sb-summary-item">
            <div class="sb-summary-icon">
                <i class="bi bi-gear-wide-connected"></i>
            </div>
            <div>
                <div class="sb-summary-label">Module Settings Table</div>
                <div class="sb-summary-value">{{ $modMeta['prefix'] }}settings</div>
                <div style="font-size: 11px; color: #64748B; margin-top: 1px;">100% Isolated for {{ $modMeta['label'] }}</div>
            </div>
        </div>

        <div class="sb-summary-item">
            <div class="sb-summary-icon">
                <i class="bi bi-person-badge"></i>
            </div>
            <div>
                <div class="sb-summary-label">Logged in Admin</div>
                <div class="sb-summary-value">{{ $user->name }}</div>
                <div style="font-size: 11px; color: #64748B; margin-top: 1px;">{{ $user->email }}</div>
            </div>
        </div>

        <div class="sb-summary-item">
            <div class="sb-summary-icon">
                <i class="bi bi-shield-check"></i>
            </div>
            <div>
                <div class="sb-summary-label">Two-Factor Security</div>
                <div class="sb-summary-value">
                    <span>{{ $user->two_factor_enabled ? 'Enabled (Code: ' . ($user->two_factor_code ?? '274916') . ')' : 'Disabled' }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Left Column: Standalone Module Settings ({{ $modMeta['prefix'] }}settings) + Global Branding -->
        <div class="col-lg-6">
            <!-- Standalone Module Settings Card -->
            <div class="sb-table-card p-4 mb-4">
                <div class="mb-3 pb-2 border-bottom d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold mb-1" style="color: #0F172A;">{{ $modMeta['label'] }} — Standalone Website Settings</h5>
                        <p class="text-muted small mb-0">Stored exclusively inside <code>{{ $modMeta['prefix'] }}settings</code> &amp; <code>Modules/{{ $modKey }}/Public/uploads/branding</code>.</p>
                    </div>
                    <span class="sb-badge-pill sb-badge-{{ $modMeta['badge'] }}">{{ $modMeta['prefix'] }}settings</span>
                </div>

                <form action="{{ route('admin.settings.module') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="cms_module" value="{{ $modKey }}">

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-dark">Module Website Title</label>
                            <input type="text" name="site_title" value="{{ $moduleSettings['site_title'] ?? $modMeta['label'] . ' Studio' }}" class="form-control" style="height: 46px; border-radius: 10px; background: #F8FAFC;" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-dark">Module Tagline</label>
                            <input type="text" name="site_tagline" value="{{ $moduleSettings['site_tagline'] ?? 'Premium Standalone Website' }}" class="form-control" style="height: 46px; border-radius: 10px; background: #F8FAFC;">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-dark">Contact Email</label>
                            <input type="email" name="contact_email" value="{{ $moduleSettings['contact_email'] ?? 'info@' . $modMeta['slug'] . '.com' }}" class="form-control" style="height: 46px; border-radius: 10px; background: #F8FAFC;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-dark">Hotline / Phone</label>
                            <input type="text" name="contact_phone" value="{{ $moduleSettings['contact_phone'] ?? '+880 1812-000000' }}" class="form-control" style="height: 46px; border-radius: 10px; background: #F8FAFC;">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-dark">Office Address</label>
                        <input type="text" name="contact_address" value="{{ $moduleSettings['contact_address'] ?? 'Gulshan-2, Dhaka, Bangladesh' }}" class="form-control" style="height: 46px; border-radius: 10px; background: #F8FAFC;">
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-dark">Module Logo (<code>Modules/{{ $modKey }}/Public</code>)</label>
                            <input type="file" name="module_logo" class="form-control" style="height: 46px; padding-top: 10px; border-radius: 10px; background: #F8FAFC;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-dark">Footer Copyright Text</label>
                            <input type="text" name="footer_copyright" value="{{ $moduleSettings['footer_copyright'] ?? '© ' . date('Y') . ' ' . $modMeta['label'] . '. All rights reserved.' }}" class="form-control" style="height: 46px; border-radius: 10px; background: #F8FAFC;">
                        </div>
                    </div>

                    <button type="submit" class="btn text-white fw-semibold w-100" style="height: 46px; background: #EC134A; border-radius: 10px; box-shadow: 0 4px 12px rgba(236, 19, 74, 0.22);">
                        Save {{ $modMeta['label'] }} Settings ({{ $modMeta['prefix'] }}settings) <i class="bi bi-check2-circle ms-1"></i>
                    </button>
                </form>
            </div>

            <!-- Global Admin Branding Card -->
            <div class="sb-table-card p-4">
                <div class="mb-3 pb-2 border-bottom">
                    <h5 class="fw-bold mb-1" style="color: #0F172A;">Global Admin Panel Branding</h5>
                    <p class="text-muted small mb-0">Customize the top-left Admin &amp; Auth panel brand name (<code>{{ $globalSiteName ?? 'SiteBuilder' }}</code>) &amp; logo.</p>
                </div>

                <form action="{{ route('admin.settings.global') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-dark">Admin Panel Brand Name</label>
                            <input type="text" name="site_name" value="{{ $globalSettings['site_name'] ?? 'SiteBuilder' }}" class="form-control" style="height: 46px; border-radius: 10px; background: #F8FAFC;" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-dark">Brand Subtitle</label>
                            <input type="text" name="site_tagline" value="{{ $globalSettings['site_tagline'] ?? 'Website Management' }}" class="form-control" style="height: 46px; border-radius: 10px; background: #F8FAFC;" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-dark">Admin Brand Logo</label>
                        <input type="file" name="site_logo" class="form-control" style="height: 46px; padding-top: 10px; border-radius: 10px; background: #F8FAFC;">
                    </div>

                    <button type="submit" class="btn btn-dark fw-semibold w-100" style="height: 44px; border-radius: 10px;">
                        Update Global Admin Brand
                    </button>
                </form>
            </div>
        </div>

        <!-- Right Column: My Profile & Change Password -->
        <div class="col-lg-6">
            <!-- Profile Update Card -->
            <div class="sb-table-card p-4 mb-4">
                <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                    <div>
                        <h5 class="fw-bold mb-1" style="color: #0F172A;">My Profile Settings</h5>
                        <p class="text-muted small mb-0">Update your personal information, avatar and 2FA preference.</p>
                    </div>
                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" style="width: 48px; height: 48px; border-radius: 50%; object-fit: cover; border: 2px solid #FFE4EC;">
                </div>

                <form action="{{ route('admin.settings.profile') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-dark">Full Name</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control" style="height: 46px; border-radius: 10px; background: #F8FAFC;" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-dark">Email Address</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control" style="height: 46px; border-radius: 10px; background: #F8FAFC;" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-dark">Phone Number</label>
                            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="form-control" style="height: 46px; border-radius: 10px; background: #F8FAFC;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-dark">Profile Avatar</label>
                            <input type="file" name="avatar" class="form-control" style="height: 46px; padding-top: 10px; border-radius: 10px; background: #F8FAFC;">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="d-flex align-items-center gap-2" style="cursor: pointer;">
                            <input type="checkbox" name="two_factor_enabled" value="1" @checked($user->two_factor_enabled) style="accent-color: #EC134A; width: 17px; height: 17px;">
                            <span class="small fw-semibold text-dark">Enable Two-Factor Authentication (2FA)</span>
                        </label>
                    </div>

                    <button type="submit" class="btn text-white fw-semibold w-100" style="height: 46px; background: #EC134A; border-radius: 10px; box-shadow: 0 4px 12px rgba(236, 19, 74, 0.22);">
                        Save Profile Changes <i class="bi bi-arrow-right ms-1"></i>
                    </button>
                </form>
            </div>

            <!-- Change Password Card -->
            <div class="sb-table-card p-4">
                <div class="mb-3 pb-2 border-bottom">
                    <h5 class="fw-bold mb-1" style="color: #0F172A;">Change Your Password</h5>
                    <p class="text-muted small mb-0">Ensure your account is using a strong password to stay secure.</p>
                </div>

                <form action="{{ route('admin.settings.password') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-dark">Current Password</label>
                        <input type="password" name="current_password" class="form-control" placeholder="Enter current password" style="height: 46px; border-radius: 10px; background: #F8FAFC;" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-dark">New Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Enter new password (min 8 chars)" style="height: 46px; border-radius: 10px; background: #F8FAFC;" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold small text-dark">Confirm New Password</label>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm new password" style="height: 46px; border-radius: 10px; background: #F8FAFC;" required>
                    </div>

                    <button type="submit" class="btn text-white fw-semibold w-100" style="height: 46px; background: #EC134A; border-radius: 10px; box-shadow: 0 4px 12px rgba(236, 19, 74, 0.22);">
                        Update Password <i class="bi bi-shield-check ms-1"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
