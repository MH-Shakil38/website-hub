@extends('auth.layouts.app')

@section('title', 'Two-Factor Authentication - SiteBuilder')

@push('styles')
<style>
    .otp-inputs {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        gap: 10px;
        margin-bottom: clamp(10px, 1.8vh, 18px);
    }

    .otp-box {
        width: 100%;
        height: clamp(44px, 6vh, 56px);
        text-align: center;
        font-size: clamp(17px, 2.2vh, 20px);
        font-weight: 700;
        color: var(--sb-dark);
        background: #F8FAFC;
        border: 1.5px solid var(--sb-border);
        border-radius: 11px;
        outline: none;
        transition: all 0.15s;
    }

    .otp-box:focus,
    .otp-box.active-box {
        background: #FFFFFF;
        border-color: var(--sb-primary);
        box-shadow: 0 0 0 3px rgba(236, 19, 74, 0.1);
    }
</style>
@endpush

@section('top_right')
    <a href="{{ route('logout') }}" class="logout-top-link">
        <i class="bi bi-box-arrow-right"></i>
        <span>Logout</span>
    </a>
@endsection

@section('left_content')
    <div>
        <a href="{{ route('login') }}" class="back-link">
            <i class="bi bi-arrow-left"></i>
            <span>Back to login</span>
        </a>

        <div class="accent-bar"></div>
        <h2 class="auth-title">Two-Factor Authentication</h2>
        <p class="auth-subtitle">For your security, please enter the 6-digit code from your authenticator app to continue.</p>

        <form action="{{ route('two-factor.verify') }}" method="POST">
            @csrf

            <div class="otp-inputs">
                <input type="text" name="digits[]" maxlength="1" class="otp-box active-box" value="2" oninput="moveToNext(this, 0)">
                <input type="text" name="digits[]" maxlength="1" class="otp-box" value="7" oninput="moveToNext(this, 1)">
                <input type="text" name="digits[]" maxlength="1" class="otp-box" value="4" oninput="moveToNext(this, 2)">
                <input type="text" name="digits[]" maxlength="1" class="otp-box" value="9" oninput="moveToNext(this, 3)">
                <input type="text" name="digits[]" maxlength="1" class="otp-box" value="1" oninput="moveToNext(this, 4)">
                <input type="text" name="digits[]" maxlength="1" class="otp-box" value="6" oninput="moveToNext(this, 5)">
            </div>

            <label class="checkbox-row">
                <input type="checkbox" name="remember_device" checked>
                <span>Remember this device for 30 days</span>
            </label>

            <button type="submit" class="btn-sb-primary">
                <span>Verify code</span>
                <i class="bi bi-arrow-right"></i>
            </button>

            <div class="or-divider">
                <span>OR</span>
            </div>

            <button type="button" class="btn-sb-outline">
                <i class="bi bi-grid-3x3-gap" style="font-size: 16px;"></i>
                <span>Use a recovery code</span>
            </button>

            <div class="soft-info-box">
                <i class="bi bi-shield-shaded info-icon"></i>
                <div>
                    <h6>Can't access your authenticator app?</h6>
                    <p>Use a recovery code or contact your administrator for assistance.</p>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('right_content')
    <h2 class="right-heading">Secure your account</h2>
    <p class="right-subtitle">Two-factor authentication adds an extra layer of security to keep your website and data safe.</p>

    <div class="feature-list">
        <div class="feature-item">
            <div class="feature-icon-box"><i class="bi bi-shield-shaded"></i></div>
            <div class="feature-text">
                <h5>Enhanced Security</h5>
                <p>Protect your account from unauthorized access.</p>
            </div>
        </div>
        <div class="feature-item">
            <div class="feature-icon-box"><i class="bi bi-phone"></i></div>
            <div class="feature-text">
                <h5>Use Any Authenticator App</h5>
                <p>Works with Google Authenticator, Authy, etc.</p>
            </div>
        </div>
        <div class="feature-item">
            <div class="feature-icon-box"><i class="bi bi-lock"></i></div>
            <div class="feature-text">
                <h5>Simple and Fast</h5>
                <p>Just enter a 6-digit code to continue.</p>
            </div>
        </div>
        <div class="feature-item">
            <div class="feature-icon-box"><i class="bi bi-check-circle-fill"></i></div>
            <div class="feature-text">
                <h5>Keep Your Data Safe</h5>
                <p>Your websites and content stay protected.</p>
            </div>
        </div>
    </div>
@endsection

@section('right_illustration')
    <!-- 3D Laptop + Authenticator Phone Illustration (Matching 2FA Screenshot) -->
    <svg viewBox="15 10 525 275" preserveAspectRatio="xMidYMax meet" width="100%" style="display: block;" fill="none" xmlns="http://www.w3.org/2000/svg">
        <defs>
            <filter id="tfaShadow" x="-12%" y="-12%" width="130%" height="130%">
                <feDropShadow dx="0" dy="14" stdDeviation="14" flood-color="#0F172A" flood-opacity="0.12"/>
            </filter>
        </defs>

        <!-- Potted Plant Left -->
        <g transform="translate(28, 120)">
            <path d="M40 90 C15 65, 5 35, 18 12 C32 25, 45 55, 45 90 Z" fill="#3B873E"/>
            <path d="M45 88 C42 48, 52 15, 72 4 C76 32, 65 62, 50 90 Z" fill="#52A447"/>
            <path d="M52 92 C70 68, 95 52, 108 60 C95 82, 72 92, 52 94 Z" fill="#68B85D"/>
            <path d="M38 92 C18 75, -5 72, -8 84 C8 98, 25 98, 40 95 Z" fill="#469642"/>
            <path d="M22 92 H72 L66 142 C65 148, 60 152, 54 152 H40 C34 152, 29 148, 28 142 L22 92 Z" fill="#F8FAFC" stroke="#E2E8F0"/>
            <ellipse cx="47" cy="92" rx="25" ry="4" fill="#CBD5E1"/>
        </g>

        <!-- 3D Laptop Screen -->
        <g transform="translate(105, 42) skewY(-3)" filter="url(#tfaShadow)">
            <rect x="0" y="0" width="320" height="215" rx="16" fill="#FFFFFF" stroke="#F1F5F9" stroke-width="2"/>
            <rect x="12" y="12" width="86" height="190" rx="10" fill="#FFFFFF"/>
            <circle cx="24" cy="28" r="6" fill="#EC134A"/>
            <text x="34" y="32" font-family="Inter, sans-serif" font-size="9.5" font-weight="800" fill="#0F172A">{{ $globalSiteName ?? 'SiteBuilder' }}</text>
            <rect x="14" y="42" width="80" height="20" rx="6" fill="#FFE6ED"/>
            <text x="32" y="55" font-family="Inter, sans-serif" font-size="7.5" font-weight="700" fill="#EC134A">Dashboard</text>
            <g fill="#64748B" font-family="Inter, sans-serif" font-size="7.5" font-weight="500">
                <text x="32" y="75">Pages</text>
                <text x="32" y="95">Menu Builder</text>
                <text x="32" y="115">Slider</text>
                <text x="32" y="135">Media Library</text>
                <text x="32" y="155">Themes</text>
                <text x="32" y="175">Settings</text>
            </g>
            <rect x="110" y="28" width="190" height="165" rx="10" fill="#F8FAFC"/>
        </g>

        <!-- Red Shield Lock Center -->
        <g transform="translate(275, 145) rotate(-4)" filter="url(#tfaShadow)">
            <path d="M46 0 L92 16 V54 C92 86, 72 110, 46 122 C20 110, 0 86, 0 54 V16 L46 0 Z" fill="#EC134A" stroke="#FFFFFF" stroke-width="4"/>
            <rect x="31" y="52" width="30" height="24" rx="5" fill="#FFFFFF"/>
            <path d="M37 52 V44 C37 37, 55 37, 55 44 V52" stroke="#FFFFFF" stroke-width="4" stroke-linecap="round"/>
            <circle cx="46" cy="62" r="2.5" fill="#EC134A"/>
            <rect x="44.8" y="64" width="2.4" height="5" fill="#EC134A"/>
        </g>

        <!-- 3D Smartphone Authenticator App Right -->
        <g transform="translate(382, 22) rotate(3)" filter="url(#tfaShadow)">
            <rect x="0" y="0" width="142" height="250" rx="24" fill="#FFFFFF" stroke="#E2E8F0" stroke-width="3"/>
            <rect x="48" y="10" width="46" height="5" rx="2.5" fill="#E2E8F0"/>

            <!-- QR Scan Icon -->
            <g transform="translate(46, 38)" stroke="#EC134A" stroke-width="2.5">
                <path d="M0 12 V4 C0 2, 2 0, 4 0 H12" fill="none"/>
                <path d="M38 0 H46 C48 0, 50 2, 50 4 V12" fill="none"/>
                <path d="M0 38 V46 C0 48, 2 50, 4 50 H12" fill="none"/>
                <path d="M38 50 H46 C48 50, 50 48, 50 46 V38" fill="none"/>
                <rect x="10" y="10" width="11" height="11" rx="2" fill="none"/>
                <rect x="29" y="10" width="11" height="11" rx="2" fill="none"/>
                <rect x="10" y="29" width="11" height="11" rx="2" fill="none"/>
                <rect x="29" y="29" width="11" height="11" rx="2" fill="none"/>
            </g>

            <text x="71" y="114" text-anchor="middle" font-family="Inter, sans-serif" font-size="10.5" font-weight="700" fill="#0F172A">Authenticator App</text>

            <!-- OTP Display Box -->
            <rect x="16" y="132" width="110" height="40" rx="10" fill="#FFF0F4"/>
            <text x="71" y="158" text-anchor="middle" font-family="Inter, sans-serif" font-size="20" font-weight="800" letter-spacing="2" fill="#0F172A">274916</text>

            <!-- Timer Bar -->
            <rect x="24" y="186" width="94" height="5" rx="2.5" fill="#F1F5F9"/>
            <rect x="24" y="186" width="44" height="5" rx="2.5" fill="#EC134A"/>
        </g>
    </svg>
@endsection

@push('scripts')
<script>
    function moveToNext(el, index) {
        const boxes = document.querySelectorAll('.otp-box');
        boxes.forEach(b => b.classList.remove('active-box'));
        if (el.value.length === 1 && index < boxes.length - 1) {
            boxes[index + 1].focus();
            boxes[index + 1].classList.add('active-box');
        } else {
            el.classList.add('active-box');
        }
    }
</script>
@endpush
