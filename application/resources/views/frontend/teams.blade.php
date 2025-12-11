@extends('layouts.app')
@section('seodetails')
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
@endsection
@section('title', "Welcome to Digital Bangladesh Legal Decisions")

@section('content')

<section class="banner-inner-sec" style="background-image:url('/frontend/assets/img/service-page-bg.jpg')">
  <div class="banner-table">
    <div class="banner-table-cell">
      <div class="container">
        <div class="banner-inner-content">
          <h2 class="banner-inner-title">Our Team</h2>
          <!-- <ul class="xs-breadcumb">
            <li><a href="{{ route('team') }}"> Home / </a> Team</li>
          </ul> -->
        </div>
      </div>
    </div>
  </div>
</section>

<section id="testimonials" class="testimonials section">

  <div class="container">
    <div class="isotope-layout" data-default-filter="*" data-layout="masonry" data-sort="original-order">
      <div class="row gy-4 isotope-container" data-aos="fade-up" data-aos-delay="200">
        @foreach($teams ?? [] as $team)
        <div class="col-lg-3 col-md-6 portfolio-item isotope-item">
          <div class="testimonial-item">
            <img src="{{ asset('uploads/teams/' . $team->photo) }}" class="img-fluid" alt="{{ $team->title.' BLD team gallery' }}">
            <div class="portfolio-info">
              <h4>{{ $team->name }}</h4>
              <p>{{ $team->designation ?? '' }}</p>
            </div>
          </div>
        </div>
        @endforeach
      </div>

    </div>
  </div>
</section>

@endsection
@section('footer')

@parent
@endsection

@push('scripts')
@endpush