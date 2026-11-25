@extends('hajjumrah::frontend.layouts.master')

@section('title', $blog->trans('title', $locale) . ' — ' . ($tr['brand_name'] ?? 'Al Hajenra'))

@section('content')
    <section style="background: #080B0A; color: #FFFFFF; padding: 54px 0;">
        <div class="hajj-container" style="max-width: 900px;">
            <div style="margin-bottom: 14px;">
                <a href="{{ route('hajjumrah.home') }}" style="color: #F4B87A; font-size: 13px; font-weight: 700; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fa-solid fa-arrow-left rtl-flip"></i>
                    <span>{{ $tr['nav_home'] ?? 'Home' }}</span>
                </a>
            </div>
            <span style="display: inline-block; background: rgba(31, 168, 79, 0.2); color: #34D970; font-size: 12px; font-weight: 700; padding: 4px 12px; border-radius: 999px; margin-bottom: 10px;">
                {{ $blog->trans('category', $locale) }} • {{ $blog->published_date }}
            </span>
            <h1 style="font-size: clamp(26px, 3.8vw, 38px); font-weight: 800; line-height: 1.25;">{{ $blog->trans('title', $locale) }}</h1>
        </div>
    </section>

    <section style="padding: 56px 0; background: #F6F9F8;">
        <div class="hajj-container" style="max-width: 900px;">
            <div style="background: #FFFFFF; border-radius: 16px; padding: 32px; border: 1px solid #E5ECE9;">
                <img src="{{ $blog->image }}" alt="{{ $blog->trans('title', $locale) }}" style="width: 100%; max-height: 420px; object-fit: cover; border-radius: 12px; margin-bottom: 24px;">
                <div style="color: #1E293B; font-size: 15.5px; line-height: 1.85;">
                    {!! $blog->trans('content', $locale) !!}
                </div>
            </div>
        </div>
    </section>
@endsection
