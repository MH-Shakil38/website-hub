@extends('portfolio::frontend.layouts.master')

@section('page_title', $project->title . ' — Portfolio Case Study')

@section('portfolio_content')
<section class="pf-portfolio" style="padding: 56px 0 84px;">
    <div class="pf-container">
        <div style="margin-bottom: 24px;">
            <a href="{{ route('portfolio.projects') }}" style="font-size: 13px; color: var(--pf-light-blue); font-weight: 600;">
                <i class="fa-solid fa-arrow-left" style="margin-right: 6px;"></i> Back to All Projects
            </a>
        </div>

        <div style="display: grid; grid-template-columns: 1.45fr 0.85fr; gap: 36px; align-items: start;">
            <!-- Left: Main Image + Gallery + Full Description -->
            <div>
                <div style="border-radius: 18px; overflow: hidden; border: 1px solid var(--pf-dark-border); margin-bottom: 20px;">
                    <img src="{{ $project->thumbnail ?: '/module-media/Portfolio/theme/project-1.jpg' }}" alt="{{ $project->title }}" id="pfMainProjImg" style="width: 100%; max-height: 440px; object-fit: cover;">
                </div>

                @if(!empty($project->gallery_images) && count($project->gallery_images) > 1)
                <div style="display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 28px;">
                    @foreach($project->gallery_images as $gImg)
                        <img src="{{ $gImg }}" alt="Gallery"
                             onclick="document.getElementById('pfMainProjImg').src = this.src;"
                             style="width: 96px; height: 66px; object-fit: cover; border-radius: 10px; cursor: pointer; border: 2px solid var(--pf-electric);">
                    @endforeach
                </div>
                @endif

                <h1 class="pf-sec-heading" style="margin-bottom: 14px;">{{ $project->title }}</h1>
                <p style="font-size: 15.5px; color: var(--pf-light-blue); font-weight: 600; margin-bottom: 16px;">{{ $project->short_description }}</p>
                <div style="font-size: 14.5px; color: var(--pf-dark-sec-muted); line-height: 1.8; white-space: pre-line;">{{ $project->description }}</div>
            </div>

            <!-- Right: Project Metadata Card -->
            <div class="pf-process-card" style="padding: 28px;">
                <h4 style="font-size: 18px; font-weight: 800; margin-bottom: 18px;">Project Information</h4>

                <div style="display: flex; flex-direction: column; gap: 14px; margin-bottom: 24px;">
                    <div style="border-bottom: 1px solid var(--pf-dark-border); padding-bottom: 10px;">
                        <small style="color: var(--pf-dark-sec-muted); display: block; font-size: 11.5px;">Category</small>
                        <strong style="font-size: 14px;">{{ $project->category }}</strong>
                    </div>
                    <div style="border-bottom: 1px solid var(--pf-dark-border); padding-bottom: 10px;">
                        <small style="color: var(--pf-dark-sec-muted); display: block; font-size: 11.5px;">Client</small>
                        <strong style="font-size: 14px;">{{ $project->client_name ?: 'Global Client' }}</strong>
                    </div>
                    <div style="border-bottom: 1px solid var(--pf-dark-border); padding-bottom: 10px;">
                        <small style="color: var(--pf-dark-sec-muted); display: block; font-size: 11.5px;">Completion Date</small>
                        <strong style="font-size: 14px;">{{ $project->completion_date ?: 'October 2025' }}</strong>
                    </div>
                    <div>
                        <small style="color: var(--pf-dark-sec-muted); display: block; font-size: 11.5px; margin-bottom: 8px;">Technologies Used</small>
                        <div style="display: flex; flex-wrap: wrap; gap: 6px;">
                            @foreach(($project->technologies ?? ['Laravel', 'PHP', 'Tailwind CSS']) as $tech)
                                <span class="pf-step-badge" style="margin: 0;">{{ $tech }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div style="display: flex; flex-direction: column; gap: 10px;">
                    @if(!empty($project->live_url))
                        <a href="{{ $project->live_url }}" target="_blank" rel="noopener" class="pf-btn-primary" style="justify-content: center;">
                            <span>Live Website Preview</span>
                            <i class="fa-solid fa-arrow-up-right-from-square"></i>
                        </a>
                    @endif
                    @if(!empty($project->github_url))
                        <a href="{{ $project->github_url }}" target="_blank" rel="noopener" class="pf-btn-outline" style="justify-content: center;">
                            <i class="fa-brands fa-github"></i>
                            <span>View Source / Repository</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>

        @if($relatedProjects->isNotEmpty())
        <div style="margin-top: 64px;">
            <h3 class="pf-sec-heading" style="font-size: 26px; margin-bottom: 20px;">More Related Projects</h3>
            <div class="pf-projects-grid" style="grid-template-columns: repeat(3, 1fr);">
                @foreach($relatedProjects as $rel)
                    <a href="{{ route('portfolio.project.show', $rel->slug) }}" class="pf-project-card">
                        <div class="pf-project-thumb">
                            <img src="{{ $rel->thumbnail ?: '/module-media/Portfolio/theme/project-1.jpg' }}" alt="{{ $rel->title }}">
                        </div>
                        <div class="pf-project-body">
                            <div>
                                <h5>{{ $rel->title }}</h5>
                                <span>{{ $rel->category }}</span>
                            </div>
                            <span class="pf-project-arrow"><i class="fa-solid fa-arrow-right"></i></span>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</section>
@endsection
