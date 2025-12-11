<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>@yield('title')</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <meta name="keywords" content="Digital Bangladesh Legal Decisions">
  <meta name="description" content="Digital Bangladesh Legal Decisions" />
  <meta name="distribution" content="global" />
  <meta name="coverage" content="Worldwide" />
  <meta name="revisit-after" content="1 day">
  <meta property="og:url" content="https://www.bldlegalized.com/" />
  <meta property="og:type" content="website" />
  <meta property="og:site_name" content="Digital Bangladesh Legal Decisions" />
  <meta property=fb:pages content="" />
  <meta property="og:title" content="Digital Bangladesh Legal Decisions" />
  <meta property="og:description" content="Digital Bangladesh Legal Decisions" />
  <meta property="og:image" content="{{ asset('assets/frontend/images/logo.png') }}" />
  <meta name="robots" content="index, follow">
  <meta name="Googlebot" content="index, follow" />
  <meta property="og:locale" content="bn_BD" />
  <meta property="og:locale:alternate" content="en_US" />
  <meta property="og:locale:alternate" content="en_GB" />
  <meta property="og:locale:alternate" content="fr_FR" />
  <meta property="og:locale:alternate" content="en_IN" />
  <meta name="twitter:card" content="summary">
  <meta name="twitter:site" content="@Digital Bangladesh Legal Decisions">
  <meta name="twitter:title" content="Digital Bangladesh Legal Decisions">
  <meta name="twitter:description" content="Digital Bangladesh Legal Decisions">
  <meta name="twitter:image:src" content="{{ asset('assets/frontend/images/logo.png') }}">
  <link rel="alternate" href="https://www.bldlegalized.com" hreflang="en-us" />
  <link rel="canonical" href="https://www.bldlegalized.com/" />


  <link rel="shortcut icon" href="{{ url('assets/img/favicon.png') }}">
  <link rel="icon" type="image/png" href="{{ url('assets/img/favicon.png') }}" sizes="192x192">
  <link rel="apple-touch-icon" sizes="180x180" href="{{ url('assets/img/apple-touch-icon.png') }}">
  <link href="https://fonts.googleapis.com/" rel="preconnect">
  <link href="https://fonts.gstatic.com/" rel="preconnect" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&amp;family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&amp;family=Nunito:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&amp;display=swap"
    rel="stylesheet">
  <link href="{{ asset('frontend/assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('frontend/assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
  <link href="{{ asset('frontend/assets/vendor/aos/aos.css') }}" rel="stylesheet">
  <link href="{{ asset('frontend/assets/vendor/glightbox/css/glightbox.min.css') }}" rel="stylesheet">
  <link href="{{ asset('frontend/assets/vendor/swiper/swiper-bundle.min.css') }}" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <link href="{{ asset('frontend/assets/css/main.css') }}" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('frontend/assets/css/custom/custom.css') }}">
  <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
  <link rel="stylesheet" href="{{ asset('assets/css/fonts.css') }}">
</head>

<body class="index-page">
  <header id="header" class="header d-flex align-items-center sticky-top">
    <div class="container position-relative d-flex align-items-center">

      <a href="{{ route('home') }}" class="logo d-flex align-items-center me-auto">
        <img src="{{ asset('frontend/assets/img/logo.png') }}">
        <span class="sitename">Digital BLD</span>
      </a>

      <nav id="navmenu" class="navmenu">
        <ul>
          @if(isset($pages))
          @foreach($pages as $page)
          <li class="{{ request()->is($page->slug) ? 'active' : '' }} {{ $page->children && $page->children->count() ? 'dropdown' : '' }}">
            <a href="{{ getPageUrl($page) }}">
              <span>{{ $page->title }}</span>
              @if($page->children && $page->children->count())
              <i class="bi bi-chevron-down toggle-dropdown"></i>
              @endif
            </a>

            @if($page->children && $page->children->count())
            <ul>
              @foreach($page->children as $child)
              <li class="{{ $child->children && $child->children->count() ? 'dropdown' : '' }}">
                <a href="{{ getPageUrl($child) }}">
                  <span>{{ $child->title }}</span>
                  @if($child->children && $child->children->count())
                  <i class="bi bi-chevron-down toggle-dropdown"></i>
                  @endif
                </a>

                @if($child->children && $child->children->count())
                <ul>
                  @foreach($child->children as $subChild)
                  <li>
                    <a href="{{ getPageUrl($subChild) }}">
                      {{ $subChild->title }}
                    </a>
                  </li>
                  @endforeach
                </ul>
                @endif
              </li>
              @endforeach
            </ul>
            @endif
          </li>
          @endforeach
          @endif

          <li>
            @auth('subscriber')
            @include('auth.subscribers.layouts._profile')
            @else
            <a href="{{ route('subscriber.login') }}" class="login-btn">Login</a>
            @endauth
          </li>

        </ul>


        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
      </nav>
    </div>
  </header>


  @yield('content')

  @section('footer')
  @include('layouts.footer')

  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i
      class="bi bi-arrow-up-short"></i></a>
  <!-- <div id="preloader"></div> -->

  <script src="{{ asset('frontend/assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('frontend/assets/vendor/php-email-form/validate.js') }}"></script>
  <script src="{{ asset('frontend/assets/vendor/aos/aos.js') }}"></script>
  <script src="{{ asset('frontend/assets/vendor/glightbox/js/glightbox.min.js') }}"></script>
  <script src="{{ asset('frontend/assets/vendor/imagesloaded/imagesloaded.pkgd.min.js') }}"></script>
  <script src="{{ asset('frontend/assets/vendor/isotope-layout/isotope.pkgd.min.js') }}"></script>
  <script src="{{ asset('frontend/assets/vendor/waypoints/noframework.waypoints.js') }}"></script>
  <script src="{{ asset('frontend/assets/vendor/swiper/swiper-bundle.min.js') }}"></script>
  <script src="{{ asset('frontend/assets/js/main.js') }}"></script>
  <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
  <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
  <script>
    $(document).ready(function() {
    /* let today = new Date();
    let eighteenYearsAgo = new Date(today.getFullYear() - 18, today.getMonth(), today.getDate());

    $(".datepicker").datepicker({
      dateFormat: "yy-mm-dd",
      changeMonth: true,
      changeYear: true,
      yearRange: "1900:" + eighteenYearsAgo.getFullYear(),
      maxDate: eighteenYearsAgo
    }); */

    $(".datepicker").datepicker({
      dateFormat: "yy-mm-dd",
      changeMonth: true,
      changeYear: true,
      yearRange: "1900:+0",
      maxDate: new Date()
      });
  });
  </script>
  @show

  @stack('scripts')

</body>

</html>