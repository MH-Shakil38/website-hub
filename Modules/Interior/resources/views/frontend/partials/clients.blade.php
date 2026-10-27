<!-- ===== OUR CLIENTS (ROUNDED LOGO MARQUEE) ===== -->
@php
    $staticClients = [
        [
            'name' => 'AURA LIVING',
            'sub'  => 'RESIDENCES',
            'svg'  => '<path d="M12 3L3 20h18L12 3zm0 5.2L16.8 18H7.2L12 8.2z"/><circle cx="12" cy="14.5" r="1.6"/>',
        ],
        [
            'name' => 'VERDE STUDIO',
            'sub'  => 'ARCHITECTS',
            'svg'  => '<path d="M4 20V7l8-4 8 4v13"/><path d="M9 20v-7h6v7"/><path d="M12 3v17"/>',
        ],
        [
            'name' => 'HAVEN & CO.',
            'sub'  => 'HOTELS',
            'svg'  => '<rect x="4" y="4" width="16" height="16" rx="3"/><path d="M4 12h16M12 4v16"/>',
        ],
        [
            'name' => 'TERRA NOVA',
            'sub'  => 'DEVELOPERS',
            'svg'  => '<circle cx="12" cy="12" r="8"/><path d="M4 12h16M12 4c2.5 2.5 2.5 13.5 0 16M12 4c-2.5 2.5-2.5 13.5 0 16"/>',
        ],
        [
            'name' => 'LUMINA',
            'sub'  => 'ATELIER',
            'svg'  => '<polygon points="12 2 15 9 22 12 15 15 12 22 9 15 2 12 9 9 12 2"/>',
        ],
        [
            'name' => 'CRESTLINE',
            'sub'  => 'ESTATES',
            'svg'  => '<path d="M3 19h18M5 19V9l7-5 7 5v10"/><path d="M9 19v-5a3 3 0 0 1 6 0v5"/>',
        ],
        [
            'name' => 'OAK & CLAY',
            'sub'  => 'ARTISANS',
            'svg'  => '<path d="M12 21V9"/><path d="M12 14c-4.5 0-7-3.2-7-7.5 4.5 0 7 3.2 7 7.5z"/><path d="M12 12c0-4.3 2.5-7.5 7-7.5 0 4.3-2.5 7.5-7 7.5z"/>',
        ],
        [
            'name' => 'SOLACE',
            'sub'  => 'RESORTS',
            'svg'  => '<path d="M3 17c3-2 6-2 9 0s6 2 9 0"/><path d="M5 12a7 7 0 0 1 14 0"/>',
        ],
    ];
@endphp

<style>
  .clients-inline-wrap {
    margin-bottom: 44px;
    text-align: center;
  }
  .clients-inline-label {
    font-size: 10.5px;
    letter-spacing: 0.22em;
    text-transform: uppercase;
    color: var(--clay);
    font-weight: 500;
    margin-bottom: 18px;
  }
  .clients-marquee-mask {
    position: relative;
    width: 100%;
    overflow: hidden;
    padding: 10px 0 14px;
    -webkit-mask-image: linear-gradient(to right, transparent 0%, #000 10%, #000 90%, transparent 100%);
    mask-image: linear-gradient(to right, transparent 0%, #000 10%, #000 90%, transparent 100%);
  }
  .clients-marquee-track {
    display: flex;
    align-items: center;
    gap: 26px;
    width: max-content;
    animation: clientMarqueeScroll 18s linear infinite;
    will-change: transform;
  }
  .clients-marquee-mask:hover .clients-marquee-track {
    animation-play-state: paused;
  }
  /* Rounded Circular Logo Badge */
  .client-round-item {
    flex: 0 0 auto;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    cursor: default;
    transition: transform 0.35s cubic-bezier(.22, 1, .36, 1);
  }
  .client-round-item:hover {
    transform: translateY(-6px);
  }
  .client-round-circle {
    width: 88px;
    height: 88px;
    border-radius: 50%;
    background: var(--bg);
    border: 1.5px solid var(--line);
    box-shadow: 0 8px 20px rgba(59, 55, 47, 0.06);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 10px;
    transition: all 0.35s cubic-bezier(.22, 1, .36, 1);
  }
  .client-round-item:hover .client-round-circle {
    background: var(--olive);
    border-color: var(--olive);
    box-shadow: 0 14px 28px rgba(91, 98, 64, 0.24);
    transform: scale(1.06);
  }
  .client-round-circle svg {
    width: 28px;
    height: 28px;
    stroke: var(--olive-dark);
    fill: none;
    stroke-width: 1.4;
    stroke-linecap: round;
    stroke-linejoin: round;
    transition: stroke 0.35s, transform 0.35s;
  }
  .client-round-item:hover .client-round-circle svg {
    stroke: #f8f2e9;
    transform: scale(1.08);
  }
  .client-round-circle span {
    display: block;
    font-family: var(--serif);
    font-size: 10px;
    font-weight: 600;
    letter-spacing: 0.08em;
    color: var(--olive-dark);
    margin-top: 4px;
    line-height: 1;
    transition: color 0.35s;
  }
  .client-round-item:hover .client-round-circle span {
    color: #fff;
  }
  .client-round-sub {
    font-size: 9px;
    letter-spacing: 0.16em;
    text-transform: uppercase;
    color: var(--muted);
    font-weight: 500;
  }
  @keyframes clientMarqueeScroll {
    0% { transform: translateX(0); }
    100% { transform: translateX(-50%); }
  }
</style>

<div class="clients-inline-wrap" id="clients">
  <div class="clients-inline-label">Our Trusted Clients &amp; Partners</div>
  <div class="clients-marquee-mask" aria-label="Our Valued Clients">
    <div class="clients-marquee-track">
      @for($loopPass = 0; $loopPass < 2; $loopPass++)
        @foreach($staticClients as $cl)
          <div class="client-round-item" @if($loopPass === 1) aria-hidden="true" @endif>
            <div class="client-round-circle">
              <svg viewBox="0 0 24 24">{!! $cl['svg'] !!}</svg>
              <span>{{ explode(' ', $cl['name'])[0] }}</span>
            </div>
            <small class="client-round-sub">{{ $cl['name'] }}</small>
          </div>
        @endforeach
      @endfor
    </div>
  </div>
</div>
