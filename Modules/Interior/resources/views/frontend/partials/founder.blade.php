<!-- ===== FOUNDER ===== -->
<section class="founder-wrap" id="about">
  <div class="container founder">
    <div class="f-photo">
      <div class="olive-blob"></div>
      <div class="photo"><img src="{{ $t['founder']['photo'] }}" alt="{{ $t['founder']['name'] }}"></div>
      <div class="clay-dot"><svg viewBox="0 0 24 24"><path d="M12 21V9"/><path d="M12 13c-4 0-6-3-6-7 4 0 6 3 6 7z"/><path d="M12 11c0-4 2-7 6-7 0 4-2 7-6 7z"/></svg></div>
    </div>
    <div class="f-text">
      <div class="eyebrow">{{ $t['founder']['eyebrow'] }}</div>
      <h2 class="h2" style="margin-top:6px">{{ $t['founder']['heading_line1'] }}<br>{{ $t['founder']['heading_line2'] }}</h2>
      <p>{{ $t['founder']['para_1'] }}</p>
      <p>{{ $t['founder']['para_2'] }}</p>
      <div class="f-sign"><b>{{ $t['founder']['name'] }}</b><small>{{ $t['founder']['designation'] }}</small></div>
      <a href="{{ $t['founder']['link_url'] }}" class="link">{{ $t['founder']['link_text'] }}<span>→</span></a>
    </div>
    <div class="f-deco ph"><img src="{{ $t['founder']['deco_image'] }}" alt="" onerror="this.style.visibility='hidden'"></div>
  </div>
</section>
