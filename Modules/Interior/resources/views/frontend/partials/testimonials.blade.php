<!-- ===== OUR CLIENTS + TESTIMONIALS ===== -->
<section class="testi-wrap" id="journal">
  <div class="container">
    @include('interior::frontend.partials.clients')

    <div class="head-center" style="margin-bottom:36px">
      <div class="eyebrow">{{ $t['testimonials']['eyebrow'] }}</div>
      <h2>{{ $t['testimonials']['heading'] }}</h2>
    </div>
    <div class="testi-slider">
      <button class="arrow prev" id="prev" aria-label="Previous">‹</button>
      <div class="testis" id="testis">
        @foreach($t['testimonials']['items'] as $idx => $item)
          <div class="testi {{ $idx === 0 ? 'show' : '' }}">
            <div class="q">“</div>
            <p>{{ $item['quote'] }}</p>
            <div class="who">
              <div><b>{{ $item['author'] }}</b><small>{{ $item['location'] }}</small></div>
              <div class="avatar">{{ $item['initial'] ?? substr(preg_replace('/[^A-Za-z]/', '', $item['author']), 0, 1) }}</div>
            </div>
          </div>
        @endforeach
      </div>
      <button class="arrow next" id="next" aria-label="Next">›</button>
    </div>
  </div>
</section>
