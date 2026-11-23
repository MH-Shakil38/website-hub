@extends('admin.layouts.master')

@section('title', 'Gym Website Builder & Bilingual Settings (EN / BN) — FitPulse Admin')

@section('admin_content')
@php
    $en = $t['translations']['en'] ?? [];
    $bn = $t['translations']['bn'] ?? [];
    $vis = $t['visibility'] ?? [];
    $defaultOrder = [
        'hero', 'why_choose', 'programs', 'trainers', 'pricing', 'schedule',
        'equipment', 'plans', 'gallery', 'videos', 'testimonials', 'registration', 'faq_cta'
    ];
    $sectionOrder = $t['section_order'] ?? $defaultOrder;
    foreach ($defaultOrder as $reqSec) {
        if (!in_array($reqSec, $sectionOrder, true)) {
            $sectionOrder[] = $reqSec;
        }
    }
    $sectionLabels = [
        'hero'         => ['title' => '1. Hero Section (NO EXCUSES. ONLY RESULTS. + Stats Card)', 'vis_key' => 'show_hero'],
        'why_choose'   => ['title' => '2. Why Train With Us (Gym Photo + 4 Features)', 'vis_key' => 'show_why_choose'],
        'programs'     => ['title' => '3. Our Training Programs (5 Cards)', 'vis_key' => 'show_programs'],
        'trainers'     => ['title' => '4. Meet Our Expert Trainers (Cards + Book A Session)', 'vis_key' => 'show_trainers'],
        'pricing'      => ['title' => '5. Membership Packages & Pricing (Basic, Pro, Elite)', 'vis_key' => 'show_pricing'],
        'schedule'     => ['title' => '6. Weekly Class Schedule Table', 'vis_key' => 'show_schedule'],
        'equipment'    => ['title' => '7. State-of-the-Art Equipment (6 Cards)', 'vis_key' => 'show_equipment'],
        'plans'        => ['title' => '8. Workout Routines & Nutrition Diet Plans', 'vis_key' => 'show_plans'],
        'gallery'      => ['title' => '9. Photo & Media Gallery (Image Gallery)', 'vis_key' => 'show_gallery'],
        'videos'       => ['title' => '10. Workout & Facility Video Gallery', 'vis_key' => 'show_videos'],
        'testimonials' => ['title' => '11. Real Results. Real People. (Testimonials)', 'vis_key' => 'show_testimonials'],
        'registration' => ['title' => '12. Frontend Membership Registration Form', 'vis_key' => 'show_registration'],
        'faq_cta'      => ['title' => '13. Frequently Asked Questions + Transform CTA Banner', 'vis_key' => 'show_faq_cta'],
    ];
@endphp

    <div class="bg-white border rounded-4 p-4 mb-4 shadow-sm d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <h3 style="font-size: 22px; font-weight: 800; color: #0F172A; margin: 0;">Gym Website Builder &amp; Bilingual Settings (EN / BN)</h3>
                <span class="sb-badge-pill sb-badge-green">gym_settings</span>
            </div>
            <p class="text-muted small mb-0">Reorder homepage sections, toggle visibility, upload images, and edit English (`EN`) &amp; Bengali (`বাংলা`) text.</p>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('gymfitness.home') }}" target="_blank" class="btn btn-outline-dark btn-sm fw-bold px-3 py-2 rounded-3">
                <i class="bi bi-eye me-1"></i> Preview Gym Website
            </a>
            <form action="{{ route('admin.gymfitness.settings.reset') }}" method="POST" onsubmit="return confirm('Reset Gym & Fitness module to default template and re-seed default data?')">
                @csrf
                <button type="submit" class="btn btn-outline-danger btn-sm fw-bold px-3 py-2 rounded-3">
                    <i class="bi bi-arrow-counterclockwise me-1"></i> Reset to Default
                </button>
            </form>
        </div>
    </div>

    <form action="{{ route('admin.gymfitness.settings.save') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row g-4">
            {{-- Left Column: Section Reordering, Visibility & Media --}}
            <div class="col-lg-5">
                <div class="bg-white border rounded-4 p-4 shadow-sm mb-4">
                    <h5 class="fw-bold mb-1"><i class="bi bi-arrows-move text-success me-1"></i> Homepage Section Order &amp; Visibility</h5>
                    <p class="text-muted small mb-3">Use the Up/Down buttons to reorder sections or toggle checkboxes to hide/show sections on the homepage.</p>

                    <div id="gymSectionOrderList" class="d-flex flex-column gap-2">
                        @foreach($sectionOrder as $secKey)
                            @if(isset($sectionLabels[$secKey]))
                                @php
                                    $info = $sectionLabels[$secKey];
                                    $vKey = $info['vis_key'];
                                @endphp
                                <div class="border rounded-3 p-2 px-3 bg-light d-flex align-items-center justify-content-between gap-2 section-order-row">
                                    <input type="hidden" name="sections[section_order][]" value="{{ $secKey }}">
                                    <div class="d-flex align-items-center gap-2">
                                        <input type="hidden" name="sections[visibility][{{ $vKey }}]" value="0">
                                        <input class="form-check-input mt-0" type="checkbox" name="sections[visibility][{{ $vKey }}]" value="1"
                                               {{ ($vis[$vKey] ?? '1') === '1' ? 'checked' : '' }}>
                                        <span class="small fw-bold text-dark">{{ $info['title'] }}</span>
                                    </div>
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-secondary py-0 px-2" onclick="moveSectionRow(this, -1)" title="Move Up">&uarr;</button>
                                        <button type="button" class="btn btn-outline-secondary py-0 px-2" onclick="moveSectionRow(this, 1)" title="Move Down">&darr;</button>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>

                {{-- General & Images Card --}}
                <div class="bg-white border rounded-4 p-4 shadow-sm">
                    <h5 class="fw-bold mb-3"><i class="bi bi-image text-primary me-1"></i> Theme Images, Stats &amp; Contact</h5>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Default Website Language</label>
                            <select name="sections[general][default_language]" class="form-select form-select-sm">
                                <option value="en" {{ ($t['general']['default_language'] ?? 'en') === 'en' ? 'selected' : '' }}>English (EN)</option>
                                <option value="bn" {{ ($t['general']['default_language'] ?? 'en') === 'bn' ? 'selected' : '' }}>Bengali (বাংলা)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Header Language Switcher</label>
                            <select name="sections[header][show_language_switcher]" class="form-select form-select-sm">
                                <option value="1" {{ ($t['header']['show_language_switcher'] ?? '1') === '1' ? 'selected' : '' }}>Show (EN | বাংলা)</option>
                                <option value="0" {{ ($t['header']['show_language_switcher'] ?? '1') === '0' ? 'selected' : '' }}>Hide</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Contact Phone</label>
                            <input type="text" name="sections[general][contact_phone]" value="{{ $t['general']['contact_phone'] ?? '' }}" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Contact Email</label>
                            <input type="text" name="sections[general][contact_email]" value="{{ $t['general']['contact_email'] ?? '' }}" class="form-control form-control-sm">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Hero Stat 1</label>
                            <input type="text" name="sections[hero][stat_1_value]" value="{{ $t['hero']['stat_1_value'] ?? '10,000+' }}" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Hero Stat 2</label>
                            <input type="text" name="sections[hero][stat_2_value]" value="{{ $t['hero']['stat_2_value'] ?? '4.9' }}" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Hero Stat 3</label>
                            <input type="text" name="sections[hero][stat_3_value]" value="{{ $t['hero']['stat_3_value'] ?? '50+' }}" class="form-control form-control-sm">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Hero Athlete Image</label>
                            <input type="file" name="hero_image_file" class="form-control form-control-sm" accept="image/*">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Why Choose Us Gym Photo</label>
                            <input type="file" name="why_gym_image_file" class="form-control form-control-sm" accept="image/*">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Transform CTA Image</label>
                            <input type="file" name="cta_image_file" class="form-control form-control-sm" accept="image/*">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Custom Header Logo</label>
                            <input type="file" name="header_logo" class="form-control form-control-sm" accept="image/*">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column: Bilingual Content Editor (English & Bengali Only) --}}
            <div class="col-lg-7">
                <div class="bg-white border rounded-4 p-4 shadow-sm">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 class="fw-bold mb-0"><i class="bi bi-translate text-success me-1"></i> Bilingual Content &amp; Section Text (English &amp; বাংলা)</h5>
                        <span class="badge bg-success-subtle text-success">2 Supported Languages: EN + BN</span>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Brand Name (EN)</label>
                            <input type="text" name="sections[translations][en][brand_name]" value="{{ $en['brand_name'] ?? 'FitPulse' }}" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Brand Name (বাংলা)</label>
                            <input type="text" name="sections[translations][bn][brand_name]" value="{{ $bn['brand_name'] ?? 'ফিটপালস' }}" class="form-control form-control-sm">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Hero Line 1 (EN)</label>
                            <input type="text" name="sections[translations][en][hero_line_1]" value="{{ $en['hero_line_1'] ?? 'NO EXCUSES.' }}" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Hero Line 2 (EN)</label>
                            <input type="text" name="sections[translations][en][hero_line_2]" value="{{ $en['hero_line_2'] ?? 'ONLY' }}" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Hero Line 3 (EN)</label>
                            <input type="text" name="sections[translations][en][hero_line_3]" value="{{ $en['hero_line_3'] ?? 'RESULTS.' }}" class="form-control form-control-sm">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Hero Line 1 (বাংলা)</label>
                            <input type="text" name="sections[translations][bn][hero_line_1]" value="{{ $bn['hero_line_1'] ?? '' }}" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Hero Line 2 (বাংলা)</label>
                            <input type="text" name="sections[translations][bn][hero_line_2]" value="{{ $bn['hero_line_2'] ?? '' }}" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Hero Line 3 (বাংলা)</label>
                            <input type="text" name="sections[translations][bn][hero_line_3]" value="{{ $bn['hero_line_3'] ?? '' }}" class="form-control form-control-sm">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Hero Subtitle (EN)</label>
                            <textarea name="sections[translations][en][hero_subtitle]" rows="2" class="form-control form-control-sm">{{ $en['hero_subtitle'] ?? '' }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Hero Subtitle (বাংলা)</label>
                            <textarea name="sections[translations][bn][hero_subtitle]" rows="2" class="form-control form-control-sm">{{ $bn['hero_subtitle'] ?? '' }}</textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Why Choose Us Title (EN)</label>
                            <input type="text" name="sections[translations][en][why_title]" value="{{ $en['why_title'] ?? '' }}" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Why Choose Us Title (বাংলা)</label>
                            <input type="text" name="sections[translations][bn][why_title]" value="{{ $bn['why_title'] ?? '' }}" class="form-control form-control-sm">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Programs Section Title (EN)</label>
                            <input type="text" name="sections[translations][en][programs_title]" value="{{ $en['programs_title'] ?? '' }}" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Programs Section Title (বাংলা)</label>
                            <input type="text" name="sections[translations][bn][programs_title]" value="{{ $bn['programs_title'] ?? '' }}" class="form-control form-control-sm">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Pricing Section Title (EN)</label>
                            <input type="text" name="sections[translations][en][pricing_title]" value="{{ $en['pricing_title'] ?? '' }}" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Pricing Section Title (বাংলা)</label>
                            <input type="text" name="sections[translations][bn][pricing_title]" value="{{ $bn['pricing_title'] ?? '' }}" class="form-control form-control-sm">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Class Schedule Title (EN)</label>
                            <input type="text" name="sections[translations][en][schedule_title]" value="{{ $en['schedule_title'] ?? '' }}" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Class Schedule Title (বাংলা)</label>
                            <input type="text" name="sections[translations][bn][schedule_title]" value="{{ $bn['schedule_title'] ?? '' }}" class="form-control form-control-sm">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Trainers Title (EN)</label>
                            <input type="text" name="sections[translations][en][trainers_title]" value="{{ $en['trainers_title'] ?? '' }}" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Trainers Title (বাংলা)</label>
                            <input type="text" name="sections[translations][bn][trainers_title]" value="{{ $bn['trainers_title'] ?? '' }}" class="form-control form-control-sm">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Equipment Title (EN)</label>
                            <input type="text" name="sections[translations][en][equipment_title]" value="{{ $en['equipment_title'] ?? '' }}" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Equipment Title (বাংলা)</label>
                            <input type="text" name="sections[translations][bn][equipment_title]" value="{{ $bn['equipment_title'] ?? '' }}" class="form-control form-control-sm">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Transform CTA Title (EN)</label>
                            <input type="text" name="sections[translations][en][cta_title]" value="{{ $en['cta_title'] ?? '' }}" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Transform CTA Title (বাংলা)</label>
                            <input type="text" name="sections[translations][bn][cta_title]" value="{{ $bn['cta_title'] ?? '' }}" class="form-control form-control-sm">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Contact Address (EN)</label>
                            <input type="text" name="sections[translations][en][contact_address]" value="{{ $en['contact_address'] ?? '' }}" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Contact Address (বাংলা)</label>
                            <input type="text" name="sections[translations][bn][contact_address]" value="{{ $bn['contact_address'] ?? '' }}" class="form-control form-control-sm">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">SEO Meta Title (EN)</label>
                            <input type="text" name="sections[translations][en][meta_title]" value="{{ $en['meta_title'] ?? '' }}" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">SEO Meta Title (বাংলা)</label>
                            <input type="text" name="sections[translations][bn][meta_title]" value="{{ $bn['meta_title'] ?? '' }}" class="form-control form-control-sm">
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top text-end">
                        <button type="submit" class="btn btn-success fw-bold px-4 py-2">
                            <i class="bi bi-check2-circle me-1"></i> Save Website Builder &amp; Bilingual Settings
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <script>
        function moveSectionRow(btn, dir) {
            const row = btn.closest('.section-order-row');
            const parent = row.parentNode;
            if (dir === -1 && row.previousElementSibling) {
                parent.insertBefore(row, row.previousElementSibling);
            } else if (dir === 1 && row.nextElementSibling) {
                parent.insertBefore(row.nextElementSibling, row);
            }
        }
    </script>
@endsection
