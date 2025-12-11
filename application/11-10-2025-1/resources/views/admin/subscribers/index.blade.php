@extends('admin.layouts.app')
@section('title', 'Subscriber List')
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
                            <button type="button" onclick="permissions('subscribers','1');" style="color:#000; " class="btn btn-success btn-sm m-2"><i class="fa fa-check"></i> Approved</button>
                            <button type="button" onclick="permissions('subscribers','0');" style="color:#000; " class="btn btn-warning btn-sm m-2"><i class="fa fa-times"></i> Disapproved</button>
                            <button type="button" onclick="deletedata('masterdelete','subscribers');" style="color:#fff; " class="btn btn-danger btn-sm m-2"><i class="fa fa-times"></i> Multiple Delete</button>
                            <a href="{{ route('subscribers.create') }}" style="color:#fff; margin-right:20px" class="btn btn-primary btn-sm m-2"><i class="fa fa-plus"></i> Add New</a>
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
                                            <th width="4%">
                                                <input type="checkbox" onclick="checkedAll();" readonly />
                                            </th>
                                            <th>SI</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Mobile</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach($subscribers as $subscriber)
                                        <tr id="tablerow{{ $subscriber->id }}" class="tablerow">
                                            <td>
                                                <input type="checkbox" name="summe_code[]" id="summe_code{{ $subscriber->id }}" value="{{ $subscriber->id }}">
                                            </td>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $subscriber->name }}</td>
                                            <td>{{ $subscriber->email }}</td>
                                            <td>{{ $subscriber->mobile }}</td>
                                            <td>
                                                @if($subscriber->status ?? true)
                                                <span class="badge text-bg-success">Active</span>
                                                @else
                                                <span class="badge text-bg-danger">Inactive</span>
                                                @endif
                                            </td>

                                            <td>
                                                <a href="{{ route('subscribers.edit', $subscriber->id) }}" title="Edit" class="btn btn-warning btn-sm me-1">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <a href="{{ route('subscribers.show', $subscriber->id) }}" title="View" class="btn btn-info btn-sm me-1">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                <button type="button" class="btn btn-danger btn-sm" title="Delete"
                                                    onclick="deleteSingle('{{ $subscriber->id }}', 'masterdelete', 'subscribers')">
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
                        {{ $subscribers->links('pagination::bootstrap-4')}}
                    </div>
                </div>
            </div>
        </div>
    </main>
    @include('admin/layouts.footer')
</div>

@endsection