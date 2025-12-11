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
                        <h3 class="mb-0">Client Feedback</h3>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-end">
                            <button type="button" onclick="permissions('client_feedbacks','1');" style="color:#000; " class="btn btn-success btn-sm m-2"><i class="fa fa-check"></i> Approved</button>
                            <button type="button" onclick="permissions('client_feedbacks','0');" style="color:#000; " class="btn btn-warning btn-sm m-2"><i class="fa fa-times"></i> Disapproved</button>
                            <button type="button" onclick="deletedata('masterdelete','client_feedbacks');" style="color:#fff; " class="btn btn-danger btn-sm m-2"><i class="fa fa-times"></i> Multiple Delete</button>
                            <a href="{{ route('client_feedbacks.create') }}" style="color:#fff; margin-right:20px" class="btn btn-primary btn-sm m-2"><i class="fa fa-plus"></i> Add New</a>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="app-content">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Client Feedback List</h3>
                        <div class="card-tools"> <button type="button" class="btn btn-tool" data-lte-toggle="card-collapse"> <i data-lte-icon="expand" class="bi bi-plus-lg"></i> <i data-lte-icon="collapse" class="bi bi-dash-lg"></i> </button> <button type="button" class="btn btn-tool" data-lte-toggle="card-remove"> <i class="bi bi-x-lg"></i> </button> </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <form id="form_check">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>SI</th>
                                            <th>Client Name</th>
                                            <th>Company</th>
                                            <th>Position</th>
                                            <th>Photo</th>
                                            <th>Rating</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($clientFeedbacks as $feedback)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $feedback->client_name }}</td>
                                            <td>{{ $feedback->company ?? '-' }}</td>
                                            <td>{{ $feedback->client_position ?? '-' }}</td>
                                            <td>
                                                @if (!empty($feedback->client_photo))
                                                <div class="mt-2">
                                                    <img src="{{ asset('uploads/feedback/image/' . $feedback->client_photo) }}" alt="Website Banner" style="max-height: 100px;">
                                                </div>
                                                @endif
                                            </td>
                                            <td>{{ $feedback->rating }}/5</td>
                                            <td>
                                                {!! $feedback->status
                                                ? '<span class="badge text-bg-success">Active</span>'
                                                : '<span class="badge text-bg-danger">Inactive</span>' !!}
                                            </td>
                                            <td>
                                                <a href="{{ route('client_feedbacks.edit', $feedback->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                                <a href="{{ route('client_feedbacks.show', $feedback->id) }}" class="btn btn-info btn-sm">View</a>
                                                <form action="{{ route('client_feedbacks.destroy', $feedback->id) }}" method="POST" class="d-inline">
                                                    @csrf @method('DELETE')
                                                    <button onclick="return confirm('Delete this feedback?')" class="btn btn-danger btn-sm">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>




                            </form>
                        </div>
                    </div>
                    <div class="card-footer clearfix">
                        {{ $clientFeedbacks->links('pagination::bootstrap-4')}}
                    </div>
                </div>
            </div>
        </div>
    </main>
    @include('admin/layouts.footer')
</div>

@endsection