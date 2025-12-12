@extends('auth.subscribers.layouts.app')
@section('title', Auth::guard('subscriber')->user()->name . ' | BLD Index')

@section('content')
    <div class="container py-4">
        @include('auth.subscribers.profile.volume_nav')

        <div class="mt-4">
            <!-- Appellate Division Section -->
            @if($appellateDecisions->count() > 0)
                <div class="mb-5">
                    <h4 class="text-center fw-bold mb-3 border-bottom pb-2">Appellate Division</h4>
                    <div class="list-group">
                        @foreach($appellateDecisions as $decision)
                            <a href="{{ route('subscriber.singleDecision', [$decision->id, Crypt::encrypt('volume.index/' . $volumeData->id)]) }}"
                                class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <span class="me-2 text-muted">{{ $loop->iteration }}.</span>
                                <span class="fw-medium text-truncate text-uppercase" style="flex: 1; min-width: 0; display: block;"
                                    title="{{ $decision->parties }}">{{ $decision->parties }}</span>
                                <span class="text-muted fw-medium text-nowrap" style="margin-left: 15rem;">
                                    {{ $decision->starting_page_no }} - {{ $decision->ending_page_no }}
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- High Court Division Section -->
            @if($highCourtDecisions->count() > 0)
                <div class="mb-5">
                    <h4 class="text-center fw-bold mb-3 border-bottom pb-2">High Court Division</h4>
                    <div class="list-group">
                        @foreach($highCourtDecisions as $decision)
                            <a href="{{ route('subscriber.singleDecision', [$decision->id, Crypt::encrypt('volume.index/' . $volumeData->id)]) }}"
                                class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <span class="me-2 text-muted">{{ $loop->iteration }}.</span>
                                <span class="fw-medium text-truncate text-uppercase" style="flex: 1; min-width: 0; display: block;"
                                    title="{{ $decision->parties }}">{{ $decision->parties }}</span>
                                <span class="text-muted fw-medium text-nowrap" style="margin-left: 15rem;">
                                    {{ $decision->starting_page_no }} - {{ $decision->ending_page_no }}
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($appellateDecisions->count() == 0 && $highCourtDecisions->count() == 0)
                <div class="alert alert-info text-center">
                    No judgments found in this volume index.
                </div>
            @endif
        </div>
    </div>
@endsection