@extends('auth.layouts.app')

@section('title', 'Sign In - ' . ($globalSiteName ?? 'SiteBuilder'))

@section('left_content')
    <div style="margin-top: 8px;">
        <h2 class="auth-title">Welcome back</h2>
        <p class="auth-subtitle">Sign in to your account to manage your website.</p>

        @if(session('status'))
            <div class="soft-info-box" style="margin-top: 0; margin-bottom: 14px; border-color: #BBF7D0; background: #F0FDF4; padding: 10px 14px;">
                <i class="bi bi-check-circle-fill info-icon" style="color: #16A34A;"></i>
                <p style="color: #15803D;">{{ session('status') }}</p>
            </div>
        @endif

        @if($errors->any())
            <div class="soft-info-box" style="margin-top: 0; margin-bottom: 14px; padding: 10px 14px;">
                <i class="bi bi-exclamation-circle-fill info-icon"></i>
                <p style="color: #BE123C;">{{ $errors->first() }}</p>
            </div>
        @endif

        <form action="{{ route('login.submit') }}" method="POST">
            @csrf

            <div class="form-group">
                <div class="form-label-row">
                    <label class="form-label" for="email">Email address</label>
                </div>
                <div class="input-wrapper">
                    <span class="input-icon-left"><i class="bi bi-envelope"></i></span>
                    <input type="email" id="email" name="email" class="form-control-sb" placeholder="you@example.com" value="{{ old('email', 'you@example.com') }}" required>
                </div>
            </div>

            <div class="form-group">
                <div class="form-label-row">
                    <label class="form-label" for="password">Password</label>
                    <a href="{{ route('password.request') }}" class="forgot-link">Forgot password?</a>
                </div>
                <div class="input-wrapper">
                    <span class="input-icon-left"><i class="bi bi-lock"></i></span>
                    <input type="password" id="password" name="password" class="form-control-sb" placeholder="Enter your password" required>
                    <button type="button" class="input-icon-right" onclick="togglePasswordVisibility('password', this)">
                        <i class="bi bi-eye-slash"></i>
                    </button>
                </div>
            </div>

            <label class="checkbox-row">
                <input type="checkbox" name="remember" checked>
                <span>Remember me</span>
            </label>

            <button type="submit" class="btn-sb-primary">
                <span>Sign in</span>
                <i class="bi bi-arrow-right"></i>
            </button>

            <div class="or-divider">
                <span>OR</span>
            </div>

            <a href="{{ route('admin.dashboard') }}" class="btn-sb-outline">
                <svg width="18" height="18" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l3.66-2.85z" fill="#FBBC05"/>
                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.85c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                </svg>
                <span>Continue with Google</span>
            </a>

            <div class="bottom-helper-text">
                Don't have an account? <a href="#">Contact your administrator.</a>
            </div>
        </form>
    </div>
@endsection

@section('right_content')
    <h2 class="right-heading">Build. Manage. Grow.</h2>
    <p class="right-subtitle">A powerful and flexible website builder to create stunning websites with ease.</p>

    <div class="feature-list">
        <div class="feature-item">
            <div class="feature-icon-box"><i class="bi bi-file-earmark-text"></i></div>
            <div class="feature-text">
                <h5>Easy Content Management</h5>
                <p>Update your content without coding.</p>
            </div>
        </div>
        <div class="feature-item">
            <div class="feature-icon-box"><i class="bi bi-palette"></i></div>
            <div class="feature-text">
                <h5>Beautiful Themes</h5>
                <p>Choose from modern, responsive themes.</p>
            </div>
        </div>
        <div class="feature-item">
            <div class="feature-icon-box"><i class="bi bi-images"></i></div>
            <div class="feature-text">
                <h5>Media Library</h5>
                <p>Organize and manage your assets.</p>
            </div>
        </div>
        <div class="feature-item">
            <div class="feature-icon-box"><i class="bi bi-box"></i></div>
            <div class="feature-text">
                <h5>Modular &amp; Scalable</h5>
                <p>Add features as your business grows.</p>
            </div>
        </div>
    </div>
@endsection

@section('right_illustration')
    <!-- 3D Dashboard Preview Illustration (Matching Login Screenshot) -->
    <svg viewBox="15 0 530 295" preserveAspectRatio="xMidYMax meet" width="100%" style="display: block;" fill="none" xmlns="http://www.w3.org/2000/svg">
        <defs>
            <filter id="cardShadow" x="-10%" y="-10%" width="130%" height="130%">
                <feDropShadow dx="0" dy="14" stdDeviation="16" flood-color="#0F172A" flood-opacity="0.10"/>
            </filter>
            <filter id="floatShadow" x="-15%" y="-15%" width="140%" height="140%">
                <feDropShadow dx="0" dy="10" stdDeviation="10" flood-color="#0F172A" flood-opacity="0.12"/>
            </filter>
            <linearGradient id="chartFill" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0%" stop-color="#EC134A" stop-opacity="0.22"/>
                <stop offset="100%" stop-color="#EC134A" stop-opacity="0.0"/>
            </linearGradient>
        </defs>

        <!-- Potted Plant Left -->
        <g transform="translate(28, 125)">
            <path d="M40 90 C15 65, 5 35, 18 12 C32 25, 45 55, 45 90 Z" fill="#3B873E"/>
            <path d="M45 88 C42 48, 52 15, 72 4 C76 32, 65 62, 50 90 Z" fill="#52A447"/>
            <path d="M52 92 C70 68, 95 52, 108 60 C95 82, 72 92, 52 94 Z" fill="#68B85D"/>
            <path d="M38 92 C18 75, -5 72, -8 84 C8 98, 25 98, 40 95 Z" fill="#469642"/>
            <!-- White Pot -->
            <path d="M22 92 H72 L66 142 C65 148, 60 152, 54 152 H40 C34 152, 29 148, 28 142 L22 92 Z" fill="#F8FAFC" stroke="#E2E8F0"/>
            <ellipse cx="47" cy="92" rx="25" ry="4" fill="#CBD5E1"/>
        </g>

        <!-- Main Tilted Dashboard Screen Card -->
        <g transform="translate(105, 25) skewY(-4)" filter="url(#cardShadow)">
            <rect x="0" y="0" width="380" height="235" rx="16" fill="#FFFFFF" stroke="#F1F5F9" stroke-width="2"/>
            <!-- Sidebar inside mockup -->
            <rect x="10" y="10" width="96" height="215" rx="10" fill="#FFFFFF"/>
            <!-- Mini Logo -->
            <circle cx="24" cy="26" r="6" fill="#EC134A"/>
            <text x="35" y="30" font-family="Inter, sans-serif" font-size="10" font-weight="800" fill="#0F172A">{{ $globalSiteName ?? 'SiteBuilder' }}</text>
            <!-- Active Pill -->
            <rect x="14" y="42" width="84" height="22" rx="6" fill="#FFE6ED"/>
            <rect x="20" y="49" width="7" height="7" rx="1.5" fill="#EC134A"/>
            <text x="32" y="56" font-family="Inter, sans-serif" font-size="7.5" font-weight="700" fill="#EC134A">Dashboard</text>

            <!-- Other Sidebar links -->
            <g fill="#64748B" font-family="Inter, sans-serif" font-size="7.5" font-weight="500">
                <text x="32" y="76">Pages</text>
                <text x="32" y="95">Menu Builder</text>
                <text x="32" y="114">Slider</text>
                <text x="32" y="133">Media Library</text>
                <text x="32" y="152">Themes</text>
                <text x="32" y="171">Settings</text>
            </g>
            <g fill="#94A3B8">
                <rect x="20" y="70" width="6" height="6" rx="1"/>
                <rect x="20" y="89" width="6" height="6" rx="1"/>
                <rect x="20" y="108" width="6" height="6" rx="1"/>
                <rect x="20" y="127" width="6" height="6" rx="1"/>
                <rect x="20" y="146" width="6" height="6" rx="1"/>
                <rect x="20" y="165" width="6" height="6" rx="1"/>
            </g>

            <!-- Main Content Area inside mockup -->
            <text x="118" y="46" font-family="Inter, sans-serif" font-size="11" font-weight="800" fill="#0F172A">Dashboard</text>
            <circle cx="356" cy="24" r="8" fill="#E2E8F0"/>
            <circle cx="356" cy="22" r="3" fill="#475569"/>

            <!-- 3 Mini Stat Cards -->
            <rect x="118" y="58" width="74" height="38" rx="8" fill="#F8FAFC" stroke="#F1F5F9"/>
            <circle cx="131" cy="77" r="7" fill="#FEF3C7"/>
            <rect x="143" y="71" width="32" height="5" rx="2.5" fill="#CBD5E1"/>
            <rect x="143" y="80" width="22" height="4" rx="2" fill="#E2E8F0"/>

            <rect x="200" y="58" width="74" height="38" rx="8" fill="#F8FAFC" stroke="#F1F5F9"/>
            <circle cx="213" cy="77" r="7" fill="#DBEAFE"/>
            <rect x="225" y="71" width="32" height="5" rx="2.5" fill="#CBD5E1"/>
            <rect x="225" y="80" width="22" height="4" rx="2" fill="#E2E8F0"/>

            <rect x="282" y="58" width="74" height="38" rx="8" fill="#F8FAFC" stroke="#F1F5F9"/>
            <circle cx="295" cy="77" r="7" fill="#DCFCE7"/>
            <rect x="307" y="71" width="32" height="5" rx="2.5" fill="#CBD5E1"/>
            <rect x="307" y="80" width="22" height="4" rx="2" fill="#E2E8F0"/>

            <!-- Chart Box -->
            <rect x="118" y="106" width="238" height="105" rx="10" fill="#F8FAFC"/>
            <path d="M128 185 Q155 162, 178 168 T228 145 T278 152 T335 118 L335 198 L128 198 Z" fill="url(#chartFill)"/>
            <path d="M128 185 Q155 162, 178 168 T228 145 T278 152 T335 118" stroke="#EC134A" stroke-width="2.5" fill="none" stroke-linecap="round"/>
        </g>

        <!-- Floating Landscape Photo Card Right -->
        <g transform="translate(425, 100) skewY(-4)" filter="url(#floatShadow)">
            <rect x="0" y="0" width="106" height="100" rx="12" fill="#FFFFFF"/>
            <rect x="8" y="8" width="90" height="52" rx="7" fill="#1E3A8A"/>
            <!-- Mountain / Lake illustration -->
            <path d="M8 46 L32 24 L55 44 L76 20 L98 46 V60 H8 Z" fill="#3B82F6" opacity="0.7"/>
            <circle cx="26" cy="20" r="5" fill="#FDE047"/>
            <rect x="12" y="69" width="66" height="6" rx="3" fill="#CBD5E1"/>
            <rect x="12" y="81" width="48" height="5" rx="2.5" fill="#E2E8F0"/>
        </g>

        <!-- Floating Bottom Card -->
        <g transform="translate(185, 222) skewY(-3)" filter="url(#floatShadow)">
            <rect x="0" y="0" width="135" height="66" rx="12" fill="#FFFFFF"/>
            <rect x="12" y="13" width="40" height="40" rx="8" fill="#60A5FA"/>
            <circle cx="24" cy="25" r="4" fill="#FFFFFF" opacity="0.85"/>
            <path d="M14 48 L28 34 L38 43 L44 37 L50 48 Z" fill="#FFFFFF" opacity="0.85"/>
            <rect x="62" y="18" width="56" height="6" rx="3" fill="#CBD5E1"/>
            <rect x="62" y="30" width="44" height="6" rx="3" fill="#E2E8F0"/>
            <rect x="62" y="42" width="50" height="6" rx="3" fill="#E2E8F0"/>
        </g>

        <!-- Pink Paper Airplane -->
        <g transform="translate(405, 205)">
            <path d="M0 24 L42 0 L28 38 L17 27 L0 24 Z" fill="#EC134A"/>
            <path d="M17 27 L42 0 L22 32 Z" fill="#BE0E3B"/>
            <path d="M-40 52 C-10 60, 15 48, 16 32" stroke="#F47296" stroke-width="1.5" stroke-dasharray="4 4" fill="none"/>
        </g>
    </svg>
@endsection
