<!-- ===== VIDEO GALLERY ===== -->
@php
    $videoCatLabels = [
        'walkthrough' => 'Walkthrough',
        '3d_tour' => '3D Tour',
        'client_story' => 'Client Story',
        'reel' => 'Design Reel',
    ];
@endphp
<section class="sec" id="videos" style="background:var(--cream);border-radius:42px;margin:10px 0">
  <div class="container">
    <div class="head-center">
      <div class="eyebrow">{{ $t['videos']['eyebrow'] ?? 'Film & Walkthroughs' }}</div>
      <h2>{{ $t['videos']['heading_prefix'] ?? 'Experience our spaces in' }} <em>{{ $t['videos']['heading_em'] ?? 'motion' }}</em>.</h2>
    </div>

    <div class="g-filters" id="vFilters">
      <button class="on" data-vf="all">All Films</button>
      <button data-vf="walkthrough">Walkthrough</button>
      <button data-vf="3d_tour">3D Tour</button>
      <button data-vf="client_story">Client Story</button>
    </div>

    <div class="v-grid" id="video-grid">
      @foreach($t['videos']['items'] ?? [] as $vIdx => $vid)
        <button type="button"
                class="v-card"
                data-vcat="{{ $vid['category'] ?? 'walkthrough' }}"
                data-vurl="{{ $vid['video_url'] }}"
                data-vtitle="{{ $vid['title'] }}"
                data-vlabel="{{ $videoCatLabels[$vid['category'] ?? 'walkthrough'] ?? 'Film' }}">
          <div class="v-thumb ph">
            <img src="{{ $vid['thumbnail'] }}" alt="{{ $vid['title'] }}" loading="lazy">
            <span class="v-play" aria-hidden="true">
              <svg viewBox="0 0 24 24"><polygon points="9,6 19,12 9,18" fill="#fff"/></svg>
            </span>
            @if(!empty($vid['duration']))
              <span class="v-dur">{{ $vid['duration'] }}</span>
            @endif
          </div>
          <div class="v-meta">
            <small>{{ $videoCatLabels[$vid['category'] ?? 'walkthrough'] ?? 'Walkthrough' }}</small>
            <h4>{{ $vid['title'] }}</h4>
          </div>
        </button>
      @endforeach
    </div>
  </div>
</section>

<!-- Video Cinema Lightbox Modal -->
<div class="lb" id="vLb" role="dialog" aria-modal="true" aria-label="Video player">
  <button class="x" id="vLbX" aria-label="Close video">×</button>
  <figure style="max-width:960px;width:100%">
    <div id="vPlayerWrap" style="position:relative;padding-top:56.25%;background:#000;border-radius:14px;overflow:hidden;box-shadow:0 20px 50px rgba(0,0,0,.5)"></div>
    <figcaption id="vLbCap" style="margin-top:16px"></figcaption>
  </figure>
</div>
