<footer id="footer" class="footer dark-background">
  <div class="container footer-top">
    <div class="row">

      {{-- 🔹 Logo Section --}}
      <div class="col-lg-3 col-md-6 mt-3">
        <a href="{{ route('home') }}" class="text-decoration-none">
          @php
          use Illuminate\Support\Facades\Storage;

          $companyLogo = $footerData['company_logo'] ?? null;
          $logoPath = $companyLogo && Storage::exists($companyLogo)
          ? asset('storage/' . $companyLogo)
          : asset('frontend/assets/img/logo.png');
          @endphp

          <img src="{{ $logoPath }}" alt="Company Logo"
            style="width:120px;height:120px;margin-left:30px;margin-bottom:15px;display:block;">


          <span class="sitename text-start w-100" style="margin-left:35px !important">
            Digital BLD
          </span>
        </a>
      </div>

      {{-- 🔹 Discover Section --}}
      <div class="col-lg-3 col-md-3 footer-links">
        <h4>{{ $footerData['discover_section'] ?? 'Discover' }}</h4>
        <ul>
          @forelse($discoverMenus as $menu)
          <li><a href="{{ getPageUrl($menu) }}">{{ $menu->title }}</a></li>
          @empty
          <li><a href="#">No Menu Found</a></li>
          @endforelse
        </ul>
      </div>

      {{-- 🔹 Quick Link Section --}}
      <div class="col-lg-3 col-md-3 footer-links">
        <h4>{{ $footerData['quick_link'] ?? 'Quick Links' }}</h4>
        <ul>
          @forelse($quickMenus as $menu)
          <li><a href="{{ getPageUrl($menu) }}">{{ $menu->title }}</a></li>
          @empty
          <li><a href="#">No Menu Found</a></li>
          @endforelse
        </ul>
      </div>

      {{-- 🔹 Contact / About Section --}}
      <div class="col-lg-3 col-md-12 footer-links">
        <div class="footer-contact">
          {!! $footerData['contact_address'] ?? '' !!}
        </div>
      </div>
    </div>

    {{-- 🔹 SSL Image --}}
    @php

    $sslImage = $footerData['ssl_image'] ?? null;
    $sslPath = $sslImage && Storage::exists($sslImage)
    ? asset('storage/' . $sslImage)
    : asset('frontend/assets/img/ssl_logo.png');
    @endphp

    <img src="{{ $sslPath }}" alt="SSL Commerz" style="width:100%;height:auto;">

  </div>

  {{-- 🔹 Copyright --}}
  <div class="container copyright text-center mt-4">
    {!! $footerData['copy_right_text'] ?? '' !!}
  </div>
</footer>