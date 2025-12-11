@extends('layouts.app')

@section('seodetails')
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
@endsection

@section('title', "Welcome to Digital Bangladesh Legal Decisions")

@section('content')

<!-- ===== Banner Section ===== -->
<section class="banner-inner-sec" style="background-image:url('/frontend/assets/img/service-page-bg.jpg')">
    <div class="banner-table">
        <div class="banner-table-cell">
            <div class="container">
                <div class="banner-inner-content text-center text-white">
                    <h2 class="banner-inner-title">Frequently Asked Questions</h2>
                    <ul class="xs-breadcumb list-inline">
                        <li><a href="{{ url('/') }}">Home</a> / FAQ</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>


<section class="service-inner-sec single-service-sec section-padding py-5 bg-light">
        <div class="container">
            
            @include('frontend._faq')
        </div>
    </section>

@endsection

@section('footer')
@parent
@endsection

@push('styles')
<style>
    .title-line {
        width: 60px;
        height: 3px;
        background: #0d6efd;
        margin-top: 10px;
        border-radius: 3px;
    }

    .accordion-button {
        background-color: #fff;
        color: #222;
        transition: all 0.3s ease;
        box-shadow: none;
    }

    .accordion-button:not(.collapsed) {
        background-color: #0d6efd;
        color: #fff;
    }

    .accordion-item {
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.08);
    }

    .accordion-body {
        background-color: #fafafa;
    }
</style>
@endpush