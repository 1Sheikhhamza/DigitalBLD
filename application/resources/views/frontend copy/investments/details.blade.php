@extends('layouts.app')
@section('seodetails')
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
@endsection
@section('title', "Welcome to Digital Bangladesh Legal Decisions")

@section('content')
<style>
    .hero-section {
        background: #ba163f;
        background-size: cover;
        color: white;
        padding: 10rem 0;
        text-align: center;
        margin-bottom: 3rem;
    }

    .hero-section h1 {
        font-size: 3.5rem;
        font-weight: 700;
        margin-bottom: 1rem;
        color: #fff;
    }

    .hero-section p {
        font-size: 1.2rem;
        max-width: 800px;
        margin: 0 auto 2rem auto;
        color: #fff;
    }

    .section-title {
        font-size: 2.2rem;
        font-weight: 600;
        color: #ba163f;
        margin-bottom: 2.5rem;
        text-align: center;
        position: relative;
    }

    .section-title::after {
        content: '';
        display: block;
        width: 80px;
        height: 4px;
        background-color: #ffc107;
        margin: 10px auto 0;
        border-radius: 2px;
    }

    .card {
        border-radius: 1rem;
        box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.08);
        border: none;
    }

    .card-header {
        background-color: #ba163f;
        color: white;
        font-weight: 600;
        font-size: 1.25rem;
        border-top-left-radius: 1rem;
        border-top-right-radius: 1rem;
        padding: 1.25rem;
    }

    .table-responsive {
        margin-bottom: 2rem;
    }

    .table {
        border-radius: 1rem;
        overflow: hidden;
    }

    .table thead {
        background-color: #e9ecef;
        color: #495057;
    }

    .table th,
    .table td {
        vertical-align: middle;
        padding: 1rem;
    }

    .table tbody tr:hover {
        background-color: #f0f2f5;
    }

    .form-control {
        border-radius: 0.75rem;
        padding: 0.75rem 1rem;
    }

    .btn-primary {
        border-radius: 0.75rem;
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(13, 110, 253, 0.2);
    }

    .payment-security-note {
        font-size: 0.9rem;
        color: #6c757d;
        margin-top: 1rem;
        text-align: center;
    }
</style>
</head>

<body>
    <section class="hero-section">
        <div class="container">
            <h1>Unlock Your Financial Future</h1>
            <p class="lead">Invest in high-growth projects with transparent returns and secure transactions. Start building your wealth today.</p>
            <a href="{{ route('project') }}" class="btn btn-warning btn-lg rounded-pill px-5 py-3 fw-bold">
                Invest Now <i class="bi bi-arrow-right-circle-fill ms-2"></i>
            </a>
        </div>
    </section>

    <main class="container py-5">
        <!-- Investment Details Section -->
        <section id="investment-details" class="mb-5">
            <h2 class="section-title">Investment Details</h2>
            <p class="mb-5">
                At InvestNow, we believe in empowering individuals to achieve their financial goals through strategic investments in vetted, high-potential projects. Our platform offers a seamless and secure way to diversify your portfolio and generate significant returns. We meticulously select projects across various sectors, ensuring transparency, robust risk management, and clear growth trajectories. Join us to be part of a future where your money works harder for you.
            </p>
            <div class="row g-4">
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title fw-bold text-primary"><i class="bi bi-cash-stack me-2"></i> Minimum Investment</h5>
                            <p class="card-text">Start with as little as $100 to begin your investment journey. Our flexible options cater to all subscriber levels.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title fw-bold text-primary"><i class="bi bi-graph-up me-2"></i> Projected Returns</h5>
                            <p class="card-text">Anticipate average annual returns of 12-18% based on historical project performance and market analysis.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title fw-bold text-primary"><i class="bi bi-shield-check me-2"></i> Risk Management</h5>
                            <p class="card-text">Diversified portfolio approach with thorough due diligence on all projects to mitigate potential risks.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title fw-bold text-primary"><i class="bi bi-calendar-check me-2"></i> Investment Duration</h5>
                            <p class="card-text">Flexible investment terms ranging from 6 months to 5 years, allowing you to choose what suits you best.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title fw-bold text-primary"><i class="bi bi-piggy-bank me-2"></i> Reinvestment Options</h5>
                            <p class="card-text">Option to reinvest your earnings to compound your returns and accelerate wealth growth.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title fw-bold text-primary"><i class="bi bi-wallet-fill me-2"></i> Withdrawal Policy</h5>
                            <p class="card-text">Transparent withdrawal policies with clear processing times, ensuring easy access to your funds.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Project Details Section -->
        <section id="project-details" class="mb-5">
            <h2 class="section-title">Featured Project Details</h2>
            <div class="card">
                <div class="card-header">
                    Current High-Growth Projects
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th scope="col">Project Name</th>
                                    <th scope="col">Sector</th>
                                    <th scope="col">Target Return (Annual)</th>
                                    <th scope="col">Min. Investment</th>
                                    <th scope="col">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($projects as $project)
                                @php
                                $profitRangeString = $project->profit_rang ?? '';

                                // Check if there's a dash in the string
                                if (strpos($profitRangeString, '-') !== false) {
                                // Split into two parts
                                [$min, $max] = explode('-', $profitRangeString, 2);
                                $min = trim($min);
                                $max = trim($max);
                                } else {
                                // No dash found — treat entire string as either min or max
                                $min = trim($profitRangeString);
                                $max = null;
                                }

                                $minValue = is_numeric($min) ? (float) $min : 0;
                                $maxValue = is_numeric($max) ? (float) $max : 0;

                                @endphp
                                <tr>
                                    <td>{{ $project->title }}</td>
                                    <td>{{ $minValue }}% - {{ $maxValue }}%</td>
                                    <td>&#2547;{{ $project->goal_amount }}</td>
                                    <td>&#2547;{{ $project->unit_price }}</td>
                                    <td><span class="badge bg-success text-white">{{ ucfirst($project->status) }}</span></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </main>
    </div>

    @endsection
    @section('footer')

    @parent
    @endsection