<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', ($globalSiteName ?? 'SiteBuilder') . ' - ' . ($globalSiteTagline ?? 'Website Management'))</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --sb-primary: #EC134A;
            --sb-primary-hover: #D40E40;
            --sb-primary-soft: #FFE6ED;
            --sb-dark: #0F172A;
            --sb-text: #1E293B;
            --sb-muted: #64748B;
            --sb-light-muted: #94A3B8;
            --sb-border: #E2E8F0;
            --sb-input-bg: #F8FAFC;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html, body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            color: var(--sb-text);
            background: #FFFFFF;
            height: 100vh;
            height: 100dvh;
            width: 100vw;
            overflow: hidden;
            -webkit-font-smoothing: antialiased;
        }

        .auth-wrapper {
            display: flex;
            height: 100vh;
            height: 100dvh;
            width: 100%;
            overflow: hidden;
        }

        /* Left Column */
        .auth-left {
            flex: 0 0 50%;
            max-width: 50%;
            height: 100%;
            background: #FFFFFF;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: clamp(22px, 4.2vh, 52px) clamp(32px, 5vw, 80px) clamp(16px, 2.8vh, 34px);
            position: relative;
            overflow: hidden;
        }

        .auth-left-inner {
            width: 100%;
            max-width: 480px;
            margin: 0 auto;
            flex: 1;
            min-height: 0;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .auth-form-body {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            min-height: 0;
        }

        /* Top Header (Logo + Optional Logout) */
        .auth-top-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: clamp(10px, 1.8vh, 24px);
            flex-shrink: 0;
        }

        .brand-logo {
            display: inline-flex;
            align-items: center;
            gap: 14px;
            text-decoration: none;
        }

        .brand-logo-icon {
            width: clamp(40px, 4.8vh, 48px);
            height: clamp(40px, 4.8vh, 48px);
            flex-shrink: 0;
        }

        .brand-logo-text h1 {
            font-size: clamp(21px, 2.6vh, 26px);
            font-weight: 800;
            color: var(--sb-dark);
            letter-spacing: -0.02em;
            line-height: 1.15;
            margin: 0;
        }

        .brand-logo-text span {
            display: block;
            font-size: clamp(12px, 1.45vh, 14px);
            font-weight: 400;
            color: var(--sb-muted);
            margin-top: 2px;
        }

        .logout-top-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 14px;
            font-weight: 500;
            color: var(--sb-muted);
            text-decoration: none;
            transition: color 0.15s;
        }

        .logout-top-link:hover {
            color: var(--sb-primary);
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            font-weight: 500;
            color: #475569;
            text-decoration: none;
            margin-bottom: clamp(10px, 1.8vh, 22px);
            transition: color 0.15s;
        }

        .back-link:hover {
            color: var(--sb-primary);
        }

        .accent-bar {
            width: 44px;
            height: 4.5px;
            background: var(--sb-primary);
            border-radius: 4px;
            margin-bottom: clamp(10px, 1.6vh, 16px);
        }

        .auth-title {
            font-size: clamp(26px, 3.8vh, 36px);
            font-weight: 800;
            color: var(--sb-dark);
            letter-spacing: -0.025em;
            line-height: 1.18;
            margin-bottom: clamp(6px, 1vh, 12px);
        }

        .auth-subtitle {
            font-size: clamp(13.5px, 1.7vh, 16px);
            color: var(--sb-muted);
            line-height: 1.5;
            margin-bottom: clamp(16px, 2.8vh, 30px);
        }

        /* Form Controls */
        .form-group {
            margin-bottom: clamp(12px, 2vh, 20px);
        }

        .form-label-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: clamp(5px, 0.9vh, 8px);
        }

        .form-label {
            font-size: 13.5px;
            font-weight: 600;
            color: var(--sb-dark);
            display: block;
        }

        .forgot-link {
            font-size: 13px;
            font-weight: 600;
            color: var(--sb-primary);
            text-decoration: none;
        }

        .forgot-link:hover {
            text-decoration: underline;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon-left {
            position: absolute;
            left: 15px;
            color: var(--sb-light-muted);
            font-size: 16px;
            pointer-events: none;
            display: flex;
            align-items: center;
        }

        .form-control-sb {
            width: 100%;
            height: clamp(42px, 5.5vh, 52px);
            padding: 0 42px 0 42px;
            font-size: 14px;
            font-family: inherit;
            color: var(--sb-dark);
            background: var(--sb-input-bg);
            border: 1px solid var(--sb-border);
            border-radius: 10px;
            outline: none;
            transition: all 0.15s ease;
        }

        .form-control-sb::placeholder {
            color: #94A3B8;
        }

        .form-control-sb:focus {
            background: #FFFFFF;
            border-color: var(--sb-primary);
            box-shadow: 0 0 0 3px rgba(236, 19, 74, 0.1);
        }

        .input-icon-right {
            position: absolute;
            right: 14px;
            color: #94A3B8;
            font-size: 16px;
            background: none;
            border: none;
            cursor: pointer;
            padding: 4px;
            display: flex;
            align-items: center;
        }

        .input-icon-right:hover {
            color: var(--sb-muted);
        }

        /* Custom Checkbox */
        .checkbox-row {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: clamp(14px, 2.2vh, 22px);
            cursor: pointer;
            user-select: none;
        }

        .checkbox-row input[type="checkbox"] {
            appearance: none;
            -webkit-appearance: none;
            width: 18px;
            height: 18px;
            border: 1.5px solid #CBD5E1;
            border-radius: 5px;
            background: #FFFFFF;
            cursor: pointer;
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.15s;
        }

        .checkbox-row input[type="checkbox"]:checked {
            background: var(--sb-primary);
            border-color: var(--sb-primary);
        }

        .checkbox-row input[type="checkbox"]:checked::after {
            content: '';
            width: 5px;
            height: 9px;
            border: solid #FFFFFF;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
            margin-bottom: 1px;
        }

        .checkbox-row span {
            font-size: 13.5px;
            font-weight: 500;
            color: var(--sb-dark);
        }

        /* Primary Button */
        .btn-sb-primary {
            width: 100%;
            height: clamp(42px, 5.5vh, 52px);
            background: linear-gradient(90deg, #EC134A 0%, #E01045 100%);
            color: #FFFFFF;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 600;
            font-family: inherit;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            cursor: pointer;
            box-shadow: 0 6px 16px rgba(236, 19, 74, 0.22);
            transition: all 0.18s ease;
            text-decoration: none;
        }

        .btn-sb-primary:hover {
            background: linear-gradient(90deg, #D91042 0%, #C80D3C 100%);
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(236, 19, 74, 0.28);
            color: #FFFFFF;
        }

        /* Secondary Outlined Button */
        .btn-sb-outline {
            width: 100%;
            height: clamp(42px, 5.5vh, 52px);
            background: #FFFFFF;
            color: var(--sb-dark);
            border: 1px solid var(--sb-border);
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            font-family: inherit;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            cursor: pointer;
            transition: all 0.15s ease;
            text-decoration: none;
        }

        .btn-sb-outline:hover {
            background: #F8FAFC;
            border-color: #CBD5E1;
        }

        /* Divider OR */
        .or-divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: clamp(10px, 2vh, 20px) 0;
            color: #94A3B8;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.04em;
        }

        .or-divider::before,
        .or-divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #F1F5F9;
        }

        .or-divider span {
            padding: 0 14px;
        }

        .bottom-helper-text {
            text-align: center;
            margin-top: clamp(12px, 2vh, 22px);
            font-size: 13.5px;
            color: var(--sb-muted);
        }

        .bottom-helper-text a {
            color: var(--sb-primary);
            font-weight: 600;
            text-decoration: none;
        }

        .bottom-helper-text a:hover {
            text-decoration: underline;
        }

        /* Soft Info / Alert Box */
        .soft-info-box {
            margin-top: clamp(12px, 2vh, 20px);
            background: #FFF5F7;
            border: 1px solid #FFE4EC;
            border-radius: 11px;
            padding: clamp(11px, 1.6vh, 16px) 16px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }

        .soft-info-box .info-icon {
            color: var(--sb-primary);
            font-size: 17px;
            line-height: 1;
            margin-top: 2px;
            flex-shrink: 0;
        }

        .soft-info-box p {
            font-size: 13px;
            color: #64748B;
            line-height: 1.48;
            margin: 0;
        }

        .soft-info-box h6 {
            font-size: 13.5px;
            font-weight: 700;
            color: var(--sb-dark);
            margin: 0 0 2px 0;
        }

        /* Footer */
        .auth-footer {
            width: 100%;
            max-width: 480px;
            margin: clamp(10px, 1.6vh, 20px) auto 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 12.5px;
            color: #94A3B8;
            flex-shrink: 0;
        }

        .auth-footer-links {
            display: flex;
            gap: 18px;
        }

        .auth-footer-links a {
            color: #64748B;
            text-decoration: none;
            transition: color 0.15s;
        }

        .auth-footer-links a:hover {
            color: var(--sb-primary);
        }

        /* Right Column */
        .auth-right {
            flex: 0 0 50%;
            max-width: 50%;
            height: 100%;
            background: linear-gradient(145deg, #FFF6F8 0%, #FDE8EE 55%, #FCDCE5 100%);
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: clamp(24px, 4.2vh, 54px) clamp(28px, 4vw, 56px) 0;
        }

        /* Decorative Shapes on Right Panel */
        .auth-right-circle-top {
            position: absolute;
            top: -120px;
            right: -120px;
            width: 380px;
            height: 380px;
            border-radius: 50%;
            background: rgba(251, 207, 218, 0.45);
            pointer-events: none;
        }

        .auth-right-circle-bottom {
            position: absolute;
            bottom: -90px;
            right: -90px;
            width: 580px;
            height: 580px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(249, 168, 188, 0.55) 0%, rgba(251, 207, 218, 0.3) 65%, transparent 100%);
            pointer-events: none;
        }

        .auth-right-circle-inner {
            position: absolute;
            bottom: -10px;
            right: -20px;
            width: 430px;
            height: 430px;
            border-radius: 50%;
            background: rgba(244, 138, 165, 0.35);
            pointer-events: none;
        }

        .auth-right-dots {
            position: absolute;
            top: 36px;
            right: 44px;
            display: grid;
            grid-template-columns: repeat(5, 6px);
            gap: 10px;
            pointer-events: none;
            opacity: 0.55;
        }

        .auth-right-dots span {
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: #F497AE;
        }

        .auth-right-content {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 560px;
            margin: 0 auto;
            flex-shrink: 0;
        }

        .right-heading {
            font-size: clamp(26px, 3.8vh, 40px);
            font-weight: 800;
            color: var(--sb-dark);
            letter-spacing: -0.025em;
            line-height: 1.15;
            margin-bottom: clamp(6px, 1vh, 12px);
        }

        .right-subtitle {
            font-size: clamp(13.5px, 1.7vh, 16px);
            color: #64748B;
            line-height: 1.48;
            margin-bottom: clamp(12px, 2vh, 22px);
        }

        .feature-list {
            display: flex;
            flex-direction: column;
            gap: clamp(8px, 1.35vh, 14px);
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .feature-icon-box {
            width: clamp(34px, 4.4vh, 42px);
            height: clamp(34px, 4.4vh, 42px);
            border-radius: 10px;
            background: #FFE0E9;
            color: var(--sb-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: clamp(15px, 1.9vh, 18px);
            flex-shrink: 0;
        }

        .feature-text h5 {
            font-size: clamp(13px, 1.55vh, 14.5px);
            font-weight: 700;
            color: var(--sb-dark);
            margin: 0 0 1px 0;
        }

        .feature-text p {
            font-size: clamp(12px, 1.4vh, 13.5px);
            color: #64748B;
            margin: 0;
        }

        /* Enlarged Right Bottom Illustration Area */
        .auth-right-illustration {
            position: relative;
            z-index: 2;
            flex: 1;
            min-height: 0;
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: flex-end;
            margin-top: clamp(4px, 0.8vh, 10px);
        }

        .auth-right-illustration svg {
            width: 100% !important;
            max-width: 780px !important;
            height: 100% !important;
            max-height: clamp(240px, 48vh, 470px) !important;
            object-fit: contain;
            object-position: bottom center;
        }

        /* Responsive Mobile & Tablet: Fits fixed window without scrolling */
        @media (max-width: 991px) {
            .auth-right {
                display: none;
            }
            .auth-left {
                flex: 0 0 100%;
                max-width: 100%;
                padding: 24px 20px 18px;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="auth-wrapper">
        <!-- Left Form Panel -->
        <div class="auth-left">
            <div class="auth-left-inner">
                <div class="auth-top-bar">
                    <a href="{{ url('/') }}" class="brand-logo">
                        @if(!empty($globalSiteLogo))
                            <img src="{{ $globalSiteLogo }}" alt="{{ $globalSiteName ?? 'SiteBuilder' }}" class="brand-logo-icon" style="object-fit: contain; border-radius: 8px;">
                        @else
                            <!-- Default 3D Red Stacked Hexagon Icon -->
                            <svg class="brand-logo-icon" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M24 4L6 14V20L24 30L42 20V14L24 4Z" fill="#EC134A"/>
                                <path d="M24 10L11 17L24 24L37 17L24 10Z" fill="#FF85A1" opacity="0.35"/>
                                <path d="M6 23L24 33L42 23V28L24 38L6 28V23Z" fill="#D90F43"/>
                                <path d="M6 31L24 41L42 31V36L24 46L6 36V31Z" fill="#B80B38"/>
                            </svg>
                        @endif
                        <div class="brand-logo-text">
                            <h1>{{ $globalSiteName ?? 'SiteBuilder' }}</h1>
                            <span>{{ $globalSiteTagline ?? 'Website Management' }}</span>
                        </div>
                    </a>

                    @hasSection('top_right')
                        @yield('top_right')
                    @endif
                </div>

                <div class="auth-form-body">
                    @yield('left_content')
                </div>
            </div>

            <div class="auth-footer">
                <div>&copy; 2025 {{ $globalSiteName ?? 'SiteBuilder' }}. All rights reserved.</div>
                <div class="auth-footer-links">
                    <a href="#">Privacy</a>
                    <a href="#">Terms</a>
                    <a href="#">Support</a>
                </div>
            </div>
        </div>

        <!-- Right Feature & Illustration Panel -->
        <div class="auth-right">
            <div class="auth-right-circle-top"></div>
            <div class="auth-right-circle-bottom"></div>
            <div class="auth-right-circle-inner"></div>
            <div class="auth-right-dots">
                @for($i = 0; $i < 15; $i++)
                    <span></span>
                @endfor
            </div>

            <div class="auth-right-content">
                <div class="accent-bar"></div>
                @yield('right_content')
            </div>

            <div class="auth-right-illustration">
                @yield('right_illustration')
            </div>
        </div>
    </div>

    <script>
        function togglePasswordVisibility(inputId, btn) {
            const input = document.getElementById(inputId);
            const icon = btn.querySelector('i');
            if (!input) return;
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'bi bi-eye';
            } else {
                input.type = 'password';
                icon.className = 'bi bi-eye-slash';
            }
        }
    </script>
    @stack('scripts')
</body>
</html>
