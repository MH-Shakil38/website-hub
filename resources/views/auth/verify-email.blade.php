@extends('auth.layouts.app')

@section('title', 'Verify Email - SiteBuilder')

@section('top_right')
    <a href="{{ route('logout') }}" class="logout-top-link">
        <i class="bi bi-box-arrow-right"></i>
        <span>Logout</span>
    </a>
@endsection

@section('left_content')
    <div>
        <div class="accent-bar"></div>
        <h2 class="auth-title">Verify your email address</h2>
        <p class="auth-subtitle" style="margin-bottom: 22px;">Before you continue, please verify your email address by clicking the link we sent to your inbox.</p>

        <!-- Centered Envelope Illustration -->
        <div style="display: flex; justify-content: center; margin: clamp(4px, 1vh, 12px) 0 clamp(6px, 1.4vh, 14px);">
            <svg style="width: clamp(115px, 16vh, 155px); height: auto;" viewBox="0 0 160 125" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="80" cy="65" r="52" fill="#FFF1F4"/>
                <!-- Celebratory Red Lines -->
                <line x1="128" y1="24" x2="136" y2="16" stroke="#EC134A" stroke-width="2.5" stroke-linecap="round"/>
                <line x1="134" y1="34" x2="144" y2="31" stroke="#EC134A" stroke-width="2.5" stroke-linecap="round"/>
                <!-- Open Envelope -->
                <path d="M36 56 L80 28 L124 56 V102 C124 106, 120 110, 116 110 H44 C40 110, 36 106, 36 102 V56 Z" fill="#FBCFE8"/>
                <!-- White Letter Card -->
                <rect x="46" y="22" width="68" height="68" rx="8" fill="#FFFFFF" stroke="#F1F5F9"/>
                <circle cx="80" cy="46" r="14" fill="#10B981"/>
                <path d="M74 46 L78 50 L86 42" stroke="#FFFFFF" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                <rect x="58" y="68" width="44" height="5" rx="2.5" fill="#E2E8F0"/>
                <rect x="64" y="77" width="32" height="5" rx="2.5" fill="#E2E8F0"/>
                <!-- Envelope Flaps -->
                <path d="M36 56 L80 86 L36 108 Z" fill="#FCE7F3"/>
                <path d="M124 56 L80 86 L124 108 Z" fill="#FBD5E5"/>
                <path d="M36 108 L80 80 L124 108 H36 Z" fill="#FDF2F8"/>
            </svg>
        </div>

        <div style="text-align: center;">
            <p style="font-size: 13px; font-weight: 600; color: #1E293B; margin-bottom: clamp(6px, 1vh, 10px);">We have sent a verification link to:</p>

            <div style="display: inline-flex; align-items: center; justify-content: center; gap: 10px; background: #FFF5F7; border: 1px solid #FFE4EC; border-radius: 10px; padding: clamp(7px, 1.2vh, 11px) 24px; margin-bottom: clamp(10px, 1.6vh, 16px); min-width: 250px;">
                <i class="bi bi-envelope" style="color: #EC134A; font-size: 15px;"></i>
                <strong style="font-size: 14px; color: #0F172A;">{{ auth()->user()->email ?? 'you@example.com' }}</strong>
            </div>

            <p style="font-size: 12.5px; color: #64748B; line-height: 1.48; max-width: 390px; margin: 0 auto clamp(12px, 2vh, 22px);">
                Please check your inbox and click the verification link to verify your email address. If you don't see the email, please check your spam folder.
            </p>
        </div>

        <form action="{{ route('verification.send') }}" method="POST">
            @csrf
            <button type="submit" class="btn-sb-primary">
                <i class="bi bi-send-fill" style="font-size: 13px;"></i>
                <span>Resend verification email</span>
            </button>
        </form>

        <div class="bottom-helper-text">
            Changed your email address? <a href="#">Contact support</a>
        </div>
    </div>
@endsection

@section('right_content')
    <h2 class="right-heading">One step closer<br>to your amazing website</h2>
    <p class="right-subtitle">Verify your email to unlock all features and start building beautiful websites with {{ $globalSiteName ?? 'SiteBuilder' }}.</p>

    <div class="feature-list">
        <div class="feature-item">
            <div class="feature-icon-box"><i class="bi bi-shield-shaded"></i></div>
            <div class="feature-text">
                <h5>Secure &amp; Safe</h5>
                <p>We protect your data and keep your account secure.</p>
            </div>
        </div>
        <div class="feature-item">
            <div class="feature-icon-box"><i class="bi bi-lightning-charge-fill"></i></div>
            <div class="feature-text">
                <h5>Quick Setup</h5>
                <p>Get started in minutes after verification.</p>
            </div>
        </div>
        <div class="feature-item">
            <div class="feature-icon-box"><i class="bi bi-grid-fill"></i></div>
            <div class="feature-text">
                <h5>Full Access</h5>
                <p>Unlock all tools and features.</p>
            </div>
        </div>
        <div class="feature-item">
            <div class="feature-icon-box"><i class="bi bi-headset"></i></div>
            <div class="feature-text">
                <h5>Dedicated Support</h5>
                <p>Our team is always here to help.</p>
            </div>
        </div>
    </div>
@endsection

@section('right_illustration')
    <!-- 3D Laptop Email Verified Illustration (Matching Verify Email Screenshot) -->
    <svg viewBox="20 5 520 275" preserveAspectRatio="xMidYMax meet" width="100%" style="display: block;" fill="none" xmlns="http://www.w3.org/2000/svg">
        <defs>
            <filter id="verShadow" x="-12%" y="-12%" width="130%" height="130%">
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
        <g transform="translate(115, 34) skewY(-3)" filter="url(#verShadow)">
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

            <!-- Content blocks inside Laptop -->
            <rect x="115" y="32" width="160" height="72" rx="10" fill="#F8FAFC"/>
            <rect x="128" y="45" width="42" height="42" rx="8" fill="#CBD5E1"/>
            <rect x="180" y="50" width="76" height="8" rx="4" fill="#E2E8F0"/>
            <rect x="180" y="65" width="58" height="7" rx="3.5" fill="#E2E8F0"/>

            <rect x="115" y="118" width="108" height="70" rx="10" fill="#F8FAFC"/>
            <rect x="127" y="136" width="28" height="28" rx="6" fill="#60A5FA"/>
            <rect x="163" y="142" width="46" height="6" rx="3" fill="#CBD5E1"/>
            <rect x="163" y="154" width="34" height="6" rx="3" fill="#E2E8F0"/>

            <rect x="235" y="118" width="108" height="70" rx="10" fill="#F8FAFC"/>
            <rect x="247" y="136" width="28" height="28" rx="6" fill="#60A5FA"/>
            <rect x="283" y="142" width="46" height="6" rx="3" fill="#CBD5E1"/>
            <rect x="283" y="154" width="34" height="6" rx="3" fill="#E2E8F0"/>
        </g>

        <!-- Laptop Base -->
        <path d="M90 262 L490 245 L510 256 L70 274 Z" fill="#E2E8F0"/>

        <!-- Floating Email Verified Card Right -->
        <g transform="translate(390, 62) skewY(-3)" filter="url(#verShadow)">
            <rect x="0" y="0" width="142" height="106" rx="14" fill="#FFFFFF"/>
            <circle cx="42" cy="34" r="16" fill="#10B981"/>
            <path d="M35 34 L40 39 L49 30" stroke="#FFFFFF" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
            <text x="22" y="68" font-family="Inter, sans-serif" font-size="11.5" font-weight="800" fill="#0F172A">Email Verified</text>
            <rect x="22" y="77" width="94" height="6" rx="3" fill="#E2E8F0"/>
            <rect x="22" y="88" width="68" height="5" rx="2.5" fill="#F1F5F9"/>
        </g>

        <!-- Paper Plane Top Right -->
        <g transform="translate(495, 10)">
            <path d="M0 18 L36 0 L23 31 L14 21 L0 18 Z" fill="#EC134A"/>
        </g>
    </svg>
@endsection
