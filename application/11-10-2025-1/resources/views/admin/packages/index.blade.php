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
                        <h3 class="mb-0">Package</h3>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-end">
                            <button type="button" onclick="permissions('packages','1');" style="color:#000; " class="btn btn-success btn-sm m-2"><i class="fa fa-check"></i> Approved</button>
                            <button type="button" onclick="permissions('packages','0');" style="color:#000; " class="btn btn-warning btn-sm m-2"><i class="fa fa-times"></i> Disapproved</button>
                            <button type="button" onclick="deletedata('masterdelete','packages');" style="color:#fff; " class="btn btn-danger btn-sm m-2"><i class="fa fa-times"></i> Multiple Delete</button>
                            <a href="{{ route('packages.create') }}" style="color:#fff; margin-right:20px" class="btn btn-primary btn-sm m-2"><i class="fa fa-plus"></i> Add New</a>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="app-content">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Package List</h3>
                        <div class="card-tools"> <button type="button" class="btn btn-tool" data-lte-toggle="card-collapse"> <i data-lte-icon="expand" class="bi bi-plus-lg"></i> <i data-lte-icon="collapse" class="bi bi-dash-lg"></i> </button> <button type="button" class="btn btn-tool" data-lte-toggle="card-remove"> <i class="bi bi-x-lg"></i> </button> </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <form id="form_check">
                                <table class="table table-striped table-bordered m-0">
                                    <thead>
                                        <tr>
                                            <th width="4%">
                                                <input name="checkbox" onclick='checkedAll();' type="checkbox" readonly="readonly" />
                                            </th>
                                            <th>SI</th>
                                            <th>Name</th>
                                            <th>Slug</th>
                                            <th>Price</th>
                                            <th>Currency</th>
                                            <th>Duration Type</th>
                                            <th>Duration (days)</th>
                                            <th>Featured</th>
                                            <th>Status</th>
                                            <th>Highlight Badge</th>
                                            <th>Icon</th>
                                            <th>Order</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($packages as $package)
                                        <tr id="tablerow{{ $package->id }}" class="tablerow">
                                            <td>
                                                <input type="checkbox" name="summe_code[]" id="summe_code{{ $package->id }}" value="{{ $package->id }}" />
                                            </td>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $package->name }}</td>
                                            <td>{{ $package->slug }}</td>
                                            <td>{{ number_format($package->price, 2) }}</td>
                                            <td>{{ $package->currency ?? '৳' }}</td>
                                            <td>{{ ucfirst($package->duration_type) }}</td>
                                            <td>{{ $package->duration_in_days }}</td>
                                            <td>
                                                {!! $package->is_featured ? '<span class="badge text-bg-primary">Yes</span>' : '<span class="badge text-bg-secondary">No</span>' !!}
                                            </td>
                                            <td>
                                                {!! $package->status ? '<span class="badge text-bg-success">Active</span>' : '<span class="badge text-bg-danger">Inactive</span>' !!}
                                            </td>
                                            <td>
                                                @if($package->highlight_badge)
                                                <span class="badge text-bg-warning">{{ $package->highlight_badge }}</span>
                                                @else
                                                <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($package->icon)
                                                <img src="{{ asset('storage/' . $package->icon) }}" alt="Icon" style="width: 30px; height: 30px;">
                                                @else
                                                <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>{{ $package->order }}</td>
                                            <td>
                                                <a href="{{ route('packages.edit', $package->id) }}" title="Edit Record" class="btn btn-warning btn-sm me-2">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <a href="{{ route('packages.show', $package->id) }}" title="View Details" class="btn btn-info btn-sm me-2">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                <button type="button" class="btn btn-danger btn-sm" title="Delete Record"
                                                    onclick="deleteSingle('{{ $package->id }}','masterdelete','packages')">
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
                        {{ $packages->links('pagination::bootstrap-4')}}
                    </div>
                </div>
            </div>
        </div>
    </main>
    @include('admin/layouts.footer')
</div>

@endsection