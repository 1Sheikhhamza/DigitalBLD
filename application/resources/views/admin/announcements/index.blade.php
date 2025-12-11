@extends('admin.layouts.app')
@section('title', 'Announcements')
@section('content')
    <div class="app-wrapper">
        @include('admin.layouts.sidebar')
        <main class="app-main">
            <div class="app-content-header">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <h3 class="mb-0">Announcements</h3>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end">
                                <a href="{{ route('admin.announcements.create') }}" style="color:#fff; margin-right:20px"
                                    class="btn btn-primary btn-sm m-2"><i class="fa fa-plus"></i> Add New</a>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="app-content">
                <div class="container-fluid">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Announcement List</h3>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table m-0">
                                    <thead>
                                        <tr>
                                            <th>SI</th>
                                            <th>Message</th>
                                            <th>Target</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($announcements as $announcement)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ Str::limit($announcement->message, 50) }}</td>
                                                <td>
                                                    @if($announcement->target_type == 'all')
                                                        <span class="badge bg-info">All Subscribers</span>
                                                    @else
                                                        <span class="badge bg-secondary">User:
                                                            {{ $announcement->user->name ?? 'Unknown' }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    {!! $announcement->is_active ? '<span class="badge text-bg-success">Active</span>' : '<span class="badge text-bg-danger">Inactive</span>' !!}
                                                </td>
                                                <td>
                                                    <a href="{{ route('admin.announcements.edit', $announcement->id) }}"
                                                        title="Edit Record" class="btn btn-warning btn-sm me-2"><i
                                                            class="fa fa-edit"></i></a>
                                                    <form action="{{ route('admin.announcements.destroy', $announcement->id) }}"
                                                        method="POST" class="d-inline"
                                                        onsubmit="return confirm('Are you sure?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm"><i
                                                                class="fa fa-trash"></i></button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        @include('admin/layouts.footer')
    </div>
@endsection