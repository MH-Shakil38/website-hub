<!-- ===== PROJECTS ===== -->
<section class="sec" id="projects" style="padding-top:20px">
  <div class="container">
    <div class="proj-head">
      <div><div class="eyebrow">{{ $t['projects']['eyebrow'] }}</div><h2 class="h2">{{ $t['projects']['heading'] }}</h2></div>
      <a href="{{ $t['projects']['view_all_url'] }}" class="link">{{ $t['projects']['view_all_text'] }}<span>→</span></a>
    </div>
    <div class="projects">
      @foreach($t['projects']['items'] as $proj)
        <div class="proj">
          <div class="ph"><img src="{{ $proj['image'] }}" alt="{{ $proj['title'] }}" onerror="this.style.visibility='hidden'"></div>
          <h4>{{ $proj['title'] }}</h4>
          <small>{{ $proj['location'] }}</small>
        </div>
      @endforeach
    </div>
  </div>
</section>
