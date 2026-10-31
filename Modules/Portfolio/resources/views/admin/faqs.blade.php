@extends('admin.layouts.master')

@section('title', 'FAQ Accordion CRUD — Developer Portfolio')

@section('admin_content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h3 style="font-size: 23px; font-weight: 800; color: #0F172A; margin: 0;">Frequently Asked Questions</h3>
            <p class="text-muted small mb-0">Manage accordion questions and answers stored in <code>port_faqs</code>.</p>
        </div>
        <button type="button" class="btn btn-primary btn-sm fw-bold px-3" data-bs-toggle="modal" data-bs-target="#addFaqModal">
            <i class="bi bi-plus-lg me-1"></i> Add FAQ
        </button>
    </div>

    <div class="sb-table-card">
        <table class="sb-data-table">
            <thead>
                <tr>
                    <th>Question</th>
                    <th>Answer</th>
                    <th>Category</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($faqs as $faq)
                    <tr>
                        <td class="fw-bold text-dark">{{ $faq->question }}</td>
                        <td style="max-width: 480px;">{{ $faq->answer }}</td>
                        <td><span class="sb-badge-pill sb-badge-blue">{{ $faq->category }}</span></td>
                        <td>
                            <div class="d-flex gap-2">
                                <button type="button" class="sb-page-btn" data-bs-toggle="modal" data-bs-target="#editFaq{{ $faq->id }}"><i class="bi bi-pencil"></i></button>
                                <form action="{{ route('admin.portfolio.faqs.destroy', $faq->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="sb-page-btn text-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>

                    <div class="modal fade" id="editFaq{{ $faq->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <form action="{{ route('admin.portfolio.faqs.update', $faq->id) }}" method="POST" class="modal-content border-0 shadow" style="border-radius: 16px;">
                                @csrf
                                @method('PUT')
                                <div class="modal-header">
                                    <h5 class="modal-title fw-bold">Edit FAQ</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold small">Question *</label>
                                        <input type="text" name="question" value="{{ $faq->question }}" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold small">Category</label>
                                        <input type="text" name="category" value="{{ $faq->category }}" class="form-control">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold small">Answer *</label>
                                        <textarea name="answer" rows="3" class="form-control" required>{{ $faq->answer }}</textarea>
                                    </div>
                                    <input type="hidden" name="is_active" value="1">
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary fw-bold">Update FAQ</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="modal fade" id="addFaqModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('admin.portfolio.faqs.store') }}" method="POST" class="modal-content border-0 shadow" style="border-radius: 16px;">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Add New FAQ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Question *</label>
                        <input type="text" name="question" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Category</label>
                        <input type="text" name="category" value="General" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Answer *</label>
                        <textarea name="answer" rows="3" class="form-control" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary fw-bold">Save FAQ</button>
                </div>
            </form>
        </div>
    </div>
@endsection
