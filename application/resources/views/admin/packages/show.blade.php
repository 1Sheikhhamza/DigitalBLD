@extends('admin.layouts.app')
@section('title', 'Add New Package')

@section('content')
<div class="app-wrapper">
    @include('admin.layouts.sidebar')

    <main class="app-main">
        <div class="app-content-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <h3 class="mb-0">Package Details</h3>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-end">
                            <li class="breadcrumb-item">
                                <a href="{{ route('packages.index') }}" class="btn btn-primary btn-sm">Package List</a>
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
                        <h5 class="mb-0">Package Information</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-striped">
                            <tbody>
                                <tr>
                                    <th scope="row" style="width: 30%;">Name</th>
                                    <td>{{ $package->name }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Description</th>
                                    <td>{!! nl2br(e($package->description)) !!}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Price</th>
                                    <td>{{ $package->currency }}{{ number_format($package->price, 2) }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Duration Type</th>
                                    <td>{{ ucfirst($package->duration_type) }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Duration (Days)</th>
                                    <td>{{ $package->duration_in_days }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Status</th>
                                    <td>{!! $package->status ? '<span class="badge text-bg-success">Active</span>' : '<span class="badge text-bg-danger">Inactive</span>' !!}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Featured</th>
                                    <td>{!! $package->is_featured ? '<span class="badge text-bg-primary">Yes</span>' : 'No' !!}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Features</th>
                                    <td>{!! nl2br(e($package->features)) !!}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Button Text</th>
                                    <td>{{ $package->button_text }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Highlight Badge</th>
                                    <td>{{ $package->highlight_badge ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Icon</th>
                                    <td>
                                        @if($package->icon)
                                        <img src="{{ asset($package->icon) }}" alt="Icon" style="max-height:40px;">
                                        @else
                                        -
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">Order</th>
                                    <td>{{ $package->order }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Created At</th>
                                    <td>{{ $package->created_at ? $package->created_at->format('d M Y H:i') : '-' }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Updated At</th>
                                    <td>{{ $package->updated_at ? $package->updated_at->format('d M Y H:i') : '-' }}</td>
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