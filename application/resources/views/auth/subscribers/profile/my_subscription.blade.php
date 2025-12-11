@extends('auth.subscribers.layouts.app')
@section('seodetails')
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
@endsection
@section('title', Auth::guard('subscriber')->user()->name.' | BLD Profile')

@section('content')
<style>
                .subscription-table {
                    font-size: 0.85rem;
                    /* Slightly smaller font */
                    border-radius: 10px;
                    overflow: hidden;
                    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
                }

                .subscription-table thead {
                    background: linear-gradient(90deg, #4facfe, #00f2fe);
                    color: white;
                }

                .subscription-table th,
                .subscription-table td {
                    vertical-align: middle;
                    padding: 8px 12px;
                }

                .status-badge {
                    font-size: 0.75rem;
                    padding: 4px 8px;
                    border-radius: 6px;
                }

                .status-active {
                    background: #d4edda;
                    color: #155724;
                }

                .status-expired {
                    background: #f8d7da;
                    color: #721c24;
                }

                .status-pending {
                    background: #fff3cd;
                    color: #856404;
                }
            </style>

<div class="container mt-5 mb-5">
    <div class="row g-4">
        <!-- Main Content -->
        <main class="col-lg-8">
            <div class="search-summary">
                <h4 class="fw-bold">My Subscription</h4>
            </div>


            

            <div class="table-responsive">
                <table class="table table-hover subscription-table">
                    <thead>
                        <tr>
                            <th><i class="bi bi-hash"></i> SI</th>
                            <th><i class="bi bi-box"></i> Package</th>
                            <th><i class="bi bi-calendar"></i> Start Date</th>
                            <th><i class="bi bi-calendar-x"></i> End Date</th>
                            <th><i class="bi bi-cash-stack"></i> Amount</th>
                            <th><i class="bi bi-check-circle"></i> Payment Method</th>
                            <th><i class="bi bi-check-circle"></i> Status</th>
                            <th><i class="bi bi-clock-history"></i> Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($subscriptions as $subscription)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $subscription->package->name ?? 'N/A' }}</td>
                            <td>{{ \Carbon\Carbon::parse($subscription->start_date)->format('d M, Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($subscription->expire_date)->format('d M, Y') }}</td>
                            <td>BDT {{ number_format($subscription->fee, 2) }}</td>
                            <td>{{ $subscription->payment_method ?? 'N/A' }}</td>
                            <td>
                                @if($subscription->status == 1)
                                <span class="badge bg-success">Active</span>
                                @else
                                <span class="badge bg-secondary">Expired</span>
                                @endif
                            </td>
                            <td>{{ $subscription->created_at->format('d M, Y') }}</td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center">No subscriptions found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </main>

        @include('auth.subscribers.profile._my_account_nav')
    </div>
</div>




@endsection