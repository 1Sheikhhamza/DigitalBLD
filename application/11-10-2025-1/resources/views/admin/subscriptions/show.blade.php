@extends('admin.layouts.app')
@section('title', 'Subscription Details')

@section('content')
<div class="app-wrapper">
    @include('admin.layouts.sidebar')

    <main class="app-main">
        <div class="app-content-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <h3 class="mb-0">Subscription Details</h3>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-end">
                            <li class="breadcrumb-item">
                                <a href="{{ route('subscriptions.index') }}" class="btn btn-primary btn-sm">Subscription List</a>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="app-content py-4">
            <div class="container col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Subscription Information</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-striped">
                            <tbody>
                                <tr>
                                    <th scope="row" style="width: 30%;">Subscriber</th>
                                    <td>{{ $subscription->subscriber ? $subscription->subscriber->name : '' }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Subscriber Email</th>
                                    <td>{{ $subscription->subscriber ? $subscription->subscriber->email : '' }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Package</th>
                                    <td>{{ $subscription->package ? $subscription->package->name : '' }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Subscription Date</th>
                                    <td>{{ \Carbon\Carbon::parse($subscription->subscription_date)->format('d M Y') }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Expire Date</th>
                                    <td>{{ \Carbon\Carbon::parse($subscription->expire_date)->format('d M Y') }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Fee</th>
                                    <td>&#2547;{{ number_format($subscription->fee, 2) }} BDT</td>
                                </tr>
                                <tr>
                                    <th scope="row">Discount</th>
                                    <td>
                                        @if(!empty($subscription->discount))
                                        &#2547;{{ number_format($subscription->discount, 2) }} BDT
                                        @else
                                        0.00 BDT
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">Total Paid</th>
                                    <td>&#2547;{{ number_format($subscription->total_paid, 2) }} BDT</td>
                                </tr>
                                <tr>
                                    <th scope="row">Payment Status</th>
                                    <td>
                                        @if($subscription->payment_status == 'paid')
                                        <span class="badge bg-success">Paid</span>
                                        @else
                                        <span class="badge bg-danger">Unpaid</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">Payment Method</th>
                                    <td>{{ ucfirst($subscription->payment_method ?? 'N/A') }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Transaction ID</th>
                                    <td>{{ $subscription->transaction_id ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Created By</th>
                                    <td>{{ $subscription->createdBy ? $subscription->createdBy->name : 'System' }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Created At</th>
                                    <td>{{ $subscription->created_at ? $subscription->created_at->format('d M Y h:i A') : '' }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Updated At</th>
                                    <td>{{ $subscription->updated_at ? $subscription->updated_at->format('d M Y h:i A') : '' }}</td>
                                </tr>
                            </tbody>
                        </table>


                    </div>
                </div>
            </div>
        </div>
    </main>

    @include('admin.layouts.footer')
</div>
@endsection