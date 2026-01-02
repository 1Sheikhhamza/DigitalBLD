@extends('auth.subscribers.layouts.app')
@section('title', 'SCOB ' . $year . ' - Index')

@section('content')
    <div class="container py-4">
        <h4 class="mb-4">SCOB Year {{ $year }} - All Cases</h4>

        <!-- Tabs -->
        <ul class="nav nav-tabs mb-4">
            <li class="nav-item">
                <a class="nav-link active" href="{{ route('subscriber.scobYear.index', $year) }}">Index</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('subscriber.scobYear.appellate', $year) }}">Appellate Division</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('subscriber.scobYear.highcourt', $year) }}">High Court Division</a>
            </li>
        </ul>

        <!-- Appellate Division Section -->
        @if($appellateDecisions->count() > 0)
            <div class="mb-5">
                <h5 class="mb-3 text-primary border-bottom pb-2">Appellate Division ({{ $appellateDecisions->count() }} cases)
                </h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle" style="table-layout: fixed;">
                        <thead class="table-light">
                            <tr>
                                <th width="5%" class="text-center">#</th>
                                <th width="50%">Parties</th>
                                <th width="30%">Case No</th>
                                <th width="15%" class="text-end">Decided On</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($appellateDecisions as $decision)
                                <tr>
                                    <td class="text-center text-muted">{{ $loop->iteration }}</td>
                                    <td>
                                        <a href="{{ route('subscriber.singleDecision', [$decision->id, Crypt::encrypt('scobYearIndex')]) }}"
                                            class="text-decoration-none fw-bold text-dark d-block text-uppercase text-truncate">
                                            {!! html_entity_decode($decision->parties) !!}
                                        </a>
                                    </td>
                                    <td class="text-secondary small">
                                        <div class="text-truncate" title="{{ $decision->case_no }}">{{ $decision->case_no }}</div>
                                    </td>
                                    <td class="text-end text-muted small text-nowrap">{{ $decision->decided_on }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- High Court Division Section -->
        @if($highCourtDecisions->count() > 0)
            <div>
                <h5 class="mb-3 text-primary border-bottom pb-2">High Court Division ({{ $highCourtDecisions->count() }} cases)
                </h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle" style="table-layout: fixed;">
                        <thead class="table-light">
                            <tr>
                                <th width="5%" class="text-center">#</th>
                                <th width="50%">Parties</th>
                                <th width="30%">Case No</th>
                                <th width="15%" class="text-end">Decided On</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($highCourtDecisions as $decision)
                                <tr>
                                    <td class="text-center text-muted">{{ $loop->iteration }}</td>
                                    <td>
                                        <a href="{{ route('subscriber.singleDecision', [$decision->id, Crypt::encrypt('scobYearIndex')]) }}"
                                            class="text-decoration-none fw-bold text-dark d-block text-uppercase text-truncate">
                                            {!! html_entity_decode($decision->parties) !!}
                                        </a>
                                    </td>
                                    <td class="text-secondary small">
                                        <div class="text-truncate" title="{{ $decision->case_no }}">{{ $decision->case_no }}</div>
                                    </td>
                                    <td class="text-end text-muted small text-nowrap">{{ $decision->decided_on }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        @if($appellateDecisions->count() == 0 && $highCourtDecisions->count() == 0)
            <div class="text-center py-5">
                <i class="bi bi-inbox" style="font-size: 3rem; color: #6c757d;"></i>
                <p class="mt-3">No cases found for year {{ $year }}</p>
            </div>
        @endif

        <!-- Year Selector -->
        <div class="mt-4">
            <label>Jump to year:</label>
            <select class="form-select w-auto d-inline-block ms-2"
                onchange="window.location.href='{{ route('subscriber.scobYear.index', '') }}/' + this.value">
                <option value="">Select year</option>
                @foreach($allYears as $y)
                    <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
        </div>
    </div>
@endsection