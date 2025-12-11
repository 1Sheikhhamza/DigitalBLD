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
                                <a href="{{ route('client_feedbacks.index') }}" class="btn btn-primary btn-sm">Package List</a>
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
                        <table class="table table-bordered">
                            <tr>
                                <th>Client Name</th>
                                <td>{{ $feedback->client_name }}</td>
                            </tr>
                            <tr>
                                <th>Position</th>
                                <td>{{ $feedback->client_position ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Feedback</th>
                                <td>{!! nl2br(e($feedback->feedback)) !!}</td>
                            </tr>
                            <tr>
                                <th>Company</th>
                                <td>{{ $feedback->company ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Website</th>
                                <td>{{ $feedback->website ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Rating</th>
                                <td>{{ $feedback->rating }}/5</td>
                            </tr>
                            <tr>
                                <th>Client Photo</th>
                                <td>
                                    @if($feedback->client_photo)
                                    <img src="{{ asset('storage/' . $feedback->client_photo) }}" style="max-height: 100px;" alt="Client Photo">
                                    @else
                                    <em>No photo uploaded</em>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>{!! $feedback->status ? 'Active' : 'Inactive' !!}</td>
                            </tr>
                            <tr>
                                <th>Created At</th>
                                <td>{{ $feedback->created_at->format('d M Y h:i A') }}</td>
                            </tr>
                            <tr>
                                <th>Updated At</th>
                                <td>{{ $feedback->updated_at->format('d M Y h:i A') }}</td>
                            </tr>
                        </table>



                    </div>
                </div>
            </div>
        </div>
    </main>

    @include('admin.layouts.footer')
</div>
@endsection