@extends('admin.layouts.master')

@section('title', 'Testimonials & FAQs — FitPulse Gym Admin')

@section('admin_content')
    <div class="bg-white border rounded-4 p-4 mb-4 shadow-sm d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <h3 style="font-size: 22px; font-weight: 800; color: #0F172A; margin: 0;">Member Testimonials &amp; FAQs</h3>
                <span class="sb-badge-pill sb-badge-green">gym_testimonials + gym_faqs</span>
            </div>
            <p class="text-muted small mb-0">Manage homepage member transformation stories and Frequently Asked Questions in English &amp; Bengali.</p>
        </div>

        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-primary btn-sm fw-bold px-3 py-2 rounded-3" data-bs-toggle="modal" data-bs-target="#addFaqModal">
                <i class="bi bi-question-circle me-1"></i> Add FAQ
            </button>
            <button type="button" class="btn btn-success btn-sm fw-bold px-3 py-2 rounded-3" data-bs-toggle="modal" data-bs-target="#addTestiModal">
                <i class="bi bi-chat-quote me-1"></i> Add Testimonial
            </button>
        </div>
    </div>

    <div class="row g-4">
        {{-- Left Column: Member Testimonials --}}
        <div class="col-lg-6">
            <div class="sb-table-card">
                <div class="p-3 border-bottom">
                    <h6 class="fw-bold mb-0">Member Testimonials (gym_testimonials)</h6>
                </div>
                <div class="table-responsive">
                    <table class="sb-data-table">
                        <thead>
                            <tr>
                                <th>Member</th>
                                <th>Review (EN / BN)</th>
                                <th>Rating</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($testimonials as $t)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <img src="{{ $t->avatar }}" alt="{{ $t->member_name }}" style="width:40px;height:40px;border-radius:50%;object-fit:cover;">
                                            <div>
                                                <div class="fw-bold text-dark small">{{ $t->member_name }}</div>
                                                <div class="text-success small" style="font-size:11px;">{{ $t->transformation_tag }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="small">
                                        <div>{{ Str::limit($t->review_text, 65) }}</div>
                                        <div class="text-muted" style="font-size:11px;">{{ Str::limit($t->review_text_bn, 65) }}</div>
                                    </td>
                                    <td class="text-warning small fw-bold">{{ $t->rating }} ★</td>
                                    <td>
                                        <form action="{{ route('admin.gymfitness.reviews.testimonials.destroy', $t->id) }}" method="POST" onsubmit="return confirm('Delete testimonial?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="sb-page-btn text-danger"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Right Column: FAQs --}}
        <div class="col-lg-6">
            <div class="sb-table-card">
                <div class="p-3 border-bottom">
                    <h6 class="fw-bold mb-0">Frequently Asked Questions (gym_faqs)</h6>
                </div>
                <div class="table-responsive">
                    <table class="sb-data-table">
                        <thead>
                            <tr>
                                <th>Question (EN / BN)</th>
                                <th>Answer</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($faqs as $faq)
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark small">{{ $faq->question }}</div>
                                        <div class="text-muted small" style="font-size:11px;">{{ $faq->question_bn }}</div>
                                    </td>
                                    <td class="small">{{ Str::limit($faq->answer, 75) }}</td>
                                    <td>
                                        <form action="{{ route('admin.gymfitness.reviews.faqs.destroy', $faq->id) }}" method="POST" onsubmit="return confirm('Delete FAQ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="sb-page-btn text-danger"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Add Testimonial Modal --}}
    <div class="modal fade" id="addTestiModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-white border-0 shadow" style="border-radius:16px;">
                <form action="{{ route('admin.gymfitness.reviews.testimonials.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header border-bottom px-4 py-3">
                        <h5 class="modal-title fw-bold">Add Member Testimonial</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Member Name (EN) *</label>
                                <input type="text" name="member_name" class="form-control" required placeholder="e.g. Tanvir Ahmed">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Member Name (BN)</label>
                                <input type="text" name="member_name_bn" class="form-control" placeholder="তানভীর আহমেদ">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Transformation Tag (EN)</label>
                                <input type="text" name="transformation_tag" value="Lost 12kg in 3 Months" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Rating (1 - 5)</label>
                                <select name="rating" class="form-select">
                                    <option value="5">5 Stars</option>
                                    <option value="4">4 Stars</option>
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label small fw-bold">Review Text (EN) *</label>
                                <textarea name="review_text" rows="2" class="form-control" required></textarea>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label small fw-bold">Review Text (BN)</label>
                                <textarea name="review_text_bn" rows="2" class="form-control"></textarea>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label small fw-bold">Avatar Image</label>
                                <input type="file" name="avatar_file" class="form-control" accept="image/*">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top px-4 py-3">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success fw-bold px-4">Save Testimonial</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Add FAQ Modal --}}
    <div class="modal fade" id="addFaqModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-white border-0 shadow" style="border-radius:16px;">
                <form action="{{ route('admin.gymfitness.reviews.faqs.store') }}" method="POST">
                    @csrf
                    <div class="modal-header border-bottom px-4 py-3">
                        <h5 class="modal-title fw-bold">Add Frequently Asked Question</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label small fw-bold">Question (EN) *</label>
                                <input type="text" name="question" class="form-control" required placeholder="e.g. Do you have separate timing for ladies?">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label small fw-bold">Question (BN)</label>
                                <input type="text" name="question_bn" class="form-control">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label small fw-bold">Answer (EN) *</label>
                                <textarea name="answer" rows="3" class="form-control" required></textarea>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label small fw-bold">Answer (BN)</label>
                                <textarea name="answer_bn" rows="2" class="form-control"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top px-4 py-3">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary fw-bold px-4">Save FAQ</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
