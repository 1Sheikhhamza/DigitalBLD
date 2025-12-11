@extends('auth.subscribers.layouts.app')
@section('seodetails')
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
@endsection
@section('title', Auth::guard('subscriber')->user()->name . ' | BLD Profile')

@section('content')

    <div class="container mt-5">
        <div class="row g-4">
            <main class="col-12">
                <div class="search-summary mb-4">
                    <div
                        class="d-flex flex-wrap justify-content-between align-items-center gap-3 p-3 bg-white rounded shadow-sm border">
                        <div class="d-flex align-items-center flex-wrap gap-2">
                            <span class="fw-semibold">Search result for:</span>
                            @forelse($searchInputParams as $key => $value)
                                <span class="badge bg-primary text-white text-capitalize">
                                    {{ str_replace('_', ' ', $key) }}: {{ $value }}
                                </span>
                            @empty
                                <span class="text-muted">All</span>
                            @endforelse
                        </div>

                        <div class="d-flex align-items-center gap-3">
                            <div class="text-muted me-2">
                                <strong>{{ $results->total() }}</strong> results found
                            </div>
                            <a href="{{ route('subscriber.leagalSearch') }}"
                                class="btn btn-outline-primary btn-sm d-flex align-items-center gap-2">
                                <i class="bi bi-pencil-square"></i> Modify Search
                            </a>
                            <a href="{{ route('subscriber.leagalSearch', ['new' => 1]) }}"
                                class="btn btn-primary btn-sm text-white d-flex align-items-center gap-2">
                                <i class="bi bi-search"></i> Search New
                            </a>
                        </div>
                    </div>
                </div>

                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4 mt-3">
                    @forelse ($results as $index => $item)
                        <div class="col d-flex">
                            <div class="card-item d-flex flex-column h-100 w-100 p-3 border rounded shadow-sm">
                                <!-- <div class="result-number me-2">
                                {{ $results->firstItem() + $index }}.
                            </div> -->

                                {{-- Card Content --}}
                                <a href="{{ route('subscriber.singleDecision', [$item->id, Crypt::encrypt('showResults')]) }}"
                                    class="text-decoration-none">

                                    {{-- Parties (3 lines max) --}}
                                    <h5 class="card-title clamp-3 mb-2">
                                        {!! $item->parties !!}
                                    </h5>

                                    {{-- Case No (1 line max) --}}
                                    <div class="text-sm text-dark clamp-1 text-center mb-2">
                                        {!! $item->case_no !!}
                                    </div>

                                    {{-- Judgment / Summary (4 lines max) --}}
                                    <p class="card-text clamp-4 flex-grow-1">
                                        {!! strip_tags($item->judgment ?? $item->summary ?? 'No summary available.') !!}
                                    </p>
                                </a>
                            </div>
                        </div>

                    @empty
                        <p>No results found.</p>
                    @endforelse
                </div>

                <div class="mt-4">
                    {{ $results->links('pagination::bootstrap-5') }}
                </div>
            </main>



        </div>
    </div>


@endsection