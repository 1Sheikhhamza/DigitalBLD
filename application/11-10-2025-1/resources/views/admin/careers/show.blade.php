@extends('admin.layouts.app')
@section('title', 'Add New Career')

@section('content')
<div class="app-wrapper">
    @include('admin.layouts.sidebar')

    <main class="app-main">
        <div class="app-content-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <h3 class="mb-0">Career Details</h3>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-end">
                            <li class="breadcrumb-item">
                                <a href="{{ route('careers.index') }}" class="btn btn-primary btn-sm">Career List</a>
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
                        <h5 class="mb-0">Career Information</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-striped">
                            <tbody>
                                <tr>
                                    <th style="width: 30%;">Job Title</th>
                                    <td>{{ $career->title }}</td>
                                </tr>
                                <tr>
                                    <th>Department</th>
                                    <td>{{ $career->department ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Job Type</th>
                                    <td>{{ ucfirst(str_replace('-', ' ', $career->job_type)) }}</td>
                                </tr>
                                <tr>
                                    <th>Job Level</th>
                                    <td>{{ ucfirst($career->job_level ?? '-') }}</td>
                                </tr>
                                <tr>
                                    <th>Vacancy</th>
                                    <td>{{ $career->vacancy ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Description</th>
                                    <td>{!! nl2br(e($career->description)) !!}</td>
                                </tr>
                                <tr>
                                    <th>Responsibilities</th>
                                    <td>{!! nl2br(e($career->responsibilities ?? '-')) !!}</td>
                                </tr>
                                <tr>
                                    <th>Requirements</th>
                                    <td>{!! nl2br(e($career->requirements ?? '-')) !!}</td>
                                </tr>
                                <tr>
                                    <th>Education</th>
                                    <td>{!! nl2br(e($career->education ?? '-')) !!}</td>
                                </tr>
                                <tr>
                                    <th>Location</th>
                                    <td>{{ $career->location ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Salary</th>
                                    <td>{{ $career->salary ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Apply Email</th>
                                    <td>{{ $career->apply_email ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Apply URL</th>
                                    <td><a href="{{ $career->apply_url }}" target="_blank">{{ $career->apply_url }}</a></td>
                                </tr>
                                <tr>
                                    <th>Deadline</th>
                                    <td>{{ $career->deadline ? \Carbon\Carbon::parse($career->deadline)->format('d M Y') : '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Published At</th>
                                    <td>{{ $career->published_at ? \Carbon\Carbon::parse($career->published_at)->format('d M Y H:i') : '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Job Status</th>
                                    <td>{{ ucfirst($career->job_status) }}</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>{!! $career->status ? '<span class="badge text-bg-success">Active</span>' : '<span class="badge text-bg-danger">Inactive</span>' !!}</td>
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