<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title>Dashboard | BLD Services</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/custom/custom.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/custom/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/custom/calender.css') }}">

    <link rel="shortcut icon" href="{{ url('assets/img/favicon.png') }}">
    <link rel="icon" type="image/png" href="{{ url('assets/img/favicon.png') }}" sizes="192x192">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ url('assets/img/favicon.png') }}">
</head>

<body>

    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="https://placehold.co/40x40/007bff/ffffff?text=Logo" alt="Logo" class="me-2 rounded-circle">
                <span class="fs-5 fw-bold text-white">Logo</span>
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
                        {{ $greeting }}, <span class="fw-bold">{{ Auth::guard('project_owner')->user()->name }}</span>
                    </span>
                    <span class="navbar-text me-3 d-none d-md-inline">
                        Package <span class="fw-bold">Standard</span> | {{ $today }}
                    </span>
                    <i class="bi bi-bell mx-2"></i>
                    <div class="dropdown">
                        <a class="d-flex align-items-center gap-2 text-decoration-none" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle mx-2 fs-4"></i>
                            <i class="bi bi-caret-down-fill"></i>
                        </a>

                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown" style="min-width: 220px;">
                            <li class="px-3 py-2">
                                <div class="fw-medium">{{ Auth::guard('project_owner')->user()->name }}</div>
                                <div class="text-muted small">{{ Auth::guard('project_owner')->user()->email }}</div>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center" href="{{ route('project_owner.profile') }}">
                                    <i class="bi bi-person-circle me-2"></i> My Profile
                                </a>
                            </li>
                            <li>
                                <form method="POST" action="{{ route('project_owner.logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item d-flex align-items-center">
                                        <i class="bi bi-box-arrow-right me-2"></i> Sign Out
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>



                </div>

        </div>
    </nav>
    @yield('content')


    <script src="{{ asset('frontend/assets/js/jquery-3.2.1.min.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/bootstrap.min.js') }}"></script>

    @stack('scripts')

</body>

</html>