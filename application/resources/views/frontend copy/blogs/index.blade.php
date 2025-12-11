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
      <div class="col-lg-8">
        <div class="blog-content-item">

          @foreach($blogs as $index => $blog)
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
                    <img src="{{ asset('frontend/assets/images/admin.jpg') }}" alt="">
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
            <p>{{ limit_description(strip_tags($blog->content), 300) }}</p>
            <div class="read-more-btn">
              <a href="{{ route('blog.details', $blog->slug) }}" class="xs-btn sm-btn">Read More</a>
            </div>
          </div>
          @endforeach

          <ul class="pagination justify-content-center">
            <li class="page-item">
              <a href="#"><i class="fa fa-angle-left"></i></a>
            </li>
            <li class="page-item active"><a href="#">1</a></li>
            <li class="page-item">
              <a href="#">2</a>
            </li>
            <li class="page-item"><a href="#">3</a></li>
            <li class="page-item disabled"><a href="#">4</a></li>
            <li class="page-item">
              <a href="#"><i class="fa fa-angle-right"></i></a>
            </li>
          </ul>
        </div>
      </div>
      <div class="col-lg-4">
        <div class="sidebar-widgets">
          <div class="widget widget-search">
            <form action="#" class="xs-serach" method="post">
              <div class="input-group">
                <input type="search" name="search" placeholder="Type Keyword" autocomplete="off">
                <button class="search-btn"><i class="icon icon-search"></i></button>
              </div>
            </form>
          </div><!-- .widget end -->
          <div class="widget">
            <h4 class="widget-title">
              <span class="light-text">Trending</span> posts
            </h4>
            <div class="widget-posts">
              <div class="widget-post media">
                <img src="assets/images/sidebar/popular-post-img-1.jpg" class="d-flex" alt="popular post image" draggable="false">
                <div class="media-body">
                  <span class="post-meta-date">
                    <a href="#"> Posted by Miller at 22 jan</a>
                  </span>
                  <h5 class="entry-title">
                    <a href="{{ route('blog.details', $blog->slug) }}">After moved created for bit likeness </a>
                  </h5>
                </div>
              </div><!-- .widget-post END -->
              <div class="widget-post media">
                <img src="assets/images/sidebar/popular-post-img-2.jpg" class="d-flex" alt="popular post image" draggable="false">
                <div class="media-body">
                  <span class="post-meta-date">
                    <a href="#"> Posted by Miller at 22 jan</a>
                  </span>
                  <h5 class="entry-title">
                    <a href="{{ route('blog.details', $blog->slug) }}">Creat In appear was, the you lesser.</a>
                  </h5>
                </div>
              </div><!-- .widget-post END -->
              <div class="widget-post media">
                <img src="assets/images/sidebar/popular-post-img-3.jpg" class="d-flex" alt="popular post image" draggable="false">
                <div class="media-body">
                  <span class="post-meta-date">
                    <a href="#"> Posted by Miller at 22 jan</a>
                  </span>
                  <h5 class="entry-title">
                    <a href="{{ route('blog.details', $blog->slug) }}">Fowl bring given living moved good </a>
                  </h5>
                </div>
              </div><!-- .widget-post END -->
              <div class="widget-post media">
                <img src="assets/images/sidebar/popular-post-img-4.jpg" class="d-flex" alt="popular post image" draggable="false">
                <div class="media-body">
                  <span class="post-meta-date">
                    <a href="#"> Posted by Miller at 22 jan</a>
                  </span>
                  <h5 class="entry-title">
                    <a href="{{ route('blog.details', $blog->slug) }}">Moved created for bit likeness flow</a>
                  </h5>
                </div>
              </div><!-- .widget-post END -->
            </div><!-- .widget-posts END -->
          </div><!-- .widget end -->


          <div class="widget widget-tag">
            <h4 class="widget-title"><span class="light-text">Populer</span> tags</h4>
            <div class="tag-lists">
              <a href="#">Garden</a>
              <a href="#">Gardening</a>
              <a href="#">Planting</a>
              <a href="#">Real gardening</a>
              <a href="#">Grass trimming</a>
              <a href="#">Snow remove</a>
              <a href="#">Garden care</a>
              <a href="#">Lawn</a>
            </div>
          </div><!-- .widget end -->
        </div><!-- .sidebar-widgets end -->
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