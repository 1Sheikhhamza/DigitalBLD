@extends('admin.layouts.app')
@section('title', 'Add New Inquiry')

@section('content')
<div class="app-wrapper">
    @include('admin.layouts.sidebar')

    <main class="app-main">
        <div class="app-content-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <h3 class="mb-0">Inquiry Details</h3>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-end">
                            <li class="breadcrumb-item">
                                <a href="{{ route('inquiries.index') }}" class="btn btn-primary btn-sm">Inquiry List</a>
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
                        <h5 class="mb-0">Inquiry Information</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-striped">
                            <tbody>
                                <tr>
                                    <th scope="row" style="width: 30%;">Name</th>
                                    <td>{{ $inquiry->name }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Email</th>
                                    <td>{{ $inquiry->email }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Phone</th>
                                    <td>{{ $inquiry->phone ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Subject</th>
                                    <td>{{ $inquiry->subject ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Message</th>
                                    <td>{!! nl2br(e($inquiry->message)) !!}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Status</th>
                                    <td>{!! $inquiry->status
                                        ? '<span class="badge text-bg-success">Reviewed</span>'
                                        : '<span class="badge text-bg-warning">Pending</span>' !!}
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">Created At</th>
                                    <td>{{ $inquiry->created_at->format('d M Y h:i A') }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Updated At</th>
                                    <td>{{ $inquiry->updated_at->format('d M Y h:i A') }}</td>
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