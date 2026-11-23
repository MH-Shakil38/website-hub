@extends('admin.layouts.master')

@section('title', 'SaaS Homepage Builder - SiteBuilder')

@section('admin_content')
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color: #0F172A;">SaaS Homepage Builder (<code>core_saas_sections</code>)</h4>
            <p class="text-muted small mb-0">
                Control your public SaaS landing page content, hero headline, theme gallery banner, pricing headers, FAQs, footer, and section visibility.
            </p>
        </div>
        <a href="{{ url('/') }}" target="_blank" class="btn btn-outline-dark fw-semibold px-3 py-2" style="border-radius: 10px; font-size: 13px;">
            <i class="bi bi-box-arrow-up-right me-1"></i> Preview SaaS Landing Page
        </a>
    </div>

    <form action="{{ route('admin.saas.homepage.update') }}" method="POST">
        @csrf

        <div class="row g-4">
            <!-- 1. Hero Banner Section -->
            @php $hero = $sections['hero'] ?? []; $heroC = $hero['content'] ?? []; @endphp
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 14px;">
                    <div class="card-header bg-white border-0 pt-3 pb-2 px-4 d-flex align-items-center justify-content-between">
                        <h6 class="fw-bold mb-0"><i class="bi bi-stars text-danger me-1"></i> 1. Hero Banner Section</h6>
                        <div class="form-check form-switch m-0">
                            <input class="form-check-input" type="checkbox" name="sections[hero][is_visible]" value="1" {{ ($hero['is_visible'] ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label small fw-semibold">Visible</label>
                        </div>
                    </div>
                    <div class="card-body px-4 pb-4">
                        <div class="row g-2 mb-2">
                            <div class="col-4">
                                <label class="form-label small fw-bold">Announcement Tag</label>
                                <input type="text" name="sections[hero][content][pill_tag]" value="{{ $heroC['pill_tag'] ?? '' }}" class="form-control form-control-sm">
                            </div>
                            <div class="col-8">
                                <label class="form-label small fw-bold">Announcement Text</label>
                                <input type="text" name="sections[hero][content][pill_text]" value="{{ $heroC['pill_text'] ?? '' }}" class="form-control form-control-sm">
                            </div>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-bold">Main Heading Line 1</label>
                            <input type="text" name="sections[hero][content][heading_line_1]" value="{{ $heroC['heading_line_1'] ?? '' }}" class="form-control form-control-sm">
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-bold">Highlighted Gradient Heading Line 2</label>
                            <input type="text" name="sections[hero][content][heading_gradient]" value="{{ $heroC['heading_gradient'] ?? '' }}" class="form-control form-control-sm">
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-bold">Hero Subheading</label>
                            <textarea name="sections[hero][content][subheading]" rows="2" class="form-control form-control-sm no-rte">{{ $heroC['subheading'] ?? '' }}</textarea>
                        </div>
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="form-label small fw-bold">Primary CTA Button</label>
                                <input type="text" name="sections[hero][content][primary_btn_text]" value="{{ $heroC['primary_btn_text'] ?? '' }}" class="form-control form-control-sm">
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-bold">Secondary CTA Button</label>
                                <input type="text" name="sections[hero][content][secondary_btn_text]" value="{{ $heroC['secondary_btn_text'] ?? '' }}" class="form-control form-control-sm">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Theme Gallery & Pricing Section Headers -->
            @php
                $themesSec = $sections['themes'] ?? []; $themesC = $themesSec['content'] ?? [];
                $pricingSec = $sections['pricing'] ?? []; $pricingC = $pricingSec['content'] ?? [];
            @endphp
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 14px;">
                    <div class="card-header bg-white border-0 pt-3 pb-2 px-4 d-flex align-items-center justify-content-between">
                        <h6 class="fw-bold mb-0"><i class="bi bi-collection-play-fill text-primary me-1"></i> 2. Theme Gallery &amp; Pricing Headers</h6>
                        <div class="d-flex gap-3">
                            <div class="form-check form-switch m-0">
                                <input class="form-check-input" type="checkbox" name="sections[themes][is_visible]" value="1" {{ ($themesSec['is_visible'] ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label small fw-semibold">Themes</label>
                            </div>
                            <div class="form-check form-switch m-0">
                                <input class="form-check-input" type="checkbox" name="sections[pricing][is_visible]" value="1" {{ ($pricingSec['is_visible'] ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label small fw-semibold">Pricing</label>
                            </div>
                        </div>
                    </div>
                    <div class="card-body px-4 pb-4">
                        <div class="mb-2">
                            <label class="form-label small fw-bold">Theme Gallery Heading (<code>#themes</code>)</label>
                            <input type="text" name="sections[themes][content][heading]" value="{{ $themesC['heading'] ?? '' }}" class="form-control form-control-sm">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Theme Gallery Subheading</label>
                            <textarea name="sections[themes][content][subheading]" rows="2" class="form-control form-control-sm no-rte">{{ $themesC['subheading'] ?? '' }}</textarea>
                        </div>
                        <hr class="my-2">
                        <div class="mb-2">
                            <label class="form-label small fw-bold">Pricing Section Heading (<code>#pricing</code>)</label>
                            <input type="text" name="sections[pricing][content][heading]" value="{{ $pricingC['heading'] ?? '' }}" class="form-control form-control-sm">
                        </div>
                        <div>
                            <label class="form-label small fw-bold">Pricing Section Subheading</label>
                            <textarea name="sections[pricing][content][subheading]" rows="2" class="form-control form-control-sm no-rte">{{ $pricingC['subheading'] ?? '' }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Bento Features & SEO / Footer -->
            @php
                $featSec = $sections['features'] ?? []; $featC = $featSec['content'] ?? [];
                $ctaSec = $sections['cta_footer'] ?? []; $ctaC = $ctaSec['content'] ?? [];
                $seoSec = $sections['seo'] ?? []; $seoC = $seoSec['content'] ?? [];
            @endphp
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 14px;">
                    <div class="card-header bg-white border-0 pt-3 pb-2 px-4 d-flex align-items-center justify-content-between">
                        <h6 class="fw-bold mb-0"><i class="bi bi-cpu-fill text-success me-1"></i> 3. Features, Bottom CTA &amp; SEO</h6>
                        <div class="d-flex gap-3">
                            <div class="form-check form-switch m-0">
                                <input class="form-check-input" type="checkbox" name="sections[features][is_visible]" value="1" {{ ($featSec['is_visible'] ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label small fw-semibold">Features</label>
                            </div>
                            <div class="form-check form-switch m-0">
                                <input class="form-check-input" type="checkbox" name="sections[cta_footer][is_visible]" value="1" {{ ($ctaSec['is_visible'] ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label small fw-semibold">Bottom CTA</label>
                            </div>
                        </div>
                    </div>
                    <div class="card-body px-4 pb-4">
                        <input type="hidden" name="sections[seo][is_visible]" value="1">
                        <div class="mb-2">
                            <label class="form-label small fw-bold">SEO Meta Title Suffix</label>
                            <input type="text" name="sections[seo][content][meta_title]" value="{{ $seoC['meta_title'] ?? '' }}" class="form-control form-control-sm">
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-bold">Features Section Heading</label>
                            <input type="text" name="sections[features][content][heading]" value="{{ $featC['heading'] ?? '' }}" class="form-control form-control-sm">
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-bold">Bottom CTA Banner Heading</label>
                            <input type="text" name="sections[cta_footer][content][cta_heading]" value="{{ $ctaC['cta_heading'] ?? '' }}" class="form-control form-control-sm">
                        </div>
                        <div>
                            <label class="form-label small fw-bold">Footer About Text</label>
                            <textarea name="sections[cta_footer][content][footer_about]" rows="2" class="form-control form-control-sm no-rte">{{ $ctaC['footer_about'] ?? '' }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. FAQ Accordion Items -->
            @php $faqSec = $sections['faq'] ?? []; $faqC = $faqSec['content'] ?? []; $faqItems = $faqC['items'] ?? []; @endphp
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 14px;">
                    <div class="card-header bg-white border-0 pt-3 pb-2 px-4 d-flex align-items-center justify-content-between">
                        <h6 class="fw-bold mb-0"><i class="bi bi-question-circle-fill text-warning me-1"></i> 4. Frequently Asked Questions (<code>#faq</code>)</h6>
                        <div class="form-check form-switch m-0">
                            <input class="form-check-input" type="checkbox" name="sections[faq][is_visible]" value="1" {{ ($faqSec['is_visible'] ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label small fw-semibold">Visible</label>
                        </div>
                    </div>
                    <div class="card-body px-4 pb-4">
                        <div class="mb-2">
                            <label class="form-label small fw-bold">FAQ Heading</label>
                            <input type="text" name="sections[faq][content][heading]" value="{{ $faqC['heading'] ?? '' }}" class="form-control form-control-sm">
                        </div>
                        @foreach($faqItems as $idx => $fItem)
                            <div class="p-2 rounded bg-light border mb-2">
                                <input type="text" name="sections[faq][faq_q][]" value="{{ $fItem['q'] ?? '' }}" class="form-control form-control-sm fw-semibold mb-1" placeholder="Question...">
                                <textarea name="sections[faq][faq_a][]" rows="2" class="form-control form-control-sm no-rte" placeholder="Answer...">{{ $fItem['a'] ?? '' }}</textarea>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4 text-end">
            <button type="submit" class="btn btn-danger fw-bold px-4 py-2" style="background: #EC134A; border: none; border-radius: 10px;">
                <i class="bi bi-check2-circle me-1"></i> Save SaaS Homepage Settings
            </button>
        </div>
    </form>
@endsection
