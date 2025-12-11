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
          <h2 class="banner-inner-title">Testimonials</h2>
          <ul class="xs-breadcumb">
            <li><a href="{{ route('clientfeedback') }}"> Home / </a> Testimonials</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</section>

<section id="testimonials" class="testimonials section">

    <section id="testimonials" class="section">
        <div class="container">
            <div class="row gy-4">
                @foreach($feedbacks as $feedback)
                <div class="col-lg-4" data-aos="fade-up" data-aos-delay="{{ $loop->iteration * 100 }}">
                    <div class="testimonial-item">
                        <img src="{{ asset('uploads/feedback/image/'.$feedback->client_photo) }}" class="testimonial-img" alt="{{ $feedback->name ?? 'Client' }}">
                        <h3>{{ $feedback->client_name ?? '' }}</h3>
                        <h4>{{ $feedback->client_position ?? '' }}</h4>
                        <div class="stars">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <=$feedback->rating)
                                <i class="bi bi-star-fill"></i>
                                @else
                                <i class="bi bi-star"></i>
                                @endif
                                @endfor
                        </div>

                        <p>
                            <i class="bi bi-quote quote-icon-left"></i>
                            <span>{{ limit_description(strip_tags($feedback->feedback), 150) }}</span>
                            <i class="bi bi-quote quote-icon-right"></i>
                        </p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>


</section>

@endsection
@section('footer')

@parent
@endsection

@push('scripts')
@endpush