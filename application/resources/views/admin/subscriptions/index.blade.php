@extends('admin.layouts.app')
@section('title', 'Subscription List')
@section('content')
<div class="app-wrapper">
    @include('admin.layouts.sidebar')
    <main class="app-main">
        <div class="app-content-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <h3 class="mb-0">Subscription</h3>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-end">
                            <button type="button" onclick="permissions('subscriptions','1');" style="color:#000; " class="btn btn-success btn-sm m-2"><i class="fa fa-check"></i> Approved</button>
                            <button type="button" onclick="permissions('subscriptions','0');" style="color:#000; " class="btn btn-warning btn-sm m-2"><i class="fa fa-times"></i> Disapproved</button>
                            <button type="button" onclick="deletedata('masterdelete','subscriptions');" style="color:#fff; " class="btn btn-danger btn-sm m-2"><i class="fa fa-times"></i> Multiple Delete</button>
                            <a href="{{ route('subscriptions.create') }}" style="color:#fff; margin-right:20px" class="btn btn-primary btn-sm m-2"><i class="fa fa-plus"></i> Add New</a>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="app-content">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Subscription List</h3>
                        <div class="card-tools"> <button type="button" class="btn btn-tool" data-lte-toggle="card-collapse"> <i data-lte-icon="expand" class="bi bi-plus-lg"></i> <i data-lte-icon="collapse" class="bi bi-dash-lg"></i> </button> <button type="button" class="btn btn-tool" data-lte-toggle="card-remove"> <i class="bi bi-x-lg"></i> </button> </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <form id="form_check">
                                <table class="table m-0">
                                    <thead>
                                        <tr>
                                            <th width="4%"><input name="checkbox" onclick="checkedAll();" type="checkbox" readonly="readonly" /></th>
                                            <th>SI</th>
                                            <th>Subscriber</th>
                                            <th>Package</th>
                                            <th>Subscription Date</th>
                                            <th>Expiry Date</th>
                                            <th>Subscription Fee</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($subscriptions as $subscription)
                                        <tr id="tablerow{{ $subscription->id }}" class="tablerow">
                                            <td>
                                                <input type="checkbox" name="summe_code[]" id="summe_code{{ $subscription->id }}" value="{{ $subscription->id }}" />
                                            </td>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $subscription->subscriber?->name ?? '' }}</td>
                                            <td>{{ $subscription->package?->name ?? '' }}</td>
                                            <td>{{ \Carbon\Carbon::parse($subscription->subscription_date)->format('d M Y') }}</td>
                                            <td>{{ $subscription->expire_date ? \Carbon\Carbon::parse($subscription->expire_date)->format('d M Y') : '-' }}</td>
                                            <td>&#2547;{{ number_format($subscription->fee, 2) }}</td>
                                            <td>
                                                @switch($subscription->status)
                                                @case(1)
                                                <span class="badge text-bg-success">Active</span>
                                                @break
                                                @case(0)
                                                <span class="badge text-bg-danger">Inactive</span>
                                                @break
                                                @case(2)
                                                <span class="badge text-bg-warning">Pending</span>
                                                @break
                                                @case(3)
                                                <span class="badge text-bg-secondary">Cancelled</span>
                                                @break
                                                @default
                                                <span class="badge text-bg-light text-dark">Unknown</span>
                                                @endswitch
                                            </td>
                                            <td>
                                                <a href="{{ route('subscriptions.edit', $subscription->id) }}" title="Edit Record" class="btn btn-warning btn-sm me-2">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <a href="{{ route('subscriptions.show', $subscription->id) }}" title="View Details" class="btn btn-info btn-sm me-2">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                <button type="button" class="btn btn-danger btn-sm" title="Delete Record"
                                                    onclick="deleteSingle('{{ $subscription->id }}','masterdelete','subscriptions')">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                            </form>
                        </div>
                    </div>
                    <div class="card-footer clearfix">
                        {{ $subscriptions->links('pagination::bootstrap-4')}}
                    </div>
                </div>
            </div>
        </div>
    </main>
    @include('admin/layouts.footer')
</div>

@endsection