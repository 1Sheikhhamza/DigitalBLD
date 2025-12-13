@extends('admin.layouts.app')

@section('title', 'Demo Bookings')

@section('content')
    <div class="app-wrapper">
        @include('admin.layouts.sidebar')
        <main class="app-main">
            <div class="app-content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h3 class="mb-0">Demo Bookings</h3>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Demo Bookings</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="app-content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">List of Demo Bookings</h3>
                                </div>
                                <div class="card-body table-responsive p-0">
                                    <table class="table table-hover text-nowrap">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Phone</th>
                                                <th>Date</th>
                                                <th>Time</th>
                                                <th>Location</th>
                                                <th>Details</th>
                                                <th>Created At</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($bookings as $booking)
                                                <tr>
                                                    <td>{{ $booking->id }}</td>
                                                    <td>{{ $booking->name }}</td>
                                                    <td>{{ $booking->email }}</td>
                                                    <td>{{ $booking->phone }}</td>
                                                    <td>{{ $booking->booking_date }}</td>
                                                    <td>{{ $booking->booking_time }}</td>
                                                    <td>{{ $booking->location ?? 'N/A' }}</td>
                                                    <td>{{ Str::limit($booking->details, 50) }}</td>
                                                    <td>{{ $booking->created_at->format('d M Y H:i') }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="9" class="text-center">No bookings found.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                <div class="card-footer clearfix">
                                    {{ $bookings->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        @include('admin/layouts.footer')
    </div>
@endsection