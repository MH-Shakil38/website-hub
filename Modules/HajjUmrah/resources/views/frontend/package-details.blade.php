@extends('hajjumrah::frontend.layouts.master')

@section('title', $package->trans('title', $locale) . ' — ' . ($tr['brand_name'] ?? 'Al Hajenra'))

@section('content')
    <section style="background: #080B0A; color: #FFFFFF; padding: 54px 0;">
        <div class="hajj-container">
            <div style="margin-bottom: 14px;">
                <a href="{{ route('hajjumrah.home') }}" style="color: #F4B87A; font-size: 13px; font-weight: 700; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fa-solid fa-arrow-left rtl-flip"></i>
                    <span>{{ $tr['nav_home'] ?? 'Home' }}</span>
                </a>
            </div>
            <span style="display: inline-block; background: rgba(244, 184, 122, 0.16); color: #F4B87A; font-size: 12px; font-weight: 700; padding: 4px 12px; border-radius: 999px; margin-bottom: 10px;">
                {{ $package->package_type }} • {{ $package->trans('duration', $locale) }}
            </span>
            <h1 style="font-size: clamp(28px, 4vw, 42px); font-weight: 800; margin-bottom: 10px;">{{ $package->trans('title', $locale) }}</h1>
            <div style="color: rgba(255,255,255,0.78); max-width: 720px; font-size: 15px;">{!! $package->trans('short_description', $locale) !!}</div>
        </div>
    </section>

    <section style="padding: 56px 0; background: #F6F9F8;">
        <div class="hajj-container">
            <div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 32px; align-items: start;">
                <!-- Left Details -->
                <div style="background: #FFFFFF; border-radius: 16px; padding: 28px; border: 1px solid #E5ECE9;">
                    <img src="{{ $package->thumbnail }}" alt="{{ $package->trans('title', $locale) }}" style="width: 100%; max-height: 380px; object-fit: cover; border-radius: 12px; margin-bottom: 24px;">

                    <h3 style="font-size: 22px; font-weight: 800; margin-bottom: 12px; color: #0F172A;">Package Overview & Itinerary</h3>
                    <div style="color: #334155; font-size: 14.5px; line-height: 1.8; margin-bottom: 24px;">
                        {!! $package->trans('description', $locale) !!}
                    </div>

                    @if(!empty($package->features))
                        <h4 style="font-size: 17px; font-weight: 800; margin-bottom: 14px; color: #0F172A;">Included Amenities & Facilities</h4>
                        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px;">
                            @foreach($package->features as $feat)
                                <div style="display: flex; align-items: center; gap: 10px; background: #F8FAFC; padding: 10px 14px; border-radius: 10px; font-size: 13.5px; font-weight: 600;">
                                    <i class="fa-solid fa-circle-check" style="color: #1FA84F;"></i>
                                    <span>{{ $feat }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Right Booking Summary Box -->
                <div style="background: #FFFFFF; border-radius: 16px; padding: 28px; border: 1px solid #E5ECE9; position: sticky; top: 96px;">
                    <div style="font-size: 12px; font-weight: 800; color: #E67E22; text-transform: uppercase; margin-bottom: 4px;">Starting Price</div>
                    <div style="font-size: 34px; font-weight: 800; color: #1A6CE7; margin-bottom: 18px;">
                        {{ $package->price_display }} <span style="font-size: 14px; color: #64748B; font-weight: 600;">{{ $package->trans('price_unit', $locale) }}</span>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 12px; padding: 16px 0; border-top: 1px solid #EEF2F6; border-bottom: 1px solid #EEF2F6; margin-bottom: 20px; font-size: 13.5px;">
                        <div><i class="fa-regular fa-clock me-2" style="color:#1FA84F;"></i> <strong>{{ $package->trans('duration', $locale) }}</strong></div>
                        @if($package->flight_info)
                            <div><i class="fa-solid fa-plane-up me-2" style="color:#1FA84F;"></i> {{ $package->flight_info }}</div>
                        @endif
                        @if($package->makkah_hotel)
                            <div><i class="fa-solid fa-kaaba me-2" style="color:#1FA84F;"></i> <strong>Makkah:</strong> {{ $package->makkah_hotel }}</div>
                        @endif
                        @if($package->madinah_hotel)
                            <div><i class="fa-solid fa-mosque me-2" style="color:#1FA84F;"></i> <strong>Madinah:</strong> {{ $package->madinah_hotel }}</div>
                        @endif
                    </div>

                    <button type="button" class="hajj-btn-select-pkg" data-open-booking data-package-name="{{ $package->title }} ({{ $package->price_display }})">
                        {{ $tr['pricing_select_btn'] ?? 'SELECT PACKAGE' }}
                    </button>
                </div>
            </div>
        </div>
    </section>
@endsection
