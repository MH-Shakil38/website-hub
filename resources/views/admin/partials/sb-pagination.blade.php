{{-- Real page links for a LengthAwarePaginator: first, last, and two pages around the current one. --}}
@php
    /** @var \Illuminate\Contracts\Pagination\LengthAwarePaginator $paginator */
    $current = $paginator->currentPage();
    $last = max(1, $paginator->lastPage());
    $pagesToShow = collect([1, $last])
        ->merge(range(max(1, $current - 2), min($last, $current + 2)))
        ->unique()->sort()->values();
@endphp
<div class="sb-pagination">
    @if($paginator->onFirstPage())
        <span class="sb-page-btn" style="opacity: .45; pointer-events: none;" aria-disabled="true"><i class="bi bi-chevron-left"></i></span>
    @else
        <a href="{{ $paginator->previousPageUrl() }}" class="sb-page-btn" rel="prev" aria-label="Previous page"><i class="bi bi-chevron-left"></i></a>
    @endif

    @foreach($pagesToShow as $idx => $p)
        @if($idx > 0 && $p - $pagesToShow[$idx - 1] > 1)
            <span style="padding: 0 4px; color: #94A3B8;">...</span>
        @endif
        <a href="{{ $paginator->url($p) }}" class="sb-page-btn {{ $p === $current ? 'active' : '' }}" @if($p === $current) aria-current="page" @endif>{{ $p }}</a>
    @endforeach

    @if($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" class="sb-page-btn" rel="next" aria-label="Next page"><i class="bi bi-chevron-right"></i></a>
    @else
        <span class="sb-page-btn" style="opacity: .45; pointer-events: none;" aria-disabled="true"><i class="bi bi-chevron-right"></i></span>
    @endif
</div>
