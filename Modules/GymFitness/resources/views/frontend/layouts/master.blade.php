<!DOCTYPE html>
<html lang="{{ $locale ?? 'en' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $tr['meta_title'] ?? 'FitPulse — Gym & Fitness' }}</title>
    <meta name="description" content="{{ $tr['meta_description'] ?? '' }}">
    <meta name="keywords" content="{{ $t['general']['meta_keywords'] ?? 'Gym, Fitness, FitPulse' }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.maateen.me/solaiman-lipi/font.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="{{ route('gymfitness.asset', ['type' => 'css', 'file' => 'style.css']) }}?v=9.0">
    @stack('gym_styles')
</head>
<body class="gym-body">

    {{-- =====================================================================
         1. FLOATING OLIVE-GREEN PILL NAVBAR (100% Same-to-Same with Reference)
         ===================================================================== --}}
    <header class="gym-header-wrap">
        <div class="gym-container">
            <div class="gym-pill-navbar">
                <a href="{{ route('gymfitness.home') }}" class="gym-brand" style="display:inline-flex;align-items:center;gap:8px;">
                    @if(!empty($t['header']['logo']))
                        <img src="{{ $t['header']['logo'] }}" alt="Logo" style="height:28px;width:auto;border-radius:4px;">
                    @endif
                    <span>{{ $tr['brand_name'] ?? 'IronPulse' }}</span>
                </a>

                <nav class="gym-nav" id="gymMainNav">
                    <a href="{{ route('gymfitness.home') }}#home" class="gym-nav-link active">
                        <span class="gym-nav-dot"></span>{{ $tr['nav_home'] ?? 'Home' }}
                    </a>
                    <a href="{{ route('gymfitness.home') }}#programs" class="gym-nav-link">{{ $tr['nav_programs'] ?? 'Programs' }}</a>
                    <a href="{{ route('gymfitness.home') }}#trainers" class="gym-nav-link">{{ $tr['nav_trainers'] ?? 'Trainers' }}</a>
                    <a href="{{ route('gymfitness.home') }}#pricing" class="gym-nav-link">{{ $tr['nav_pricing'] ?? 'Pricing' }}</a>
                    <a href="{{ route('gymfitness.home') }}#gallery" class="gym-nav-link">{{ ($locale ?? 'en') === 'bn' ? 'গ্যালারি' : 'Gallery' }}</a>
                    <a href="{{ route('gymfitness.home') }}#videos" class="gym-nav-link">{{ ($locale ?? 'en') === 'bn' ? 'ভিডিও' : 'Videos' }}</a>
                    <a href="{{ route('gymfitness.home') }}#testimonials" class="gym-nav-link">{{ $tr['nav_transformations'] ?? (($locale ?? 'en') === 'bn' ? 'মতামত' : 'Transformations') }}</a>
                    <a href="{{ route('gymfitness.home') }}#contact" class="gym-nav-link">{{ $tr['nav_contact'] ?? 'Contact' }}</a>
                </nav>

                <div class="gym-header-actions">
                    @if(($t['header']['show_language_switcher'] ?? '1') === '1')
                        <a href="{{ route('gymfitness.lang', ($locale ?? 'en') === 'bn' ? 'en' : 'bn') }}"
                           class="gym-lang-pill"
                           title="Switch Language (English / বাংলা)">
                            <i class="fa-solid fa-globe" style="font-size:11px;"></i>
                            <span>{{ ($locale ?? 'en') === 'bn' ? 'EN' : 'বাংলা' }}</span>
                        </a>
                    @endif

                    @if(($t['header']['show_portal_btn'] ?? '1') === '1')
                        <a href="{{ route('gymfitness.portal') }}"
                           class="gym-lang-pill"
                           title="{{ $tr['nav_portal'] ?? 'Member Portal' }}">
                            <i class="fa-solid fa-id-card" style="font-size:11px;"></i>
                            <span>{{ ($locale ?? 'en') === 'bn' ? 'পোর্টাল' : 'Portal' }}</span>
                        </a>
                    @endif

                    <a href="#register" data-open-register class="gym-btn-lime gym-nav-cta">
                        <span>{{ $tr['nav_join_btn'] ?? 'Join Now' }}</span>
                        <svg class="gym-arrow-ne" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M2.5 9.5L9.5 2.5M9.5 2.5H3.5M9.5 2.5V8.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>

                    <button type="button" class="gym-mobile-toggle" id="gymMobileToggle" aria-label="Toggle Menu">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                </div>
            </div>
        </div>
    </header>

    {{-- Flash Alerts --}}
    @if(session('gym_success'))
        <div class="gym-container" style="position:relative;z-index:60;margin-top:14px;">
            <div style="background:rgba(204,249,54,0.16);border:1px solid var(--gym-lime);border-radius:10px;padding:12px 18px;color:#ECFCCB;display:flex;align-items:center;justify-content:space-between;gap:12px;">
                <div><i class="fa-solid fa-circle-check" style="color:var(--gym-lime);margin-right:8px;"></i> {{ session('gym_success') }}</div>
                <a href="{{ route('gymfitness.portal') }}" class="gym-btn-lime" style="padding:6px 14px;font-size:12px;">Open Member Portal</a>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="gym-container" style="position:relative;z-index:60;margin-top:14px;">
            <div style="background:rgba(239,68,68,0.15);border:1px solid #EF4444;border-radius:10px;padding:12px 18px;color:#FECACA;">
                <strong><i class="fa-solid fa-triangle-exclamation"></i> Please check the form:</strong>
                <ul style="margin:6px 0 0 18px;font-size:13px;">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    {{-- Main Page Content --}}
    <main>
        @yield('gym_content')
    </main>

    {{-- =====================================================================
         FOOTER SECTION (100% Same-to-Same with Reference Image)
         ===================================================================== --}}
    <footer class="gym-footer" id="contact">
        <div class="gym-container">
            <div class="gym-footer-grid">
                <div>
                    <a href="{{ route('gymfitness.home') }}" class="gym-brand" style="font-size:16px;">
                        {{ $tr['brand_name'] ?? 'FitPulse' }}
                    </a>
                </div>

                <div>
                    <h4>{{ $tr['footer_quick_links'] ?? 'Quick Links' }}</h4>
                    <ul>
                        <li><a href="{{ route('gymfitness.home') }}#programs">{{ $tr['nav_programs'] ?? 'Programs' }}</a></li>
                        <li><a href="{{ route('gymfitness.home') }}#pricing">{{ $tr['nav_pricing'] ?? 'Pricing' }}</a></li>
                        <li><a href="{{ route('gymfitness.home') }}#trainers">{{ $tr['nav_trainers'] ?? 'Trainers' }}</a></li>
                        <li><a href="{{ route('gymfitness.home') }}#contact">{{ $tr['nav_contact'] ?? 'Contact' }}</a></li>
                    </ul>
                </div>

                <div>
                    <h4>{{ $tr['footer_contact_us'] ?? 'Contact Us' }}</h4>
                    <ul>
                        <li>{{ $t['general']['contact_phone'] ?? '+880 123 456 789' }}</li>
                        <li>{{ $t['general']['contact_email'] ?? 'hello@fitpulse.com' }}</li>
                        <li>{{ $tr['contact_address'] ?? '123 Fitness Street, Dhaka 1207' }}</li>
                    </ul>
                </div>

                <div>
                    <form action="{{ route('gymfitness.newsletter.submit') }}" method="POST" class="gym-news-form">
                        @csrf
                        <input type="email" name="newsletter_email" required placeholder="{{ ($locale ?? 'en') === 'bn' ? 'আপনার ইমেইল লিখুন' : 'Enter your email' }}">
                        <button type="submit" class="gym-btn-lime">
                            {{ $tr['footer_news_btn'] ?? 'Subscribe' }}
                            <i class="fa-solid fa-arrow-up-right-from-square" style="font-size:9px;"></i>
                        </button>
                    </form>
                </div>
            </div>

            <div class="gym-footer-social-col">
                <a href="{{ $t['general']['facebook_url'] ?? '#' }}" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                <a href="{{ $t['general']['instagram_url'] ?? '#' }}" aria-label="Twitter"><i class="fa-brands fa-twitter"></i></a>
                <a href="{{ $t['general']['youtube_url'] ?? '#' }}" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
            </div>

            <div class="gym-footer-bottom">
                <span>{{ $tr['footer_copyright'] ?? '© 2026 FitPulse. All rights reserved.' }}</span>
                <div style="display:flex;align-items:center;gap:14px;">
                    <a href="{{ route('gymfitness.portal') }}" style="color:#6B7280;text-decoration:none;">Member Portal</a>
                    <a href="{{ route('gymfitness.lang', ($locale ?? 'en') === 'bn' ? 'en' : 'bn') }}" style="color:#6B7280;text-decoration:none;">
                        {{ ($locale ?? 'en') === 'bn' ? 'English' : 'বাংলা' }}
                    </a>
                </div>
            </div>
        </div>
    </footer>

    {{-- =====================================================================
         MEMBERSHIP REGISTRATION MODAL (Triggered by Join Now / Choose Plan)
         ===================================================================== --}}
    @php
        $modalPackages = $packages ?? \Modules\GymFitness\App\Models\GymPackage::where('is_active', true)->orderBy('sort_order')->get();
        $isBnModal = ($locale ?? 'en') === 'bn';
    @endphp
    <div class="gym-modal-backdrop" id="gymRegisterModal">
        <div class="gym-modal-box">
            <button type="button" class="gym-modal-close" data-close-modal><i class="fa-solid fa-xmark"></i></button>
            <div style="margin-bottom:18px;">
                <h3 style="font-size:22px;font-weight:800;color:#fff;margin-bottom:4px;">
                    {{ $tr['reg_title'] ?? 'Join FitPulse Gym Today' }}
                </h3>
                <p style="color:#9CA3AF;font-size:12.5px;">
                    {{ $tr['reg_subtitle'] ?? 'Select your membership package and complete registration below.' }}
                </p>
            </div>

            <form action="{{ route('gymfitness.register.submit') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="gym-form-grid">
                    <div class="gym-form-group">
                        <label>{{ $tr['reg_full_name'] ?? 'Full Name' }} *</label>
                        <input type="text" name="name" class="gym-input" required placeholder="e.g. Tanvir Ahmed" value="{{ old('name') }}">
                    </div>

                    <div class="gym-form-group">
                        <label>{{ $tr['reg_email'] ?? 'Email Address' }} *</label>
                        <input type="email" name="email" class="gym-input" required placeholder="member@example.com" value="{{ old('email') }}">
                    </div>

                    <div class="gym-form-group">
                        <label>{{ $tr['reg_phone'] ?? 'Phone Number' }} *</label>
                        <input type="text" name="phone" class="gym-input" required placeholder="+880 1711-000000" value="{{ old('phone') }}">
                    </div>

                    <div class="gym-form-group">
                        <label>{{ $tr['reg_dob'] ?? 'Date of Birth' }}</label>
                        <input type="date" name="date_of_birth" class="gym-input" value="{{ old('date_of_birth', '1997-05-15') }}">
                    </div>

                    <div class="gym-form-group">
                        <label>{{ $tr['reg_gender'] ?? 'Gender' }} *</label>
                        <select name="gender" class="gym-select" required>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <div class="gym-form-group">
                        <label>Blood Group</label>
                        <select name="blood_group" class="gym-select">
                            <option value="O+">O+</option>
                            <option value="A+">A+</option>
                            <option value="B+">B+</option>
                            <option value="AB+">AB+</option>
                            <option value="O-">O-</option>
                        </select>
                    </div>

                    <div class="gym-form-group">
                        <label>{{ $tr['reg_address'] ?? 'Present Address' }} *</label>
                        <input type="text" name="address" class="gym-input" required placeholder="House, Road, Area, Dhaka" value="{{ old('address') }}">
                    </div>

                    <div class="gym-form-group">
                        <label>{{ $tr['reg_emergency_name'] ?? 'Emergency Contact Name' }} *</label>
                        <input type="text" name="emergency_contact_name" class="gym-input" required placeholder="Parent / Spouse Name" value="{{ old('emergency_contact_name') }}">
                    </div>

                    <div class="gym-form-group">
                        <label>{{ $tr['reg_emergency_phone'] ?? 'Emergency Contact Phone' }} *</label>
                        <input type="text" name="emergency_contact_phone" class="gym-input" required placeholder="+880 1811-000000" value="{{ old('emergency_contact_phone') }}">
                    </div>

                    <div class="gym-form-group">
                        <label>{{ $tr['reg_select_package'] ?? 'Select Membership Package' }} *</label>
                        <select name="package_id" id="gymRegPackageSelect" class="gym-select" required>
                            @foreach($modalPackages as $pkg)
                                <option value="{{ $pkg->id }}" {{ $pkg->is_featured ? 'selected' : '' }}>
                                    {{ $isBnModal ? ($pkg->name_bn ?: $pkg->name) : $pkg->name }} — {{ $pkg->currency ?? '$' }}{{ (float) $pkg->monthly_price }}/mo
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="gym-form-group">
                        <label>{{ $tr['reg_select_duration'] ?? 'Membership Duration' }} *</label>
                        <select name="billing_cycle" id="gymRegBillingSelect" class="gym-select" required>
                            <option value="monthly">{{ $tr['billing_monthly'] ?? 'Monthly (1 Month)' }}</option>
                            <option value="quarterly">{{ $tr['billing_quarterly'] ?? 'Quarterly (3 Months)' }}</option>
                            <option value="six_month">{{ $tr['billing_six_month'] ?? 'Six-Month (6 Months)' }}</option>
                            <option value="yearly">{{ $tr['billing_yearly'] ?? 'Yearly (12 Months)' }}</option>
                        </select>
                    </div>

                    <div class="gym-form-group">
                        <label>{{ $tr['reg_payment_method'] ?? 'Select Payment Method' }} *</label>
                        <select name="payment_method" class="gym-select" required>
                            <option value="bKash">bKash Mobile Banking</option>
                            <option value="Nagad">Nagad Mobile Banking</option>
                            <option value="Visa / MasterCard">Visa / MasterCard</option>
                            <option value="Cash at Gym Desk">Pay Cash at Gym Desk</option>
                        </select>
                    </div>

                    <div class="gym-form-group">
                        <label>Transaction Reference (Optional)</label>
                        <input type="text" name="transaction_reference" class="gym-input" placeholder="e.g. BKS9845120">
                    </div>

                    <div class="gym-form-group">
                        <label>Profile Photo (Optional)</label>
                        <input type="file" name="photo_file" class="gym-input" accept="image/*" style="padding-top:7px;">
                    </div>

                    <div class="gym-form-group" style="display:flex;align-items:flex-end;">
                        <button type="submit" class="gym-btn-lime" style="width:100%;height:40px;">
                            {{ $tr['reg_submit_btn'] ?? 'Complete Registration' }}
                            <i class="fa-solid fa-arrow-up-right-from-square" style="font-size:9px;"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- =====================================================================
         VIDEO PLAYER LIGHTBOX MODAL (Triggered by Video Gallery Cards)
         ===================================================================== --}}
    <div class="gym-modal-backdrop" id="gymVideoModal">
        <div class="gym-modal-box" style="max-width:860px;padding:18px;">
            <button type="button" class="gym-modal-close" data-close-modal><i class="fa-solid fa-xmark"></i></button>
            <h4 id="gymVideoModalTitle" style="font-family:var(--gym-font-display);font-size:18px;color:#fff;margin-bottom:12px;">Workout Video</h4>
            <div style="position:relative;padding-bottom:56.25%;height:0;overflow:hidden;border-radius:10px;background:#000;">
                <iframe id="gymVideoIframe" src="" style="position:absolute;top:0;left:0;width:100%;height:100%;border:0;" allow="autoplay; encrypted-media" allowfullscreen></iframe>
            </div>
        </div>
    </div>

    {{-- =====================================================================
         IMAGE LIGHTBOX MODAL (Triggered by Photo Gallery Cards)
         ===================================================================== --}}
    <div class="gym-modal-backdrop" id="gymImageModal">
        <div class="gym-modal-box" style="max-width:820px;padding:18px;text-align:center;">
            <button type="button" class="gym-modal-close" data-close-modal><i class="fa-solid fa-xmark"></i></button>
            <img id="gymImageModalSrc" src="" alt="Gallery Preview" style="width:100%;max-height:74vh;object-fit:contain;border-radius:10px;">
            <h4 id="gymImageModalTitle" style="font-family:var(--gym-font-display);font-size:18px;color:#fff;margin-top:12px;"></h4>
        </div>
    </div>

    <script src="{{ route('gymfitness.asset', ['type' => 'js', 'file' => 'main.js']) }}?v=5.0"></script>
    @stack('gym_scripts')
</body>
</html>
