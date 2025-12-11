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
                    <h2 class="banner-inner-title">{{ $blog->title }}</h2>
                    <ul class="xs-breadcumb">
                        <li><a href="{{ route('blog') }}"> Home / </a> Blogs</li>
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

                    <!-- Related Blogs Section -->
                    <div class="widgets">
            <h3 class="widget-title"><span>Related</span> Blogs</h3>
            <div class="related-blogs">
                @if($allBlogs!='' && count($allBlogs) > 0)
                    @foreach($allBlogs as $otherBlog)
                        <div class="related-blog-card {{ $currentSlug == $otherBlog->slug ? 'active' : '' }}">
                            <a href="{{ url('blog/' . $otherBlog->slug) }}">
                                <div class="blog-thumb">
                                    <img src="{{ asset('uploads/blogs/' . $otherBlog->featured_image) }}" 
                                         alt="{{ $otherBlog->title }}">
                                </div>
                                <div class="blog-info">
                                    <h5>{{ \Illuminate\Support\Str::limit($otherBlog->title, 50) }}</h5>
                                    <span class="blog-date">
                                        <i class="fa fa-calendar"></i> 
                                        {{ \Carbon\Carbon::parse($otherBlog->created_at)->format('M d, Y') }}
                                    </span>
                                </div>
                            </a>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
                </div><!-- service sidebar -->
            </div>

            <div class="col-lg-9 col-md-8">
                <div class="main-single-blog-content">
                    <div class="single-blog-post-content">
                        <img src="{{ asset('uploads/blogs/' . ($blog->featured_image ?? '') ) }}" alt="{{ $blog->title }}">
                        <p>{!! $blog->content!!}</p>
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