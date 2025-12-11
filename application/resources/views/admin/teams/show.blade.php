@extends('admin.layouts.app')
@section('title', 'Add New Team')

@section('content')
<div class="app-wrapper">
    @include('admin.layouts.sidebar')

    <main class="app-main">
        <div class="app-content-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <h3 class="mb-0">Team Details</h3>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-end">
                            <li class="breadcrumb-item">
                                <a href="{{ route('teams.index') }}" class="btn btn-primary btn-sm">Team List</a>
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
                        <h5 class="mb-0">Team Information</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-striped">
                            <tbody>
                                <tr>
                                    <th scope="row" style="width: 30%;">Name</th>
                                    <td>{{ $team->name }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Designation</th>
                                    <td>{{ $team->designation }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Email</th>
                                    <td>{{ $team->email ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Facebook</th>
                                    <td>
                                        @if($team->facebook)
                                        <a href="{{ $team->facebook }}" target="_blank">{{ $team->facebook }}</a>
                                        @else
                                        -
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">LinkedIn</th>
                                    <td>
                                        @if($team->linkedin)
                                        <a href="{{ $team->linkedin }}" target="_blank">{{ $team->linkedin }}</a>
                                        @else
                                        -
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">Bio</th>
                                    <td>{!! nl2br(e($team->bio)) !!}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Status</th>
                                    <td>{!! $team->status
                                        ? '<span class="badge text-bg-success">Active</span>'
                                        : '<span class="badge text-bg-danger">Inactive</span>' !!}
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">Created At</th>
                                    <td>{{ $team->created_at->format('d M Y h:i A') }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Updated At</th>
                                    <td>{{ $team->updated_at->format('d M Y h:i A') }}</td>
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