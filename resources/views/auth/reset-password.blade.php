@extends('auth.layouts.app')

@section('title', 'Reset Password - SiteBuilder')

@push('styles')
<style>
    .password-rules {
        list-style: none;
        margin: clamp(6px, 1.2vh, 12px) 0 clamp(10px, 1.8vh, 18px);
        padding: 0;
        display: flex;
        flex-direction: column;
        gap: clamp(4px, 0.8vh, 7px);
    }

    .password-rules li {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: clamp(11.5px, 1.4vh, 13px);
        color: #64748B;
        transition: color 0.15s;
    }

    .password-rules li .rule-icon {
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background: #E2E8F0;
        color: #FFFFFF;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 10.5px;
        transition: background 0.15s;
    }

    .password-rules li.valid {
        color: #16A34A;
    }

    .password-rules li.valid .rule-icon {
        background: #22C55E;
    }
</style>
@endpush

@section('left_content')
    <div>
        <a href="{{ route('login') }}" class="back-link">
            <i class="bi bi-arrow-left"></i>
            <span>Back to login</span>
        </a>

        <div class="accent-bar"></div>
        <h2 class="auth-title">Reset your password</h2>
        <p class="auth-subtitle">Enter your new password below to complete the reset process and regain access to your account.</p>

        <form action="{{ route('password.update') }}" method="POST">
            @csrf
            <input type="hidden" name="token" value="{{ $token ?? 'demo-token' }}">
            <input type="hidden" name="email" value="{{ $email ?? 'you@example.com' }}">

            @if($errors->any())
                <div class="soft-info-box" style="margin-top: 0; margin-bottom: 12px; padding: 9px 12px;">
                    <i class="bi bi-exclamation-circle-fill info-icon"></i>
                    <p style="color: #BE123C;">{{ $errors->first() }}</p>
                </div>
            @endif

            <div class="form-group" style="margin-bottom: 8px;">
                <div class="form-label-row">
                    <label class="form-label" for="new_password">New password</label>
                </div>
                <div class="input-wrapper">
                    <span class="input-icon-left"><i class="bi bi-lock"></i></span>
                    <input type="password" id="new_password" name="password" class="form-control-sb" placeholder="Enter your new password" oninput="checkPasswordRules(this.value)" required>
                    <button type="button" class="input-icon-right" onclick="togglePasswordVisibility('new_password', this)">
                        <i class="bi bi-eye-slash"></i>
                    </button>
                </div>
            </div>

            <ul class="password-rules">
                <li id="rule-length">
                    <span class="rule-icon"><i class="bi bi-check"></i></span>
                    <span>At least 8 characters</span>
                </li>
                <li id="rule-number">
                    <span class="rule-icon"><i class="bi bi-check"></i></span>
                    <span>Include a number</span>
                </li>
                <li id="rule-upper">
                    <span class="rule-icon"><i class="bi bi-check"></i></span>
                    <span>Include an uppercase letter</span>
                </li>
                <li id="rule-special">
                    <span class="rule-icon"><i class="bi bi-check"></i></span>
                    <span>Include a special character (e.g. !@#$%)</span>
                </li>
            </ul>

            <div class="form-group" style="margin-bottom: 24px;">
                <div class="form-label-row">
                    <label class="form-label" for="password_confirmation">Confirm new password</label>
                </div>
                <div class="input-wrapper">
                    <span class="input-icon-left"><i class="bi bi-lock"></i></span>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control-sb" placeholder="Confirm your new password" required>
                    <button type="button" class="input-icon-right" onclick="togglePasswordVisibility('password_confirmation', this)">
                        <i class="bi bi-eye-slash"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn-sb-primary">
                <span>Reset password</span>
                <i class="bi bi-arrow-right"></i>
            </button>
        </form>
    </div>
@endsection

@section('right_content')
    <h2 class="right-heading">Secure your account</h2>
    <p class="right-subtitle">A strong password keeps your website and data safe. Choose a unique password that you don't use on other sites.</p>

    <div class="feature-list">
        <div class="feature-item">
            <div class="feature-icon-box"><i class="bi bi-shield-shaded"></i></div>
            <div class="feature-text">
                <h5>Keep Your Data Safe</h5>
                <p>Your websites and content stay protected.</p>
            </div>
        </div>
        <div class="feature-item">
            <div class="feature-icon-box"><i class="bi bi-lock"></i></div>
            <div class="feature-text">
                <h5>Prevent Unauthorized Access</h5>
                <p>A strong password blocks unwanted access.</p>
            </div>
        </div>
        <div class="feature-item">
            <div class="feature-icon-box"><i class="bi bi-people"></i></div>
            <div class="feature-text">
                <h5>Stay in Control</h5>
                <p>Keep managing your websites with confidence.</p>
            </div>
        </div>
        <div class="feature-item">
            <div class="feature-icon-box"><i class="bi bi-clock"></i></div>
            <div class="feature-text">
                <h5>Easy and Secure</h5>
                <p>Reset and continue in just a few seconds.</p>
            </div>
        </div>
    </div>
@endsection

@section('right_illustration')
    <!-- 3D Laptop Security Lock Illustration (Matching Reset Password Screenshot) -->
    <svg viewBox="20 12 520 278" preserveAspectRatio="xMidYMax meet" width="100%" style="display: block;" fill="none" xmlns="http://www.w3.org/2000/svg">
        <defs>
            <filter id="secShadow" x="-12%" y="-12%" width="130%" height="130%">
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
        <g transform="translate(120, 32) skewY(-3)" filter="url(#secShadow)">
            <rect x="0" y="0" width="365" height="225" rx="16" fill="#FFFFFF" stroke="#F1F5F9" stroke-width="2"/>
            <!-- Sidebar -->
            <rect x="12" y="12" width="90" height="200" rx="10" fill="#FFFFFF"/>
            <circle cx="24" cy="28" r="6" fill="#EC134A"/>
            <text x="34" y="32" font-family="Inter, sans-serif" font-size="9.5" font-weight="800" fill="#0F172A">{{ $globalSiteName ?? 'SiteBuilder' }}</text>
            <g fill="#64748B" font-family="Inter, sans-serif" font-size="7.5" font-weight="500">
                <text x="32" y="54" fill="#EC134A" font-weight="700">Dashboard</text>
                <text x="32" y="74">Pages</text>
                <text x="32" y="94">Menu Builder</text>
                <text x="32" y="114">Slider</text>
                <text x="32" y="134">Media Library</text>
                <text x="32" y="154">Themes</text>
                <text x="32" y="174">Settings</text>
            </g>

            <!-- Right Panel inside Laptop -->
            <rect x="112" y="20" width="238" height="190" rx="12" fill="#F8FAFC"/>
            <!-- Soft Pink Circle + Big Red Padlock -->
            <circle cx="231" cy="92" r="44" fill="#FFE4EC"/>
            <rect x="213" y="88" width="36" height="28" rx="6" fill="#EC134A"/>
            <path d="M221 88 V78 C221 71, 241 71, 241 78 V88" stroke="#EC134A" stroke-width="5" stroke-linecap="round"/>
            <circle cx="231" cy="100" r="3" fill="#FFFFFF"/>
            <rect x="229.5" y="102" width="3" height="6" rx="1.5" fill="#FFFFFF"/>

            <!-- Password Masked Dots Bar -->
            <g filter="url(#secShadow)">
                <rect x="155" y="148" width="145" height="32" rx="9" fill="#FFF0F4"/>
                <g fill="#EC134A">
                    <circle cx="173" cy="164" r="3.5"/>
                    <circle cx="188" cy="164" r="3.5"/>
                    <circle cx="203" cy="164" r="3.5"/>
                    <circle cx="218" cy="164" r="3.5"/>
                    <circle cx="233" cy="164" r="3.5"/>
                    <circle cx="248" cy="164" r="3.5"/>
                    <circle cx="263" cy="164" r="3.5"/>
                </g>
            </g>

            <!-- Green Verified Circle Badge -->
            <circle cx="298" cy="144" r="15" fill="#10B981"/>
            <path d="M292 144 L296 148 L304 140" stroke="#FFFFFF" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
        </g>

        <!-- Laptop Base -->
        <path d="M95 262 L495 245 L515 256 L75 274 Z" fill="#E2E8F0"/>
        <path d="M75 274 H515 C512 282, 502 285, 488 285 H102 C88 285, 78 282, 75 274 Z" fill="#CBD5E1"/>

        <!-- Floating Shield Card Right -->
        <g transform="translate(445, 72) skewY(-3)" filter="url(#secShadow)">
            <rect x="0" y="0" width="76" height="76" rx="16" fill="#FFFFFF"/>
            <path d="M38 16 L56 23 V38 C56 50, 48 58, 38 62 C28 58, 20 50, 20 38 V23 L38 16 Z" fill="#EC134A"/>
            <path d="M31 39 L36 44 L46 34" stroke="#FFFFFF" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
        </g>
    </svg>
@endsection

@push('scripts')
<script>
    function checkPasswordRules(val) {
        document.getElementById('rule-length').classList.toggle('valid', val.length >= 8);
        document.getElementById('rule-number').classList.toggle('valid', /\d/.test(val));
        document.getElementById('rule-upper').classList.toggle('valid', /[A-Z]/.test(val));
        document.getElementById('rule-special').classList.toggle('valid', /[!@#$%^&*(),.?":{}|<>_\-]/.test(val));
    }
</script>
@endpush
