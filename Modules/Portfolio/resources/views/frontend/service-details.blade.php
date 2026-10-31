@extends('portfolio::frontend.layouts.master')

@section('page_title', $service->title . ' — Web Development Service')

@section('portfolio_content')
<section class="pf-portfolio" style="padding: 56px 0 84px; min-height: 75vh;">
    <div class="pf-container">
        <div style="margin-bottom: 24px;">
            <a href="{{ url('/developer-portfolio') }}#services" style="font-size: 13px; color: var(--pf-light-blue); font-weight: 600;">
                <i class="fa-solid fa-arrow-left" style="margin-right: 6px;"></i> Back to All Services
            </a>
        </div>

        <div style="display: grid; grid-template-columns: 1.4fr 0.8fr; gap: 36px; align-items: start;">
            <div class="pf-process-card" style="padding: 36px;">
                <div style="width: 56px; height: 56px; border-radius: 14px; background: rgba(7, 139, 255, 0.16); color: var(--pf-light-blue); display: flex; align-items: center; justify-content: center; font-size: 26px; margin-bottom: 20px;">
                    <i class="{{ $service->icon_class ?: 'fa-solid fa-code' }}"></i>
                </div>
                <span class="pf-step-badge">{{ $service->subtitle }}</span>
                <h1 class="pf-sec-heading" style="margin-bottom: 14px;">{{ $service->title }}</h1>
                <p style="font-size: 15px; color: var(--pf-dark-sec-muted); line-height: 1.75; margin-bottom: 26px;">
                    {{ $service->description ?: $service->short_description }}
                </p>

                @if(!empty($service->features))
                <h4 style="font-size: 16px; font-weight: 700; margin-bottom: 14px;">What's Included in This Service</h4>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 12px; margin-bottom: 28px;">
                    @foreach($service->features as $feat)
                        <div style="display: flex; align-items: center; gap: 10px; font-size: 13.5px;">
                            <i class="fa-solid fa-circle-check" style="color: var(--pf-light-blue);"></i>
                            <span>{{ $feat }}</span>
                        </div>
                    @endforeach
                </div>
                @endif

                <a href="{{ url('/developer-portfolio') }}#contact" class="pf-btn-primary">
                    <span>Request a Quote for {{ $service->title }}</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>

            <div class="pf-process-card" style="padding: 26px;">
                <h4 style="font-size: 17px; font-weight: 800; margin-bottom: 16px;">Other Services</h4>
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    @foreach($otherServices as $oSrv)
                        <a href="{{ route('portfolio.service.show', $oSrv->slug) }}" style="display: flex; align-items: center; justify-content: space-between; padding: 12px 14px; border-radius: 10px; border: 1px solid var(--pf-dark-border);">
                            <span style="font-size: 13.5px; font-weight: 600;">{{ $oSrv->title }}</span>
                            <i class="fa-solid fa-chevron-right" style="font-size: 11px; color: var(--pf-light-blue);"></i>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
