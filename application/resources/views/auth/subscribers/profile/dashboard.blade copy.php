@extends('auth.subscribers.layouts.app')
@section('seodetails')
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
@endsection
@section('title', Auth::guard('subscriber')->user()->name.' | BLD Profile')

@section('content')


<div class="container py-4">
    <div class="row justify-content-center">
        <!-- Main Content Area -->
        <main class="col-lg-9 pe-4">
            <!-- BLD Services Section -->
            <section class="mb-5">
                <h5 class="mb-4">BLD Services</h5>
                <div class="d-flex flex-wrap gap-3 justify-content-start service-card-group">
                    <div class="service-card active">
                        <a href="{{ route('subscriber.leagalSearch') }}">
                            <i class="bi bi-search"></i>
                            <span>Legal Search</span>
                        </a>
                    </div>

                    <div class="service-card">
                        <a href="{{ route('subscriber.bldVolume') }}">
                            <i class="bi bi-book"></i>
                            <span>BLD Volume</span>
                        </a>
                    </div>

                    <div class="service-card">
                        <a href="{{ route('subscriber.myFolder') }}">
                            <i class="bi bi-folder"></i>
                            <span>My Folder</span>
                        </a>
                    </div>

                    <div class="service-card">
                        <a href="#" onclick="alert('Comming Soon')">
                            <i class="bi bi-lightbulb"></i>
                            <span>Legal Terminology</span>
                        </a>
                    </div>

                    <div class="service-card">
                        <a href="{{ route('subscriber.bookmark') }}">
                            <i class="bi bi-bookmark"></i>
                            <span>Bookmark</span>
                        </a>
                    </div>

                    <div class="service-card">
                        <a href="{{ route('subscriber.profile') }}">
                            <i class="bi bi-person-gear"></i>
                            <span>My Account</span>
                        </a>
                    </div>

                </div>
            </section>

            <div class="row g-4">
                <!-- Bookmark Section -->
                <section class="col-md-6">
                    <div class="col-sm-12 bg-white p-3 rounded">
                        <div class="section-header">
                            <h5>Bookmark</h5>
                            <a href="{{ route('subscriber.bookmark') }}" class="see-more">See all <i class="bi bi-chevron-right ms-1"></i></a>
                        </div>

                        @foreach($bookmarkedDecisions as $bookmark)
                        <a class="content-card d-flex align-items-center" href="{{ route('subscriber.singleDecision', $bookmark->id) }}">
                            <i class="bi bi-bookmark-fill see-more me-3 fs-4"></i> <!-- `me-3` adds space to the right -->

                            <div class="card-body border-bottom p-0 pb-2 pt-2">
                                <p class="card-text text-muted mb-1 small">{{ $bookmark->parties }}</p>
                                <h6 class="card-title fw-bold mb-2">{{ $bookmark->decided_on ?? 'N/A' }} - {{ $bookmark->division ?? 'N/A' }}</h6>
                                <p class="card-text text-muted small">{{ Str::limit($bookmark->judgment ?? 'No description available.', 200) }}</p>
                            </div>
                        </a>

                        @endforeach
                    </div>
                </section>

                <!-- My Folder Section -->
                <section class="col-md-6">
                    <div class="col-sm-12 bg-white p-3 rounded">
                        <div class="section-header">
                            <h5>My Folder</h5>
                            <a href="{{ route('subscriber.myFolder') }}" class="see-more">See all <i class="bi bi-chevron-right ms-1"></i></a>
                        </div>
                        <div class="row">
                            @foreach($folders as $folder)
                            <div class="col-sm-3 p-0">
                                <a href="{{ route('subscriber.files.index', Crypt::encrypt($folder->id)) }}" class="content-card folder-item">
                                    <img src="{{ asset('frontend/assets/img/folder.png') }}" class="w-50">
                                    <span>{{$folder->name}}</span>
                                </a>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="col-12 mt-5">
                        <div class="section-header">
                            <h5 class="mb-0 text-secondary">Upcomming Event</h5>
                            <a href="{{ route('subscriber.events.index') }}" class="see-more">See all event<i class="bi bi-chevron-right ms-1"></i></a>
                        </div>
                        <div class="row mt-4">
                            @forelse($events as $event)
                            <div class="col-md-6 mb-4">
                                <div class="card shadow-sm h-100" style="border-left: 6px solid {{ $event->color ?? '#0d6efd' }}">
                                    <div class="card-body p-3">
                                        <h6 class="card-title mb-2">{{ $event->title }}</h6>

                                        <p class="card-text text-muted mb-1">
                                            <i class="bi bi-calendar-event"></i>
                                            {{ \Carbon\Carbon::parse($event->start_date)->format('M d, Y') }}
                                            @if ($event->end_date && $event->end_date !== $event->start_date)
                                            → {{ \Carbon\Carbon::parse($event->end_date)->format('M d, Y') }}
                                            @endif
                                        </p>

                                        <p class="card-text text-muted mb-1">
                                            <i class="bi bi-clock"></i>
                                            @if ($event->start_time && $event->end_time)
                                            {{ $event->start_time }} → {{ $event->end_time }}
                                            @elseif ($event->start_time)
                                            {{ $event->start_time }}
                                            @else
                                            No time
                                            @endif
                                        </p>

                                        <p class="card-text">
                                            {{ $event->description ?? 'No description' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <p class="text-center text-muted my-3">No events found.</p>
                            @endforelse
                        </div>
                    </div>

                </section>
            </div>
        </main>

        <aside class="col-lg-3 sidebar p-0">
            <div class="section-header">
                <h5>My Reminder</h5>
                <a href="{{ route('subscriber.events.index') }}" class="see-more">
                    See all <i class="bi bi-chevron-right ms-1"></i>
                </a>
            </div>

            @include('auth.subscribers.layouts.calender')
        </aside>
    </div>
</div>

@endsection