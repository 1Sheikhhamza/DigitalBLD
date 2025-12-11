@extends('auth.subscribers.layouts.app')
@section('seodetails')
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
@endsection
@section('title', Auth::guard('subscriber')->user()->name.' | BLD Profile')

@section('content')
<div class="container py-4">
    <!-- Top Navigation Bar with Tabs and Info -->
    <div class="top-navbar">
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="index-tab" data-bs-toggle="tab" data-bs-target="#index" type="button" role="tab" aria-controls="index" aria-selected="false">Index</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="appellate-tab" data-bs-toggle="tab" data-bs-target="#appellate" type="button" role="tab" aria-controls="appellate" aria-selected="true">Appellate Division</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="highcourt-tab" data-bs-toggle="tab" data-bs-target="#highcourt" type="button" role="tab" aria-controls="highcourt" aria-selected="false">High Court Division</button>
            </li>
        </ul>
        <div class="info-text mt-2 mt-md-0">
            VOLUME {{ $volumeData->number }}
        </div>
    </div>

    <div class="content-area">
        <div class="tab-content" id="myTabContent">
            <!-- Index Tab Content -->
            <div class="tab-pane fade show active" id="index" role="tabpanel" aria-labelledby="index-tab">
                <div class="row">
                    <!-- Example of displaying PDF using iframe -->
                    <div class="col-12">
                        @if(!empty($volumeData->index_file) && file_exists(public_path('uploads/volume/pdfs/' . $volumeData->index_file)))
                        <iframe src="{{ asset('uploads/volume/pdfs/' . $volumeData->index_file) }}" width="100%" height="600px"></iframe>
                        @else
                        <p>File not found.</p>
                        @endif

                    </div>
                </div>
            </div>

            <!-- Appellate Division Tab Content -->
            <div class="tab-pane fade" id="appellate" role="tabpanel" aria-labelledby="appellate-tab">
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                    @forelse ($appellateDecisions as $decision)
                    <div class="col d-flex">
                        <a class="card-item d-flex flex-column h-100 w-100 p-3 border rounded shadow-sm text-decoration-none"
                            href="{{ route('subscriber.singleDecision', [$decision->id, Crypt::encrypt('legalDecision')]) }}">

                            {{-- Parties (3 lines max) --}}
                            <h5 class="card-title clamp-3 mb-2">
                                {!! html_entity_decode($decision->parties, ENT_QUOTES | ENT_HTML5) !!}
                            </h5>

                            {{-- Case No (1 line max) --}}
                            <div class="text-sm text-dark clamp-1 text-center mb-2">
                                {!! $decision->case_no !!}
                            </div>

                            {{-- Judgment (4 lines max) --}}
                            <p class="card-text clamp-4 flex-grow-1">
                                {!! strip_tags($decision->judgment) ?? 'No description available.' !!}
                            </p>
                        </a>
                    </div>
                    @empty
                    <p>No decisions found for Appellate Division.</p>
                    @endforelse
                </div>


                <!-- Pagination -->
                <div class="mt-4">
                    {{ $appellateDecisions->links('pagination::bootstrap-5') }}
                </div>
            </div>

            <!-- High Court Division Tab Content -->
            <div class="tab-pane fade" id="highcourt" role="tabpanel" aria-labelledby="highcourt-tab">
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                    @forelse ($highCourtDecisions as $decision)
                    <div class="col d-flex">
                        <a class="card-item d-flex flex-column h-100 w-100 p-3 border rounded shadow-sm text-decoration-none"
                            href="{{ route('subscriber.singleDecision', [$decision->id, Crypt::encrypt('legalDecision')]) }}">

                            {{-- Parties (max 3 lines, fixed height) --}}
                            <h5 class="card-title clamp-3 mb-2">
                                {!! html_entity_decode($decision->parties, ENT_QUOTES | ENT_HTML5) !!}
                            </h5>

                            {{-- Case No (1 line only) --}}
                            <div class="text-sm text-dark clamp-1 text-center mb-2">
                                {!! $decision->case_no !!}
                            </div>

                            {{-- Judgment (max 4 lines, fixed height, fills remaining space) --}}
                            <p class="card-text clamp-4 flex-grow-1">
                                {!! strip_tags($decision->judgment) ?? 'No description available.' !!}
                            </p>
                        </a>
                    </div>
                    @empty
                    <p>No decisions found for High Court Division.</p>
                    @endforelse
                </div>


                <!-- Pagination -->
                <div class="mt-4">
                    {{ $highCourtDecisions->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>



</div>


@endsection