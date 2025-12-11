@extends('layouts.app')
@section('seodetails')
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
@endsection
@section('title', "Welcome to Digital Bangladesh Legal Decisions")

@section('content')

<!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> -->
<!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"> -->
<!-- <link href="https://fonts.googl    eapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet"> -->
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

    .investment-table {
        width: 100%;
        background-color: #fff;
    }

    .investment-table td {
        vertical-align: middle;
        padding: 10px;
    }

    .investment-table td:first-child {
        width: 40%;
        font-weight: bold;
        color: #333;
    }

    .investment-table i {
        margin-right: 6px;
        color: #28a745;
        /* Greenish icon for investment feel */
    }
</style>
</head>

<body>
    <main class="container py-5">
        <!-- Investment Details Section -->
        <section id="investment-details" class="mb-5">
            <h2 class="section-title mt-5 mb-4">Investment Details</h2>
            <div class="row">
                <div class="col-lg-6">
                    <p>{!! $project->investment_details !!}</p>
                </div>
                <div class="col-lg-6">
                    <table class="table table-bordered investment-table">
                        <tbody>
                            <tr>
                                <td><i class="icon icon-tags"></i> <strong>Business Type</strong></td>
                                <td>{{ $project->category }}</td>
                            </tr>
                            <tr>
                                <td><i class="icon icon-calendar-full"></i> <strong>Project Start Date</strong></td>
                                <td>{{ $project->start_date }}</td>
                            </tr>
                            <tr>
                                <td><i class="icon icon-calendar-full"></i> <strong>Project End Date</strong></td>
                                <td>{{ $project->end_date }}</td>
                            </tr>
                            <tr>
                                <td><i class="icon icon-user2"></i> <strong>Investment Time</strong></td>
                                <td>{{ $project->duration }}</td>
                            </tr>
                            <tr>
                                <td><i class="icon icon-invest"></i> <strong>Investment Goal</strong></td>
                                <td>&#2547;{{ $project->goal_amount }}</td>
                            </tr>
                            <tr>
                                <td><i class="icon icon-map-marker2"></i> <strong>Raised</strong></td>
                                <td>&#2547;{{ $project->raised_amount }}</td>
                            </tr>
                            <tr>
                                <td><i class="icon icon-map-marker2"></i> <strong>In Waiting</strong></td>
                                <td>&#2547;{{ $project->raised_amount }}</td>
                            </tr>
                            <tr>
                                <td><i class="icon icon-calendar-full"></i> <strong>Project Duration</strong></td>
                                <td>{{ $project->duration }}</td>
                            </tr>
                            <tr>
                                <td><i class="icon icon-tags"></i> <strong>Minimum Investment</strong></td>
                                <td>{{ $project->unit_price }}</td>
                            </tr>
                            <tr>
                                <td><i class="icon icon-user2"></i> <strong>ROI</strong></td>
                                <td>{{ $project->roi_method.' '.$project->roi.$project->roi_profit_type }}</td>
                            </tr>
                            <tr>
                                <td><i class="icon icon-invest"></i> <strong>Projected</strong></td>
                                <td>{{ $project->projected }}</td>
                            </tr>
                            <tr>
                                <td><i class="icon icon-map-marker2"></i> <strong>Status</strong></td>
                                <td>{{ $project->project_status }}</td>
                            </tr>
                        </tbody>
                    </table>

                </div>
            </div>


        </section>

        <section id="payment-form" class="mb-5">
            <h2 class="section-title">Make Your Investment</h2>
            <div class="row justify-content-center">
                <div class="col-lg-7 col-md-9">
                    <div class="card">
                        <div class="card-header">
                            Enter Investment Amount
                        </div>
                        <div class="card-body">
                            <form id="investmentForm" action="{{ route('investment.submit', $project->id) }}" method="POST">
                                {{ csrf_field() }}
                                <div class="mb-4">
                                    <label for="investmentAmount" class="form-label fw-bold">Investment Amount (BDT)</label>
                                    <div class="input-group">
                                        <span class="input-group-text rounded-start-pill">&#2547;</span>
                                        <input type="hidden" name="payment_method" value="card">
                                        <input type="hidden" name="payment_status" value="paid">
                                        <input type="hidden" name="transaction_id" value="TX586900">
                                        <input type="hidden" name="payment_method" value="card">
                                        <input type="number" class="form-control rounded-end-pill" id="investmentAmount" name="amount" placeholder="e.g., 500" min="100" required>
                                    </div>
                                    <div class="form-text mt-2">Minimum investment is &#2547;{{ $project->unit_price }}.</div>
                                </div>

                                <div class="d-flex justify-content-center">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        Proceed to Secure Payment <i class="bi bi-lock-fill ms-2"></i>
                                    </button>
                                </div>
                                <p class="payment-security-note mt-3">
                                    <i class="bi bi-shield-fill-check me-1 text-success"></i> Your payment will be processed securely via SSL.
                                    This is a simulated payment gateway. In a real application, clicking this button would redirect you to a payment provider.
                                </p>
                            </form>
                        </div>
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