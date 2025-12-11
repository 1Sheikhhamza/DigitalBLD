@extends('auth.subscribers.layouts.app')
@section('seodetails')
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
@endsection
@section('title', Auth::guard('subscriber')->user()->name.' | BLD Profile')

@section('content')

<div class="container py-4">
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

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 mt-3">
        @forelse ($bookmarkedDecisions as $index => $item)
        <div class="col d-flex">
            <div class="card-item d-flex flex-column h-100 w-100 p-3 border rounded shadow-sm">
                <a href="{{ route('subscriber.singleDecision', [$item->id, Crypt::encrypt('bookmark')]) }}" class=" text-decoration-none">
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
                <form action="{{ route('subscriber.bookmark.destroy', $item->id) }}" method="POST" style="display: inline;" 
                onsubmit="return confirm('Are you sure you want to remove this bookmark?');" class="d-flex justify-content-end">
                    @csrf
                    @method('DELETE')
                    <button type="submit" style="border: none; background: none; padding: 0;" title="Remove from bookmark items">
                        <i class="bi bi-trash text-danger"></i>
                    </button>
                </form>
            </div>

        </div>
        @empty
        <p>No results found.</p>
        @endforelse
    </div>
</div>


@endsection