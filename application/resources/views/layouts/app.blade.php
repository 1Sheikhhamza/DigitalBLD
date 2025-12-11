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
  <style>
    .countdown-container {
      display: flex;
      align-items: center;
      /* background-color: #1a3c6b; */
      color: white;
      padding: 8px 15px;
      border-radius: 4px;
      font-family: Arial, sans-serif;
      gap: 12px;
      /* spacing between items */
    }

    .launch-text {
      font-size: 14px;
      font-weight: 600;
      white-space: nowrap;
    }

    .timer-unit {
      display: flex;
      flex-direction: column;
      align-items: center;
      line-height: 1;
    }

    .time-value {
      font-size: 20px;
      font-weight: 700;
    }

    .time-label {
      font-size: 10px;
      text-transform: uppercase;
      opacity: 0.8;
      margin-top: 2px;
    }

    .separator {
      font-size: 22px;
      font-weight: 700;
      margin: 0 2px;
      line-height: 1;
    }
  </style>
</head>

<body class="index-page">
  <header id="header" class="header d-flex align-items-center sticky-top">
    <!-- <div class="container position-relative d-flex align-items-center"> -->
    <div class="container position-relative">

      <div class="row align-items-center">
        <!-- Left (col-2) -->
        <div class="col-2 d-flex justify-content-start">
          <a href="{{ route('home') }}" class="logo d-flex align-items-center me-auto">
            <img src="{{ asset('frontend/assets/img/logo.png') }}">
            <span class="sitename">Digital BLD</span>
          </a>
        </div>

        <!-- Center (col-4) -->
        <div class="col-6 d-flex justify-content-center">
          <div id="countdownArea" class="countdown-container">
            <div class="launch-text">Launching In:</div>

            <div class="timer-unit">
              <div class="time-value" id="days">00</div>
              <div class="time-label">Days</div>
            </div>
            <div class="separator">:</div>

            <div class="timer-unit">
              <div class="time-value" id="hours">00</div>
              <div class="time-label">Hours</div>
            </div>
            <div class="separator">:</div>

            <div class="timer-unit">
              <div class="time-value" id="minutes">00</div>
              <div class="time-label">Mins</div>
            </div>
            <div class="separator">:</div>

            <div class="timer-unit">
              <div class="time-value" id="seconds">00</div>
              <div class="time-label">Secs</div>
            </div>
          </div>
        </div>

        <!-- Right (col-6) -->
        <div class="col-4 d-flex justify-content-end">
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
      </div>





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
      $(".datepicker").datepicker({
        dateFormat: "yy-mm-dd",
        changeMonth: true,
        changeYear: true,
        yearRange: "1900:+0",
        maxDate: new Date()
      });
    });
  </script>

  <script>
    // Disable right-click
    document.addEventListener('contextmenu', function(e) {
      e.preventDefault();
    });

    // Disable text selection
    document.addEventListener('selectstart', function(e) {
      e.preventDefault();
    });
    document.addEventListener('copy', function(e) {
      e.preventDefault();
    });

    // Disable drag
    document.addEventListener('dragstart', function(e) {
      e.preventDefault();
    });

    // Disable common keyboard shortcuts
    document.addEventListener('keydown', function(e) {
      // Block F12 (DevTools)
      if (e.key === "F12") {
        e.preventDefault();
      }

      // Block Ctrl+Shift+I or Cmd+Option+I
      if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.key.toLowerCase() === 'i') {
        e.preventDefault();
      }

      // Block Ctrl+S or Cmd+S (Save page)
      if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 's') {
        e.preventDefault();
      }

      // Block Ctrl+U or Cmd+U (View source)
      if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'u') {
        e.preventDefault();
      }

      // Block Ctrl+C / Ctrl+X / Ctrl+V (Copy / Cut / Paste)
      if ((e.ctrlKey || e.metaKey) && ['c', 'x', 'v', 'a', 'p'].includes(e.key.toLowerCase())) {
        e.preventDefault();
      }
    });

    document.addEventListener('DOMContentLoaded', function() {
      document.body.style.userSelect = 'none';
      document.body.style.webkitUserSelect = 'none';
      document.body.style.msUserSelect = 'none';
    });
  </script>
  <script>
    function startCountdown() {
      const target = new Date("2025-11-24T16:00:00");

      function update() {
        const now = new Date();
        const diff = target - now;

        if (diff <= 0) {
          document.getElementById("countdownArea").style.display = "none";
          return;
        }

        const days = Math.floor(diff / (1000 * 60 * 60 * 24));
        const hours = Math.floor((diff / (1000 * 60 * 60)) % 24);
        const minutes = Math.floor((diff / (1000 * 60)) % 60);
        const seconds = Math.floor((diff / 1000) % 60);

        document.getElementById("days").innerText = String(days).padStart(2, "0");
        document.getElementById("hours").innerText = String(hours).padStart(2, "0");
        document.getElementById("minutes").innerText = String(minutes).padStart(2, "0");
        document.getElementById("seconds").innerText = String(seconds).padStart(2, "0");
      }

      update();
      setInterval(update, 1000);
    }

    startCountdown();
  </script>




  @show

  @stack('scripts')

</body>

</html>