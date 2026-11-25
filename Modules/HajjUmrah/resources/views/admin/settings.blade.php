@extends('admin.layouts.master')

@php
    $currentTab = $activeTab ?? request()->query('tab', 'builder');
    $tabsMeta = [
        'builder'      => ['title' => 'Homepage Builder & Section Order', 'sub' => 'Drag and drop homepage sections to reorder them and toggle visibility ON/OFF.'],
        'header'       => ['title' => 'Header, Brand Logo & Language Switcher', 'sub' => 'Manage Al Hajenra brand logo, header navigation labels in EN/BN/AR, and language switcher.'],
        'hero'         => ['title' => 'Hero Kaaba Banner & Services Visual', 'sub' => 'Manage the Holy Kaaba hero banner, pilgrim services image, and CTA buttons.'],
        'translations' => ['title' => 'Multilingual Content & RTL Manager (EN / BN / AR)', 'sub' => 'Edit all website headlines, section titles, buttons, and labels across English, Bengali, and Arabic (RTL).'],
        'footer'       => ['title' => 'Footer Kaaba Image, Contact & SEO Settings', 'sub' => 'Manage footer Kaaba night visual, contact details, social links, and multilingual SEO metadata.'],
    ];
    $info = $tabsMeta[$currentTab] ?? $tabsMeta['builder'];
@endphp

@section('title', $info['title'] . ' — Hajj & Umrah Admin')

@section('admin_content')
    <div class="bg-white border rounded-4 p-4 mb-4 shadow-sm">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <h3 style="font-size: 22px; font-weight: 800; color: #0F172A; margin: 0;">{{ $info['title'] }}</h3>
                    <span class="sb-badge-pill sb-badge-green">hajj_settings</span>
                </div>
                <p class="text-muted small mb-0">{{ $info['sub'] }}</p>
            </div>

            <div class="d-flex align-items-center gap-2">
                <a href="{{ url('/hajj-umrah') }}" target="_blank" class="btn btn-outline-success btn-sm fw-semibold px-3 py-2 rounded-3">
                    <i class="bi bi-box-arrow-up-right me-1"></i> Preview Website
                </a>
                <form action="{{ route('admin.hajjumrah.settings.reset') }}" method="POST" onsubmit="return confirm('Reset Hajj & Umrah module back to 100% original default design & seeded packages?')">
                    @csrf
                    <button type="submit" class="btn btn-warning btn-sm fw-bold px-3 py-2 rounded-3">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Defaults
                    </button>
                </form>
            </div>
        </div>

        <!-- Quick Section Switcher Tabs -->
        <div class="d-flex flex-wrap gap-2 mt-3 pt-3 border-top">
            <a href="{{ route('admin.hajjumrah.settings', ['tab' => 'builder']) }}" class="btn btn-sm {{ $currentTab === 'builder' ? 'btn-success' : 'btn-light' }} rounded-pill px-3 fw-semibold">
                <i class="bi bi-layout-wtf me-1"></i> Homepage Builder
            </a>
            <a href="{{ route('admin.hajjumrah.settings', ['tab' => 'header']) }}" class="btn btn-sm {{ $currentTab === 'header' ? 'btn-success' : 'btn-light' }} rounded-pill px-3 fw-semibold">
                <i class="bi bi-segmented-nav me-1"></i> Header &amp; Navigation
            </a>
            <a href="{{ route('admin.hajjumrah.settings', ['tab' => 'hero']) }}" class="btn btn-sm {{ $currentTab === 'hero' ? 'btn-success' : 'btn-light' }} rounded-pill px-3 fw-semibold">
                <i class="bi bi-moon-stars-fill me-1"></i> Hero &amp; Section Images
            </a>
            <a href="{{ route('admin.hajjumrah.settings', ['tab' => 'translations']) }}" class="btn btn-sm {{ $currentTab === 'translations' ? 'btn-success' : 'btn-light' }} rounded-pill px-3 fw-semibold">
                <i class="bi bi-translate me-1"></i> Languages (EN / BN / AR)
            </a>
            <a href="{{ route('admin.hajjumrah.settings', ['tab' => 'footer']) }}" class="btn btn-sm {{ $currentTab === 'footer' ? 'btn-success' : 'btn-light' }} rounded-pill px-3 fw-semibold">
                <i class="bi bi-globe2 me-1"></i> Footer, Contact &amp; SEO
            </a>
        </div>
    </div>

    <form action="{{ route('admin.hajjumrah.settings.save') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- =================================================================
             TAB 1: HOMEPAGE BUILDER (Drag & Drop Section Order + Visibility)
             ================================================================= --}}
        @if($currentTab === 'builder')
            @php
                $sectionLabels = [
                    'hero'           => ['title' => 'Hero Kaaba & Masjid Al-Haram Banner', 'vis' => 'show_hero', 'icon' => 'bi-moon-stars-fill'],
                    'trust_badges'   => ['title' => 'Trustpilot, Facebook & BAAT Review Badges Strip', 'vis' => 'show_trust_badges', 'icon' => 'bi-patch-check-fill'],
                    'special_offers' => ['title' => 'Hajj And Umrah Special Offers (4 Cards)', 'vis' => 'show_special_offers', 'icon' => 'bi-airplane-engines'],
                    'services'       => ['title' => 'What Do We Offer (6 Services + Pilgrim Visual)', 'vis' => 'show_services', 'icon' => 'bi-grid-1x2-fill'],
                    'partners'       => ['title' => 'Our Affiliated Airlines & Hotels Logo Strip', 'vis' => 'show_partners', 'icon' => 'bi-building-check'],
                    'pricing'        => ['title' => 'All Inclusive Packages (Economy, Standard & Classic Pricing)', 'vis' => 'show_pricing', 'icon' => 'bi-tags-fill'],
                    'learning'       => ['title' => 'Featured Learning Section (How to Perform Umrah Videos)', 'vis' => 'show_learning', 'icon' => 'bi-play-btn-fill'],
                    'testimonials'   => ['title' => 'Pilgrim Testimonials & Stories', 'vis' => 'show_testimonials', 'icon' => 'bi-chat-quote-fill'],
                    'gallery'        => ['title' => 'Makkah & Madinah Photo Gallery', 'vis' => 'show_gallery', 'icon' => 'bi-images'],
                    'blog'           => ['title' => 'Latest Hajj & Umrah Travel Guides (Blog)', 'vis' => 'show_blog', 'icon' => 'bi-journal-richtext'],
                ];
                $currentOrder = $t['section_order'] ?? array_keys($sectionLabels);
            @endphp

            <div class="sb-table-card p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <h5 class="fw-bold mb-1">Drag &amp; Drop Homepage Section Builder</h5>
                        <p class="text-muted small mb-0">Use the up/down buttons or drag rows to rearrange sections on the homepage, and toggle switches to show or hide any section.</p>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <label class="small fw-bold text-muted">Default Visitor Language:</label>
                        <select name="sections[general][default_language]" class="form-select form-select-sm" style="width: 170px;">
                            <option value="en" {{ ($t['general']['default_language'] ?? 'en') === 'en' ? 'selected' : '' }}>English (LTR)</option>
                            <option value="bn" {{ ($t['general']['default_language'] ?? 'en') === 'bn' ? 'selected' : '' }}>বাংলা - Bengali (LTR)</option>
                            <option value="ar" {{ ($t['general']['default_language'] ?? 'en') === 'ar' ? 'selected' : '' }}>العربية - Arabic (RTL)</option>
                        </select>
                    </div>
                </div>

                <div id="hajjSortableSections" class="d-flex flex-column gap-2">
                    @foreach($currentOrder as $secKey)
                        @if(isset($sectionLabels[$secKey]))
                            @php
                                $meta = $sectionLabels[$secKey];
                                $visKey = $meta['vis'];
                                $isVisible = ($t['visibility'][$visKey] ?? '1') === '1';
                            @endphp
                            <div class="hajj-builder-item d-flex align-items-center justify-content-between p-3 rounded-3 border bg-light" draggable="true">
                                <input type="hidden" name="sections[section_order][]" value="{{ $secKey }}">
                                <div class="d-flex align-items-center gap-3">
                                    <span class="text-secondary" style="cursor: grab;"><i class="bi bi-grip-vertical fs-5"></i></span>
                                    <div class="rounded-3 bg-white border d-flex align-items-center justify-content-center text-success" style="width: 38px; height: 38px;">
                                        <i class="bi {{ $meta['icon'] }}"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $meta['title'] }}</div>
                                        <code class="small text-muted">Section ID: {{ $secKey }}</code>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center gap-3">
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-secondary move-up" title="Move Up"><i class="bi bi-arrow-up"></i></button>
                                        <button type="button" class="btn btn-outline-secondary move-down" title="Move Down"><i class="bi bi-arrow-down"></i></button>
                                    </div>
                                    <input type="hidden" name="sections[visibility][{{ $visKey }}]" value="0">
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input" type="checkbox" name="sections[visibility][{{ $visKey }}]" value="1" id="vis_{{ $secKey }}" {{ $isVisible ? 'checked' : '' }}>
                                        <label class="form-check-label small fw-semibold" for="vis_{{ $secKey }}">{{ $isVisible ? 'Visible' : 'Hidden' }}</label>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const container = document.getElementById('hajjSortableSections');
                    if (!container) return;
                    container.addEventListener('click', function (e) {
                        const upBtn = e.target.closest('.move-up');
                        const downBtn = e.target.closest('.move-down');
                        if (upBtn) {
                            const item = upBtn.closest('.hajj-builder-item');
                            if (item.previousElementSibling) {
                                container.insertBefore(item, item.previousElementSibling);
                            }
                        }
                        if (downBtn) {
                            const item = downBtn.closest('.hajj-builder-item');
                            if (item.nextElementSibling) {
                                container.insertBefore(item.nextElementSibling, item);
                            }
                        }
                    });
                });
            </script>
        @endif

        {{-- =================================================================
             TAB 2: HEADER, BRAND LOGO & NAVIGATION
             ================================================================= --}}
        @if($currentTab === 'header')
            <div class="sb-table-card p-4">
                <div class="row g-4">
                    <div class="col-lg-4">
                        @include('admin.partials.image-dropzone', [
                            'name'    => 'header_logo',
                            'label'   => 'Custom Header Brand Logo (Optional PNG/SVG)',
                            'preview' => $t['header']['logo'] ?? '',
                            'height'  => '180px',
                            'fit'     => 'contain',
                            'hint'    => 'Leave empty to use the signature Gold 3D Kaaba icon',
                        ])
                        <div class="mt-3 p-3 rounded-3 border bg-light">
                            <input type="hidden" name="sections[header][show_language_switcher]" value="0">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="sections[header][show_language_switcher]" value="1" id="showLangSwitch" {{ ($t['header']['show_language_switcher'] ?? '1') === '1' ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold small" for="showLangSwitch">Show Header Language Switcher (BN / EN / AR)</label>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-8">
                        <h6 class="fw-bold mb-3">Brand Name &amp; Header Menu Labels (English / বাংলা / العربية)</h6>
                        <div class="row g-3">
                            @foreach(['en' => 'English', 'bn' => 'বাংলা (Bengali)', 'ar' => 'العربية (Arabic RTL)'] as $langKey => $langLabel)
                                <div class="col-md-4">
                                    <div class="p-3 rounded-3 border bg-light">
                                        <div class="fw-bold text-success mb-2">{{ $langLabel }}</div>
                                        <div class="mb-2">
                                            <label class="form-label small fw-semibold mb-1">Brand Title</label>
                                            <input type="text" name="sections[translations][{{ $langKey }}][brand_name]" value="{{ $t['translations'][$langKey]['brand_name'] ?? '' }}" class="form-control form-control-sm">
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label small fw-semibold mb-1">Brand Subtitle</label>
                                            <input type="text" name="sections[translations][{{ $langKey }}][brand_subtitle]" value="{{ $t['translations'][$langKey]['brand_subtitle'] ?? '' }}" class="form-control form-control-sm">
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label small fw-semibold mb-1">Home Link</label>
                                            <input type="text" name="sections[translations][{{ $langKey }}][nav_home]" value="{{ $t['translations'][$langKey]['nav_home'] ?? '' }}" class="form-control form-control-sm">
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label small fw-semibold mb-1">Hajj Dropdown</label>
                                            <input type="text" name="sections[translations][{{ $langKey }}][nav_hajj]" value="{{ $t['translations'][$langKey]['nav_hajj'] ?? '' }}" class="form-control form-control-sm">
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label small fw-semibold mb-1">Umrah Dropdown</label>
                                            <input type="text" name="sections[translations][{{ $langKey }}][nav_umrah]" value="{{ $t['translations'][$langKey]['nav_umrah'] ?? '' }}" class="form-control form-control-sm">
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label small fw-semibold mb-1">Facilities Link</label>
                                            <input type="text" name="sections[translations][{{ $langKey }}][nav_facilities]" value="{{ $t['translations'][$langKey]['nav_facilities'] ?? '' }}" class="form-control form-control-sm">
                                        </div>
                                        <div class="mb-0">
                                            <label class="form-label small fw-semibold mb-1">Contact Us Button</label>
                                            <input type="text" name="sections[translations][{{ $langKey }}][nav_contact_btn]" value="{{ $t['translations'][$langKey]['nav_contact_btn'] ?? '' }}" class="form-control form-control-sm">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- =================================================================
             TAB 3: HERO KAABA BANNER & SERVICES PILGRIM IMAGE
             ================================================================= --}}
        @if($currentTab === 'hero')
            <div class="sb-table-card p-4">
                <div class="row g-4">
                    <div class="col-lg-6">
                        @include('admin.partials.image-dropzone', [
                            'name'    => 'hero_image_file',
                            'label'   => 'Hero Makkah Tawaf Slider Background Image',
                            'preview' => $t['hero']['hero_image'] ?? '/module-media/HajjUmrah/theme/hero-makkah-tawaf.jpg',
                            'height'  => '240px',
                            'fit'     => 'cover',
                            'hint'    => 'Recommended: 16:9 Makkah Tawaf & Holy Kaaba slider image',
                        ])
                    </div>
                    <div class="col-lg-6">
                        @include('admin.partials.image-dropzone', [
                            'name'    => 'services_pilgrim_file',
                            'label'   => '"What Do We Offer" Right-Side Pilgrim & Airplane Visual',
                            'preview' => $t['services_section']['pilgrim_image'] ?? '/module-media/HajjUmrah/theme/services-pilgrim.jpg',
                            'height'  => '240px',
                            'fit'     => 'contain',
                            'hint'    => 'Recommended: Pilgrim in Ihram with luggage & Makkah Clock Tower',
                        ])
                    </div>

                    @foreach(['en' => 'English', 'bn' => 'বাংলা (Bengali)', 'ar' => 'العربية (Arabic RTL)'] as $langKey => $langLabel)
                        <div class="col-md-4">
                            <div class="p-3 rounded-3 border bg-light">
                                <h6 class="fw-bold text-success mb-2">Hero Content ({{ $langLabel }})</h6>
                                <div class="mb-2">
                                    <label class="form-label small fw-semibold mb-1">Hero Headline</label>
                                    <input type="text" name="sections[translations][{{ $langKey }}][hero_title]" value="{{ $t['translations'][$langKey]['hero_title'] ?? '' }}" class="form-control form-control-sm">
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small fw-semibold mb-1">Hero Subtitle / Description</label>
                                    <textarea name="sections[translations][{{ $langKey }}][hero_subtitle]" rows="3" class="form-control form-control-sm sb-rich-editor sb-rte-compact" dir="{{ $langKey === 'ar' ? 'rtl' : 'ltr' }}">{{ $t['translations'][$langKey]['hero_subtitle'] ?? '' }}</textarea>
                                </div>
                                <div class="mb-0">
                                    <label class="form-label small fw-semibold mb-1">CTA Button Text</label>
                                    <input type="text" name="sections[translations][{{ $langKey }}][hero_cta]" value="{{ $t['translations'][$langKey]['hero_cta'] ?? '' }}" class="form-control form-control-sm">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- =================================================================
             TAB 4: MULTILINGUAL TRANSLATIONS (EN / BN / AR)
             ================================================================= --}}
        @if($currentTab === 'translations')
            <div class="sb-table-card p-4">
                <h5 class="fw-bold mb-3">Section Titles &amp; Labels Across English (EN), Bengali (BN), and Arabic (AR - RTL)</h5>
                <div class="row g-4">
                    @php
                        $transFields = [
                            'offers_title'        => 'Special Offers Title',
                            'offers_subtitle'     => 'Special Offers Subtitle',
                            'offers_view_details' => 'View Details Button',
                            'services_eyebrow'    => 'Services Eyebrow',
                            'services_title'      => 'Services Main Title',
                            'services_subtitle'   => 'Services Description',
                            'services_btn'        => 'Services Learn More Button',
                            'partners_title'      => 'Affiliated Partners Title',
                            'pricing_eyebrow'     => 'Pricing Eyebrow',
                            'pricing_title'       => 'All Inclusive Pricing Title',
                            'pricing_subtitle'    => 'Pricing Subtitle',
                            'pricing_select_btn'  => 'Select Package Button',
                            'learning_title'      => 'Featured Learning Title',
                            'learning_watch_btn'  => 'Watch Video Button',
                            'newsletter_title'    => 'Newsletter Title',
                            'newsletter_btn'      => 'Subscribe Button',
                        ];
                    @endphp

                    @foreach(['en' => 'English (LTR)', 'bn' => 'বাংলা - Bengali (LTR)', 'ar' => 'العربية - Arabic (RTL)'] as $langKey => $langTitle)
                        <div class="col-lg-4">
                            <div class="p-3 rounded-4 border bg-light">
                                <h6 class="fw-bold text-success border-bottom pb-2 mb-3">{{ $langTitle }}</h6>
                                @foreach($transFields as $fKey => $fLabel)
                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold mb-1">{{ $fLabel }}</label>
                                        @if(in_array($fKey, ['offers_subtitle', 'services_subtitle', 'pricing_subtitle']))
                                            <textarea name="sections[translations][{{ $langKey }}][{{ $fKey }}]"
                                                      rows="2"
                                                      class="form-control form-control-sm sb-rich-editor sb-rte-compact"
                                                      dir="{{ $langKey === 'ar' ? 'rtl' : 'ltr' }}">{{ $t['translations'][$langKey][$fKey] ?? '' }}</textarea>
                                        @else
                                            <input type="text"
                                                   name="sections[translations][{{ $langKey }}][{{ $fKey }}]"
                                                   value="{{ $t['translations'][$langKey][$fKey] ?? '' }}"
                                                   class="form-control form-control-sm"
                                                   dir="{{ $langKey === 'ar' ? 'rtl' : 'ltr' }}">
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- =================================================================
             TAB 5: FOOTER KAABA IMAGE, CONTACT & SEO SETTINGS
             ================================================================= --}}
        @if($currentTab === 'footer')
            <div class="sb-table-card p-4">
                <div class="row g-4">
                    <div class="col-lg-4">
                        @include('admin.partials.image-dropzone', [
                            'name'    => 'footer_kaaba_file',
                            'label'   => 'Footer Left Kaaba Night Image',
                            'preview' => $t['footer']['kaaba_image'] ?? '/module-media/HajjUmrah/theme/footer-kaaba-night.jpg',
                            'height'  => '200px',
                            'fit'     => 'cover',
                            'hint'    => 'Displayed on the left side of the dark newsletter footer',
                        ])
                    </div>
                    <div class="col-lg-8">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Contact Phone / WhatsApp</label>
                                <input type="text" name="sections[general][contact_phone]" value="{{ $t['general']['contact_phone'] ?? '' }}" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Contact Email</label>
                                <input type="email" name="sections[general][contact_email]" value="{{ $t['general']['contact_email'] ?? '' }}" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Address (English)</label>
                                <input type="text" name="sections[translations][en][contact_address]" value="{{ $t['translations']['en']['contact_address'] ?? '' }}" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Address (বাংলা)</label>
                                <input type="text" name="sections[translations][bn][contact_address]" value="{{ $t['translations']['bn']['contact_address'] ?? '' }}" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Address (العربية)</label>
                                <input type="text" name="sections[translations][ar][contact_address]" value="{{ $t['translations']['ar']['contact_address'] ?? '' }}" class="form-control" dir="rtl">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">SEO Meta Title (EN)</label>
                                <input type="text" name="sections[translations][en][meta_title]" value="{{ $t['translations']['en']['meta_title'] ?? '' }}" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">SEO Meta Title (BN)</label>
                                <input type="text" name="sections[translations][bn][meta_title]" value="{{ $t['translations']['bn']['meta_title'] ?? '' }}" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">SEO Meta Title (AR)</label>
                                <input type="text" name="sections[translations][ar][meta_title]" value="{{ $t['translations']['ar']['meta_title'] ?? '' }}" class="form-control" dir="rtl">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold small">SEO Meta Keywords</label>
                                <input type="text" name="sections[general][meta_keywords]" value="{{ $t['general']['meta_keywords'] ?? '' }}" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="mt-4 d-flex justify-content-end">
            <button type="submit" class="btn btn-success fw-bold px-4 py-2 rounded-3 shadow-sm">
                <i class="bi bi-check2-circle me-1"></i> Save Changes to hajj_settings
            </button>
        </div>
    </form>
@endsection
