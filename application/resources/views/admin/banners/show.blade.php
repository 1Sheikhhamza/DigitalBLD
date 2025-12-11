@extends('admin.layouts.app')
@section('title', 'Add New Banner')

@section('content')
<div class="app-wrapper">
    @include('admin.layouts.sidebar')

    <main class="app-main">
        <div class="app-content-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <h3 class="mb-0">Banner Details</h3>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-end">
                            <li class="breadcrumb-item">
                                <a href="{{ route('banners.index') }}" class="btn btn-primary btn-sm">Banner List</a>
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
                        <h5 class="mb-0">Banner Information</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-striped">
                            <tbody>
                                <tr>
                                    <th scope="row" style="width: 30%;">Name</th>
                                    <td>{{ $banner->name }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Slug</th>
                                    <td>{{ $banner->slug }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Description</th>
                                    <td>{!! nl2br(e($banner->description)) !!}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Price</th>
                                    <td>{{ number_format($banner->price, 2) }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Duration</th>
                                    <td>{{ $banner->duration }} days</td>
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
