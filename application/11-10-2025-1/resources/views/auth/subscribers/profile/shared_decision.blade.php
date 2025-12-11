@extends('auth.subscribers.layouts.app')
@section('seodetails')
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
@endsection
@section('title', Auth::guard('subscriber')->user()->name.' | BLD Profile')

@section('content')
<div class="container py-4">
    <h3 class="mb-4">Decisions Shared with Me</h3>

    @forelse($sharedDecisions as $share)
    <div class="card mb-3 shadow-sm">
        <div class="card-body">
            <h5 class="card-title">Decision #{{ $share->decision->id ?? 'N/A' }}</h5>
            <p class="card-text">{{ Str::limit(strip_tags($share->decision->judgment), 150, '...') }}</p>

            <p class="mb-1">
                <strong>Shared by:</strong> {{ $share->sender->name }}
            </p>
            <p class="text-muted mb-2">
                <small>Shared on {{ $share->created_at->format('M d, Y H:i') }}</small>
            </p>


            <a href="{{ route('subscriber.sharedDecision', Crypt::encrypt($share->id)) }}" class="btn btn-primary btn-sm">
                View Decision
            </a>
        </div>
    </div>
    @empty
    <p>No decisions have been shared with you yet.</p>
    @endforelse
</div>
@endsection