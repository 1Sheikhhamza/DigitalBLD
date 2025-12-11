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
                    <h2 class="banner-inner-title">{{ $project->title }}</h2>
                    <!-- <ul class="xs-breadcumb">
                        <li><a href="{{ route('project') }}"> Home / </a> Projects</li>
                    </ul> -->
                </div>
            </div>
        </div>
    </div>
</section>

<section class="single-service-inner-v2-sec service-inner-sec section-padding">
    <div class="container">
        <div class="main-single-service-v2">
            <div class="single-service-post-content">
                <img src="{{ asset('uploads/projects/thumbnail/' . ($project->thumbnail ?? '') ) }}" alt="{{ $project->title }}">
                <p>{!! $project->full_description !!}</p>
            </div>

            <div class="related-case-item">
                <h3 class="xs-single-title">Related Case</h3>
                <div class="row">
                    @foreach($allProjects as $relProject)
                        <div class="col-lg-4 col-md-4">
                            <a href="{{ route('project.details', $relProject->slug )}}">
                                <div class="single-related-case">
                                    <img src="{{ asset('uploads/projects/thumbnail/' . ($relProject->thumbnail ?? '') ) }}" alt="">
                                    <div class="overlay-content">
                                        <h4 class="xs-single-title">{{ $relProject->title }}</h4>
                                        <p>{{ $relProject->short_description }}</p>
                                    </div>
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