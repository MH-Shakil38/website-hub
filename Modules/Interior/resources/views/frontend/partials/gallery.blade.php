<!-- ===== GALLERY ===== -->
<section class="sec" id="gallery" style="padding-top:30px">
  <div class="container">
    <div class="head-center">
      <div class="eyebrow">{{ $t['gallery']['eyebrow'] }}</div>
      <h2>{{ $t['gallery']['heading_prefix'] }} <em>{{ $t['gallery']['heading_em'] }}</em>.</h2>
    </div>
    <div class="g-filters" id="gFilters">
      <button class="on" data-f="all">All</button>
      <button data-f="living">Living</button>
      <button data-f="kitchen">Kitchen</button>
      <button data-f="bedroom">Bedroom</button>
      <button data-f="bath">Bath</button>
      <button data-f="dining">Dining &amp; Decor</button>
    </div>
    <div class="gallery" id="gallery-grid"></div>
  </div>
</section>

<div class="lb" id="lb" role="dialog" aria-modal="true" aria-label="Image viewer">
  <button class="x" id="lbX" aria-label="Close">×</button>
  <button class="lp" id="lbP" aria-label="Previous image">‹</button>
  <figure><img id="lbImg" alt=""><figcaption id="lbCap"></figcaption></figure>
  <button class="ln" id="lbN" aria-label="Next image">›</button>
</div>
