<!-- ===== HEADER ===== -->
<header>
  <div class="container nav">
    <a href="#home" class="brand">
      <img src="{{ $t['header']['logo'] }}" alt="{{ $t['header']['brand_name'] }} logo">
      <div class="brand-text"><b>{{ $t['header']['brand_name'] }}</b><small>{{ $t['header']['brand_sub'] }}</small></div>
    </a>
    <ul class="menu" id="menu">
      @foreach($t['header']['menus'] as $idx => $menuItem)
        <li><a href="{{ $menuItem['url'] }}" class="{{ $idx === 0 ? 'active' : '' }}">{{ $menuItem['title'] }}</a></li>
      @endforeach
    </ul>
    <a href="{{ $t['header']['cta_url'] }}" class="btn">{{ $t['header']['cta_text'] }}</a>
    <button class="burger" id="burger" aria-label="Open menu"><span></span><span></span><span></span></button>
  </div>
</header>
