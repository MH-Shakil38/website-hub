@extends('portfolio::frontend.layouts.master')

@section('page_title', $blog->title . ' — Developer Blog')

@section('portfolio_content')
<section class="pf-extra-light" style="min-height: 75vh;">
    <div class="pf-container" style="max-width: 900px;">
        <div style="margin-bottom: 20px;">
            <a href="{{ route('portfolio.blog') }}" style="font-size: 13px; color: var(--pf-electric); font-weight: 700;">
                <i class="fa-solid fa-arrow-left" style="margin-right: 6px;"></i> Back to All Articles
            </a>
        </div>

        <div class="pf-form-card">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px; font-size: 12px; color: var(--pf-text-muted-light);">
                <span style="background: rgba(7,139,255,0.12); color: var(--pf-electric); padding: 4px 12px; border-radius: 99px; font-weight: 700;">{{ $blog->category }}</span>
                <span><i class="fa-regular fa-calendar"></i> {{ $blog->published_date ?: 'Oct 20, 2025' }}</span>
                <span><i class="fa-regular fa-eye"></i> {{ $blog->views }} views</span>
            </div>

            <h1 class="pf-sec-heading" style="margin-bottom: 20px;">{{ $blog->title }}</h1>

            <div style="border-radius: 16px; overflow: hidden; margin-bottom: 26px;">
                <img src="{{ $blog->image ?: '/module-media/Portfolio/theme/blog-1.jpg' }}" alt="{{ $blog->title }}" style="width: 100%; max-height: 420px; object-fit: cover;">
            </div>

            <div style="font-size: 15.5px; line-height: 1.85; color: var(--pf-text-dark);">
                {!! $blog->content ?: '<p>' . e($blog->short_description) . '</p>' !!}
            </div>

            <!-- Social Share Bar -->
            <div style="margin-top: 32px; padding-top: 20px; border-top: 1px solid var(--pf-light-border); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
                <strong style="font-size: 13.5px;">Share This Article:</strong>
                <div class="pf-social-row">
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->fullUrl()) }}" target="_blank" class="pf-social-icon"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->fullUrl()) }}&text={{ urlencode($blog->title) }}" target="_blank" class="pf-social-icon"><i class="fa-brands fa-x-twitter"></i></a>
                    <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(request()->fullUrl()) }}" target="_blank" class="pf-social-icon"><i class="fa-brands fa-linkedin-in"></i></a>
                </div>
            </div>
        </div>

        @if($relatedBlogs->isNotEmpty())
        <div style="margin-top: 48px;">
            <h3 class="pf-sec-heading" style="font-size: 24px; margin-bottom: 18px;">Related Articles</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px;">
                @foreach($relatedBlogs as $rBlog)
                    <a href="{{ route('portfolio.blog.show', $rBlog->slug) }}" class="pf-blog-card">
                        <div class="pf-blog-thumb">
                            <img src="{{ $rBlog->image ?: '/module-media/Portfolio/theme/blog-1.jpg' }}" alt="{{ $rBlog->title }}">
                            <span class="pf-blog-date">{{ $rBlog->published_date }}</span>
                        </div>
                        <div class="pf-blog-card-body">
                            <h6>{{ $rBlog->title }}</h6>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</section>
@endsection
