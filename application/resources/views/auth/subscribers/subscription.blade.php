@extends('auth.subscribers.layouts.app')
@section('seodetails')
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
@endsection
@section('title', "Welcome to Nexus Development")

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
    <?php
      $originalPrice = $package->price;
      $discount = $package->discount;
      $type = $package->discount_type;

      $finalPrice = $originalPrice; // default

      if (!empty($discount)) {
        if ($type === '%') {
          $finalPrice = $originalPrice - ($originalPrice * ($discount / 100));
        } elseif ($type === 'Tk') {
          $finalPrice = $originalPrice - $discount;
        }
      }
      ?>
    <main class="container py-5">
        <!-- Investment Details Section -->
        <section id="investment-details" class="mb-5">
            <h2 class="section-title mt-5 mb-4">Package Details</h2>
            <div class="row">
                <div class="col-lg-8 offset-2">
                    <div class="card shadow p-4">
                        <h3 class="mb-3">{{ $package->name }}</h3>
                        <p class="text-muted">{{ $package->description }}</p>

                        <h4 class="my-3">
                            <sup>{{ $package->currency ?? '৳' }}</sup>{{ $finalPrice }}
                            <span class="text-muted">
                                @php
                                $durationMap = [
                                'monthly' => '/ month',
                                'quarterly' => '/ quarter',
                                'half_yearly' => '/ half year',
                                'yearly' => '/ year',
                                ];
                                echo $durationMap[$package->duration_type] ?? '/ month';
                                @endphp
                            </span>
                        </h4>

                        <table class="table table-bordered mt-4">
                            <tbody>
                                <tr>
                                    <th scope="row">Package Name</th>
                                    <td>{{ $package->name }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Price</th>
                                    <td>{{ $package->currency ?? '৳' }}{{ $finalPrice }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Duration</th>
                                    <td>
                                        {{ ucfirst($package->duration_type) }}
                                        @if(isset($durationMap[$package->duration_type]))
                                        {{ $durationMap[$package->duration_type] }}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">Featured</th>
                                    <td>{{ $package->is_featured ? 'Yes' : 'No' }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Badge</th>
                                    <td>{{ $package->highlight_badge ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Button Text</th>
                                    <td>{{ $package->button_text ?? 'Sign up Now' }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Features</th>
                                    <td>
                                        <ul class="list-unstyled mb-0">

                                             @php $features = json_decode($package->features_mask, true) ?: []; @endphp
                                            @foreach($features as $feature)
                                            @php
                                            $isAvailable = true;
                                            $featureText = $feature;
                                            if (stripos($feature, 'na:') === 0) {
                                            $isAvailable = false;
                                            $featureText = substr($feature, 3);
                                            }
                                            @endphp
                                            <li>
                                                @if($isAvailable)
                                                <i class="bi bi-check text-success me-1"></i>
                                                @else
                                                <i class="bi bi-x text-danger me-1"></i>
                                                @endif
                                                {{ $featureText }}
                                            </li>
                                            @endforeach
                                        </ul>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>


                </div>
            </div>


        </section>

        <section id="payment-form" class="mb-5">
            <h2 class="section-title">Make Your Payment</h2>
            <div class="row justify-content-center">
                <div class="col-lg-7 col-md-9">
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    <div class="card">
                        <div class="card-header">
                            Enter Package Amount
                        </div>
                        <div class="card-body">
                            <!-- <form id="investmentForm" action="{{ route('subscriber.subscription.submit', $package->id) }}" method="POST">
                                <input type="hidden" value="{{ csrf_token() }}" name="_token" />
                                <div class="mb-4">
                                    <label for="investmentAmount" class="form-label fw-bold">Package Amount (BDT)</label>
                                    <div class="input-group">
                                        <input type="hidden" name="payment_status" value="paid">
                                        <input type="hidden" name="transaction_id" value="TX586900">
                                        <input type="hidden" name="payment_method" value="card">
                                        <input type="number" class="form-control rounded-end-pill" id="investmentAmount" readonly value="{{ $package->price }}" name="amount" placeholder="e.g., 500" min="100" required>
                                    </div>
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
                            </form> -->


                            <form id="investmentForm" action="{{ route('subscriber.subscription.submit', $package->id) }}" method="POST">
                                <input type="hidden" value="{{ csrf_token() }}" name="_token" />

                                <div class="mb-4">
                                    <label for="investmentAmount" class="form-label fw-bold">Package Amount (BDT)</label>
                                    <div class="input-group">
                                        <input type="hidden" name="payment_status" value="paid">
                                        <input type="hidden" name="transaction_id" value="TX586900">
                                        <input type="hidden" name="payment_method" value="card">
                                        <input type="number" class="form-control rounded-end-pill" id="investmentAmount" readonly value="{{ $finalPrice }}" name="amount" placeholder="e.g., 500" min="100" required>
                                    </div>
                                </div>

                                <!-- Terms & Conditions -->
                                <div class="form-check d-flex justify-content-center align-items-center gap-2 mb-3">
                                    <input class="form-check-input mt-0" type="checkbox" id="termsCheck" name="terms" value="1">
                                    <label class="form-check-label fw-semibold mb-0" for="termsCheck">
                                        I accept the <a href="{{ route('content', ['terms-conditions-of-service-tcs']) }}" target="_blank">Terms & Conditions</a>
                                    </label>
                                </div>



                                <!-- Submit button -->
                                <div class="d-flex justify-content-center">
                                    <button type="submit" id="submitBtn" class="btn btn-primary btn-lg" disabled>
                                        Proceed to Secure Payment <i class="bi bi-lock-fill ms-2"></i>
                                    </button>
                                </div>

                                <p class="payment-security-note mt-3">
                                    <i class="bi bi-shield-fill-check me-1 text-success"></i> Your payment will be processed securely via SSL.
                                    This is a simulated payment gateway. In a real application, clicking this button would redirect you to a payment provider.
                                </p>
                            </form>

                            <!-- Script to control checkbox -->
                            <script>
                                document.addEventListener("DOMContentLoaded", function() {
                                    const checkbox = document.getElementById("termsCheck");
                                    const button = document.getElementById("submitBtn");

                                    checkbox.addEventListener("change", function() {
                                        button.disabled = !this.checked;
                                    });
                                });
                            </script>

                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
    </div>
    @endsection