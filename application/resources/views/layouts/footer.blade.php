<footer id="footer" class="footer dark-background">
  <div class="container footer-top">
    <div class="row">

      {{-- 🔹 Logo Section --}}
      <div class="col-lg-3 col-md-6 mt-3">
        <a href="{{ route('home') }}" class="text-decoration-none">
          @php
            $companyLogo = $footerData['company_logo'] ?? null;

            $logoPath = $companyLogo && file_exists(public_path('uploads/configuration/' . $companyLogo))
              ? asset('uploads/configuration/' . $companyLogo)
              : asset('frontend/assets/img/logo.png');
          @endphp


          <img src="{{ $logoPath }}" alt="Company Logo"
            style="width:120px;height:120px;margin-bottom:15px;display:block;">


          <span class="sitename text-start w-100" style="margin-left: 8px !important;">
            Digital BLD
          </span>
        </a>


        @php
          $sslImage = $footerData['ssl_image'] ?? null;

          $sslPath = $sslImage && file_exists(public_path('uploads/configuration/' . $sslImage))
            ? asset('uploads/configuration/' . $sslImage)
            : asset('frontend/assets/img/ssl_logo.png');
        @endphp

        <div class="col-sm-12 mt-5" style="margin-left: 7px !important;">
          <p class="p-0 m-0">Verified by</p>
          <img src="{{ $sslPath }}" alt="SSL Commerz" style="width:150px;height:auto;">
        </div>
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


  </div>

  {{-- 🔹 Copyright --}}
  <div class="container copyright text-center mt-4">
    @php
      $copyrightText = $footerData['copy_right_text'] ?? '';
      $companyName = 'Legalized Education Bangladesh Ltd';
      $styledLink = '<a href="https://legalizedbd.com/" target="_blank" class="text-decoration-none" style="color: #FFFF00 !important;">Legalized Education Bangladesh Ltd.</a>';

      if (str_contains($copyrightText, $companyName)) {
        $output = str_replace($companyName, $styledLink, $copyrightText);
      } else {
        $output = $copyrightText . '<br>Designed & Developed by ' . $styledLink;
      }
    @endphp
    {!! $output !!}
  </div>
</footer>