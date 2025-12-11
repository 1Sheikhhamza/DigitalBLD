    @extends('admin.layouts.app')
    @section('title', 'Dashboard')
    @section('content')
    <div class="app-wrapper">
        @include('admin.layouts.sidebar')
        <main class="app-main">
            <div class="app-content-header">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <h3 class="mb-0">Dashboard</h3>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end">
                                <li class="breadcrumb-item"><a href="#">Home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="app-content">
                <div class="container-fluid">

                    {{-- Info Boxes --}}
                    <div class="row">
                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon text-bg-primary shadow-sm"><i class="bi bi-people-fill"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Subscriber</span>
                                    <span class="info-box-number">{{ $subscriberCount }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon text-bg-danger shadow-sm"><i class="bi bi-cash-stack"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Payment</span>
                                    <span class="info-box-number">{{ number_format($totalPayments, 2) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon text-bg-success shadow-sm"><i class="bi bi-boxes"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Packages</span>
                                    <span class="info-box-number">{{ $packageCount }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon text-bg-warning shadow-sm"><i class="bi bi-journals"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Volume</span>
                                    <span class="info-box-number">{{ $volumeCount }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon text-bg-secondary shadow-sm"><i class="bi bi-journal-text"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Legal Decisions</span>
                                    <span class="info-box-number">{{ $legalDecisionCount }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon text-bg-secondary shadow-sm"><i class="bi bi-journal-text"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Appellate Division</span>
                                    <span class="info-box-number">{{ $appellateDivisionCount }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon text-bg-secondary shadow-sm"><i class="bi bi-journal-text"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">High Court Division</span>
                                    <span class="info-box-number">{{ $highCourtDivisionCount }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon text-bg-info shadow-sm"><i class="bi bi-card-checklist"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Subscription</span>
                                    <span class="info-box-number">{{ $subscriptionCount }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon text-bg-dark shadow-sm"><i class="bi bi-gear-fill"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Service</span>
                                    <span class="info-box-number">{{ $serviceCount }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon text-bg-muted shadow-sm"><i class="bi bi-people"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Team Member</span>
                                    <span class="info-box-number">{{ $teamMemberCount }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- New Card: Client Feedback -->
                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon text-bg-success shadow-sm"><i class="bi bi-chat-dots"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Client Feedback</span>
                                    <span class="info-box-number">{{ $clientFeedbackCount }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- New Card: Users -->
                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon text-bg-primary shadow-sm"><i class="bi bi-people-fill"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Users</span>
                                    <span class="info-box-number">{{ $userCount }}</span>
                                </div>
                            </div>
                        </div>
                    </div>



                    {{-- Monthly Report --}}
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title">Monthly Subscription Report</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="monthlyReportChart" width="600" height="300"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Latest Subscribers Table --}}
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Latest Subscriber</h3>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-lte-toggle="card-collapse">
                                            <i data-lte-icon="expand" class="bi bi-plus-lg"></i>
                                            <i data-lte-icon="collapse" class="bi bi-dash-lg"></i>
                                        </button>
                                        <button type="button" class="btn btn-tool" data-lte-toggle="card-remove">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table m-0">
                                            <thead>
                                                <tr>
                                                    <th>SI</th>
                                                    <th>Subscriber Name</th>
                                                    <th>Subscriber Contact</th>
                                                    <th>Subscription Time</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($subscribers as $subscriber)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $subscriber->name }}</td>
                                                    <td>{{ $subscriber->mobile }}</td>
                                                    <td>{{ $subscriber->created_at->format('d M, Y') }}</td>
                                                    <td><span class="badge text-bg-success">Active</span></td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="card-footer clearfix">
                                    <a href="{{ route('subscribers.index') }}" class="btn btn-sm btn-secondary float-end">View All Subscriber</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Latest Subscriptions</h3>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-lte-toggle="card-collapse">
                                            <i data-lte-icon="expand" class="bi bi-plus-lg"></i>
                                            <i data-lte-icon="collapse" class="bi bi-dash-lg"></i>
                                        </button>
                                        <button type="button" class="btn btn-tool" data-lte-toggle="card-remove">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table m-0">
                                            <thead>
                                                <tr>
                                                    <th>SI</th>
                                                    <th>Subscriber Name</th>
                                                    <th>Subscription Fee</th>
                                                    <th>Subscription Date</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($latestSubscriptions as $subscription)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $subscription->subscriber->name ?? 'N/A' }}</td>
                                                    <td>{{ number_format($subscription->fee, 2) }}</td>
                                                    <td>{{ $subscription->created_at->format('d M, Y') }}</td>
                                                    <td><span class="badge text-bg-success">Active</span></td>
                                                </tr>
                                                @endforeach
                                                @if($latestSubscriptions->isEmpty())
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted">No subscriptions found.</td>
                                                </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="card-footer clearfix">
                                    <a href="{{ route('subscriptions.index') }}" class="btn btn-sm btn-secondary float-end">View All Subscriptions</a>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </main>
        @include('admin/layouts.footer')
    </div>

    @endsection



    @section('page-script')
    <script>
        const ctx = document.getElementById('monthlyReportChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($months),
                datasets: [{
                        label: 'Monthly Subscriptions',
                        data: @json($subscriberTotals),
                        backgroundColor: 'rgba(54, 162, 235, 0.6)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Monthly Payments',
                        data: @json($paymentTotals),
                        backgroundColor: 'rgba(0, 192, 50, 0.6)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString(); // Format with commas
                            }
                        }
                    }
                }
            }
        });
    </script>

    @endsection