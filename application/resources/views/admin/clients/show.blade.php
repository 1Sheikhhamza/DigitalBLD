@extends('admin.layouts.app')
@section('title', 'Add New Client')

@section('content')
<div class="app-wrapper">
    @include('admin.layouts.sidebar')

    <main class="app-main">
        <div class="app-content-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <h3 class="mb-0">Client Details</h3>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-end">
                            <li class="breadcrumb-item">
                                <a href="{{ route('clients.index') }}" class="btn btn-primary btn-sm">Client List</a>
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
                        <h5 class="mb-0">Client Information</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-striped">
                            <tbody>
                                <tr>
                                    <th>Name</th>
                                    <td>{{ $client->name }}</td>
                                </tr>
                                <tr>
                                    <th>Logo</th>
                                    <td>
                                        @if($client->logo)
                                        <img src="{{ asset('storage/' . $client->logo) }}" alt="Logo" style="max-height: 60px;">
                                        @else
                                        N/A
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Testimonial</th>
                                    <td>{{ $client->testimonial ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Website</th>
                                    <td>
                                        @if($client->website)
                                        <a href="{{ $client->website }}" target="_blank">{{ $client->website }}</a>
                                        @else
                                        N/A
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Meta Title</th>
                                    <td>{{ $client->meta_title ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Meta Description</th>
                                    <td>{{ $client->meta_description ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Meta Keywords</th>
                                    <td>{{ $client->meta_keywords ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>{{ $client->status ? 'Active' : 'Inactive' }}</td>
                                </tr>
                                <tr>
                                    <th>Created At</th>
                                    <td>{{ $client->created_at->format('d M Y h:i A') }}</td>
                                </tr>
                                <tr>
                                    <th>Updated At</th>
                                    <td>{{ $client->updated_at->format('d M Y h:i A') }}</td>
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