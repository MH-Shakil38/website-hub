@extends('portfolio::frontend.layouts.master')

@section('page_title', 'All Portfolio Projects — ' . ($t['header']['brand_name'] ?? 'Alex Morgan'))

@section('portfolio_content')
<section class="pf-portfolio" style="padding: 64px 0 90px; min-height: 75vh;">
    <div class="pf-container">
        <div style="display: flex; align-items: flex-end; justify-content: space-between; gap: 20px; flex-wrap: wrap; margin-bottom: 32px;">
            <div>
                <div class="pf-sec-tag">COMPLETE PORTFOLIO SHOWCASE</div>
                <h1 class="pf-sec-heading" style="margin-bottom: 6px;">Explore All My Works</h1>
                <p style="font-size: 14px; color: var(--pf-dark-sec-muted);">Filter by industry category or search by project name and technology stack.</p>
            </div>

            <form action="{{ route('portfolio.projects') }}" method="GET" style="display: flex; gap: 10px; flex-wrap: wrap;">
                @if(!empty($category))
                    <input type="hidden" name="category" value="{{ $category }}">
                @endif
                <input type="text" name="search" value="{{ $search }}" placeholder="Search projects..." class="pf-input" style="width: 240px; height: 40px;">
                <button type="submit" class="pf-btn-primary" style="height: 40px;">Search</button>
            </form>
        </div>

        <!-- Category Filter Bar -->
        <div class="pf-skill-filter-bar" style="margin-bottom: 28px;">
            <a href="{{ route('portfolio.projects') }}" class="pf-filter-chip {{ empty($category) || $category === 'All' ? 'active' : '' }}">All Categories</a>
            @foreach($categories as $c)
                <a href="{{ route('portfolio.projects', ['category' => $c]) }}" class="pf-filter-chip {{ $category === $c ? 'active' : '' }}">{{ $c }}</a>
            @endforeach
        </div>

        <!-- Projects Grid -->
        <div class="pf-projects-grid">
            @forelse($projects as $proj)
                <a href="{{ route('portfolio.project.show', $proj->slug) }}" class="pf-project-card">
                    <div class="pf-project-thumb" style="height: 175px;">
                        <img src="{{ $proj->thumbnail ?: '/module-media/Portfolio/theme/project-1.jpg' }}" alt="{{ $proj->title }}">
                    </div>
                    <div class="pf-project-body">
                        <div>
                            <h5>{{ $proj->title }}</h5>
                            <span>{{ $proj->category }}</span>
                        </div>
                        <span class="pf-project-arrow">
                            <i class="fa-solid fa-arrow-right"></i>
                        </span>
                    </div>
                </a>
            @empty
                <div style="grid-column: 1 / -1; text-align: center; padding: 48px; color: var(--pf-dark-sec-muted);">
                    No portfolio projects matched your filter.
                </div>
            @endforelse
        </div>

        <div style="margin-top: 32px;">
            {{ $projects->links() }}
        </div>
    </div>
</section>
@endsection
