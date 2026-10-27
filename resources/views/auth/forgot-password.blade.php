@extends('auth.layouts.app')

@section('title', 'Forgot Password - SiteBuilder')

@section('left_content')
    <div>
        <a href="{{ route('login') }}" class="back-link">
            <i class="bi bi-arrow-left"></i>
            <span>Back to login</span>
        </a>

        <h2 class="auth-title">Forgot your password?</h2>
        <p class="auth-subtitle">No problem. Enter your email address and we'll send you a password reset link to get back into your account.</p>

        @if(session('status'))
            <div class="soft-info-box" style="margin-top: 0; margin-bottom: 20px; border-color: #BBF7D0; background: #F0FDF4;">
                <i class="bi bi-check-circle-fill info-icon" style="color: #16A34A;"></i>
                <p style="color: #15803D;">{{ session('status') }}</p>
            </div>
        @endif

        <form action="{{ route('password.email') }}" method="POST">
            @csrf

            <div class="form-group" style="margin-bottom: 24px;">
                <div class="form-label-row">
                    <label class="form-label" for="email">Email address</label>
                </div>
                <div class="input-wrapper">
                    <span class="input-icon-left"><i class="bi bi-envelope"></i></span>
                    <input type="email" id="email" name="email" class="form-control-sb" placeholder="you@example.com" value="{{ old('email') }}" required>
                </div>
            </div>

            <button type="submit" class="btn-sb-primary">
                <span>Send reset link</span>
                <i class="bi bi-arrow-right"></i>
            </button>

            <div class="soft-info-box">
                <i class="bi bi-info-circle info-icon"></i>
                <p>We'll send you an email with a link to reset your password.<br>If you don't see the email, please check your spam folder.</p>
            </div>
        </form>
    </div>
@endsection

@section('right_content')
    <h2 class="right-heading">Get back to building</h2>
    <p class="right-subtitle">Reset your password and continue managing your amazing websites.</p>

    <div class="feature-list">
        <div class="feature-item">
            <div class="feature-icon-box"><i class="bi bi-shield-check"></i></div>
            <div class="feature-text">
                <h5>Your Data is Safe</h5>
                <p>We use secure email verification.</p>
            </div>
        </div>
        <div class="feature-item">
            <div class="feature-icon-box"><i class="bi bi-envelope"></i></div>
            <div class="feature-text">
                <h5>Quick &amp; Easy</h5>
                <p>Reset your password in just a few clicks.</p>
            </div>
        </div>
        <div class="feature-item">
            <div class="feature-icon-box"><i class="bi bi-lock"></i></div>
            <div class="feature-text">
                <h5>Secure Access</h5>
                <p>Keep your account protected.</p>
            </div>
        </div>
        <div class="feature-item">
            <div class="feature-icon-box"><i class="bi bi-people"></i></div>
            <div class="feature-text">
                <h5>Back to Work</h5>
                <p>Continue building your websites.</p>
            </div>
        </div>
    </div>
@endsection

@section('right_illustration')
    <!-- 3D Envelope Reset Password Illustration (Matching Forgot Password Screenshot) -->
    <svg viewBox="35 10 490 295" preserveAspectRatio="xMidYMax meet" width="100%" style="display: block;" fill="none" xmlns="http://www.w3.org/2000/svg">
        <defs>
            <filter id="envShadow" x="-15%" y="-15%" width="135%" height="135%">
                <feDropShadow dx="0" dy="14" stdDeviation="14" flood-color="#0F172A" flood-opacity="0.11"/>
            </filter>
        </defs>

        <!-- Potted Plant Left -->
        <g transform="translate(52, 112)">
            <path d="M40 90 C15 65, 5 35, 18 12 C32 25, 45 55, 45 90 Z" fill="#3B873E"/>
            <path d="M45 88 C42 48, 52 15, 72 4 C76 32, 65 62, 50 90 Z" fill="#52A447"/>
            <path d="M52 92 C70 68, 95 52, 108 60 C95 82, 72 92, 52 94 Z" fill="#68B85D"/>
            <path d="M38 92 C18 75, -5 72, -8 84 C8 98, 25 98, 40 95 Z" fill="#469642"/>
            <path d="M22 92 H72 L66 142 C65 148, 60 152, 54 152 H40 C34 152, 29 148, 28 142 L22 92 Z" fill="#F8FAFC" stroke="#E2E8F0"/>
            <ellipse cx="47" cy="92" rx="25" ry="4" fill="#CBD5E1"/>
        </g>

        <!-- Background Browser Window Card -->
        <g transform="translate(155, 100) rotate(-3)" filter="url(#envShadow)">
            <rect x="0" y="0" width="340" height="165" rx="14" fill="#FFFFFF" stroke="#F1F5F9"/>
            <circle cx="18" cy="16" r="4" fill="#FB7185"/>
            <circle cx="30" cy="16" r="4" fill="#FBBF24"/>
            <circle cx="42" cy="16" r="4" fill="#4ADE80"/>
            <rect x="56" y="12" width="60" height="7" rx="3.5" fill="#F1F5F9"/>
            <rect x="22" y="42" width="52" height="9" rx="4.5" fill="#E2E8F0"/>
            <rect x="22" y="64" width="52" height="9" rx="4.5" fill="#E2E8F0"/>
            <rect x="22" y="86" width="52" height="9" rx="4.5" fill="#E2E8F0"/>
            <rect x="22" y="108" width="52" height="9" rx="4.5" fill="#E2E8F0"/>
            <rect x="22" y="130" width="52" height="9" rx="4.5" fill="#E2E8F0"/>
        </g>

        <!-- Open 3D Pink & White Envelope with Reset Password Card -->
        <g transform="translate(235, 58) rotate(4)" filter="url(#envShadow)">
            <!-- Envelope Back Flap -->
            <path d="M0 115 L115 48 L230 115 V225 C230 232, 224 238, 216 238 H14 C6 238, 0 232, 0 225 V115 Z" fill="#EC134A"/>
            <!-- White Reset Password Letter Inside -->
            <rect x="24" y="15" width="182" height="185" rx="14" fill="#FFFFFF"/>
            <!-- Red Circle Lock Icon -->
            <circle cx="115" cy="56" r="21" stroke="#EC134A" stroke-width="2.5" fill="#FFF5F7"/>
            <rect x="108" y="54" width="14" height="11" rx="2.5" stroke="#EC134A" stroke-width="2.2"/>
            <path d="M111 54 V50 C111 47.5, 119 47.5, 119 50 V54" stroke="#EC134A" stroke-width="2.2"/>
            <text x="115" y="96" text-anchor="middle" font-family="Inter, sans-serif" font-size="11.5" font-weight="800" fill="#0F172A">Reset Password</text>
            <rect x="52" y="109" width="126" height="8" rx="4" fill="#E2E8F0"/>
            <rect x="66" y="124" width="98" height="8" rx="4" fill="#E2E8F0"/>
            <rect x="56" y="144" width="118" height="26" rx="7" fill="#EC134A"/>

            <!-- Envelope Front Pocket Flaps -->
            <path d="M0 115 L115 182 L0 236 Z" fill="#FBF2F5"/>
            <path d="M230 115 L115 182 L230 236 Z" fill="#F5E1E7"/>
            <path d="M0 236 L115 172 L230 236 H0 Z" fill="#FCEEF2"/>
        </g>

        <!-- Paper Plane + Dashed Trail Top Right -->
        <g transform="translate(465, 22)">
            <path d="M0 22 L44 0 L28 38 L17 26 L0 22 Z" fill="#EC134A"/>
            <path d="M17 26 L44 0 L22 31 Z" fill="#BE0E3B"/>
            <path d="M-95 85 C-65 45, -35 75, -5 32" stroke="#F47296" stroke-width="1.6" stroke-dasharray="5 5" fill="none"/>
        </g>
    </svg>
@endsection
