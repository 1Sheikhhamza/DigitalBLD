@extends('layouts.app')
@section('seodetails')
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
@endsection
@section('title', "Welcome to Digital Bangladesh Legal Decisions")

@section('content')

<section id="portfolio" class="portfolio section">
        <div class="container section-title" data-aos="fade-up">
            <h2>Photo Gallery</h2>
        </div>

        <div class="container">
            <div class="isotope-layout" data-default-filter="*" data-layout="masonry" data-sort="original-order">
                <div class="row gy-4 isotope-container" data-aos="fade-up" data-aos-delay="200">
                    @foreach($photos ?? [] as $photo)
                    @php
                    $categorySlug = isset($photo->category) ? Str::slug($photo->category->name) : 'uncategorized';
                    @endphp
                    <div class="col-lg-4 col-md-6 portfolio-item isotope-item filter-{{ $categorySlug }}">
                        <img src="{{ asset('uploads/photos/' . $photo->image) }}" class="img-fluid" alt="{{ $photo->title.' BLD photo gallery' }}">
                        <div class="portfolio-info">
                            <h4>{{ $photo->title }}</h4>
                            <p>{{ format_date($photo->created_at) ?? '' }}</p>
                            <a href="{{ asset('uploads/photos/' . $photo->image) }}" title="{{ $photo->title }}"
                                data-gallery="portfolio-gallery-{{ $categorySlug }}" class="glightbox preview-link">
                                <i class="bi bi-zoom-in"></i>
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="col-sm-12 d-flex align-items-center justify-content-center mt-5">{{ $photos->links('pagination::bootstrap-4') }}</div>

            </div>
        </div>
    </section>

@endsection
@section('footer')

@parent
@endsection

@push('scripts')
@endpush