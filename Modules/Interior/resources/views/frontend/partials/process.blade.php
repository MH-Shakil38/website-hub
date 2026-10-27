<!-- ===== PROCESS ===== -->
@php
    $stepIcons = [
        '<path d="M12 21V9"/><path d="M12 13c-4 0-6-3-6-7 4 0 6 3 6 7z"/>',
        '<path d="M12 21V9"/><path d="M12 11c0-4 2-7 6-7 0 4-2 7-6 7z"/><path d="M12 14c-3 0-5-2-5-5 3 0 5 2 5 5z"/>',
        '<path d="M12 21V5"/><path d="M12 9c-3 0-5-2-5-5 3 0 5 2 5 5z"/><path d="M12 14c3 0 5-2 5-5-3 0-5 2-5 5z"/>',
        '<path d="M12 21V8"/><path d="M12 12c-4 0-6-3-6-7 4 0 6 3 6 7z"/><path d="M12 12c4 0 6-3 6-7-4 0-6 3-6 7z"/>',
        '<path d="M12 21V10"/><path d="M12 14c-4 0-7-3-7-8 5 0 7 3 7 8z"/><path d="M12 12c0-4 2-7 6-7 0 4-2 7-6 7z"/>',
    ];
    $whyIcons = [
        '<path d="M12 21V9"/><path d="M12 13c-4 0-6-3-6-7 4 0 6 3 6 7z"/><path d="M12 11c0-4 2-7 6-7 0 4-2 7-6 7z"/>',
        '<path d="M5 19c0-8 6-14 14-14 0 8-6 14-14 14z"/><path d="M5 19 13 11"/>',
        '<rect x="4" y="4" width="16" height="16" rx="3"/><path d="M9 12l2 2 4-4"/>',
        '<circle cx="12" cy="12" r="8"/><path d="M12 8v4l3 2"/>',
    ];
@endphp
<section class="sec" id="process">
  <div class="container process-wrap">
    <div>
      <div class="head-center" style="margin-bottom:34px">
        <div class="eyebrow">{{ $t['process']['eyebrow'] }}</div>
        <h2>{{ $t['process']['heading'] }}</h2>
      </div>
      <div class="steps">
        @foreach($t['process']['steps'] as $idx => $step)
          <div class="step">
            <div class="icon-circle"><svg viewBox="0 0 24 24">{!! $stepIcons[$idx % count($stepIcons)] !!}</svg></div>
            <h5>{{ $step['title'] }}</h5>
            <p>{{ $step['description'] }}</p>
          </div>
        @endforeach
      </div>
    </div>
    <div class="why">
      <div class="eyebrow">{{ $t['process']['why_eyebrow'] }}</div>
      <ul>
        @foreach($t['process']['why_items'] as $idx => $whyItem)
          <li><svg viewBox="0 0 24 24">{!! $whyIcons[$idx % count($whyIcons)] !!}</svg>{{ $whyItem }}</li>
        @endforeach
      </ul>
      <svg style="position:absolute;right:26px;bottom:22px;width:90px;opacity:.5" viewBox="0 0 100 100" fill="none" stroke="#efe6d3" stroke-width="1"><path d="M10 95 C40 70 60 40 92 8"/><path d="M40 68 C26 62 22 48 26 38 C38 44 42 56 40 68Z"/><path d="M58 48 C72 46 80 36 82 24 C70 26 60 36 58 48Z"/><path d="M70 32 C58 26 56 16 58 6 C68 10 72 20 70 32Z"/></svg>
    </div>
  </div>
</section>
