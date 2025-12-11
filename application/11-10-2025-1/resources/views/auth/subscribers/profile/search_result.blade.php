@extends('auth.subscribers.layouts.app')
@section('seodetails')
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
@endsection
@section('title', Auth::guard('subscriber')->user()->name.' | BLD Profile')

@section('content')

<div class="container mt-5">
    <div class="row g-4">
        <main class="col-lg-9">
            <div class="search-summary">
                <div class="row d-flex flex-wrap justify-content-between align-items-center">
                    <div class="col-sm-10">
                        <span class="fw-semibold me-2">Search result for:</span>
                        @forelse($searchInputParams as $key => $value)
                        <span class="badge bg-primary text-white text-capitalize">
                            {{ str_replace('_', ' ', $key) }}: {{ $value }}
                        </span>
                        @empty
                        <span class="text-muted">All</span>
                        @endforelse
                    </div>
                    <div class="col-sm-2">
                        <strong>{{ $results->total() }}</strong> results found
                    </div>
                </div>
                <!-- <div class="dropdown dropdown-sort">
                    <button class="btn dropdown-toggle" type="button" id="dropdownSortBy" data-bs-toggle="dropdown" aria-expanded="false">
                        By date
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownSortBy">
                        <li><a class="dropdown-item" href="#">By relevance</a></li>
                        <li><a class="dropdown-item" href="#">By name</a></li>
                    </ul>
                </div> -->
            </div>

            <div class="search-results-list">
                @forelse ($results as $index => $item)
                <div class="search-result-item d-flex text-decoration-none text-reset">
                    {{-- Result Number --}}
                    <div class="result-number me-2">
                        {{ $results->firstItem() + $index }}.
                    </div>

                    {{-- Card Content --}}
                    <a href="{{ route('subscriber.singleDecision', [$item->id, Crypt::encrypt('showResults')]) }}"
                        class="card-item d-flex flex-column h-100 w-100 p-1 shadow-none text-decoration-none">

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

                @empty
                <p>No results found.</p>
                @endforelse
            </div>

            <div class="mt-4">
                {{ $results->links('pagination::bootstrap-5') }}
            </div>
        </main>

        <aside class="col-lg-3 d-none d-lg-block">
            <div class="sidebar">
                <h5>Quick Links</h5>

                <a href="{{ route('subscriber.leagalSearch') }}" class="quick-link-item">
                    <i class="bi bi-pencil-square"></i>
                    <span>Modify Search</span>
                    <i class="bi bi-chevron-right ms-auto"></i>
                </a>

                <a href="{{ route('subscriber.leagalSearch', ['new' => 1]) }}" class="quick-link-item">
                    <i class="bi bi-search"></i>
                    <span>Search New</span>
                    <i class="bi bi-chevron-right ms-auto"></i>
                </a>
            </div>
        </aside>

    </div>
</div>


@endsection