@extends('admin.layouts.master')

@section('title', 'Hero, Countdown & Venue Settings - Eventify Admin')

@section('admin_content')
<div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
    <div>
        <h3 class="fw-bolder mb-1" style="color: #071E24;">Eventify — Hero, Countdown & Venue Settings (em_settings)</h3>
        <p class="text-muted small mb-0">Customize every headline, live countdown timer, about stats, venue contact info, and footer texts.</p>
    </div>
    <form action="{{ route('admin.eventmanagement.settings.reset') }}" method="POST"
          onsubmit="return confirm('Reset EventManagement module 100% back to original Eventify design & data?');">
        @csrf
        <button type="submit" class="btn btn-outline-danger rounded-3 fw-semibold">
            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset to Default Eventify Design
        </button>
    </form>
</div>

<form action="{{ route('admin.eventmanagement.settings.save') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row g-4">
        <!-- 1. Topbar & Hero Section -->
        <div class="col-lg-6">
            <div class="bg-white border rounded-4 p-4 shadow-sm h-100">
                <h5 class="fw-bold mb-3"><i class="bi bi-lightning-charge-fill text-warning me-1"></i> Top Bar & Hero Section</h5>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Brand Name</label>
                        <input type="text" name="brand_name" value="{{ $s['brand_name'] }}" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Topbar Email</label>
                        <input type="text" name="topbar_email" value="{{ $s['topbar_email'] }}" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Topbar Phone</label>
                        <input type="text" name="topbar_phone" value="{{ $s['topbar_phone'] }}" class="form-control">
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold">Hero Eyebrow Tagline</label>
                        <input type="text" name="hero_eyebrow" value="{{ $s['hero_eyebrow'] }}" class="form-control">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label small fw-bold">Hero Title Line 1</label>
                        <input type="text" name="hero_title_line1" value="{{ $s['hero_title_line1'] }}" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Highlighted Word</label>
                        <input type="text" name="hero_title_highlight" value="{{ $s['hero_title_highlight'] }}" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Edition No.</label>
                        <input type="text" name="hero_title_edition" value="{{ $s['hero_title_edition'] }}" class="form-control">
                    </div>
                    <div class="col-3">
                        <label class="form-label small fw-bold">Days</label>
                        <input type="text" name="hero_countdown_days" value="{{ $s['hero_countdown_days'] }}" class="form-control">
                    </div>
                    <div class="col-3">
                        <label class="form-label small fw-bold">Hours</label>
                        <input type="text" name="hero_countdown_hours" value="{{ $s['hero_countdown_hours'] }}" class="form-control">
                    </div>
                    <div class="col-3">
                        <label class="form-label small fw-bold">Minutes</label>
                        <input type="text" name="hero_countdown_minutes" value="{{ $s['hero_countdown_minutes'] }}" class="form-control">
                    </div>
                    <div class="col-3">
                        <label class="form-label small fw-bold">Seconds</label>
                        <input type="text" name="hero_countdown_seconds" value="{{ $s['hero_countdown_seconds'] }}" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Hero Primary CTA Text</label>
                        <input type="text" name="hero_btn_text" value="{{ $s['hero_btn_text'] }}" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Replace Hero Speaker Image</label>
                        <input type="file" name="hero_speaker_file" class="form-control" accept="image/*">
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. About Section, Venue & Bottom Countdown Banner -->
        <div class="col-lg-6">
            <div class="bg-white border rounded-4 p-4 shadow-sm h-100">
                <h5 class="fw-bold mb-3"><i class="bi bi-geo-alt-fill text-danger me-1"></i> About, Venue & Bottom Banner</h5>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label small fw-bold">About Section Heading</label>
                        <input type="text" name="about_title" value="{{ $s['about_title'] }}" class="form-control">
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold">About Description</label>
                        <textarea name="about_description" rows="2" class="form-control">{{ $s['about_description'] }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Stat 1 Badge</label>
                        <input type="text" name="about_stat1_text" value="{{ $s['about_stat1_text'] }}" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Stat 2 Badge</label>
                        <input type="text" name="about_stat2_text" value="{{ $s['about_stat2_text'] }}" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Venue Email</label>
                        <input type="text" name="venue_email_value" value="{{ $s['venue_email_value'] }}" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Venue Call Number</label>
                        <input type="text" name="venue_phone_value" value="{{ $s['venue_phone_value'] }}" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Venue Schedule</label>
                        <input type="text" name="venue_schedule_value" value="{{ $s['venue_schedule_value'] }}" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Bottom Banner Date & Time</label>
                        <input type="text" name="banner_date_time" value="{{ $s['banner_date_time'] }}" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Bottom Banner Location</label>
                        <input type="text" name="banner_location" value="{{ $s['banner_location'] }}" class="form-control">
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold">Replace Venue Map Image</label>
                        <input type="file" name="venue_map_file" class="form-control" accept="image/*">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 fw-bold">
            <i class="bi bi-check2-circle me-1"></i> Save Eventify Settings
        </button>
    </div>
</form>
@endsection
