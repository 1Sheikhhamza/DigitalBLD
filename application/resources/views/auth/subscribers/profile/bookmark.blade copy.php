@extends('auth.subscribers.layouts.app')
@section('seodetails')
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
@endsection
@section('title', Auth::guard('subscriber')->user()->name.' | BLD Profile')

@section('content')

<div class="container mt-5">
    <div class="row g-4">
        <main class="col-lg-9 offset-2">
            <h3 class="mb-4">Bookmarks</h3>
            @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
            @endif

            @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
            @endif

            <div class="search-results-list">
                @forelse ($bookmarkedDecisions as $index => $item)
                <div class="search-result-item d-flex text-decoration-none text-reset">
                    <a href="{{ route('subscriber.singleDecision', [$item->id, Crypt::encrypt('bookmark')]) }}" class="card-item d-flex flex-column h-100 w-100 p-1 shadow-none text-decoration-none">
                        {{-- Parties (3 lines max) --}}
                        <h5 class="card-title clamp-3 mb-2">
                            {!! $item->parties !!}
                        </h5>

                        {{-- Case No (1 line max) --}}
                        <div class="text-sm text-dark clamp-1 text-center mb-2">
                            {!! $item->case_no !!}
                        </div>

                        {{-- Judgment (4 lines max) --}}
                        <p class="card-text clamp-4 flex-grow-1">
                            {!! strip_tags($item->judgment) ?? 'No description available.' !!}
                        </p>
                    </a>
                    <!-- <i class="bi bi-bookmark-fill text-secondary"></i> -->

                    <form action="{{ route('subscriber.bookmark.destroy', $item->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to remove this bookmark?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" style="border: none; background: none; padding: 0;">
                            <i class="bi bi-trash text-danger"></i>
                        </button>
                    </form>


                </div>
                @empty
                <p>No results found.</p>
                @endforelse
            </div>
        </main>
    </div>
</div>


@endsection