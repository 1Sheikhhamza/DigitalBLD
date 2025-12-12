@extends('auth.subscribers.layouts.app')

@section('title', 'Upgrade Plan | BLD')

@section('content')
    <div class="container d-flex justify-content-center align-items-center" style="min-height: 60vh;">
        <div class="card shadow-sm border-0 text-center p-5" style="max-width: 600px; width: 100%;">
            <div class="card-body">
                <div class="mb-4">
                    <i class="bi bi-shield-lock text-warning" style="font-size: 4rem;"></i>
                </div>
                <h2 class="card-title fw-bold mb-3">Upgrade Your Package</h2>
                <p class="card-text text-muted mb-4 fs-5">
                    You do not have permission to access this feature with your current plan.
                    Please update your package to enjoy full access, including downloading and printing judgments.
                </p>
                <a href="{{ route('subscriber.mySubscription') }}" class="btn btn-primary btn-lg px-5 rounded-pill">
                    View Plans & Upgrade
                </a>
                <div class="mt-4">
                    <a href="{{ url()->previous() }}" class="text-decoration-none text-secondary">
                        <i class="bi bi-arrow-left"></i> Go Back
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection