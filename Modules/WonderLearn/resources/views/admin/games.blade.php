@extends('admin.layouts.master')

@section('title', 'Play & Learn Games - WonderLearn Admin')

@section('admin_content')
<div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
    <div>
        <h3 class="fw-bolder mb-1" style="color: #0B2545;">Play & Learn Games (wl_games)</h3>
        <p class="text-muted small mb-0">Manage interactive mini-game cards, questions, and multiple-choice answers.</p>
    </div>
    <button type="button" class="btn btn-primary rounded-3 fw-semibold" data-bs-toggle="modal" data-bs-target="#addGameModal">
        <i class="bi bi-plus-lg me-1"></i> Add Mini-Game
    </button>
</div>

<div class="row g-3">
    @foreach($games as $game)
        <div class="col-md-6 col-lg-4">
            <div class="bg-white border rounded-4 p-3 shadow-sm h-100">
                <img src="{{ $game->image }}" alt="{{ $game->title }}" class="w-100 rounded-3 mb-3" style="height: 140px; object-fit: cover;">
                <form action="{{ route('admin.wonderlearn.games.update', $game->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="mb-2">
                        <label class="form-label small fw-bold mb-1">Game Title</label>
                        <input type="text" name="title" value="{{ $game->title }}" class="form-control form-control-sm" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-bold mb-1">Topic Category</label>
                        <input type="text" name="category" value="{{ $game->category }}" class="form-control form-control-sm" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-bold mb-1">Interactive Question Prompt</label>
                        <input type="text" name="question_prompt" value="{{ $game->question_prompt }}" class="form-control form-control-sm">
                    </div>
                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label class="form-label small fw-bold mb-1">Correct Answer</label>
                            <input type="text" name="correct_answer" value="{{ $game->correct_answer }}" class="form-control form-control-sm">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold mb-1">Options (CSV)</label>
                            <input type="text" name="options_csv" value="{{ is_array($game->options_json) ? implode(', ', $game->options_json) : '' }}" class="form-control form-control-sm">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold mb-1">Replace Card Art</label>
                        <input type="file" name="image_file" class="form-control form-control-sm" accept="image/*">
                    </div>
                    <div class="d-flex justify-content-between">
                        <button type="submit" class="btn btn-sm btn-primary rounded-3 fw-semibold">Save Game</button>
                        <button type="button" class="btn btn-sm btn-outline-danger rounded-3"
                                onclick="if(confirm('Delete this game?')) document.getElementById('del-game-{{ $game->id }}').submit();">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </form>
                <form id="del-game-{{ $game->id }}" action="{{ route('admin.wonderlearn.games.destroy', $game->id) }}" method="POST" class="d-none">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>
    @endforeach
</div>

<!-- Add Game Modal -->
<div class="modal fade" id="addGameModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('admin.wonderlearn.games.store') }}" method="POST" enctype="multipart/form-data" class="modal-content border-0 rounded-4 shadow">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Add Play & Learn Mini-Game</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-bold">Game Title *</label>
                    <input type="text" name="title" class="form-control" required placeholder="e.g. Dino Addition Quest">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Category *</label>
                    <input type="text" name="category" value="Math Magic" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Challenge Question</label>
                    <input type="text" name="question_prompt" class="form-control" placeholder="e.g. What is 4 + 3?">
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-bold">Correct Answer</label>
                        <input type="text" name="correct_answer" value="7" class="form-control">
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold">Choices (Comma separated)</label>
                        <input type="text" name="options_csv" value="5, 6, 7, 8" class="form-control">
                    </div>
                </div>
                <div class="mb-2">
                    <label class="form-label small fw-bold">Game Card Image</label>
                    <input type="file" name="image_file" class="form-control" accept="image/*">
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary rounded-3 fw-semibold">Create Game</button>
            </div>
        </form>
    </div>
</div>
@endsection
