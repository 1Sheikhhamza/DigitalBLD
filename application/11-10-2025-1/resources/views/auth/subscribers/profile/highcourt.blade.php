@extends('auth.subscribers.layouts.app')
@section('title', Auth::guard('subscriber')->user()->name.' | Appellate Division')

@section('content')
<div class="container py-4">
    @include('auth.subscribers.profile.volume_nav')

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 mt-3">
        @forelse ($highCourtDecisions as $decision)
            <div class="col d-flex">

                <a class="card-item d-flex flex-column h-100 w-100 p-3 border rounded shadow-sm text-decoration-none"
                   href="{{ route('subscriber.singleDecision', [$decision->id, Crypt::encrypt('legalDecisionHighCourt')]) }}?page={{ request()->get('page') ?? 1 }}">
                    <h5 class="card-title clamp-3 mb-2">{!! html_entity_decode($decision->parties) !!}</h5>
                    <div class="text-sm text-dark clamp-1 text-center mb-2">{!! $decision->case_no !!}</div>
                    <p class="card-text clamp-4 flex-grow-1">{!! strip_tags($decision->judgment) !!}</p>
                </a>
            </div>
        @empty
            <p>No decisions found for Appellate Division.</p>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $highCourtDecisions->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
