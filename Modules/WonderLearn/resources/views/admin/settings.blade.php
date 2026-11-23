@extends('admin.layouts.master')

@section('title', 'Hero & Theme Settings - WonderLearn Admin')

@section('admin_content')
<div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
    <div>
        <h3 class="fw-bolder mb-1" style="color: #0B2545;">WonderLearn — Hero, Trust Badges & Theme Settings</h3>
        <p class="text-muted small mb-0">Customize every heading, badge, CTA button, and illustration stored in `wl_settings`.</p>
    </div>
    <div class="d-flex gap-2">
        <form action="{{ route('admin.wonderlearn.settings.reset') }}" method="POST" onsubmit="return confirm('Restore WonderLearn 100% to default design & content?');">
            @csrf
            <button type="submit" class="btn btn-outline-danger rounded-3 fw-semibold">
                <i class="bi bi-arrow-counterclockwise me-1"></i> Reset to Default
            </button>
        </form>
        <a href="{{ url('/wonder-learn') }}" target="_blank" class="btn btn-outline-primary rounded-3 fw-semibold">
            <i class="bi bi-eye me-1"></i> View Live Frontend
        </a>
    </div>
</div>

<form action="{{ route('admin.wonderlearn.settings.save') }}" method="POST" enctype="multipart/form-data" class="bg-white border rounded-4 p-4 shadow-sm">
    @csrf
    <h5 class="fw-bold text-primary mb-3"><i class="bi bi-window-dock me-2"></i>1. Brand & Cloud Navbar</h5>
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <label class="form-label small fw-bold">Brand Name</label>
            <input type="text" name="brand_name" value="{{ $s['brand_name'] ?? 'WonderLearn' }}" class="form-control">
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-bold">Brand Tagline</label>
            <input type="text" name="brand_tagline" value="{{ $s['brand_tagline'] ?? 'Learn • Discover • Grow' }}" class="form-control">
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-bold">Log In Button Text</label>
            <input type="text" name="login_text" value="{{ $s['login_text'] ?? 'Log In' }}" class="form-control">
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-bold">Join Now Button Text</label>
            <input type="text" name="join_text" value="{{ $s['join_text'] ?? 'Join Now' }}" class="form-control">
        </div>
    </div>

    <h5 class="fw-bold text-primary mb-3"><i class="bi bi-stars me-2"></i>2. Magical Treehouse Hero Section</h5>
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <label class="form-label small fw-bold">Hero Heading Line 1 (Blue)</label>
            <input type="text" name="hero_title_line1" value="{{ $s['hero_title_line1'] ?? 'Let Curiosity' }}" class="form-control">
        </div>
        <div class="col-md-4">
            <label class="form-label small fw-bold">Hero Heading Line 2 (Green)</label>
            <input type="text" name="hero_title_line2" value="{{ $s['hero_title_line2'] ?? 'Lead the Way' }}" class="form-control">
        </div>
        <div class="col-md-4">
            <label class="form-label small fw-bold">CTA Button Text</label>
            <input type="text" name="hero_btn_text" value="{{ $s['hero_btn_text'] ?? 'Start Exploring' }}" class="form-control">
        </div>
        <div class="col-md-8">
            <label class="form-label small fw-bold">Hero Subtitle</label>
            <input type="text" name="hero_subtitle" value="{{ $s['hero_subtitle'] ?? '' }}" class="form-control">
        </div>
        <div class="col-md-4">
            <label class="form-label small fw-bold">Custom Hero Background Image</label>
            <input type="file" name="hero_bg_file" class="form-control" accept="image/*">
        </div>
    </div>

    <h5 class="fw-bold text-primary mb-3"><i class="bi bi-shield-check me-2"></i>3. Floating 4-Badge Trust Bar</h5>
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <label class="form-label small fw-bold">Badge 1 (Green Shield)</label>
            <input type="text" name="trust_badge_1" value="{{ $s['trust_badge_1'] ?? 'Safe & Ad-Free' }}" class="form-control">
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-bold">Badge 2 (Gold Star)</label>
            <input type="text" name="trust_badge_2" value="{{ $s['trust_badge_2'] ?? 'Expert Curated' }}" class="form-control">
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-bold">Badge 3 (Game Controller)</label>
            <input type="text" name="trust_badge_3" value="{{ $s['trust_badge_3'] ?? 'Learn Through Play' }}" class="form-control">
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-bold">Badge 4 (Red Heart)</label>
            <input type="text" name="trust_badge_4" value="{{ $s['trust_badge_4'] ?? 'Age 4+ to 12' }}" class="form-control">
        </div>
    </div>

    <h5 class="fw-bold text-primary mb-3"><i class="bi bi-layout-text-window-reverse me-2"></i>4. Section Headings & Footer</h5>
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <label class="form-label small fw-bold">Topics Heading</label>
            <input type="text" name="topics_heading" value="{{ $s['topics_heading'] ?? 'Explore by Topic' }}" class="form-control">
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-bold">Stories Heading</label>
            <input type="text" name="stories_heading" value="{{ $s['stories_heading'] ?? 'Magical Stories' }}" class="form-control">
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-bold">Games Heading</label>
            <input type="text" name="games_heading" value="{{ $s['games_heading'] ?? 'Play & Learn' }}" class="form-control">
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-bold">Adventures Heading</label>
            <input type="text" name="adventures_heading" value="{{ $s['adventures_heading'] ?? 'Featured Adventures' }}" class="form-control">
        </div>
        <div class="col-md-4">
            <label class="form-label small fw-bold">App Download Heading</label>
            <input type="text" name="app_heading" value="{{ $s['app_heading'] ?? 'Learning on the Go!' }}" class="form-control">
        </div>
        <div class="col-md-4">
            <label class="form-label small fw-bold">App Download Subtext</label>
            <input type="text" name="app_subtext" value="{{ $s['app_subtext'] ?? '' }}" class="form-control">
        </div>
        <div class="col-md-4">
            <label class="form-label small fw-bold">Footer Tagline</label>
            <input type="text" name="footer_tagline" value="{{ $s['footer_tagline'] ?? '' }}" class="form-control">
        </div>
    </div>

    <div class="text-end">
        <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 fw-bold">
            <i class="bi bi-check2-circle me-1"></i> Save WonderLearn Settings
        </button>
    </div>
</form>
@endsection
