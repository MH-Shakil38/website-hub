@extends('admin.layouts.master')

@php
    $currentTab = $activeTab ?? request()->query('tab', 'hero');
    $sectionMeta = [
        'header'       => ['title' => 'Header & Brand Logo', 'sub' => 'Change your website logo, brand name, and top consultation button.'],
        'hero'         => ['title' => 'Hero Banner (#home)', 'sub' => 'Customize the main top banner text, buttons, and background image.'],
        'services'     => ['title' => 'Our Services (#services)', 'sub' => 'Edit service cards, drag & drop photos, or click "+ Add More Service" to add new services.'],
        'founder'      => ['title' => 'Meet the Founder (#about)', 'sub' => 'Update founder portrait photo, name, designation, and story paragraphs.'],
        'projects'     => ['title' => 'Featured Projects (#projects)', 'sub' => 'Edit the 5 featured project cards and their cover photos on the homepage.'],
        'gallery'      => ['title' => 'Photo Gallery (#gallery)', 'sub' => 'Customize the Photo Gallery section heading and upload room photos.'],
        'videos'       => ['title' => 'Video Gallery (#videos)', 'sub' => 'Customize the Video Gallery section heading and manage videos.'],
        'process'      => ['title' => '5-Step Process & Why Choose Us (#process)', 'sub' => 'Edit the 5 design process steps and the 4 "Why Choose Us" bullet points.'],
        'testimonials' => ['title' => 'Client Testimonials (#journal)', 'sub' => 'Update client quotes, names, and locations.'],
        'cta'          => ['title' => 'Contact CTA Banner (#contact)', 'sub' => 'Customize the call-to-action banner text, button, and photo.'],
        'footer'       => ['title' => 'Footer & Theme Colors', 'sub' => 'Update footer contact details, arch image, copyright text, and theme colors.'],
    ];
    $secInfo = $sectionMeta[$currentTab] ?? $sectionMeta['hero'];
    $activeModCount = isset($activeModuleWebsites) ? $activeModuleWebsites->count() : 1;
    $liveUrl = $activeModCount === 1 ? url('/') : url('/interior-design');
@endphp

@section('title', $secInfo['title'] . ' — Interior Design')

@section('admin_content')
    <!-- TOP SINGLE-ROW HEADER: Heading in col-8, Preview & Reset Buttons in col-4 -->
    <div class="bg-white border rounded-4 p-4 mb-4 shadow-sm">
        <div class="row align-items-center g-3">
            <div class="col-md-8">
                <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                    <h3 style="font-size: 22px; font-weight: 800; color: #0B2545; margin: 0;">
                        {{ $secInfo['title'] }}
                    </h3>
                    <span class="badge rounded-pill" style="background: #DCFCE7; color: #15803D; font-size: 11px; font-weight: 600;">
                        <i class="bi bi-shield-check me-1"></i>Smart Default Fallback
                    </span>
                </div>
                <p style="font-size: 13px; color: #64748B; margin: 0;">
                    {{ $secInfo['sub'] }} <strong>Untouched photos keep original default.</strong>
                </p>
            </div>

            <div class="col-md-4 d-flex justify-content-md-end align-items-center gap-2">
                <a href="{{ $liveUrl }}" target="_blank" class="btn btn-outline-dark fw-semibold px-3" style="height: 40px; border-radius: 10px; display: inline-flex; align-items: center; gap: 6px; font-size: 13px;">
                    <i class="bi bi-eye"></i>
                    <span>Preview</span>
                </a>

                <form action="{{ route('admin.interior.theme.settings.reset') }}" method="POST" class="m-0" onsubmit="return confirm('Reset ALL sections & images back to the 100% original Geotech default template?')">
                    @csrf
                    <button type="submit" class="btn btn-warning fw-bold px-3" style="height: 40px; border-radius: 10px; display: inline-flex; align-items: center; gap: 6px; font-size: 13px;">
                        <i class="bi bi-arrow-counterclockwise"></i>
                        <span>Reset</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- MAIN ORGANIZED SECTION FORM -->
    <form action="{{ route('admin.interior.theme.settings.save') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- 1. HEADER & LOGO -->
        @if($currentTab === 'header')
            <div class="sb-table-card p-4">
                <div class="row g-4 align-items-stretch">
                    <!-- Left Column: Unified Drag & Drop Logo Preview + Uploader -->
                    <div class="col-lg-4">
                        @include('admin.partials.image-dropzone', [
                            'name'    => 'header_logo',
                            'label'   => 'Brand Logo (Drag & Drop or Click)',
                            'preview' => $t['header']['logo'],
                            'height'  => '215px',
                            'fit'     => 'contain',
                            'hint'    => 'Drop new logo here or click to browse',
                        ])
                    </div>

                    <!-- Right Column: Organized Brand & Header Fields -->
                    <div class="col-lg-8">
                        <div class="p-3 rounded-3 border bg-light h-100 d-flex flex-column justify-content-center">
                            <h6 class="fw-bold text-dark mb-3"><i class="bi bi-sliders me-1 text-danger"></i> Brand Identity &amp; Top Action Button</h6>
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label fw-semibold small">Browser Page Title (SEO Title)</label>
                                    <input type="text" name="sections[meta][page_title]" value="{{ $t['meta']['page_title'] }}" class="form-control" style="height: 44px; border-radius: 10px;">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Brand Main Name</label>
                                    <input type="text" name="sections[header][brand_name]" value="{{ $t['header']['brand_name'] }}" class="form-control" style="height: 44px; border-radius: 10px;">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Brand Subtitle</label>
                                    <input type="text" name="sections[header][brand_sub]" value="{{ $t['header']['brand_sub'] }}" class="form-control" style="height: 44px; border-radius: 10px;">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Top Right Button Text</label>
                                    <input type="text" name="sections[header][cta_text]" value="{{ $t['header']['cta_text'] }}" class="form-control" style="height: 44px; border-radius: 10px;">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Top Right Button Link</label>
                                    <input type="text" name="sections[header][cta_url]" value="{{ $t['header']['cta_url'] }}" class="form-control" style="height: 44px; border-radius: 10px;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        <!-- 2. HERO BANNER -->
        @elseif($currentTab === 'hero')
            <div class="sb-table-card p-4">
                <div class="row g-4">
                    <!-- Left Column: Unified Drag & Drop Hero Background -->
                    <div class="col-lg-5">
                        @include('admin.partials.image-dropzone', [
                            'name'    => 'hero_bg_image',
                            'label'   => 'Hero Background Image (Drag & Drop or Click)',
                            'preview' => $t['hero']['bg_image'],
                            'height'  => '285px',
                            'fit'     => 'cover',
                            'hint'    => 'Drop hero banner photo here or click to browse',
                        ])
                    </div>

                    <!-- Right Column: Organized Hero Headlines & Buttons -->
                    <div class="col-lg-7">
                        <div class="p-3 rounded-3 border bg-light h-100">
                            <h6 class="fw-bold text-dark mb-3"><i class="bi bi-type-h1 me-1 text-danger"></i> Hero Headline &amp; Call-to-Action Buttons</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Small Top Eyebrow Text</label>
                                    <input type="text" name="sections[hero][eyebrow]" value="{{ $t['hero']['eyebrow'] }}" class="form-control" style="height: 42px; border-radius: 10px;">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Heading Line 1</label>
                                    <input type="text" name="sections[hero][line_1]" value="{{ $t['hero']['line_1'] }}" class="form-control" style="height: 42px; border-radius: 10px;">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Heading Line 2 (Italic Highlight Word)</label>
                                    <input type="text" name="sections[hero][line_2_em]" value="{{ $t['hero']['line_2_em'] }}" class="form-control" style="height: 42px; border-radius: 10px;">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Heading Line 3 (Italic Highlight Word)</label>
                                    <input type="text" name="sections[hero][line_3_em]" value="{{ $t['hero']['line_3_em'] }}" class="form-control" style="height: 42px; border-radius: 10px;">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-semibold small">Hero Subtitle Paragraph</label>
                                    <input type="text" name="sections[hero][description]" value="{{ $t['hero']['description'] }}" class="form-control" style="height: 42px; border-radius: 10px;">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Primary Button Text</label>
                                    <input type="text" name="sections[hero][btn_primary_text]" value="{{ $t['hero']['btn_primary_text'] }}" class="form-control" style="height: 42px; border-radius: 10px;">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Primary Button Link</label>
                                    <input type="text" name="sections[hero][btn_primary_url]" value="{{ $t['hero']['btn_primary_url'] }}" class="form-control" style="height: 42px; border-radius: 10px;">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Secondary Call Button Text</label>
                                    <input type="text" name="sections[hero][btn_secondary_text]" value="{{ $t['hero']['btn_secondary_text'] }}" class="form-control" style="height: 42px; border-radius: 10px;">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Secondary Call Button Link</label>
                                    <input type="text" name="sections[hero][btn_secondary_url]" value="{{ $t['hero']['btn_secondary_url'] }}" class="form-control" style="height: 42px; border-radius: 10px;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        <!-- 3. SERVICES (DYNAMIC ADD MORE / REMOVE CARDS + INSTANT ONCHANGE CAROUSEL SWITCHER) -->
        @elseif($currentTab === 'services')
            @php
                $svcDisplayMode = $t['services']['display_mode'] ?? 'grid';
                $svcCarouselMode = $t['services']['carousel_mode'] ?? 'auto';
            @endphp
            <div class="sb-table-card p-4">
                <!-- TOP LIVE ONCHANGE LAYOUT & OWL CAROUSEL CONTROL BAR -->
                <div class="p-3 rounded-4 border mb-4 d-flex align-items-center justify-content-between flex-wrap gap-3" style="background: linear-gradient(90deg, #FFF1F5 0%, #F8FAFC 100%); border-color: #FECDD3 !important;">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width: 44px; height: 44px; border-radius: 12px; background: #EC134A; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0; box-shadow: 0 4px 10px rgba(236, 19, 74, 0.25);">
                            <i class="bi bi-view-stacked"></i>
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2">
                                <h6 class="fw-bold text-dark m-0">Frontend Services Display Mode</h6>
                                <span id="svcQuickSaveStatus" class="badge rounded-pill" style="display: none; background: #10B981; color: #fff; font-size: 11px;">
                                    <i class="bi bi-check2-circle me-1"></i>Saved!
                                </span>
                            </div>
                            <div class="small text-muted">Choose whether services wrap into multiple rows or slide in an Owl Carousel (changes save automatically on click).</div>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-3 flex-wrap">
                        <!-- Switch 1: Normal Multi-Row Grid vs Owl Carousel Slider -->
                        <div class="bg-white p-1 rounded-3 border d-inline-flex align-items-center gap-1 shadow-sm">
                            <label class="m-0" style="cursor: pointer;">
                                <input type="radio" name="sections[services][display_mode]" value="grid" class="d-none svc-mode-radio" {{ $svcDisplayMode === 'grid' ? 'checked' : '' }}>
                                <span class="px-3 py-2 rounded-2 d-inline-flex align-items-center gap-1 fw-bold small svc-mode-pill" style="transition: all 0.2s;">
                                    <i class="bi bi-grid-3x2-gap"></i> Normal Row Grid
                                </span>
                            </label>
                            <label class="m-0" style="cursor: pointer;">
                                <input type="radio" name="sections[services][display_mode]" value="carousel" class="d-none svc-mode-radio" {{ $svcDisplayMode === 'carousel' ? 'checked' : '' }}>
                                <span class="px-3 py-2 rounded-2 d-inline-flex align-items-center gap-1 fw-bold small svc-mode-pill" style="transition: all 0.2s;">
                                    <i class="bi bi-collection-play"></i> Owl Carousel Slider
                                </span>
                            </label>
                        </div>

                        <!-- Switch 2: Auto Slide vs Click Slide (Shown when Owl Carousel is active) -->
                        <div id="svcCarouselBehaviorWrap" class="bg-white p-1 rounded-3 border align-items-center gap-1 shadow-sm" style="display: {{ $svcDisplayMode === 'carousel' ? 'inline-flex' : 'none' }};">
                            <span class="small fw-bold text-muted px-2">Slide:</span>
                            <label class="m-0" style="cursor: pointer;">
                                <input type="radio" name="sections[services][carousel_mode]" value="auto" class="d-none svc-behavior-radio" {{ $svcCarouselMode === 'auto' ? 'checked' : '' }}>
                                <span class="px-3 py-2 rounded-2 d-inline-flex align-items-center gap-1 fw-bold small svc-behavior-pill" style="transition: all 0.2s;">
                                    <i class="bi bi-play-circle-fill"></i> Auto Slide
                                </span>
                            </label>
                            <label class="m-0" style="cursor: pointer;">
                                <input type="radio" name="sections[services][carousel_mode]" value="click" class="d-none svc-behavior-radio" {{ $svcCarouselMode === 'click' ? 'checked' : '' }}>
                                <span class="px-3 py-2 rounded-2 d-inline-flex align-items-center gap-1 fw-bold small svc-behavior-pill" style="transition: all 0.2s;">
                                    <i class="bi bi-hand-index-thumb"></i> Click Only
                                </span>
                            </label>
                        </div>
                    </div>
                </div>

                @push('module_styles')
                <style>
                    .svc-mode-radio + .svc-mode-pill,
                    .svc-behavior-radio + .svc-behavior-pill {
                        color: #475569;
                        background: transparent;
                    }
                    .svc-mode-radio:checked + .svc-mode-pill {
                        background: #EC134A;
                        color: #FFFFFF;
                        box-shadow: 0 3px 8px rgba(236, 19, 74, 0.25);
                    }
                    .svc-behavior-radio:checked + .svc-behavior-pill {
                        background: #0B2545;
                        color: #FFFFFF;
                        box-shadow: 0 3px 8px rgba(11, 37, 69, 0.22);
                    }
                </style>
                @endpush

                <!-- Section Heading Configuration Box -->
                <div class="p-3 rounded-4 border bg-light mb-4">
                    <h6 class="fw-bold text-dark mb-3">
                        <i class="bi bi-sliders me-1 text-danger"></i> Services Section Header Text
                    </h6>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small">Section Eyebrow</label>
                            <input type="text" name="sections[services][eyebrow]" value="{{ $t['services']['eyebrow'] }}" class="form-control" style="height: 42px; border-radius: 10px;">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Main Heading (Line 1)</label>
                            <input type="text" name="sections[services][heading_line1]" value="{{ $t['services']['heading_line1'] }}" class="form-control" style="height: 42px; border-radius: 10px;">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold small">Line 2 Prefix</label>
                            <input type="text" name="sections[services][heading_line2_prefix]" value="{{ $t['services']['heading_line2_prefix'] }}" class="form-control" style="height: 42px; border-radius: 10px;">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small">Highlight Word (Italic)</label>
                            <div class="input-group">
                                <input type="text" name="sections[services][heading_line2_em]" value="{{ $t['services']['heading_line2_em'] }}" class="form-control" placeholder="you" style="height: 42px; border-radius: 10px 0 0 10px;">
                                <input type="text" name="sections[services][heading_line2_suffix]" value="{{ $t['services']['heading_line2_suffix'] }}" class="form-control" placeholder="live." style="height: 42px; border-radius: 0 10px 10px 0;">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Services Repeater Toolbar -->
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <h6 class="fw-bold text-dark m-0">
                            <i class="bi bi-grid-1x2-fill me-1 text-danger"></i> Service Cards
                        </h6>
                        <span class="badge rounded-pill" id="servicesCountBadge" style="background: #FFE4EC; color: #EC134A; font-size: 12px;">
                            {{ count($t['services']['items']) }} Services
                        </span>
                    </div>

                    <button type="button" id="addMoreServiceBtn" class="btn text-white fw-bold px-3" style="height: 40px; background: #0B2545; border-radius: 10px; font-size: 13px; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 4px 10px rgba(11, 37, 69, 0.15);">
                        <i class="bi bi-plus-circle-fill text-warning"></i>
                        <span>Add More Service</span>
                    </button>
                </div>

                <!-- Organized 2-Column Service Cards Grid -->
                <div class="row g-4" id="servicesRepeaterGrid">
                    @foreach($t['services']['items'] as $idx => $svc)
                        <div class="col-xl-6 service-card-item" data-service-index="{{ $idx }}">
                            <div class="p-3 border rounded-4 bg-white shadow-sm h-100">
                                <!-- Card Top Header -->
                                <div class="d-flex align-items-center justify-content-between pb-2 mb-3 border-bottom">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge rounded-pill service-num-badge" style="background: #0B2545; color: #fff; font-size: 11.5px; padding: 5px 11px;">
                                            Service #{{ $idx + 1 }}
                                        </span>
                                        <span class="fw-bold small text-dark service-card-heading">{{ $svc['title'] }}</span>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-danger remove-service-btn py-1 px-2" style="border-radius: 8px; font-size: 11.5px; font-weight: 600;">
                                        <i class="bi bi-trash3 me-1"></i>Remove
                                    </button>
                                </div>

                                @if(!str_starts_with($svc['image'] ?? '', 'data:image/'))
                                    <input type="hidden" name="sections[services][items][{{ $idx }}][existing_image]" value="{{ $svc['image'] }}">
                                @endif

                                <!-- Card Inner 2-Column Split: Drag & Drop Photo on Left, Inputs on Right -->
                                <div class="row g-3 align-items-stretch">
                                    <div class="col-sm-5">
                                        @include('admin.partials.image-dropzone', [
                                            'name'    => "service_image_{$idx}",
                                            'label'   => 'Service Photo',
                                            'preview' => $svc['image'],
                                            'height'  => '182px',
                                            'fit'     => 'cover',
                                            'hint'    => 'Drop photo or click',
                                        ])
                                    </div>

                                    <div class="col-sm-7 d-flex flex-column justify-content-between">
                                        <div class="mb-2">
                                            <label class="form-label fw-semibold small mb-1">Service Title</label>
                                            <input type="text"
                                                   name="sections[services][items][{{ $idx }}][title]"
                                                   value="{{ $svc['title'] }}"
                                                   placeholder="e.g. Full-Service Interior Design"
                                                   class="form-control service-title-input"
                                                   style="height: 40px; border-radius: 9px;"
                                                   required>
                                        </div>

                                        <div class="mb-2">
                                            <label class="form-label fw-semibold small mb-1">Short Description</label>
                                            <textarea name="sections[services][items][{{ $idx }}][description]"
                                                      rows="2"
                                                      placeholder="Brief description of this service..."
                                                      class="form-control"
                                                      style="border-radius: 9px; font-size: 13px;">{{ $svc['description'] }}</textarea>
                                        </div>

                                        <div class="row g-2">
                                            <div class="col-6">
                                                <label class="form-label fw-semibold small mb-1">Link Text</label>
                                                <input type="text"
                                                       name="sections[services][items][{{ $idx }}][link_text]"
                                                       value="{{ $svc['link_text'] ?? 'Learn more' }}"
                                                       class="form-control"
                                                       style="height: 36px; border-radius: 8px; font-size: 12.5px;">
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label fw-semibold small mb-1">Link URL</label>
                                                <input type="text"
                                                       name="sections[services][items][{{ $idx }}][link_url]"
                                                       value="{{ $svc['link_url'] ?? '#contact' }}"
                                                       class="form-control"
                                                       style="height: 36px; border-radius: 8px; font-size: 12.5px;">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            @push('module_scripts')
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const grid = document.getElementById('servicesRepeaterGrid');
                    const addBtn = document.getElementById('addMoreServiceBtn');
                    const countBadge = document.getElementById('servicesCountBadge');
                    const defaultPlaceholderImg = @json($t['services']['items'][0]['image'] ?? '');
                    let nextServiceIndex = {{ count($t['services']['items']) }};

                    function refreshServiceNumbers() {
                        const cards = grid.querySelectorAll('.service-card-item');
                        cards.forEach((card, i) => {
                            const numBadge = card.querySelector('.service-num-badge');
                            if (numBadge) numBadge.textContent = 'Service #' + (i + 1);
                            const rmBtn = card.querySelector('.remove-service-btn');
                            if (rmBtn) rmBtn.style.display = cards.length > 1 ? 'inline-block' : 'none';
                        });
                        if (countBadge) {
                            countBadge.textContent = cards.length + (cards.length === 1 ? ' Service' : ' Services');
                        }
                    }

                    grid.addEventListener('input', function (e) {
                        if (e.target.classList.contains('service-title-input')) {
                            const card = e.target.closest('.service-card-item');
                            const heading = card ? card.querySelector('.service-card-heading') : null;
                            if (heading) heading.textContent = e.target.value || 'New Service';
                        }
                    });

                    grid.addEventListener('click', function (e) {
                        const rmBtn = e.target.closest('.remove-service-btn');
                        if (!rmBtn) return;
                        const cards = grid.querySelectorAll('.service-card-item');
                        if (cards.length <= 1) {
                            alert('At least 1 service card is required.');
                            return;
                        }
                        rmBtn.closest('.service-card-item').remove();
                        refreshServiceNumbers();
                    });

                    if (addBtn) {
                        addBtn.addEventListener('click', function () {
                            const idx = nextServiceIndex++;
                            const dzId = 'dz_service_image_' + idx + '_' + Date.now();
                            const cardCol = document.createElement('div');
                            cardCol.className = 'col-xl-6 service-card-item';
                            cardCol.setAttribute('data-service-index', idx);
                            cardCol.innerHTML = `
                                <div class="p-3 border rounded-4 bg-white shadow-sm h-100" style="border-color: #EC134A !important;">
                                    <div class="d-flex align-items-center justify-content-between pb-2 mb-3 border-bottom">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge rounded-pill service-num-badge" style="background: #EC134A; color: #fff; font-size: 11.5px; padding: 5px 11px;">
                                                Service #${idx + 1}
                                            </span>
                                            <span class="fw-bold small text-dark service-card-heading">New Interior Service</span>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-danger remove-service-btn py-1 px-2" style="border-radius: 8px; font-size: 11.5px; font-weight: 600;">
                                            <i class="bi bi-trash3 me-1"></i>Remove
                                        </button>
                                    </div>

                                    <div class="row g-3 align-items-stretch">
                                        <div class="col-sm-5">
                                            <div class="sb-dropzone-wrap">
                                                <label class="form-label fw-bold small text-dark mb-2 d-flex align-items-center justify-content-between">
                                                    <span class="sb-dz-label-text">Service Photo</span>
                                                    <span class="badge rounded-pill fw-semibold" id="${dzId}_badge" style="background: #F1F5F9; color: #64748B; font-size: 10.5px;">Default Ready</span>
                                                </label>
                                                <div class="sb-dropzone"
                                                     id="${dzId}"
                                                     data-original-src="${defaultPlaceholderImg}"
                                                     style="position: relative; height: 182px; border: 2px dashed #CBD5E1; border-radius: 14px; background: #F8FAFC; overflow: hidden; cursor: pointer; transition: all 0.2s ease; display: flex; align-items: center; justify-content: center;">
                                                    <input type="file"
                                                           name="service_image_${idx}"
                                                           id="${dzId}_input"
                                                           accept="image/*"
                                                           style="position: absolute; inset: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer; z-index: 5;">
                                                    <img src="${defaultPlaceholderImg}"
                                                         id="${dzId}_img"
                                                         alt="Preview"
                                                         style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.25s ease;">
                                                    <div class="sb-dz-overlay"
                                                         style="position: absolute; inset: 0; background: linear-gradient(to top, rgba(15, 23, 42, 0.82) 0%, rgba(15, 23, 42, 0.35) 55%, rgba(15, 23, 42, 0.15) 100%); display: flex; flex-direction: column; align-items: center; justify-content: flex-end; padding: 14px; text-align: center; color: #FFFFFF; pointer-events: none;">
                                                        <div style="width: 34px; height: 34px; border-radius: 50%; background: rgba(236, 19, 74, 0.92); color: #fff; display: flex; align-items: center; justify-content: center; margin-bottom: 5px;">
                                                            <i class="bi bi-cloud-arrow-up-fill" style="font-size: 15px;"></i>
                                                        </div>
                                                        <div style="font-size: 12px; font-weight: 700;">Drop photo or click</div>
                                                        <div style="font-size: 10.5px; color: #E2E8F0;" id="${dzId}_filename">PNG, JPG, WEBP or SVG</div>
                                                    </div>
                                                    <button type="button"
                                                            id="${dzId}_reset"
                                                            style="display: none; position: absolute; top: 10px; right: 10px; z-index: 10; background: rgba(15, 23, 42, 0.85); color: #fff; border: none; border-radius: 8px; padding: 4px 10px; font-size: 11px; font-weight: 600; cursor: pointer;">
                                                        <i class="bi bi-x-lg me-1"></i>Undo
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-7 d-flex flex-column justify-content-between">
                                            <div class="mb-2">
                                                <label class="form-label fw-semibold small mb-1">Service Title</label>
                                                <input type="text"
                                                       name="sections[services][items][${idx}][title]"
                                                       value="New Interior Service"
                                                       class="form-control service-title-input"
                                                       style="height: 40px; border-radius: 9px;"
                                                       required>
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label fw-semibold small mb-1">Short Description</label>
                                                <textarea name="sections[services][items][${idx}][description]"
                                                          rows="2"
                                                          class="form-control"
                                                          style="border-radius: 9px; font-size: 13px;">Tailored interior design crafted around your space and lifestyle.</textarea>
                                            </div>
                                            <div class="row g-2">
                                                <div class="col-6">
                                                    <label class="form-label fw-semibold small mb-1">Link Text</label>
                                                    <input type="text"
                                                           name="sections[services][items][${idx}][link_text]"
                                                           value="Learn more"
                                                           class="form-control"
                                                           style="height: 36px; border-radius: 8px; font-size: 12.5px;">
                                                </div>
                                                <div class="col-6">
                                                    <label class="form-label fw-semibold small mb-1">Link URL</label>
                                                    <input type="text"
                                                           name="sections[services][items][${idx}][link_url]"
                                                           value="#contact"
                                                           class="form-control"
                                                           style="height: 36px; border-radius: 8px; font-size: 12.5px;">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;
                            grid.appendChild(cardCol);
                            if (window.initSbDropzones) window.initSbDropzones();
                            refreshServiceNumbers();
                            cardCol.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        });
                    }

                    refreshServiceNumbers();

                    // Instant onchange AJAX save for Services Display Mode & Owl Carousel Slide Behavior
                    const modeRadios = document.querySelectorAll('.svc-mode-radio');
                    const behaviorRadios = document.querySelectorAll('.svc-behavior-radio');
                    const behaviorWrap = document.getElementById('svcCarouselBehaviorWrap');
                    const statusBadge = document.getElementById('svcQuickSaveStatus');

                    function triggerQuickSave() {
                        const selectedMode = document.querySelector('.svc-mode-radio:checked')?.value || 'grid';
                        const selectedBehavior = document.querySelector('.svc-behavior-radio:checked')?.value || 'auto';

                        if (behaviorWrap) {
                            behaviorWrap.style.display = selectedMode === 'carousel' ? 'inline-flex' : 'none';
                        }

                        if (statusBadge) {
                            statusBadge.style.display = 'inline-block';
                            statusBadge.style.background = '#64748B';
                            statusBadge.innerHTML = '<i class="bi bi-arrow-repeat me-1"></i>Saving...';
                        }

                        fetch(@json(route('admin.interior.theme.settings.quick-toggle')), {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': @json(csrf_token()),
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({
                                display_mode: selectedMode,
                                carousel_mode: selectedBehavior,
                            }),
                        })
                        .then(res => res.json())
                        .then(() => {
                            if (statusBadge) {
                                statusBadge.style.background = '#10B981';
                                statusBadge.innerHTML = '<i class="bi bi-check2-circle me-1"></i>Saved Automatically!';
                                setTimeout(() => { statusBadge.style.display = 'none'; }, 2200);
                            }
                        })
                        .catch(() => {
                            if (statusBadge) statusBadge.style.display = 'none';
                        });
                    }

                    modeRadios.forEach(r => r.addEventListener('change', triggerQuickSave));
                    behaviorRadios.forEach(r => r.addEventListener('change', triggerQuickSave));
                });
            </script>
            @endpush

        <!-- 4. MEET THE FOUNDER -->
        @elseif($currentTab === 'founder')
            <div class="sb-table-card p-4">
                <div class="row g-4">
                    <div class="col-lg-4">
                        @include('admin.partials.image-dropzone', [
                            'name'    => 'founder_photo',
                            'label'   => 'Founder Portrait Photo',
                            'preview' => $t['founder']['photo'],
                            'height'  => '270px',
                            'fit'     => 'cover',
                            'hint'    => 'Drop founder photo here or click to browse',
                        ])
                    </div>

                    <div class="col-lg-8">
                        <div class="p-3 rounded-3 border bg-light h-100">
                            <h6 class="fw-bold text-dark mb-3"><i class="bi bi-person-lines-fill me-1 text-danger"></i> Founder Details &amp; Story</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Founder Name</label>
                                    <input type="text" name="sections[founder][name]" value="{{ $t['founder']['name'] }}" class="form-control" style="height: 42px; border-radius: 10px;">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Founder Designation</label>
                                    <input type="text" name="sections[founder][designation]" value="{{ $t['founder']['designation'] }}" class="form-control" style="height: 42px; border-radius: 10px;">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Heading Line 1</label>
                                    <input type="text" name="sections[founder][heading_line1]" value="{{ $t['founder']['heading_line1'] }}" class="form-control" style="height: 42px; border-radius: 10px;">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Heading Line 2</label>
                                    <input type="text" name="sections[founder][heading_line2]" value="{{ $t['founder']['heading_line2'] }}" class="form-control" style="height: 42px; border-radius: 10px;">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-semibold small">Story Paragraph 1</label>
                                    <textarea name="sections[founder][para_1]" rows="2" class="form-control" style="border-radius: 10px;">{{ $t['founder']['para_1'] }}</textarea>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-semibold small">Story Paragraph 2</label>
                                    <textarea name="sections[founder][para_2]" rows="2" class="form-control" style="border-radius: 10px;">{{ $t['founder']['para_2'] }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        <!-- 5. FEATURED PROJECTS (5 CARDS) -->
        @elseif($currentTab === 'projects')
            <div class="sb-table-card p-4">
                <div class="p-3 rounded-3 border bg-light mb-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Section Eyebrow</label>
                            <input type="text" name="sections[projects][eyebrow]" value="{{ $t['projects']['eyebrow'] }}" class="form-control" style="height: 42px; border-radius: 10px;">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-semibold small">Section Heading</label>
                            <input type="text" name="sections[projects][heading]" value="{{ $t['projects']['heading'] }}" class="form-control" style="height: 42px; border-radius: 10px;">
                        </div>
                    </div>
                </div>

                <div class="row g-4">
                    @foreach($t['projects']['items'] as $idx => $proj)
                        <div class="col-md-4">
                            <div class="p-3 border rounded-4 bg-white shadow-sm h-100">
                                <div class="mb-3">
                                    @include('admin.partials.image-dropzone', [
                                        'name'    => "project_image_{$idx}",
                                        'label'   => 'Project #' . ($idx + 1) . ' Photo',
                                        'preview' => $proj['image'],
                                        'height'  => '145px',
                                        'fit'     => 'cover',
                                        'hint'    => 'Drop project photo or click to change',
                                    ])
                                </div>
                                <div class="mb-2">
                                    <label class="form-label fw-bold small mb-1">Project Title</label>
                                    <input type="text" name="sections[projects][items][{{ $idx }}][title]" value="{{ $proj['title'] }}" class="form-control" style="height: 40px; border-radius: 9px;">
                                </div>
                                <div>
                                    <label class="form-label fw-semibold small mb-1">Location</label>
                                    <input type="text" name="sections[projects][items][{{ $idx }}][location]" value="{{ $proj['location'] }}" class="form-control" style="height: 40px; border-radius: 9px;">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        <!-- 6. PHOTO GALLERY -->
        @elseif($currentTab === 'gallery')
            <div class="sb-table-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h6 class="fw-bold mb-1">Photo Gallery Section Heading</h6>
                        <p class="text-muted small mb-0">Edit gallery heading or click "Upload Gallery Photos" to manage room photos.</p>
                    </div>
                    <a href="{{ route('admin.media-library', ['cms_module' => 'Interior']) }}" class="btn btn-sm text-white fw-semibold px-3 py-2" style="background: #EC134A; border-radius: 9px;">
                        <i class="bi bi-cloud-arrow-up me-1"></i> Upload Gallery Photos
                    </a>
                </div>

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Gallery Eyebrow</label>
                        <input type="text" name="sections[gallery][eyebrow]" value="{{ $t['gallery']['eyebrow'] }}" class="form-control" style="height: 44px; border-radius: 10px;">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label fw-semibold small">Gallery Heading</label>
                        <input type="text" name="sections[gallery][heading_prefix]" value="{{ $t['gallery']['heading_prefix'] }}" class="form-control" style="height: 44px; border-radius: 10px;">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold small">Italic Highlight Word</label>
                        <input type="text" name="sections[gallery][heading_em]" value="{{ $t['gallery']['heading_em'] }}" class="form-control" style="height: 44px; border-radius: 10px;">
                    </div>
                </div>
            </div>

        <!-- 7. VIDEO GALLERY -->
        @elseif($currentTab === 'videos')
            <div class="sb-table-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h6 class="fw-bold mb-1">Video Gallery Section Heading</h6>
                        <p class="text-muted small mb-0">Customize the Video Gallery heading or click "Add / Manage Videos" to add YouTube links or MP4 videos.</p>
                    </div>
                    <a href="{{ route('admin.video-gallery', ['cms_module' => 'Interior']) }}" class="btn btn-sm text-white fw-semibold px-3 py-2" style="background: #EC134A; border-radius: 9px;">
                        <i class="bi bi-play-btn me-1"></i> Add / Manage Videos
                    </a>
                </div>

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Video Section Eyebrow</label>
                        <input type="text" name="sections[videos][eyebrow]" value="{{ $t['videos']['eyebrow'] }}" class="form-control" style="height: 44px; border-radius: 10px;">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label fw-semibold small">Video Section Heading</label>
                        <input type="text" name="sections[videos][heading_prefix]" value="{{ $t['videos']['heading_prefix'] }}" class="form-control" style="height: 44px; border-radius: 10px;">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold small">Italic Highlight Word</label>
                        <input type="text" name="sections[videos][heading_em]" value="{{ $t['videos']['heading_em'] }}" class="form-control" style="height: 44px; border-radius: 10px;">
                    </div>
                </div>
            </div>

        <!-- 8. PROCESS & WHY CHOOSE US -->
        @elseif($currentTab === 'process')
            <div class="sb-table-card p-4">
                <h6 class="fw-bold mb-3"><i class="bi bi-diagram-3 me-1 text-danger"></i> 5-Step Design Process Cards</h6>
                <div class="row g-3 mb-4">
                    @foreach($t['process']['steps'] as $idx => $step)
                        <div class="col-md-4">
                            <div class="p-3 border rounded-3 bg-light h-100">
                                <label class="form-label fw-bold small mb-1">Step #{{ $idx + 1 }} Title</label>
                                <input type="text" name="sections[process][steps][{{ $idx }}][title]" value="{{ $step['title'] }}" class="form-control mb-2" style="height: 40px; border-radius: 9px;">
                                <label class="form-label fw-semibold small mb-1">Description</label>
                                <input type="text" name="sections[process][steps][{{ $idx }}][description]" value="{{ $step['description'] }}" class="form-control" style="height: 40px; border-radius: 9px;">
                            </div>
                        </div>
                    @endforeach
                </div>

                <h6 class="fw-bold mb-2"><i class="bi bi-check2-all me-1 text-danger"></i> Why Choose Us (4 Bullet Points)</h6>
                <div class="row g-3">
                    @foreach($t['process']['why_items'] as $idx => $whyItem)
                        <div class="col-md-6">
                            <input type="text" name="sections[process][why_items][{{ $idx }}]" value="{{ $whyItem }}" class="form-control" style="height: 42px; border-radius: 10px;">
                        </div>
                    @endforeach
                </div>
            </div>

        <!-- 9. TESTIMONIALS -->
        @elseif($currentTab === 'testimonials')
            <div class="sb-table-card p-4">
                <div class="row g-4">
                    @foreach($t['testimonials']['items'] as $idx => $item)
                        <div class="col-md-4">
                            <div class="p-3 border rounded-4 bg-light h-100">
                                <label class="form-label fw-bold small mb-1">Client Quote #{{ $idx + 1 }}</label>
                                <textarea name="sections[testimonials][items][{{ $idx }}][quote]" rows="3" class="form-control mb-2" style="border-radius: 9px;">{{ $item['quote'] }}</textarea>
                                <label class="form-label fw-semibold small mb-1">Client Name</label>
                                <input type="text" name="sections[testimonials][items][{{ $idx }}][author]" value="{{ $item['author'] }}" class="form-control mb-2" style="height: 40px; border-radius: 9px;">
                                <label class="form-label fw-semibold small mb-1">Location</label>
                                <input type="text" name="sections[testimonials][items][{{ $idx }}][location]" value="{{ $item['location'] }}" class="form-control" style="height: 40px; border-radius: 9px;">
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        <!-- 10. CONTACT CTA BANNER -->
        @elseif($currentTab === 'cta')
            <div class="sb-table-card p-4">
                <div class="row g-4 align-items-stretch">
                    <div class="col-lg-4">
                        @include('admin.partials.image-dropzone', [
                            'name'    => 'cta_image',
                            'label'   => 'CTA Banner Image (Drag & Drop or Click)',
                            'preview' => $t['cta']['image'],
                            'height'  => '195px',
                            'fit'     => 'cover',
                            'hint'    => 'Drop CTA photo here or click to browse',
                        ])
                    </div>
                    <div class="col-lg-8">
                        <div class="p-3 rounded-3 border bg-light h-100 d-flex flex-column justify-content-center">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Eyebrow</label>
                                    <input type="text" name="sections[cta][eyebrow]" value="{{ $t['cta']['eyebrow'] }}" class="form-control" style="height: 44px; border-radius: 10px;">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Heading Line 1</label>
                                    <input type="text" name="sections[cta][heading_line1]" value="{{ $t['cta']['heading_line1'] }}" class="form-control" style="height: 44px; border-radius: 10px;">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Button Text</label>
                                    <input type="text" name="sections[cta][btn_text]" value="{{ $t['cta']['btn_text'] }}" class="form-control" style="height: 44px; border-radius: 10px;">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Button Phone / URL</label>
                                    <input type="text" name="sections[cta][btn_url]" value="{{ $t['cta']['btn_url'] }}" class="form-control" style="height: 44px; border-radius: 10px;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        <!-- 11. FOOTER & THEME COLORS -->
        @elseif($currentTab === 'footer')
            <div class="sb-table-card p-4">
                <div class="row g-4">
                    <div class="col-lg-4">
                        @include('admin.partials.image-dropzone', [
                            'name'    => 'footer_arch_image',
                            'label'   => 'Footer Arch Image (Drag & Drop or Click)',
                            'preview' => $t['footer']['arch_image'],
                            'height'  => '245px',
                            'fit'     => 'cover',
                            'hint'    => 'Drop footer arch photo or click to change',
                        ])
                    </div>

                    <div class="col-lg-8">
                        <div class="p-3 rounded-3 border bg-light h-100">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold small">Phone Number</label>
                                    <input type="text" name="sections[footer][phone]" value="{{ $t['footer']['phone'] }}" class="form-control" style="height: 42px; border-radius: 10px;">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold small">Website Label</label>
                                    <input type="text" name="sections[footer][website]" value="{{ $t['footer']['website'] }}" class="form-control" style="height: 42px; border-radius: 10px;">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold small">Address</label>
                                    <input type="text" name="sections[footer][address]" value="{{ $t['footer']['address'] }}" class="form-control" style="height: 42px; border-radius: 10px;">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Footer About Text</label>
                                    <input type="text" name="sections[footer][about_text]" value="{{ $t['footer']['about_text'] }}" class="form-control" style="height: 42px; border-radius: 10px;">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Copyright Bar Text</label>
                                    <input type="text" name="sections[footer][copyright]" value="{{ $t['footer']['copyright'] }}" class="form-control" style="height: 42px; border-radius: 10px;">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-semibold small">Background Cream (<code>--bg</code>)</label>
                                    <input type="color" name="sections[colors][bg]" value="{{ $t['colors']['bg'] }}" class="form-control form-control-color w-100" style="height: 42px; border-radius: 10px;">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold small">Primary Olive (<code>--olive</code>)</label>
                                    <input type="color" name="sections[colors][olive]" value="{{ $t['colors']['olive'] }}" class="form-control form-control-color w-100" style="height: 42px; border-radius: 10px;">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold small">Accent Clay (<code>--clay</code>)</label>
                                    <input type="color" name="sections[colors][clay]" value="{{ $t['colors']['clay'] }}" class="form-control form-control-color w-100" style="height: 42px; border-radius: 10px;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Save Bar -->
        <div class="mt-4 p-3 bg-white border rounded-4 shadow-sm d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="small text-muted">
                <i class="bi bi-info-circle text-danger me-1"></i>
                Tip: Drag &amp; drop a new image only if you want to replace the current one — untouched images automatically stay at their default!
            </div>
            <button type="submit" class="btn text-white fw-bold px-4 py-2" style="background: #EC134A; border-radius: 10px; height: 46px; box-shadow: 0 4px 12px rgba(236, 19, 74, 0.25);">
                <i class="bi bi-check2-circle me-1"></i> Save {{ $secInfo['title'] }}
            </button>
        </div>
    </form>
@endsection
