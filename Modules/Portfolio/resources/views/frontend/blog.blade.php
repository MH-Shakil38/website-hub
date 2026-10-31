@extends('portfolio::frontend.layouts.master')

@section('page_title', 'Developer Blog & Articles — ' . ($t['header']['brand_name'] ?? 'Alex Morgan'))

@section('portfolio_content')
<section class="pf-extra-light" style="min-height: 75vh;">
    <div class="pf-container">
        <div style="display: flex; align-items: flex-end; justify-content: space-between; gap: 20px; flex-wrap: wrap; margin-bottom: 32px;">
            <div>
                <div class="pf-sec-tag">DEVELOPER JOURNAL &amp; TUTORIALS</div>
                <h1 class="pf-sec-heading" style="margin-bottom: 6px;">Latest Articles &amp; Insights</h1>
                <p style="font-size: 14px; color: var(--pf-text-muted-light);">Tutorials and best practices on Laravel, Next.js, UI/UX, and software engineering.</p>
            </div>

            <form action="{{ route('portfolio.blog') }}" method="GET" style="display: flex; gap: 10px;">
                <input type="text" name="search" value="{{ $search }}" placeholder="Search articles..." class="pf-input" style="width: 240px; height: 40px; background: #FFFFFF;">
                <button type="submit" class="pf-btn-primary" style="height: 40px;">Search</button>
            </form>
        </div>

        <div class="pf-skill-filter-bar" style="margin-bottom: 28px;">
            <a href="{{ route('portfolio.blog') }}" class="pf-filter-chip {{ empty($category) || $category === 'All' ? 'active' : '' }}" style="color: var(--pf-text-dark);">All Topics</a>
            @foreach($categories as $c)
                <a href="{{ route('portfolio.blog', ['category' => $c]) }}" class="pf-filter-chip {{ $category === $c ? 'active' : '' }}" style="color: var(--pf-text-dark);">{{ $c }}</a>
            @endforeach
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 22px;">
            @forelse($blogs as $blog)
                <a href="{{ route('portfolio.blog.show', $blog->slug) }}" class="pf-blog-card" style="display: flex; flex-direction: column;">
                    <div class="pf-blog-thumb" style="height: 195px;">
                        <img src="{{ $blog->image ?: '/module-media/Portfolio/theme/blog-1.jpg' }}" alt="{{ $blog->title }}">
                        <span class="pf-blog-date">{{ $blog->published_date ?: 'Oct 20, 2025' }}</span>
                    </div>
                    <div class="pf-blog-card-body" style="padding: 18px;">
                        <span style="font-size: 11px; font-weight: 700; color: var(--pf-electric); text-transform: uppercase;">{{ $blog->category }}</span>
                        <h4 style="font-size: 17px; font-weight: 800; margin: 6px 0 8px; color: var(--pf-text-dark);">{{ $blog->title }}</h4>
                        <p style="font-size: 13px; color: var(--pf-text-muted-light); margin-bottom: 12px;">{{ $blog->short_description }}</p>
                        <span style="font-size: 12.5px; font-weight: 700; color: var(--pf-electric);">Read Full Article <i class="fa-solid fa-arrow-right"></i></span>
                    </div>
                </a>
            @empty
                <div style="grid-column: 1 / -1; text-align: center; padding: 48px;">No articles found.</div>
            @endforelse
        </div>

        <div style="margin-top: 32px;">
            {{ $blogs->links() }}
        </div>
    </div>
</section>
@endsection
