@extends('auth.subscribers.layouts.app')
@section('seodetails')
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
@endsection
@section('title', Auth::guard('subscriber')->user()->name.' | BLD Profile')

@section('content')

<style>

</style>

<div class="container mt-5 mb-5">
    <div class="row g-4">
        <!-- Main Content -->
        <main class="col-lg-8">
            <div class="search-summary">
                <h4 class="fw-bold">Change Password</h4>
            </div>

            <div class="card profile-card shadow-sm p-4">

                @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <form action="{{ route('subscriber.profile.password.update') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="current_password" class="form-label">Current Password *</label>
                    <input type="password" class="form-control" name="current_password" required>
                    @error('current_password')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="new_password" class="form-label">New Password *</label>
                    <input type="password" class="form-control" name="new_password" required>
                    @error('new_password')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="new_password_confirmation" class="form-label">Confirm New Password *</label>
                    <input type="password" class="form-control" name="new_password_confirmation" required>
                </div>

                <button type="submit" class="btn btn-primary">Update Password</button>
            </form>
            </div>
        </main>

        @include('auth.subscribers.profile._my_account_nav')
    </div>
</div>




@endsection