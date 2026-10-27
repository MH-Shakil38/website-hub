<!-- ===== HERO ===== -->
<section class="hero" id="home">
  <div class="hero-bg ph"><img src="{{ $t['hero']['bg_image'] }}" alt="Warm living room interior" onerror="this.style.visibility='hidden'"></div>
  <div class="hero-light"></div>
  <div class="leaves" id="leaves"></div>
  <div class="container hero-content">
    <div class="inner">
      <div class="eyebrow">{{ $t['hero']['eyebrow'] }}</div>
      <h1><span class="line"><span>{{ $t['hero']['line_1'] }}</span></span><span class="line"><span>{{ $t['hero']['line_2_prefix'] }} <em>{{ $t['hero']['line_2_em'] }}</em> {{ $t['hero']['line_2_suffix'] }}</span></span><span class="line"><span><em>{{ $t['hero']['line_3_em'] }}</em>.</span></span></h1>
      <p>{{ $t['hero']['description'] }}</p>
      <div class="hero-actions">
        <a href="{{ $t['hero']['btn_primary_url'] }}" class="btn">{{ $t['hero']['btn_primary_text'] }}</a>
        <a href="{{ $t['hero']['btn_secondary_url'] }}" class="btn btn-ghost">{{ $t['hero']['btn_secondary_text'] }}</a>
      </div>
    </div>
  </div>
  <div class="hero-ring" aria-hidden="true">
    <svg viewBox="0 0 120 120"><defs><path id="ringPath" d="M60,60 m-46,0 a46,46 0 1,1 92,0 a46,46 0 1,1 -92,0"/></defs><text font-family="Jost, sans-serif" font-size="8.6" fill="#474d31"><textPath href="#ringPath" textLength="286" lengthAdjust="spacing">{{ $t['hero']['ring_text'] }}</textPath></text></svg>
    <div class="dot"><svg viewBox="0 0 24 24"><path d="M12 21V9"/><path d="M12 13c-4 0-6-3-6-7 4 0 6 3 6 7z"/><path d="M12 11c0-4 2-7 6-7 0 4-2 7-6 7z"/></svg></div>
  </div>
  <a href="#services" class="scroll-cue" aria-label="Scroll down"></a>
  <svg class="hero-wave" viewBox="0 0 1440 110" preserveAspectRatio="none"><path d="M0,70 C240,20 420,110 720,70 C1000,32 1200,95 1440,40 L1440,110 L0,110 Z" fill="{{ $t['colors']['bg'] }}"/></svg>
</section>
