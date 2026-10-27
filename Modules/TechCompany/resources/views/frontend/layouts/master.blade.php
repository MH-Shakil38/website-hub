{{-- TechCompany Module — Standalone Frontend Layout (converted from agency-template, static) --}}
<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="theme-color" content="#000000">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600;9..40,700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ $asset('css/style.css') }}">

<title>@yield('title', 'YourCompany')</title>
<meta name="description" content="@yield('meta_description', 'YourCompany builds websites, e-commerce, POS, ERP, HRM, hospital and accounting software.')">
@stack('styles')
</head>
<body>

<header class="header">
  <div class="container nav">
    <a href="{{ $url('index') }}"{!! $active('index') !!} class="logo"><span class="logo-mark"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m12 2 10 5-10 5L2 7l10-5z"/><path d="m2 12 10 5 10-5M2 17l10 5 10-5"/></svg></span>YourCompany</a>
    <ul class="nav-links">
      <li><a href="{{ $url('index') }}"{!! $active('index') !!}>Home</a></li>
      <li><a href="{{ $url('about') }}"{!! $active('about') !!}>About</a></li>
      <li class="has-drop"><button aria-haspopup="true">Solutions <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg></button><div class="dropdown"><a href="{{ $url('service-ecommerce') }}"><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="9" cy="20" r="1.5"/><circle cx="18" cy="20" r="1.5"/><path d="M2 3h3l2.7 12.4a2 2 0 0 0 2 1.6h7.7a2 2 0 0 0 2-1.5L21 8H6"/></svg></span><span><b>E-commerce</b><small>Online Store</small></span></a><a href="{{ $url('service-pos') }}"><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="18" height="12" rx="2"/><path d="M7 21h10M12 15v6M7 8h4M7 11h7"/></svg></span><span><b>POS System</b><small>Retail & Restaurant</small></span></a><a href="{{ $url('service-real-estate') }}"><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 21h18M5 21V7l7-4 7 4v14"/><path d="M9 9h1M14 9h1M9 13h1M14 13h1M10 21v-4h4v4"/></svg></span><span><b>Real Estate</b><small>Property & Developers</small></span></a><a href="{{ $url('service-interior-design') }}"><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 11V8a3 3 0 0 1 3-3h10a3 3 0 0 1 3 3v3"/><path d="M2 13a2 2 0 0 1 4 0v2h12v-2a2 2 0 0 1 4 0v5H2z"/><path d="M5 18v2M19 18v2"/></svg></span><span><b>Interior Design</b><small>Design Studios</small></span></a><a href="{{ $url('service-hospital') }}"><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="3"/><path d="M12 8v8M8 12h8"/></svg></span><span><b>Hospital / Clinic</b><small>Healthcare</small></span></a><a href="{{ $url('service-hrm') }}"><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="9" cy="8" r="3.5"/><path d="M2 20c0-3.5 3-6 7-6s7 2.5 7 6"/><path d="M16 4.5a3.5 3.5 0 0 1 0 7M22 20c0-2.7-1.7-4.8-4.3-5.6"/></svg></span><span><b>HRM & Payroll</b><small>People Management</small></span></a><a href="{{ $url('service-manufacturing') }}"><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 21V10l6 4V10l6 4V6l8 4v11z"/><path d="M6 17h2M11 17h2M16 17h2"/></svg></span><span><b>Manufacturing</b><small>Factory & Production</small></span></a><a href="{{ $url('service-accounting') }}"><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="5" y="2" width="14" height="20" rx="2"/><path d="M8 6h8M8 11h.01M12 11h.01M16 11h.01M8 15h.01M12 15h.01M16 15h.01M8 18h.01M12 18h4"/></svg></span><span><b>Accounting</b><small>Finance</small></span></a><a href="{{ $url('service-education') }}"><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m2 9 10-5 10 5-10 5z"/><path d="M6 11v5c0 1.5 3 3 6 3s6-1.5 6-3v-5M22 9v6"/></svg></span><span><b>School / Education</b><small>Institutions</small></span></a><a href="{{ $url('service-custom-development') }}"><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m8 6-6 6 6 6M16 6l6 6-6 6M14 4l-4 16"/></svg></span><span><b>Custom Software</b><small>Built for You</small></span></a></div></li>
      <li><a href="{{ $url('portfolio') }}"{!! $active('portfolio') !!}>Portfolio</a></li>
      <li><a href="{{ $url('pricing') }}"{!! $active('pricing') !!}>Pricing</a></li>
      <li><a href="{{ $url('faq') }}"{!! $active('faq') !!}>FAQ</a></li>
      <li><a href="{{ $url('contact') }}"{!! $active('contact') !!}>Contact</a></li>
    </ul>
    <div class="nav-cta">
      <a href="{{ $url('contact') }}"{!! $active('contact') !!} class="btn btn-primary">Get a Quote</a>
      <button class="burger" aria-label="Open menu" aria-expanded="false"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16"/></svg></button>
    </div>
  </div>
</header>
<nav class="mobile-menu" aria-label="Mobile">
  <a href="{{ $url('index') }}"{!! $active('index') !!}>Home</a><a href="{{ $url('about') }}"{!! $active('about') !!}>About</a><a href="{{ $url('services') }}"{!! $active('services') !!}>Solutions</a>
  <div class="sub"><a href="{{ $url('service-ecommerce') }}">E-commerce</a><a href="{{ $url('service-pos') }}">POS System</a><a href="{{ $url('service-real-estate') }}">Real Estate</a><a href="{{ $url('service-interior-design') }}">Interior Design</a><a href="{{ $url('service-hospital') }}">Hospital / Clinic</a><a href="{{ $url('service-hrm') }}">HRM & Payroll</a><a href="{{ $url('service-manufacturing') }}">Manufacturing</a><a href="{{ $url('service-accounting') }}">Accounting</a><a href="{{ $url('service-education') }}">School / Education</a><a href="{{ $url('service-custom-development') }}">Custom Software</a></div>
  <a href="{{ $url('portfolio') }}"{!! $active('portfolio') !!}>Portfolio</a><a href="{{ $url('pricing') }}"{!! $active('pricing') !!}>Pricing</a><a href="{{ $url('faq') }}"{!! $active('faq') !!}>FAQ</a><a href="{{ $url('contact') }}"{!! $active('contact') !!}>Contact</a>
  <a href="{{ $url('contact') }}"{!! $active('contact') !!} class="btn btn-primary">Get a Quote</a>
</nav>
@yield('content')

<footer class="footer">
  <div class="container">
    <div class="foot-grid">
      <div class="foot-about"><a href="{{ $url('index') }}" class="logo"><span class="logo-mark"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m12 2 10 5-10 5L2 7l10-5z"/><path d="m2 12 10 5 10-5M2 17l10 5 10-5"/></svg></span>YourCompany</a><p>Software & Web Solutions Studio. Websites, e-commerce, POS, ERP and custom software for growing businesses.</p>
        <div class="socials"><a href="#" aria-label="Facebook"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 8h-2a1 1 0 0 0-1 1v3h3l-.5 3H12v7H9v-7H7v-3h2V8.5A3.5 3.5 0 0 1 12.5 5H15z"/></svg></a><a href="#" aria-label="LinkedIn"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="3"/><path d="M8 10v7M8 7v.01M12 17v-4a2 2 0 0 1 4 0v4M12 10v7"/></svg></a><a href="#" aria-label="YouTube"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="5" width="20" height="14" rx="4"/><path d="m10 9 5 3-5 3z"/></svg></a><a href="#" aria-label="Instagram"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><path d="M17.5 6.5v.01"/></svg></a></div></div>
      <div><h5>Company</h5><ul><li><a href="{{ $url('about') }}">About Us</a></li><li><a href="{{ $url('services') }}">Solutions</a></li><li><a href="{{ $url('portfolio') }}">Portfolio</a></li><li><a href="{{ $url('pricing') }}">Pricing</a></li><li><a href="{{ $url('faq') }}">FAQ</a></li><li><a href="{{ $url('contact') }}">Contact</a></li></ul></div>
      <div><h5>Solutions</h5><ul><li><a href="{{ $url('service-ecommerce') }}">E-commerce</a></li><li><a href="{{ $url('service-pos') }}">POS System</a></li><li><a href="{{ $url('service-real-estate') }}">Real Estate</a></li><li><a href="{{ $url('service-interior-design') }}">Interior Design</a></li><li><a href="{{ $url('service-hospital') }}">Hospital / Clinic</a></li><li><a href="{{ $url('service-hrm') }}">HRM & Payroll</a></li><li><a href="{{ $url('services') }}">View all →</a></li></ul></div>
      <div><h5>Stay Updated</h5>
        <form class="subscribe" data-demo><input type="email" placeholder="Enter your email" required aria-label="Email"><button class="btn btn-primary">Subscribe</button></form>
        <ul style="margin-top:22px"><li><a href="mailto:hello@yourcompany.com">hello@yourcompany.com</a></li><li><a href="tel:+8801XXX-XXXXXX">+880 1XXX-XXXXXX</a></li><li><a href="{{ $url('contact') }}">Gulshan, Dhaka</a></li></ul></div>
    </div>
    <div class="foot-bottom"><span>© <span data-year>2026</span> YourCompany. All rights reserved.</span><div><a href="#">Terms &amp; Conditions</a><a href="#">Privacy Policy</a></div></div>
  </div>
</footer>
<button class="to-top" aria-label="Back to top"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 19V5M5 12l7-7 7 7"/></svg></button>
<script src="{{ $asset('js/main.js') }}"></script>
@stack('scripts')
</body>
</html>
