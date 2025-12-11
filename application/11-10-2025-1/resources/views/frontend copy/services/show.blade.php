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
                    <h2 class="banner-inner-title">{{ $service->name }}</h2>
                    <ul class="xs-breadcumb">
                        <li><a href="{{ route('service') }}"> Home / </a> Services</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="service-inner-sec single-service-sec section-padding">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-4">
                <div class="service-sidebar">
                    <div class="widgets">
                        <h3 class="widget-title"><span>Our</span> Services</h3>
                        <ul class="services-link-item">
                            @if($allServices!='' && count($allServices) > 0)
                            @foreach($allServices as $otherService)
                            <li class="{{ $currentSlug == $otherService->slug ? 'active' : '' }}">
                                <a href="{{ url('service/' . $otherService->slug) }}">
                                    {{ $otherService->name }}
                                </a>
                            </li>
                            @endforeach
                            @endif
                        </ul>
                    </div>

                    <div class="widgets">
                        <h3 class="widget-title"><span>Lets</span> Subscribe</h3>
                        <ul class="brochures-list">
                            <li><a href="{{ route('subscriber.register') }}"><i class="fa fa-user-plus"></i>Sign Up</a></li>
                            <li><a href="{{ route('subscriber.login') }}"><i class="fa fa-sign-in"></i>Sign In</a></li>
                        </ul>
                    </div>

                    <div class="widgets testmonial-widget owl-carousel">
                        <div class="single-testmonial-widget">
                            <i class="fa fa-thumbs-o-up testmonial-icon"></i>
                            <p>
                                "They are awesome and friendlygardening team. We love there there there work and stay”
                            </p>
                            <div class="author-rating">
                                <i class="icon icon-star"></i>
                                <i class="icon icon-star"></i>
                                <i class="icon icon-star"></i>
                                <i class="icon icon-star"></i>
                                <i class="icon icon-star"></i>
                                <label>- Kim Jon</label>
                            </div>
                        </div>
                        <div class="single-testmonial-widget">
                            <i class="fa fa-thumbs-o-up testmonial-icon"></i>
                            <p>
                                "They are awesome and friendlygardening team. We love there there there work and stay”
                            </p>
                            <div class="author-rating">
                                <i class="icon icon-star"></i>
                                <i class="icon icon-star"></i>
                                <i class="icon icon-star"></i>
                                <i class="icon icon-star"></i>
                                <i class="icon icon-star"></i>
                                <label>- Kim Jon</label>
                            </div>
                        </div>
                        <div class="single-testmonial-widget">
                            <i class="fa fa-thumbs-o-up testmonial-icon"></i>
                            <p>
                                "They are awesome and friendlygardening team. We love there there there work and stay”
                            </p>
                            <div class="author-rating">
                                <i class="icon icon-star"></i>
                                <i class="icon icon-star"></i>
                                <i class="icon icon-star"></i>
                                <i class="icon icon-star"></i>
                                <i class="icon icon-star"></i>
                                <label>- Kim Jon</label>
                            </div>
                        </div>
                    </div><!-- widgets -->

                    <div class="widgets call-to-action">
                        <h3>Need help?</h3>
                        <p>
                            Donec ligula habitant tempor risus congue use gravida nostra tempus feugiat tempor.
                        </p>
                        <a href="#" class="xs-btn sm-btn">Contact Us</a>
                    </div><!-- widgets -->
                </div><!-- srvice sidebar -->
            </div><!-- col end -->
            <div class="col-lg-9 col-md-8">
                <div class="main-single-service-content">
                    <div class="single-service-post-content">
                        <img src="{{ asset('uploads/services/banner/' . ($service->banner ?? '') ) }}" alt="{{ $service->name }}">
                        <p>{!! $service->description!!}</p>
                    </div>

                </div>
            </div>

        </div>
    </div>
</section>

@endsection
@section('footer')

@parent
@endsection