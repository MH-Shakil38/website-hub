<!-- ===== FOOTER ===== -->
<footer>
  <div class="container foot">
    <div class="foot-about">
      <div class="row">
        <a href="#home" class="brand"><img src="{{ $t['footer']['logo'] }}" alt="{{ $t['footer']['brand_name'] }} logo"><div class="brand-text"><b>{{ $t['footer']['brand_name'] }}</b><small>{{ $t['footer']['brand_sub'] }}</small></div></a>
        <p>{{ $t['footer']['about_text'] }}</p>
      </div>
      <div class="socials">
        <a href="{{ $t['footer']['instagram_url'] }}" aria-label="Instagram"><svg viewBox="0 0 24 24"><path d="M12 7a5 5 0 1 0 0 10 5 5 0 0 0 0-10zm0 8.2a3.2 3.2 0 1 1 0-6.4 3.2 3.2 0 0 1 0 6.4zM17.3 5.5a1.2 1.2 0 1 0 0 2.4 1.2 1.2 0 0 0 0-2.4zM12 2c-2.7 0-3.1 0-4.1.1C3.6 2.3 2.3 3.6 2.1 7.9 2 8.9 2 9.3 2 12s0 3.1.1 4.1c.2 4.3 1.5 5.6 5.8 5.8 1 .1 1.4.1 4.1.1s3.1 0 4.1-.1c4.3-.2 5.6-1.5 5.8-5.8.1-1 .1-1.4.1-4.1s0-3.1-.1-4.1c-.2-4.3-1.5-5.6-5.8-5.8C15.1 2 14.7 2 12 2z"/></svg></a>
        <a href="{{ $t['footer']['pinterest_url'] }}" aria-label="Pinterest"><svg viewBox="0 0 24 24"><path d="M12 2a10 10 0 0 0-3.6 19.3c-.1-.8-.2-2 0-2.9l1.2-5s-.3-.6-.3-1.5c0-1.4.8-2.5 1.8-2.5.9 0 1.3.7 1.3 1.5 0 .9-.6 2.2-.9 3.4-.2 1 .5 1.9 1.6 1.9 1.9 0 3.3-2 3.3-4.9 0-2.6-1.8-4.4-4.5-4.4-3 0-4.8 2.3-4.8 4.6 0 .9.4 1.9.8 2.4l.1.4-.3 1.1c0 .2-.2.3-.4.2-1.4-.6-2.2-2.6-2.2-4.2 0-3.4 2.5-6.6 7.2-6.6 3.8 0 6.7 2.7 6.7 6.3 0 3.8-2.4 6.8-5.7 6.8-1.1 0-2.2-.6-2.5-1.3l-.7 2.6c-.2 1-.9 2.2-1.4 2.9A10 10 0 1 0 12 2z"/></svg></a>
        <a href="{{ $t['footer']['facebook_url'] }}" aria-label="Facebook"><svg viewBox="0 0 24 24"><path d="M14 8h3V4h-3c-2.8 0-4 1.8-4 4.3V10H7v4h3v8h4v-8h3l1-4h-4V8.6c0-.4.3-.6.6-.6z"/></svg></a>
      </div>
    </div>
    <div>
      <h6>Quick links</h6>
      <ul>
        @foreach($t['header']['menus'] as $qm)
          @if($qm['url'] !== '#home')
            <li><a href="{{ $qm['url'] }}">{{ $qm['title'] }}</a></li>
          @endif
        @endforeach
      </ul>
    </div>
    <div>
      <h6>Services</h6>
      <ul>
        @foreach($t['services']['items'] as $sv)
          <li>{{ $sv['title'] }}</li>
        @endforeach
      </ul>
    </div>
    <div>
      <h6>Let’s connect</h6>
      <ul>
        <li><a href="tel:{{ $t['footer']['phone'] }}">{{ $t['footer']['phone'] }}</a></li>
        <li><a href="{{ $t['footer']['website_url'] }}">{{ $t['footer']['website'] }}</a></li>
        <li>{{ $t['footer']['address'] }}</li>
      </ul>
    </div>
    <div class="arch ph"><img src="{{ $t['footer']['arch_image'] }}" alt="" onerror="this.style.visibility='hidden'"></div>
  </div>
  <div class="copy">{{ $t['footer']['copyright'] }}</div>
</footer>
