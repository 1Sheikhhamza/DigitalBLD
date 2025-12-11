@extends('auth.subscribers.layouts.app')
@section('seodetails')
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
@endsection
@section('title', Auth::guard('subscriber')->user()->name.' | BLD Profile')

@section('content')

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

                    <div class="mb-3 position-relative">
                        <label for="current_password" class="form-label">Current Password *</label>
                        <input type="password" class="form-control login-password" id="current_password" name="current_password" required>
                        <span class="toggle-password" style="position: absolute; right: 10px; top: 55%; cursor: pointer;">
                            <i class="bi bi-eye"></i>
                        </span>
                        @error('current_password')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="mb-3 position-relative">
                        <label for="new_password" class="form-label">New Password *</label>
                        <input type="password" class="form-control login-password" id="new_password" name="new_password" required>
                        <span class="toggle-password" style="position: absolute; right: 10px; top: 55%; cursor: pointer;">
                            <i class="bi bi-eye"></i>
                        </span>
                        @error('new_password')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="mb-3 position-relative">
                        <label for="new_password_confirmation" class="form-label">Confirm New Password *</label>
                        <input type="password" class="form-control login-password" id="new_password_confirmation" name="new_password_confirmation" required>
                        <span class="toggle-password" style="position: absolute; right: 10px; top: 55%; cursor: pointer;">
                            <i class="bi bi-eye"></i>
                        </span>
                    </div>

                    <button type="submit" class="btn btn-primary">Update Password</button>
                </form>
            </div>
        </main>

        @include('auth.subscribers.profile._my_account_nav')
    </div>
</div>
@push('scripts')
<script>
    $(document).ready(function() {
        $(document).on('click', '.toggle-password', function() {
            const $password = $(this).siblings('.login-password');
            const $icon = $(this).find('i');

            if ($password.attr('type') === 'password') {
                $password.attr('type', 'text');
                $icon.removeClass('bi-eye').addClass('bi-eye-slash');
            } else {
                $password.attr('type', 'password');
                $icon.removeClass('bi-eye-slash').addClass('bi-eye');
            }
        });
    });
</script>
@endpush
@endsection
