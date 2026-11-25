@extends('admin.layouts.master')

@section('admin_content')
<div class="mb-4">
    <h4 class="fw-bold mb-1">IVF Center Theme & Contact Settings</h4>
    <p class="text-muted small mb-0">Manage contact information, emergency lines, and theme configurations</p>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-4">
                <form action="{{ route('admin.ivfcenter.settings.save') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Clinic Phone</label>
                            <input type="text" name="phone" class="form-control" value="{{ $settings['phone'] ?? '+1 (555) 123-4567' }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Contact Email</label>
                            <input type="email" name="email" class="form-control" value="{{ $settings['email'] ?? 'hello@ivfcenter.com' }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Clinic Physical Address</label>
                            <input type="text" name="address" class="form-control" value="{{ $settings['address'] ?? '123 Wellness Ave, New York, NY 10001' }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Hero Heading Highlight</label>
                            <input type="text" name="hero_title_accent" class="form-control" value="{{ $settings['hero_title_accent'] ?? 'Parenthood' }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Success Rate</label>
                            <input type="text" name="stat_success" class="form-control" value="{{ $settings['stat_success'] ?? '98%+' }}">
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check2-circle me-1"></i> Save Theme Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
