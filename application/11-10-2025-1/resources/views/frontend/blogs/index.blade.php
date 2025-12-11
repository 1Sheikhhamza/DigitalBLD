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
          <h2 class="banner-inner-title">Our Blogs</h2>
          <ul class="xs-breadcumb">
            <li><a href="{{ route('blog') }}"> Home / </a> Blogs</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="xs-blog-sec section-padding">
  <div class="container">
    <div class="row">
      <div class="col-lg-12">
        <div class="blog-content-item">
          <div class="row">
            @foreach($blogs as $index => $blog)
            <div class="col-sm-6 col-12">
              <div class="single-blog-item">
                <div class="blog-post-img">
                  <a href="{{ route('blog.details', $blog->slug) }}">
                    <img src="{{ asset('uploads/blogs/' . $blog->featured_image) }}" alt="{{ $blog->title }}">
                  </a>
                  <div class="blog-date-info">
                    <label>{{ $blog->created_at->format('d') }}</label>
                    <span>{{ $blog->created_at->format('M') }}</span>
                  </div>

                </div>
                <div class="blog-meta">
                  <ul>
                    <li class="author">
                      <a href="{{ route('blog.details', $blog->slug) }}">
                        <img src="{{ asset('frontend/assets/img/default-user.png') }}" alt="">
                        by <span>{{ $blog->author }}</span>
                      </a>
                    </li>
                    <li>
                      <a href="#">
                        <i class="icon icon-calendar-full"></i>
                        {{ $blog->created_at->format('d M, Y') }}
                      </a>
                    </li>
                  </ul>
                </div>
                <h3 class="xs-blog-title">
                  <a href="{{ route('blog.details', $blog->slug) }}">
                    {{ $blog->title }}
                  </a>
                </h3>
                <p>{!! Str::limit(strip_tags($blog->content), 300) !!}</p>
                <div class="read-more-btn">
                  <a href="{{ route('blog.details', $blog->slug) }}" class="xs-btn sm-btn">Read More</a>
                </div>
              </div>
            </div>
            @endforeach
          </div>
          
          <div class="pagination-wraper">
            {{ $blogs->links('pagination::bootstrap-4') }}
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