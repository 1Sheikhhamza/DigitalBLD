@extends('layouts.app')
@section('seodetails')
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
@endsection
@section('title', "Welcome to Digital Bangladesh Legal Decisions")

@section('content')


<section class="banner-inner-sec" style="background-image: url('{{ asset('uploads/pages/image/' . ($contents['content']->image ?? '') ) }}')">
  <div class="banner-table">
    <div class="banner-table-cell">
      <div class="container">
        <div class="banner-inner-content">
          <h2 class="banner-inner-title">{{ $contents['content']->title ?? '' }}</h2>
          <ul class="xs-breadcumb">
            <li><a href="index-2.html"> Home / {{ $contents['parentMenu'] ? $contents['parentMenu'].' / ' : ''}}</a> {{ $contents['content']->title }}</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="service-inner-sec single-service-sec section-padding">
  <div class="container">
    <div class="row">
      <!-- <div class="col-lg-3 col-md-4">
        <div class="service-sidebar">
          <div class="widgets">
            <h3 class="widget-title"><span>{{$contents['parentMenu']}}</span> Pages</h3>
            <ul class="services-link-item">
              @if($contents['othersPages']!='' && count($contents['othersPages']) > 0)
              @foreach($contents['othersPages'] as $otherPage)
              @if($contents['parentMenu'])
              <li class="{{ $currentSlug == $otherPage->slug ? 'active' : '' }}">
                <a href="{{ url('content/' . $otherPage->parent->slug . '/' . $otherPage->slug) }}">
                  {{ $otherPage->title }}
                </a></li>
              @else
              <li class="{{ $currentSlug == $otherPage->slug ? 'active' : '' }}">
                <a href="{{ url('content/' . $otherPage->slug) }}">
                  {{ $otherPage->title }}
                </a></li>
              @endif
              @endforeach
              @endif

              <li><a href="{{ route('faq') }}">FAQ</a></li>
            </ul>
          </div>

          <div class="widgets">
            <h3 class="widget-title"><span>Lets</span> Subscribe</h3>
            <ul class="brochures-list">
              <li><a href="{{ route('subscriber.register') }}"><i class="fa fa-user-plus"></i>Sign Up</a></li>
              <li><a href="{{ route('subscriber.login') }}"><i class="fa fa-sign-in"></i>Sign In</a></li>
            </ul>
          </div>

          

          
        </div>
      </div> -->
      <div class="col-lg-12 col-md-8">
        <div class="main-single-service-content">
          <img src="assets/images/services/single-post-img.jpg" alt="">
          <p>{!! $contents['content']->content ?? '' !!}</p>
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
@if(session('scroll_to'))
<script>
  if ({
      {
        session() - > has('scroll_to') ? 'true' : 'false'
      }
    }) {
    document.addEventListener('DOMContentLoaded', () => {
      const selector = "{{ session('scroll_to') }}";
      const el = document.querySelector(selector);
      if (el) {
        const headerOffset = document.querySelector('header')?.offsetHeight || 0;
        const elementRect = el.getBoundingClientRect().top;
        const offsetPosition = elementRect + window.pageYOffset - headerOffset - 10; // 10px extra spacing

        window.scrollTo({
          top: offsetPosition,
          behavior: 'smooth'
        });

        el.classList.add('flash-highlight');
        setTimeout(() => el.classList.remove('flash-highlight'), 2000);
      }
    });
  }
</script>
@endif
@endpush