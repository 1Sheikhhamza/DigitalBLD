@extends('auth.subscribers.layouts.app')
@section('seodetails')
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
@endsection
@section('title', Auth::guard('subscriber')->user()->name.' | BLD Profile')

@section('content')
<div class="container py-4">
    <!-- Search Bar -->
    <div class="search-bar-container">
        <div class="input-group search-input-group position-relative">
            <i class="bi bi-search"></i>
            <input type="text" class="form-control" placeholder="Search by volume number">
        </div>
    </div>

    <div class="container py-4">
        @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- Volume Grid -->
        @foreach ($events as $event)
        <div class="card mb-2">
            <div class="card-body">
                <h5>{{ $event->title }}</h5>
                <p>{{ $event->start_date }} - {{ $event->end_date ?? $event->start_date }}</p>
                <p>{{ $event->description }}</p>
                <a href="{{ route('subscriber.events.show', $event->id) }}" class="btn btn-sm btn-info">View</a>
                <form action="{{ route('subscriber.events.destroy', $event->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Delete?');">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-danger">Delete</button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
</div>


@endsection