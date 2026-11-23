@extends('admin.layouts.master')

@section('title', 'WonderLearn Dashboard - Admin')

@section('admin_content')
<div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
    <div>
        <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1 fw-bold mb-1">
            <i class="bi bi-stars me-1"></i> Kids Learning Module (wl_*)
        </span>
        <h3 class="fw-bolder mb-0" style="color: #0B2545;">WonderLearn — Curriculum & Content Hub</h3>
        <p class="text-muted small mb-0">Manage Explore Topics, Magical Stories, Play & Learn Games, Adventures, and Kid Enrollments.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ url('/wonder-learn') }}" target="_blank" class="btn btn-outline-primary rounded-3 fw-semibold">
            <i class="bi bi-box-arrow-up-right me-1"></i> Preview WonderLearn Frontend
        </a>
        <a href="{{ route('admin.wonderlearn.settings') }}" class="btn btn-primary rounded-3 fw-semibold">
            <i class="bi bi-palette2 me-1"></i> Hero & Theme Settings
        </a>
    </div>
</div>

<!-- KPI Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3 col-sm-6">
        <div class="bg-white border rounded-4 p-3 shadow-sm h-100 d-flex align-items-center justify-content-between">
            <div>
                <div class="text-muted small fw-semibold">Explore Topics</div>
                <div class="fs-3 fw-bolder text-dark">{{ $topicsCount }}</div>
                <a href="{{ route('admin.wonderlearn.topics.index') }}" class="small text-primary text-decoration-none fw-semibold">Manage Topics &rarr;</a>
            </div>
            <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: #FEF3C7; color: #D97706; font-size: 22px;">
                <i class="bi bi-grid-3x3-gap-fill"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="bg-white border rounded-4 p-3 shadow-sm h-100 d-flex align-items-center justify-content-between">
            <div>
                <div class="text-muted small fw-semibold">Magical Stories</div>
                <div class="fs-3 fw-bolder text-dark">{{ $storiesCount }}</div>
                <a href="{{ route('admin.wonderlearn.stories.index') }}" class="small text-primary text-decoration-none fw-semibold">Manage Stories &rarr;</a>
            </div>
            <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: #DBEAFE; color: #2563EB; font-size: 22px;">
                <i class="bi bi-book-half"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="bg-white border rounded-4 p-3 shadow-sm h-100 d-flex align-items-center justify-content-between">
            <div>
                <div class="text-muted small fw-semibold">Play & Learn Games</div>
                <div class="fs-3 fw-bolder text-dark">{{ $gamesCount }}</div>
                <a href="{{ route('admin.wonderlearn.games.index') }}" class="small text-primary text-decoration-none fw-semibold">Manage Games &rarr;</a>
            </div>
            <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: #DCFCE7; color: #16A34A; font-size: 22px;">
                <i class="bi bi-controller"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="bg-white border rounded-4 p-3 shadow-sm h-100 d-flex align-items-center justify-content-between">
            <div>
                <div class="text-muted small fw-semibold">Kid Enrollments</div>
                <div class="fs-3 fw-bolder text-dark">{{ $enrollmentsCount }}</div>
                <a href="{{ route('admin.wonderlearn.enrollments.index') }}" class="small text-primary text-decoration-none fw-semibold">View Explorers &rarr;</a>
            </div>
            <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: #FCE7F3; color: #DB2777; font-size: 22px;">
                <i class="bi bi-person-hearts"></i>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Recent Magical Stories -->
    <div class="col-lg-6">
        <div class="bg-white border rounded-4 p-4 shadow-sm h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h5 class="fw-bold mb-0">Featured Magical Stories</h5>
                <a href="{{ route('admin.wonderlearn.stories.index') }}" class="btn btn-sm btn-outline-primary rounded-pill">View All</a>
            </div>
            <div class="d-flex flex-column gap-3">
                @foreach($recentStories as $story)
                    <div class="d-flex align-items-center justify-content-between border-bottom pb-2">
                        <div class="d-flex align-items-center gap-3">
                            <img src="{{ $story->image }}" alt="{{ $story->title }}" style="width: 60px; height: 42px; object-fit: cover; border-radius: 8px;">
                            <div>
                                <div class="fw-bold text-dark">{{ $story->title }}</div>
                                <small class="text-muted">{{ $story->reading_time }} • {{ $story->age_range }}</small>
                            </div>
                        </div>
                        <span class="badge rounded-pill" style="background: {{ $story->tag_bg }}; color: {{ $story->tag_color }};">
                            {{ $story->category_tag }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Recent Kid Enrollments -->
    <div class="col-lg-6">
        <div class="bg-white border rounded-4 p-4 shadow-sm h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h5 class="fw-bold mb-0">Recent Kid Explorer Enrollments</h5>
                <a href="{{ route('admin.wonderlearn.enrollments.index') }}" class="btn btn-sm btn-outline-primary rounded-pill">Manage All</a>
            </div>
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                    <thead>
                        <tr class="text-muted small">
                            <th>Child</th>
                            <th>Parent</th>
                            <th>Favorite Topic</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentEnrollments as $enr)
                            <tr>
                                <td class="fw-bold">{{ $enr->child_name }} <span class="text-muted small">({{ $enr->child_age }}y)</span></td>
                                <td class="small">{{ $enr->parent_name }}</td>
                                <td><span class="badge bg-warning-subtle text-dark rounded-pill">{{ $enr->favorite_topic }}</span></td>
                                <td><span class="badge bg-success-subtle text-success rounded-pill">{{ $enr->status }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
