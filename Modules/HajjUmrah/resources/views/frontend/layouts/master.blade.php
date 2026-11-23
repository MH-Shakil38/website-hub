<!DOCTYPE html>
<html lang="{{ $locale ?? 'en' }}" dir="{{ ($isRtl ?? false) ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', $tr['meta_title'] ?? 'Al Hajenra — Hajj & Umrah Travel Agency')</title>
    <meta name="description" content="{{ $tr['meta_description'] ?? '' }}">
    <meta name="keywords" content="{{ $t['general']['meta_keywords'] ?? 'Hajj Package, Umrah Package, Makkah, Madinah' }}">

    <!-- Google Fonts & SolaimanLipi (Bangla), Plus Jakarta Sans (EN), Cairo & Amiri (AR) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@500;600;700;800&family=Hind+Siliguri:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.maateen.me/solaiman-lipi/font.css" rel="stylesheet">

    <!-- Font Awesome 6 & Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <!-- Isolated Hajj & Umrah Theme CSS -->
    <link rel="stylesheet" href="{{ route('hajjumrah.asset', ['type' => 'css', 'file' => 'style.css']) }}?v=1.2">
    @stack('styles')
</head>
<body class="hajj-body">

    @if(session('hajj_success'))
        <div style="background: #0D8A47; color: #FFFFFF; text-align: center; padding: 11px 20px; font-size: 13.5px; font-weight: 700;">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('hajj_success') }}
        </div>
    @endif

    <!-- =====================================================================
         A. HOMEPAGE HEADER (Screenshot 1 Reference)
         ===================================================================== -->
    <header class="hajj-header">
        <div class="hajj-container hajj-header-inner">
            <!-- Brand Logo -->
            <a href="{{ route('hajjumrah.home') }}" class="hajj-brand">
                @if(!empty($t['header']['logo']))
                    <img src="{{ $t['header']['logo'] }}" alt="Logo" style="height: 40px; width: auto; border-radius: 6px;">
                @else
                    <div class="hajj-brand-cube" aria-hidden="true">
                        <svg width="24" height="24" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M18 4L5 10.5V25.5L18 32L31 25.5V10.5L18 4Z" fill="#2A241E" stroke="#F4B87A" stroke-width="1.5"/>
                            <path d="M5 14.5L18 21L31 14.5" stroke="#F4B87A" stroke-width="2.5"/>
                            <path d="M18 21V32" stroke="#F4B87A" stroke-width="1.2" opacity="0.6"/>
                        </svg>
                    </div>
                @endif
                <div class="hajj-brand-text">
                    <span class="hajj-brand-title">{{ $tr['brand_name'] ?? 'Al Hajenra' }}</span>
                    <span class="hajj-brand-sub">{{ $tr['brand_subtitle'] ?? 'Hajj & Umrah' }}</span>
                </div>
            </a>

            <!-- Main Navigation Links with Hajj & Umrah Dropdowns -->
            <nav class="hajj-nav" id="hajjMainNav">
                <div class="hajj-nav-item">
                    <a href="{{ route('hajjumrah.home') }}" class="hajj-nav-link active">{{ $tr['nav_home'] ?? 'Home' }}</a>
                </div>

                <!-- Hajj Dropdown -->
                <div class="hajj-nav-item">
                    <a href="#special-offers" class="hajj-nav-link">
                        <span>{{ $tr['nav_hajj'] ?? 'Hajj' }}</span>
                        <i class="fa-solid fa-chevron-down chevron"></i>
                    </a>
                    <div class="hajj-dropdown">
                        <a href="#pricing"><span>{{ $tr['nav_hajj_economy'] ?? 'Economy Hajj Package' }}</span> <i class="fa-solid fa-arrow-right rtl-flip" style="font-size: 11px;"></i></a>
                        <a href="#pricing"><span>{{ $tr['nav_hajj_standard'] ?? 'Standard Hajj Package' }}</span> <i class="fa-solid fa-arrow-right rtl-flip" style="font-size: 11px;"></i></a>
                        <a href="#pricing"><span>{{ $tr['nav_hajj_vip'] ?? 'Classic / VIP Hajj Package' }}</span> <i class="fa-solid fa-arrow-right rtl-flip" style="font-size: 11px;"></i></a>
                    </div>
                </div>

                <!-- Umrah Dropdown -->
                <div class="hajj-nav-item">
                    <a href="#special-offers" class="hajj-nav-link">
                        <span>{{ $tr['nav_umrah'] ?? 'Umrah' }}</span>
                        <i class="fa-solid fa-chevron-down chevron"></i>
                    </a>
                    <div class="hajj-dropdown">
                        <a href="#special-offers"><span>{{ $tr['nav_umrah_ramadan'] ?? 'Ramadan Umrah Package' }}</span> <i class="fa-solid fa-arrow-right rtl-flip" style="font-size: 11px;"></i></a>
                        <a href="#special-offers"><span>{{ $tr['nav_umrah_family'] ?? 'Family Umrah Package' }}</span> <i class="fa-solid fa-arrow-right rtl-flip" style="font-size: 11px;"></i></a>
                        <a href="#special-offers"><span>{{ $tr['nav_umrah_group'] ?? '14 Days Group Umrah' }}</span> <i class="fa-solid fa-arrow-right rtl-flip" style="font-size: 11px;"></i></a>
                    </div>
                </div>

                <div class="hajj-nav-item">
                    <a href="#facilities" class="hajj-nav-link">{{ $tr['nav_facilities'] ?? 'Facilities' }}</a>
                </div>
                <div class="hajj-nav-item">
                    <a href="#pricing" class="hajj-nav-link">{{ $tr['nav_tour'] ?? 'Tour' }}</a>
                </div>
                <div class="hajj-nav-item">
                    <a href="#blog" class="hajj-nav-link">{{ $tr['nav_blog'] ?? 'Blog' }}</a>
                </div>
                <div class="hajj-nav-item">
                    <a href="#gallery" class="hajj-nav-link">{{ $tr['nav_gallery'] ?? 'Gallery' }}</a>
                </div>
            </nav>

            <!-- Language Switcher (BN / EN / AR) + Contact Us Pill Button -->
            <div class="hajj-header-actions">
                @if(($t['header']['show_language_switcher'] ?? '1') === '1')
                    <div class="hajj-lang-switcher" title="Switch Language (BN / EN / AR)">
                        <a href="{{ route('hajjumrah.lang.switch', 'bn') }}" class="hajj-lang-btn {{ ($locale ?? 'en') === 'bn' ? 'active' : '' }}">BN</a>
                        <a href="{{ route('hajjumrah.lang.switch', 'en') }}" class="hajj-lang-btn {{ ($locale ?? 'en') === 'en' ? 'active' : '' }}">EN</a>
                        <a href="{{ route('hajjumrah.lang.switch', 'ar') }}" class="hajj-lang-btn {{ ($locale ?? 'en') === 'ar' ? 'active' : '' }}">AR</a>
                    </div>
                @endif

                <a href="#contact" data-open-booking data-package-name="General Consultation" class="hajj-btn-contact-outline">
                    {{ $tr['nav_contact_btn'] ?? 'Contact Us' }}
                </a>

                <button type="button" class="hajj-mobile-toggle" id="hajjMobileToggle" aria-label="Menu">
                    <i class="fa-solid fa-bars"></i>
                </button>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- =====================================================================
         H. DARK KAABA NEWSLETTER & FOOTER (Screenshot 2 Reference)
         ===================================================================== -->
    <footer class="hajj-footer" id="contact">
        <div class="hajj-footer-main">
            <!-- Left Kaaba Night Image -->
            <div class="hajj-footer-kaaba">
                <img src="{{ $t['footer']['kaaba_image'] ?? '/module-media/HajjUmrah/theme/footer-kaaba-night.jpg' }}" alt="Holy Kaaba">
            </div>

            <!-- Center Subscribe for Newsletter -->
            <div class="hajj-footer-newsletter">
                <h3>{{ $tr['newsletter_title'] ?? 'Subscribe for Newsletter' }}</h3>
                <p>{{ $tr['newsletter_subtitle'] ?? 'Get the latest Hajj & Umrah updates, travel tips and special offers.' }}</p>
                <form action="{{ route('hajjumrah.newsletter.submit') }}" method="POST" class="hajj-newsletter-form">
                    @csrf
                    <input type="email" name="newsletter_email" placeholder="{{ $tr['newsletter_placeholder'] ?? 'Your Email Address' }}" required>
                    <button type="submit">{{ $tr['newsletter_btn'] ?? 'Subscribe' }}</button>
                </form>
            </div>

            <!-- Right 3-Column Links & Contact Info -->
            <div class="hajj-footer-links-grid">
                <div class="hajj-footer-col">
                    <h4>{{ $tr['footer_quick_links'] ?? 'Quick Links' }}</h4>
                    <ul>
                        <li><a href="{{ route('hajjumrah.home') }}">{{ $tr['nav_home'] ?? 'Home' }}</a></li>
                        <li><a href="#special-offers">{{ $tr['nav_hajj'] ?? 'Hajj' }}</a></li>
                        <li><a href="#special-offers">{{ $tr['nav_umrah'] ?? 'Umrah' }}</a></li>
                        <li><a href="#pricing">{{ $tr['pricing_eyebrow'] ?? 'Packages' }}</a></li>
                        <li><a href="#contact" data-open-booking>{{ $tr['nav_contact_btn'] ?? 'Contact' }}</a></li>
                    </ul>
                </div>

                <div class="hajj-footer-col">
                    <h4>{{ $tr['footer_offers'] ?? 'Offers' }}</h4>
                    <ul>
                        <li><a href="#special-offers">{{ $tr['footer_link_special'] ?? 'Special Offers' }}</a></li>
                        <li><a href="#pricing">{{ $tr['footer_link_group'] ?? 'Group Packages' }}</a></li>
                        <li><a href="#special-offers">{{ $tr['footer_link_family'] ?? 'Family Packages' }}</a></li>
                        <li><a href="#pricing">{{ $tr['footer_link_vip'] ?? 'VIP Services' }}</a></li>
                        <li><a href="#blog">{{ $tr['footer_link_tips'] ?? 'Travel Tips' }}</a></li>
                    </ul>
                </div>

                <div class="hajj-footer-col">
                    <h4>{{ $tr['footer_contact'] ?? 'Contact Us' }}</h4>
                    <ul>
                        <li>
                            <i class="fa-solid fa-phone"></i>
                            <a href="tel:{{ $t['general']['contact_phone'] ?? '+880 1234 567890' }}">{{ $t['general']['contact_phone'] ?? '+880 1234 567890' }}</a>
                        </li>
                        <li>
                            <i class="fa-solid fa-envelope"></i>
                            <a href="mailto:{{ $t['general']['contact_email'] ?? 'info@alhajenra.com' }}">{{ $t['general']['contact_email'] ?? 'info@alhajenra.com' }}</a>
                        </li>
                        <li>
                            <i class="fa-solid fa-location-dot"></i>
                            <span>{{ $tr['contact_address'] ?? 'Dhaka, Bangladesh' }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Bottom Copyright & Social Bar -->
        <div class="hajj-footer-bottom">
            <div class="hajj-container hajj-footer-bottom-inner">
                <div>{{ $tr['footer_copyright'] ?? '© 2028 Al Hajenra. All Rights Reserved.' }}</div>
                <div class="hajj-social-links">
                    <a href="{{ $t['general']['facebook_url'] ?? '#' }}" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="{{ $t['general']['youtube_url'] ?? '#' }}" aria-label="YouTube"><i class="fa-brands fa-youtube"></i></a>
                    <a href="{{ $t['general']['instagram_url'] ?? '#' }}" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                    <a href="{{ $t['general']['linkedin_url'] ?? '#' }}" aria-label="LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
                    <a href="{{ $t['general']['twitter_url'] ?? '#' }}" aria-label="X"><i class="fa-brands fa-x-twitter"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <!-- =====================================================================
         PACKAGE BOOKING & CONSULTATION MODAL
         ===================================================================== -->
    <div class="hajj-modal-backdrop" id="hajjBookingModal">
        <div class="hajj-modal-box">
            <button type="button" class="hajj-modal-close" data-close-modal aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
            <div style="margin-bottom: 18px;">
                <span style="display: inline-block; background: #E3F5EA; color: #0E7A3E; font-size: 11.5px; font-weight: 800; padding: 4px 12px; border-radius: 999px; margin-bottom: 6px;" id="hajjModalPackageLabel">
                    Economy Hajj & Umrah Package
                </span>
                <h3 style="font-size: 22px; font-weight: 800; color: #0F172A;">{{ $tr['booking_modal_title'] ?? 'Book Your Hajj / Umrah Package' }}</h3>
                <p style="font-size: 13px; color: #64748B;">{{ $tr['booking_modal_subtitle'] ?? 'Fill in your details below and our dedicated Muallim advisor will contact you shortly.' }}</p>
            </div>

            <form action="{{ route('hajjumrah.inquiry.submit') }}" method="POST">
                @csrf
                <div class="hajj-form-group">
                    <label>{{ $tr['form_package'] ?? 'Selected Package' }}</label>
                    <input type="text" name="package_name" id="hajjSelectedPackageInput" class="hajj-form-control" value="Economy Hajj & Umrah Package" readonly style="background: #F8FAFC; font-weight: 700;">
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <div class="hajj-form-group">
                        <label>{{ $tr['form_name'] ?? 'Full Name' }} *</label>
                        <input type="text" name="name" class="hajj-form-control" required placeholder="Alhaj Mohammad...">
                    </div>
                    <div class="hajj-form-group">
                        <label>{{ $tr['form_phone'] ?? 'Phone / WhatsApp Number' }} *</label>
                        <input type="text" name="phone" class="hajj-form-control" required placeholder="+880 17...">
                    </div>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <div class="hajj-form-group">
                        <label>{{ $tr['form_email'] ?? 'Email Address' }}</label>
                        <input type="email" name="email" class="hajj-form-control" placeholder="you@example.com">
                    </div>
                    <div class="hajj-form-group">
                        <label>{{ $tr['form_pilgrims'] ?? 'Number of Pilgrims' }}</label>
                        <input type="number" name="pilgrim_count" class="hajj-form-control" value="2" min="1" max="50">
                    </div>
                </div>
                <div class="hajj-form-group">
                    <label>{{ $tr['form_date'] ?? 'Preferred Travel Month / Season' }}</label>
                    <input type="text" name="travel_date" class="hajj-form-control" placeholder="e.g. Ramadan 2026 / Hajj 2028">
                </div>
                <div class="hajj-form-group">
                    <label>{{ $tr['form_notes'] ?? 'Special Requirements (Wheelchair, Room Type, etc.)' }}</label>
                    <textarea name="message" rows="2" class="hajj-form-control" placeholder="Any specific Makkah/Madinah hotel distance or flight preference..."></textarea>
                </div>
                <button type="submit" class="hajj-btn-select-pkg" style="margin-top: 6px;">
                    {{ $tr['form_submit'] ?? 'Submit Booking Inquiry' }}
                </button>
            </form>
        </div>
    </div>

    <!-- =====================================================================
         EDUCATIONAL VIDEO MODAL PLAYER
         ===================================================================== -->
    <div class="hajj-modal-backdrop" id="hajjVideoModal">
        <div class="hajj-modal-box" style="max-width: 780px; padding: 16px; background: #080B0A;">
            <button type="button" class="hajj-modal-close" data-close-modal aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
            <div style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; border-radius: 12px;">
                <iframe id="hajjVideoIframe" src="" style="position: absolute; top:0; left:0; width:100%; height:100%; border:0;" allow="autoplay; encrypted-media" allowfullscreen></iframe>
            </div>
        </div>
    </div>

    <script src="{{ route('hajjumrah.asset', ['type' => 'js', 'file' => 'main.js']) }}?v=1.1"></script>
    @stack('scripts')
</body>
</html>
