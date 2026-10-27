@extends('auth.layouts.app')

@section('title', 'Confirm Password - SiteBuilder')

@section('left_content')
    <div>
        <a href="{{ url()->previous() }}" class="back-link">
            <i class="bi bi-arrow-left"></i>
            <span>Back to previous page</span>
        </a>

        <div class="accent-bar"></div>
        <h2 class="auth-title">Confirm your password</h2>
        <p class="auth-subtitle">For your security, please confirm your password to continue.</p>

        <form action="{{ route('password.confirm.submit') }}" method="POST">
            @csrf

            @if($errors->any())
                <div class="soft-info-box" style="margin-top: 0; margin-bottom: 14px; padding: 10px 14px;">
                    <i class="bi bi-exclamation-circle-fill info-icon"></i>
                    <p style="color: #BE123C;">{{ $errors->first() }}</p>
                </div>
            @endif

            <div class="form-group" style="margin-bottom: 24px;">
                <div class="form-label-row">
                    <label class="form-label" for="confirm_pass">Password</label>
                </div>
                <div class="input-wrapper">
                    <span class="input-icon-left"><i class="bi bi-lock"></i></span>
                    <input type="password" id="confirm_pass" name="password" class="form-control-sb" placeholder="Enter your password" required>
                    <button type="button" class="input-icon-right" onclick="togglePasswordVisibility('confirm_pass', this)">
                        <i class="bi bi-eye-slash"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn-sb-primary">
                <span>Confirm</span>
                <i class="bi bi-arrow-right"></i>
            </button>
        </form>
    </div>
@endsection

@section('right_content')
    <h2 class="right-heading">Your security matters</h2>
    <p class="right-subtitle">We take your account security seriously.<br>Confirm your password to continue safely.</p>

    <div class="feature-list">
        <div class="feature-item">
            <div class="feature-icon-box"><i class="bi bi-shield-shaded"></i></div>
            <div class="feature-text">
                <h5>Secure &amp; Protected</h5>
                <p>Keep your data safe and secure.</p>
            </div>
        </div>
        <div class="feature-item">
            <div class="feature-icon-box"><i class="bi bi-lock-fill"></i></div>
            <div class="feature-text">
                <h5>Prevent Unauthorized Access</h5>
                <p>Your password helps protect your account.</p>
            </div>
        </div>
        <div class="feature-item">
            <div class="feature-icon-box"><i class="bi bi-people-fill"></i></div>
            <div class="feature-text">
                <h5>Your Privacy</h5>
                <p>We never share your personal information.</p>
            </div>
        </div>
        <div class="feature-item">
            <div class="feature-icon-box"><i class="bi bi-clock"></i></div>
            <div class="feature-text">
                <h5>Quick and Easy</h5>
                <p>Just confirm your password to continue.</p>
            </div>
        </div>
    </div>
@endsection

@section('right_illustration')
    <!-- 3D Laptop Confirm Password Illustration (Matching Confirm Password Screenshot) -->
    <svg viewBox="20 12 520 278" preserveAspectRatio="xMidYMax meet" width="100%" style="display: block;" fill="none" xmlns="http://www.w3.org/2000/svg">
        <defs>
            <filter id="confShadow" x="-12%" y="-12%" width="130%" height="130%">
                <feDropShadow dx="0" dy="14" stdDeviation="14" flood-color="#0F172A" flood-opacity="0.11"/>
            </filter>
        </defs>

        <!-- Potted Plant Left -->
        <g transform="translate(36, 120)">
            <path d="M40 90 C15 65, 5 35, 18 12 C32 25, 45 55, 45 90 Z" fill="#3B873E"/>
            <path d="M45 88 C42 48, 52 15, 72 4 C76 32, 65 62, 50 90 Z" fill="#52A447"/>
            <path d="M52 92 C70 68, 95 52, 108 60 C95 82, 72 92, 52 94 Z" fill="#68B85D"/>
            <path d="M38 92 C18 75, -5 72, -8 84 C8 98, 25 98, 40 95 Z" fill="#469642"/>
            <path d="M22 92 H72 L66 142 C65 148, 60 152, 54 152 H40 C34 152, 29 148, 28 142 L22 92 Z" fill="#F8FAFC" stroke="#E2E8F0"/>
            <ellipse cx="47" cy="92" rx="25" ry="4" fill="#CBD5E1"/>
        </g>

        <!-- 3D Laptop Screen -->
        <g transform="translate(120, 32) skewY(-3)" filter="url(#confShadow)">
            <rect x="0" y="0" width="365" height="225" rx="16" fill="#FFFFFF" stroke="#F1F5F9" stroke-width="2"/>
            <!-- Sidebar -->
            <rect x="12" y="12" width="90" height="200" rx="10" fill="#FFFFFF"/>
            <circle cx="24" cy="28" r="6" fill="#EC134A"/>
            <text x="34" y="32" font-family="Inter, sans-serif" font-size="9.5" font-weight="800" fill="#0F172A">{{ $globalSiteName ?? 'SiteBuilder' }}</text>
            <rect x="14" y="42" width="82" height="20" rx="6" fill="#FFE6ED"/>
            <text x="32" y="55" font-family="Inter, sans-serif" font-size="7.5" font-weight="700" fill="#EC134A">Dashboard</text>
            <g fill="#64748B" font-family="Inter, sans-serif" font-size="7.5" font-weight="500">
                <text x="32" y="75">Pages</text>
                <text x="32" y="95">Menu Builder</text>
                <text x="32" y="115">Slider</text>
                <text x="32" y="135">Media Library</text>
                <text x="32" y="155">Themes</text>
                <text x="32" y="175">Settings</text>
            </g>

            <!-- User Avatar Top Right of Laptop -->
            <circle cx="342" cy="22" r="9" fill="#DBEAFE"/>
            <circle cx="342" cy="19" r="3.5" fill="#1E293B"/>
            <path d="M336 28 C337 24, 347 24, 348 28" fill="#1E293B"/>

            <!-- Right Panel inside Laptop -->
            <rect x="112" y="34" width="238" height="176" rx="12" fill="#F8FAFC"/>
            <!-- Soft Pink Circle + Big Red Padlock -->
            <circle cx="231" cy="96" r="44" fill="#FFE4EC"/>
            <rect x="213" y="92" width="36" height="28" rx="6" fill="#EC134A"/>
            <path d="M221 92 V82 C221 75, 241 75, 241 82 V92" stroke="#EC134A" stroke-width="5" stroke-linecap="round"/>
            <circle cx="231" cy="104" r="3" fill="#FFFFFF"/>
            <rect x="229.5" y="106" width="3" height="6" rx="1.5" fill="#FFFFFF"/>

            <!-- Password Masked Dots Bar -->
            <g filter="url(#confShadow)">
                <rect x="155" y="152" width="145" height="32" rx="9" fill="#FFF0F4"/>
                <g fill="#EC134A">
                    <circle cx="175" cy="168" r="3.5"/>
                    <circle cx="192" cy="168" r="3.5"/>
                    <circle cx="209" cy="168" r="3.5"/>
                    <circle cx="226" cy="168" r="3.5"/>
                    <circle cx="243" cy="168" r="3.5"/>
                    <circle cx="260" cy="168" r="3.5"/>
                </g>
            </g>

            <!-- Green Verified Circle Badge -->
            <circle cx="298" cy="148" r="15" fill="#10B981"/>
            <path d="M292 148 L296 152 L304 144" stroke="#FFFFFF" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
        </g>

        <!-- Laptop Base -->
        <path d="M95 262 L495 245 L515 256 L75 274 Z" fill="#E2E8F0"/>
        <path d="M75 274 H515 C512 282, 502 285, 488 285 H102 C88 285, 78 282, 75 274 Z" fill="#CBD5E1"/>

        <!-- Floating Shield Card Right -->
        <g transform="translate(445, 72) skewY(-3)" filter="url(#confShadow)">
            <rect x="0" y="0" width="76" height="76" rx="16" fill="#FFFFFF"/>
            <path d="M38 16 L56 23 V38 C56 50, 48 58, 38 62 C28 58, 20 50, 20 38 V23 L38 16 Z" fill="#EC134A"/>
            <path d="M31 39 L36 44 L46 34" stroke="#FFFFFF" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
        </g>
    </svg>
@endsection
