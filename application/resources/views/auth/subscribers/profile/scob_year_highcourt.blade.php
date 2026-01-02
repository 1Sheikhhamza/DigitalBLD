@extends('auth.subscribers.layouts.app')
@section('title', 'SCOB ' . $year . ' - High Court Division')

@section('content')
    <div class="container py-4">
        <h4 class="mb-4">SCOB Year {{ $year }} - High Court Division</h4>

        <!-- Tabs -->
        <ul class="nav nav-tabs mb-4">
            <li class="nav-item">
                <a class="nav-link" href="{{ route('subscriber.scobYear.index', $year) }}">Index</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('subscriber.scobYear.appellate', $year) }}">Appellate Division</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="{{ route('subscriber.scobYear.highcourt', $year) }}">High Court
                    Division</a>
            </li>
        </ul>

        <!-- Cases Card Grid -->
        @if($highCourtDecisions->count() > 0)
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                @foreach($highCourtDecisions as $decision)
                    <div class="col d-flex">
                        <a class="card-item d-flex flex-column h-100 w-100 p-3 border rounded shadow-sm text-decoration-none"
                            href="{{ route('subscriber.singleDecision', [$decision->id, Crypt::encrypt('scobYearHighCourt')]) }}">
                            <h5 class="card-title text-truncate mb-2 text-dark"
                                title="{!! html_entity_decode($decision->parties) !!}">
                                {!! html_entity_decode($decision->parties) !!}
                            </h5>
                            <div class="text-sm text-muted clamp-1 text-center mb-2">{{ $decision->case_no }}</div>
                            <p class="card-text clamp-4 flex-grow-1 text-secondary small">
                                {!! strip_tags($decision->judgment) !!}
                            </p>
                            <div class="mt-2 text-end text-muted small">
                                <i class="bi bi-calendar3 me-1"></i> {{ $decision->decided_on }}
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>

            <div class="mt-4">
                {{ $highCourtDecisions->links('pagination::bootstrap-5') }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-inbox" style="font-size: 3rem; color: #6c757d;"></i>
                <p class="mt-3">No High Court Division cases found for year {{ $year }}</p>
            </div>
        @endif


        <!-- Year Selector -->
        <div class="mt-4">
            <label>Jump to year:</label>
            <select class="form-select w-auto d-inline-block ms-2"
                onchange="window.location.href='{{ route('subscriber.scobYear.highcourt', '') }}/' + this.value">
                <option value="">Select year</option>
                @foreach($allYears as $y)
                    <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
        </div>
    </div>
@endsection