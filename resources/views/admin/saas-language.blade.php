@extends('admin.layouts.master')

@section('title', 'Language - ' . ($globalSiteName ?? 'SiteBuilder'))

@section('admin_content')
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge rounded-pill px-3 py-1" style="background: #E0F2FE; color: #0284C7; font-weight: 700; font-size: 11px;">
                    <i class="bi bi-translate me-1"></i> SAAS LANDING PAGE LANGUAGE
                </span>
            </div>
            <h4 class="fw-bold mb-1" style="color: #0F172A;">Language</h4>
            <p class="text-muted small mb-0">
                Choose the language your public SaaS landing page is shown in. Bangla is the default. Arabic is shown right-to-left (RTL).
            </p>
        </div>
        <a href="{{ url('/') }}" target="_blank" class="btn btn-outline-dark fw-semibold px-3 py-2" style="border-radius: 10px; font-size: 13px;">
            <i class="bi bi-box-arrow-up-right me-1"></i> Preview SaaS Landing Page
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger rounded-3 border-0 shadow-sm mb-4">
            <ul class="mb-0 small ps-3">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.saas.language.update') }}" method="POST">
        @csrf

        <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px;">
            <div class="card-header bg-white border-0 pt-3 pb-2 px-4">
                <h6 class="fw-bold mb-0"><i class="bi bi-globe2 text-primary me-1"></i> Landing Page Language</h6>
            </div>
            <div class="card-body px-4 pb-4">
                <div class="row g-3">
                    @foreach($languages as $code => $lang)
                        <div class="col-md-4">
                            <label class="d-block h-100 p-3 border rounded-3 lang-option {{ $currentLocale === $code ? 'border-danger' : '' }}"
                                   style="cursor: pointer; {{ $currentLocale === $code ? 'background: #FFF1F5;' : '' }}"
                                   for="lang_{{ $code }}">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <input class="form-check-input m-0" type="radio" name="landing_locale" id="lang_{{ $code }}" value="{{ $code }}" {{ $currentLocale === $code ? 'checked' : '' }}>
                                    <span class="fs-5">{{ $lang['flag'] }}</span>
                                    <span class="fw-bold" @if($lang['dir'] === 'rtl') dir="rtl" @endif>{{ $lang['native'] }}</span>
                                    @if($code === \App\Support\LandingLocale::DEFAULT)
                                        <span class="badge bg-secondary-subtle text-secondary rounded-pill ms-auto" style="font-size: 10px;">Default</span>
                                    @endif
                                </div>
                                <div class="text-muted small">
                                    {{ $lang['name'] }} · {{ $lang['dir'] === 'rtl' ? 'Right-to-left (RTL)' : 'Left-to-right' }}
                                </div>
                            </label>
                        </div>
                    @endforeach
                </div>

                <div class="form-check form-switch mt-4">
                    <input type="hidden" name="language_switcher" value="0">
                    <input class="form-check-input" type="checkbox" name="language_switcher" id="language_switcher" value="1" {{ $switcherEnabled ? 'checked' : '' }}>
                    <label class="form-check-label small fw-semibold" for="language_switcher">
                        Show a language switcher to visitors on the landing page
                    </label>
                    <div class="text-muted small">
                        When off, every visitor sees the language selected above. When on, visitors start in that language and can switch to the others.
                    </div>
                </div>

                <div class="alert alert-light border small mt-4 mb-0">
                    <i class="bi bi-info-circle me-1"></i>
                    Built-in landing page text is translated automatically. Text you changed yourself in the
                    <a href="{{ route('admin.saas.homepage') }}">SaaS Homepage Builder</a> is shown exactly as you typed it.
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-danger fw-semibold px-4 py-2" style="border-radius: 10px;">
            <i class="bi bi-check2-circle me-1"></i> Save Language
        </button>
    </form>

    <script>
        document.querySelectorAll('.lang-option input[type="radio"]').forEach(function (radio) {
            radio.addEventListener('change', function () {
                document.querySelectorAll('.lang-option').forEach(function (card) {
                    var on = card.querySelector('input').checked;
                    card.classList.toggle('border-danger', on);
                    card.style.background = on ? '#FFF1F5' : '';
                });
            });
        });
    </script>
@endsection
