@extends('auth.subscribers.layouts.app')
@section('seodetails')
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
@endsection
@section('title', Auth::guard('subscriber')->user()->name.' | BLD Profile')

@section('content')
<style>
    .beta-disclaimer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: linear-gradient(90deg, #fff3cd, #ffe69c);
        border-left: 6px solid #ffc107;
        border-radius: 10px;
        padding: 15px 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        transition: opacity 0.4s ease, transform 0.4s ease;
    }

    .beta-disclaimer.fade-out {
        opacity: 0;
        transform: translateY(-10px);
    }

    .beta-disclaimer .content {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .beta-disclaimer i {
        font-size: 24px;
        color: #ffc107;
    }

    .btn-close {
        border: none;
        background: none;
        font-size: 20px;
        cursor: pointer;
        color: #555;
        transition: color 0.2s ease;
    }

    .btn-close:hover {
        color: #000;
    }
</style>

<div class="container py-4">
    <div class="row justify-content-center">

        @if ($hasAnySubscription && !$hasAnySubscription->activeSubscription)
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <strong>ℹ️ Attention:</strong> You need to subscribe to access features.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <div class="col-sm-12 bg-white p-3 mb-4">
            <div class="row">
                <div class="col-sm-6">
                    <h4>Subscribe</h4>
                    <p>to any BLD package to enjoy our services</p>
                </div>
                <div class="col-sm-6 align-items-center justify-content-end d-flex">
                    <a href="{{ route('package') }}" class="book-now-btn">Subscribe Now</a>
                </div>
            </div>
        </div>
        @endif



        <main class="col-lg-9 pe-4">
            <div class="beta-disclaimer" id="betaDisclaimer">
                <div class="content">
                    <i class="bi bi-exclamation-triangle-fill fs-3 text-warning"></i>
                    <div>
                        <strong>Beta Testing Notice:</strong>
                        <p style="margin: 0;">This site and all its features are currently <strong>only for beta testing</strong>. Data and functionality may be incomplete or subject to change.</p>
                    </div>
                </div>
                <button type="button" class="btn-close" aria-label="Close">&times;</button>
            </div>

            <!-- BLD Services Section -->
            <section class="mb-5">
                <h5 class="mb-4">BLD Services</h5>
                <div class="d-flex flex-wrap gap-2 justify-content-start service-card-group">
                    @if(auth('subscriber')->user()->hasAnyPermissionOnResource('legal-search'))
                    <div class="service-card active">
                        <a href="{{ route('subscriber.leagalSearch', ['new' => 1]) }}">
                            <i class="bi bi-search"></i>
                            <span>Legal Search</span>
                        </a>
                    </div>
                    @endif

                    @if(auth('subscriber')->user()->hasAnyPermissionOnResource('bld-volume'))
                    <div class="service-card">
                        <a href="{{ route('subscriber.bldVolume') }}">
                            <i class="bi bi-book"></i>
                            <span>BLD Volume</span>
                        </a>
                    </div>
                    @endif

                    
                    @if(auth('subscriber')->user()->hasAnyPermissionOnResource('my-folder'))
                        <div class="service-card">
                            <a href="{{ route('subscriber.myFolder') }}">
                                <i class="bi bi-folder"></i>
                                <span>My Folder</span>
                            </a>
                        </div>
                    @endif

                    @if(auth('subscriber')->user()->hasAnyPermissionOnResource('legal-dictionary'))
                    <div class="service-card">
                        <a href="{{ route('subscriber.dictionary.index') }}">
                            <i class="bi bi-lightbulb"></i>
                            <span>Legal Terminology</span>
                        </a>
                    </div>
                    @endif

                    @if(auth('subscriber')->user()->hasAnyPermissionOnResource('bookmark'))
                    <div class="service-card">
                        <a href="{{ route('subscriber.bookmark') }}">
                            <i class="bi bi-bookmark"></i>
                            <span>Bookmark</span>
                        </a>
                    </div>
                    @endif

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
                    <div style="width: 100%; min-height:280px; height:auto;" class=" p-3 rounded h-100 d-flex flex-column bg-white">
                        <div class="section-header d-flex justify-content-between align-items-center">
                            <h5>Bookmark</h5>
                            <a href="{{ route('subscriber.bookmark') }}" class="see-more">See all <i class="bi bi-chevron-right ms-1"></i></a>
                        </div>

                        <div class="flex-grow-1 overflow-auto">
                            @forelse($bookmarkedDecisions as $bookmark)
                            <a class="content-card d-flex align-items-center" href="{{ route('subscriber.singleDecision', [$bookmark->id, Crypt::encrypt('dashboard')]) }}">
                                <i class="bi bi-bookmark-fill see-more me-3 fs-4"></i>
                                <div class="card-item card-body  shadow-none p-0 pb-2 pt-2">
                                    <p class="card-title clamp-3 mb-2">{!! $bookmark->parties !!}</p>
                                    <div class="text-sm text-dark clamp-1 text-center mb-2">
                                        {!! $bookmark->case_no !!}
                                    </div>
                                    <p class="card-text clamp-4 flex-grow-1">
                                        {!! strip_tags($bookmark->judgment) ?? 'No description available.' !!}
                                    </p>
                                </div>
                            </a>

                            @empty
                            <div class="d-flex flex-column justify-content-center align-items-center text-muted h-100">
                                <i class="bi bi-file-earmark-x" style="font-size: 3rem; color: #6c757d;"></i>
                                <p>No bookmarks found</p>
                            </div>
                            @endforelse
                        </div>
                    </div>

                </section>


                <!-- My Folder Section -->
                <section class="col-md-6">
                    <div style="width: 100%; min-height: 280px;" class="p-3 rounded h-100 d-flex flex-column bg-white">
                        <div class="section-header d-flex justify-content-between align-items-center">
                            <h5>My Folder</h5>
                            <a href="{{ route('subscriber.myFolder') }}" class="see-more">See all <i class="bi bi-chevron-right ms-1"></i></a>
                        </div>
                        <div class="row overflow-auto">
                            @forelse($folders as $folder)
                            <div class="col-sm-3 p-0">
                                <a href="{{ route('subscriber.files.index', Crypt::encrypt($folder->id)) }}" class="content-card folder-item">
                                    <img src="{{ asset('frontend/assets/img/folder.png') }}" class="w-50">
                                    <span>{{$folder->name}}</span>
                                </a>
                            </div>
                            @empty
                            <div class="d-flex flex-column justify-content-center align-items-center text-muted h-100">
                                <i class="bi bi-file-earmark-x" style="font-size: 3rem; color: #6c757d;"></i>
                                <p>No Folders found</p>
                            </div>
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
<script>
    document.querySelector('.btn-close').addEventListener('click', function() {
        const alertBox = document.getElementById('betaDisclaimer');
        alertBox.classList.add('fade-out');
        setTimeout(() => alertBox.style.display = 'none', 400);
    });
</script>
@endsection