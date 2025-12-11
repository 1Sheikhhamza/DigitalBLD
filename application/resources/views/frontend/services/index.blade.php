@extends('layouts.app')
@section('seodetails')
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
@endsection

@section('title', 'Service')

@section('content')

<section class="banner-inner-sec" style="background-image:url('/frontend/assets/img/service-page-bg.jpg')">
  <div class="banner-table">
    <div class="banner-table-cell">
      <div class="container">
        <div class="banner-inner-content">
          <h2 class="banner-inner-title">Our Services</h2>
          <!-- <ul class="xs-breadcumb">
            <li><a href="{{ route('service') }}"> Home / </a> Services</li>
          </ul> -->
        </div>
      </div>
    </div>
  </div>
</section>
<section class="service-sec service-v2-sec service-inner-sec section-padding">
  <div class="container">
    <div class="row">
      <div class="col-lg-3 col-md-4">
        <div class="service-sidebar">
          <div class="widgets">
            <h3 class="widget-title"><span>Lets</span> Subscribe</h3>
            <ul class="brochures-list">
              <li><a href="{{ route('subscriber.register') }}"><i class="fa fa-user-plus"></i>Sign Up</a></li>
              <li><a href="{{ route('subscriber.login') }}"><i class="fa fa-sign-in"></i>Sign In</a></li>
            </ul>
          </div>

          
        </div>
      </div>
      <div class="col-lg-9 col-md-8">
        <div class="row">
          @foreach($services as $index => $service)
          <div class="col-lg-4 col-sm-6 wow fadeInUp p-0" data-wow-duration="1.5s" data-wow-delay="{{ 300 + $index * 100 }}ms">
            <a href="{{ route('service.details', $service->slug) }}">
              <div class="single-service">
                <div class="service-img">
                  @if(!empty($service->image))
                  <img src="{{ asset('uploads/services/image/' . $service->image) }}" alt="{{ $service->name }}">
                  @endif

                  @if(!empty($service->icon))
                  <img src="{{ asset('uploads/services/icon/' . $service->icon) }}" alt="{{ $service->name }} Icon">
                  @endif
                </div>
                <h3 class="xs-service-title"><a href="{{ route('service.details', $service->slug) }}">{{ $service->name }}</a></h3>
                <p>{{ limit_description(strip_tags($service->description), 150) }}</p>
              </div>
            </a>
          </div>
          @endforeach

        </div>
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