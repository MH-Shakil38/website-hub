<!-- ===== SERVICES ===== -->
@php
    $serviceIcons = [
        '<path d="M5 11V8a3 3 0 0 1 3-3h8a3 3 0 0 1 3 3v3"/><path d="M3 13a2 2 0 0 1 4 0v2h10v-2a2 2 0 0 1 4 0v4H3z"/><path d="M5 17v2M19 17v2"/>',
        '<path d="M12 21V9"/><path d="M12 13c-4 0-6-3-6-7 4 0 6 3 6 7z"/><path d="M12 11c0-4 2-7 6-7 0 4-2 7-6 7z"/>',
        '<path d="M9 3h6M10 3v3c-3 1-5 4-5 7a7 7 0 0 0 14 0c0-3-2-6-5-7V3"/><path d="M8 14c2 1 6 1 8 0"/>',
        '<path d="M4 21V9l8-6 8 6v12"/><path d="M9 21v-6h6v6"/><path d="M4 21h16"/>',
    ];
    $svcDisplayMode = $t['services']['display_mode'] ?? 'grid';
    $svcCarouselMode = $t['services']['carousel_mode'] ?? 'auto';
    $svcItems = $t['services']['items'] ?? [];
@endphp
<style>
  /* Multi-row spacing for Normal Grid mode when 5+ services exist */
  .services.services-grid-mode {
    row-gap: 46px;
  }
  /* Owl Carousel Slider styles matching Geotech theme */
  .svc-owl-wrap {
    position: relative;
    padding: 0 46px;
  }
  .svc-owl-viewport {
    overflow: hidden;
    padding-top: 32px;
    margin-top: -28px;
    padding-bottom: 8px;
  }
  .svc-owl-stage {
    display: flex;
    gap: 22px;
    transition: transform 0.55s cubic-bezier(.22, .8, .22, 1);
    will-change: transform;
  }
  .svc-owl-stage .svc {
    flex: 0 0 calc((100% - 66px) / 4);
    margin-top: 0;
  }
  .svc-owl-nav {
    position: absolute;
    top: 46%;
    transform: translateY(-50%);
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: #fff;
    border: 1px solid var(--line);
    color: var(--olive-dark);
    font-size: 18px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 5;
    transition: background 0.2s, color 0.2s, box-shadow 0.2s;
    box-shadow: 0 4px 12px rgba(59, 55, 47, 0.08);
  }
  .svc-owl-nav:hover {
    background: var(--olive);
    color: #fff;
    border-color: var(--olive);
  }
  .svc-owl-nav.prev { left: 0; }
  .svc-owl-nav.next { right: 0; }
  .svc-owl-dots {
    display: flex;
    justify-content: center;
    gap: 8px;
    margin-top: 24px;
  }
  .svc-owl-dot {
    width: 9px;
    height: 9px;
    border-radius: 50%;
    border: 1px solid var(--olive);
    background: transparent;
    padding: 0;
    cursor: pointer;
    transition: all 0.25s;
  }
  .svc-owl-dot.active {
    width: 24px;
    border-radius: 10px;
    background: var(--olive);
  }
  @media (max-width: 1024px) {
    .svc-owl-stage .svc {
      flex: 0 0 calc((100% - 22px) / 2);
    }
  }
  @media (max-width: 640px) {
    .svc-owl-wrap { padding: 0 36px; }
    .svc-owl-stage .svc {
      flex: 0 0 100%;
    }
  }
</style>
<section class="sec" id="services">
  <svg class="leaf-deco" style="right:3%;top:20px;width:110px" viewBox="0 0 100 140" fill="none" stroke="#a69c84" stroke-width="1"><path d="M20 135 C40 100 60 60 85 10"/><path d="M38 100 C20 95 12 80 14 68 C28 72 38 84 38 100Z"/><path d="M50 78 C66 74 76 62 76 50 C62 52 52 64 50 78Z"/><path d="M60 58 C44 54 38 40 40 28 C54 32 60 44 60 58Z"/><path d="M72 36 C84 32 90 22 90 12 C80 14 72 24 72 36Z"/></svg>
  <div class="container">
    <div class="head-center">
      <div class="eyebrow">{{ $t['services']['eyebrow'] }}</div>
      <h2>{{ $t['services']['heading_line1'] }}<br>{{ $t['services']['heading_line2_prefix'] }} <em>{{ $t['services']['heading_line2_em'] }}</em> {{ $t['services']['heading_line2_suffix'] }}</h2>
    </div>

    @if($svcDisplayMode === 'carousel')
      <!-- OWL CAROUSEL SLIDER MODE (Auto or Click) -->
      <div class="svc-owl-wrap" id="svcOwlCarousel" data-carousel-mode="{{ $svcCarouselMode }}">
        <button type="button" class="svc-owl-nav prev" id="svcOwlPrev" aria-label="Previous service">‹</button>
        <div class="svc-owl-viewport">
          <div class="svc-owl-stage" id="svcOwlStage">
            @foreach($svcItems as $idx => $svc)
              <div class="svc">
                <div class="icon-circle"><svg viewBox="0 0 24 24">{!! $serviceIcons[$idx % count($serviceIcons)] !!}</svg></div>
                <div class="ph"><img src="{{ $svc['image'] }}" alt="{{ $svc['title'] }}" onerror="this.style.visibility='hidden'"></div>
                <h3>{{ $svc['title'] }}</h3>
                <p>{{ $svc['description'] }}</p>
                <a href="{{ $svc['link_url'] ?? '#contact' }}" class="link">{{ $svc['link_text'] ?? 'Learn more' }}<span>→</span></a>
              </div>
            @endforeach
          </div>
        </div>
        <button type="button" class="svc-owl-nav next" id="svcOwlNext" aria-label="Next service">›</button>
        <div class="svc-owl-dots" id="svcOwlDots"></div>
      </div>

      <script>
        (function () {
          const wrap = document.getElementById('svcOwlCarousel');
          const stage = document.getElementById('svcOwlStage');
          const prevBtn = document.getElementById('svcOwlPrev');
          const nextBtn = document.getElementById('svcOwlNext');
          const dotsWrap = document.getElementById('svcOwlDots');
          if (!wrap || !stage) return;

          const mode = wrap.getAttribute('data-carousel-mode') || 'auto';
          const slides = Array.from(stage.querySelectorAll('.svc'));
          let currentIdx = 0;
          let autoTimer = null;

          function getPerView() {
            if (window.innerWidth <= 640) return 1;
            if (window.innerWidth <= 1024) return 2;
            return 4;
          }

          function getMaxIndex() {
            return Math.max(0, slides.length - getPerView());
          }

          function buildDots() {
            const maxIdx = getMaxIndex();
            dotsWrap.innerHTML = '';
            if (maxIdx <= 0) {
              prevBtn.style.display = 'none';
              nextBtn.style.display = 'none';
              return;
            }
            prevBtn.style.display = 'flex';
            nextBtn.style.display = 'flex';
            for (let i = 0; i <= maxIdx; i++) {
              const d = document.createElement('button');
              d.type = 'button';
              d.className = 'svc-owl-dot' + (i === currentIdx ? ' active' : '');
              d.setAttribute('aria-label', 'Slide ' + (i + 1));
              d.addEventListener('click', () => {
                goToSlide(i);
                restartAuto();
              });
              dotsWrap.appendChild(d);
            }
          }

          function goToSlide(idx) {
            const maxIdx = getMaxIndex();
            if (idx > maxIdx) idx = 0;
            if (idx < 0) idx = maxIdx;
            currentIdx = idx;

            if (slides.length > 0) {
              const cardWidth = slides[0].getBoundingClientRect().width;
              const gap = 22;
              stage.style.transform = `translateX(-${currentIdx * (cardWidth + gap)}px)`;
            }

            Array.from(dotsWrap.children).forEach((dot, i) => {
              dot.classList.toggle('active', i === currentIdx);
            });
          }

          function startAuto() {
            if (mode !== 'auto') return;
            stopAuto();
            autoTimer = setInterval(() => {
              if (getMaxIndex() > 0) {
                goToSlide(currentIdx + 1);
              }
            }, 3500);
          }

          function stopAuto() {
            if (autoTimer) {
              clearInterval(autoTimer);
              autoTimer = null;
            }
          }

          function restartAuto() {
            stopAuto();
            startAuto();
          }

          prevBtn.addEventListener('click', () => {
            goToSlide(currentIdx - 1);
            restartAuto();
          });

          nextBtn.addEventListener('click', () => {
            goToSlide(currentIdx + 1);
            restartAuto();
          });

          wrap.addEventListener('mouseenter', stopAuto);
          wrap.addEventListener('mouseleave', startAuto);

          window.addEventListener('resize', () => {
            if (currentIdx > getMaxIndex()) currentIdx = getMaxIndex();
            buildDots();
            goToSlide(currentIdx);
          });

          buildDots();
          goToSlide(0);
          startAuto();
        })();
      </script>
    @else
      <!-- NORMAL MULTI-ROW GRID MODE -->
      <div class="services services-grid-mode">
        @foreach($svcItems as $idx => $svc)
          <div class="svc">
            <div class="icon-circle"><svg viewBox="0 0 24 24">{!! $serviceIcons[$idx % count($serviceIcons)] !!}</svg></div>
            <div class="ph"><img src="{{ $svc['image'] }}" alt="{{ $svc['title'] }}" onerror="this.style.visibility='hidden'"></div>
            <h3>{{ $svc['title'] }}</h3>
            <p>{{ $svc['description'] }}</p>
            <a href="{{ $svc['link_url'] ?? '#contact' }}" class="link">{{ $svc['link_text'] ?? 'Learn more' }}<span>→</span></a>
          </div>
        @endforeach
      </div>
    @endif
  </div>
</section>
