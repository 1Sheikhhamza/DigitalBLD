@extends('admin.layouts.app')
@section('title', 'Add New Subscription')

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