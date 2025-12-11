<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title')</title>
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> -->
    <link rel="stylesheet" href="{{ asset('frontend/assets/vendor/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/custom/custom.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/custom/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/custom/calender.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="{{asset('assets/select2/select2.min.css')}}" rel="stylesheet" type="text/css" />
    <link rel="shortcut icon" href="{{ url('assets/img/favicon.png') }}">
    <link rel="icon" type="image/png" href="{{ url('assets/img/favicon.png') }}" sizes="192x192">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ url('assets/img/favicon.png') }}">
</head>

<body>

    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand logo d-flex align-items-center" href="{{ route('subscriber.dashboard') }}">
                <img src="{{ asset('frontend/assets/img/logo.png') }}">
                <span class="sitename">Digital Bangladesh Legal Decisions</span>
            </a>
            @php
            $hour = now()->format('H');
            if ($hour < 12) {
                $greeting='Good morning' ;
                } elseif ($hour < 18) {
                $greeting='Good afternoon' ;
                } else {
                $greeting='Good evening' ;
                }
                $today=now()->format('d F, Y');
                @endphp

                <div class="d-flex align-items-center ms-auto">
                    <span class="navbar-text me-3 d-none d-sm-inline">
                        {{ $greeting }}, <span class="fw-bold">{{ Auth::guard('subscriber')->user()->name }}</span>
                    </span>
                    <span class="navbar-text me-3 d-none d-md-inline">
                        Package <span class="fw-bold">Standard</span> | {{ $today }}
                    </span>

                    <i class="bi bi-bell mx-2"></i>
                    @auth('subscriber')
                    @include('auth.subscribers.layouts._profile')
                    @endauth



                </div>

        </div>
    </nav>
    @yield('content')


    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="{{ asset('frontend/assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{asset('assets/select2/select2.min.js')}}"></script>
    <script>
        $('.select2Data').select2({
            placeholder: "Select an option",
            allowClear: true
        });
    </script>
    @stack('scripts')

</body>

</html>