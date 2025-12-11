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
          <h2 class="banner-inner-title">Investment Projects</h2>
          <ul class="xs-breadcumb">
            <li><a href="{{ route('project') }}"> Home / </a> Projects</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="service-inner-v2-sec">
  <div class="container">

    @foreach($projects as $index => $project)
    @if($index % 2 == 0)
    <div class="single-service-v2-item single-service-v2-item-1">
      <div class="row">
        <div class="col-lg-6 col-md-7">
          <div class="single-service-v2-img">
            <img src="{{ asset('uploads/projects/thumbnail/' . $project->thumbnail) }}" alt="{{ $project->title }}">
          </div>
        </div>
        <div class="col-lg-5 offset-lg-1 col-md-5 align-self-center">
          <div class="single-service-v2-content">
            <h4 class="xs-single-title">{{ $project->title }}</h4>
            <p>{{ $project->short_description }}</p>
            <a href="{{ route('project.details', $project->slug )}}" class="load-more-btn" data-effect="mfp-zoom-in">Details</a>
            <a href="#popup_{{ $index }}" class="xs-image-popup book-now-btn" data-effect="mfp-zoom-in" data-effect="mfp-zoom-in">Invest Now</a>
            <div id="popup_{{ $index }}" class="container xs-gallery-popup-item mfp-hide">
              <div class="xs-popup-content">
                <div class="row">
                  <div class="col-lg-6">
                    <ul class="xs-popup-left-content">
                      <li>
                        <i class="icon icon-tags"></i>
                        <label>Business Type</label>
                        <span>{{ $project->category }}</span>
                      </li>
                      <li>
                        <i class="icon icon-calendar-full"></i>
                        <label>Project Start Date</label>
                        <span>{{ $project->start_date }}</span>
                      </li>
                      <li>
                        <i class="icon icon-calendar-full"></i>
                        <label>Project End Date</label>
                        <span>{{ $project->end_date }}</span>
                      </li>
                      <li>
                        <i class="icon icon-user2"></i>
                        <label>Investment Time</label>
                        <span>{{ $project->duration }}</span>
                      </li>
                      <li>
                        <i class="icon icon-invest"></i>
                        <label>Investment goal</label>
                        <span>&#2547;{{ $project->goal_amount }}</span>
                      </li>
                      <li>
                        <i class="icon icon-map-marker2"></i>
                        <label>Raised</label>
                        <span>&#2547;{{ $project->raised_amount }}</span>
                      </li>
                    </ul>
                  </div>
                  <div class="col-lg-6">
                    <ul class="xs-popup-left-content">
                      <li>
                        <i class="icon icon-map-marker2"></i>
                        <label>In waiting</label>
                        <span>&#2547;{{ $project->raised_amount }}</span>
                      </li>
                      <li>
                        <i class="icon icon-calendar-full"></i>
                        <label>Project Duration/label>
                          <span>{{ $project->duration }}</span>
                      </li>
                      <li>
                        <i class="icon icon-tags"></i>
                        <label>Minimum Investment</label>
                        <span>{{ $project->unit_price }}</span>
                      </li>
                      <li>
                        <i class="icon icon-user2"></i>
                        <label>ROI</label>
                        <span>{{ $project->roi_method.' '.$project->roi.$project->roi_profit_type }}</span>
                      </li>
                      <li>
                        <i class="icon icon-invest"></i>
                        <label>Projected</label>
                        <span>{{ $project->projected }}</span>
                      </li>
                      <li>
                        <i class="icon icon-map-marker2"></i>
                        <label>Status</label>
                        <span>{{ $project->project_status }}</span>
                      </li>
                    </ul>
                  </div>

                </div>
                <div class="d-flex justify-content-center align-items-center mt-4">
                  <a href="{{ route('investment.form') }}" class="xs-btn">INVEST NOW</a>
                </div>

              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    @else
    <div class="single-service-v2-item section-bg">
      <div class="row">
        <div class="col-lg-5 col-md-5 align-self-center">
          <div class="single-service-v2-content">
            <h4 class="xs-single-title">{{ $project->title }}</h4>
            <p>{{ $project->short_description }}</p>
            <a href="{{ route('project.details', $project->slug )}}" class="load-more-btn" data-effect="mfp-zoom-in">Details</a>
            <a href="#popup_{{ $index }}" class="xs-image-popup book-now-btn" data-effect="mfp-zoom-in" data-effect="mfp-zoom-in">Invest Now</a>
            <div id="popup_{{ $index }}" class="container xs-gallery-popup-item mfp-hide">
              <div class="xs-popup-content">
                <div class="row">
                  <div class="col-lg-6">
                    <ul class="xs-popup-left-content">
                      <li>
                        <i class="icon icon-tags"></i>
                        <label>Business Type</label>
                        <span>{{ $project->category }}</span>
                      </li>
                      <li>
                        <i class="icon icon-calendar-full"></i>
                        <label>Project Start Date</label>
                        <span>{{ $project->start_date }}</span>
                      </li>
                      <li>
                        <i class="icon icon-calendar-full"></i>
                        <label>Project End Date</label>
                        <span>{{ $project->end_date }}</span>
                      </li>
                      <li>
                        <i class="icon icon-user2"></i>
                        <label>Investment Time</label>
                        <span>{{ $project->duration }}</span>
                      </li>
                      <li>
                        <i class="icon icon-invest"></i>
                        <label>Investment goal</label>
                        <span>&#2547;{{ $project->goal_amount }}</span>
                      </li>
                      <li>
                        <i class="icon icon-map-marker2"></i>
                        <label>Raised</label>
                        <span>&#2547;{{ $project->raised_amount }}</span>
                      </li>
                    </ul>
                  </div>
                  <div class="col-lg-6">
                    <ul class="xs-popup-left-content">
                      <li>
                        <i class="icon icon-map-marker2"></i>
                        <label>In waiting</label>
                        <span>&#2547;{{ $project->raised_amount }}</span>
                      </li>
                      <li>
                        <i class="icon icon-calendar-full"></i>
                        <label>Project Duration/label>
                          <span>{{ $project->duration }}</span>
                      </li>
                      <li>
                        <i class="icon icon-tags"></i>
                        <label>Minimum Investment</label>
                        <span>{{ $project->unit_price }}</span>
                      </li>
                      <li>
                        <i class="icon icon-user2"></i>
                        <label>ROI</label>
                        <span>{{ $project->roi_method.' '.$project->roi.$project->roi_profit_type }}</span>
                      </li>
                      <li>
                        <i class="icon icon-invest"></i>
                        <label>Projected</label>
                        <span>{{ $project->projected }}</span>
                      </li>
                      <li>
                        <i class="icon icon-map-marker2"></i>
                        <label>Status</label>
                        <span>{{ $project->project_status }}</span>
                      </li>
                    </ul>
                  </div>

                </div>
                <div class="d-flex justify-content-center align-items-center mt-4">
                  <a href="{{ $project->link }}" class="xs-btn">INVEST NOW</a>
                </div>

              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-6 offset-lg-1 col-md-7">
          <div class="single-service-v2-img">
            <img src="{{ asset('uploads/projects/thumbnail/' . $project->thumbnail) }}" alt="{{ $project->title }}">
          </div>
        </div>
      </div>
    </div>
    @endif
    @endforeach

  </div>
</section>

@endsection
@section('footer')

@parent
@endsection

@push('scripts')
@endpush