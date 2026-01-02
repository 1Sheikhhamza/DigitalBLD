@extends('auth.subscribers.layouts.app')
@section('seodetails')
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
@endsection
@section('title', Auth::guard('subscriber')->user()->name . ' | SCOB Volume')

@section('content')
    <div class="container py-4">
        <h4 class="mb-4">SCOB Volume - Browse by Year</h4>

        <!-- Year Grid -->
        <div class="volume-grid-container">
            <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-6 row-cols-xl-8 g-4">
                @forelse($yearList as $yearData)
                    <div class="col">
                        <div class="book-item">
                            <a class="book-icon-wrapper" href="{{ route('subscriber.scobYear.index', $yearData->year) }}">
                                <img src="{{ asset('frontend/assets/img/book.png') }}" alt="Year {{ $yearData->year }}">
                                <span class="book-number" style="font-size: 12px; margin-top: 6px;">{{ $yearData->year }}</span>
                            </a>
                            <small class="text-center d-block mt-1">{{ $yearData->count }} cases</small>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <i class="bi bi-inbox" style="font-size: 3rem; color: #6c757d;"></i>
                        <p class="mt-3">No SCOB judgments found</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

@endsection