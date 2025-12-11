@extends('layouts.app')
@section('seodetails')
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
@endsection
@section('title', "Welcome to Digital Bangladesh Legal Decisions")

@section('content')


<section class="xs-banner-sec owl-carousel banner-slider">
    @foreach($homepageData['banners'] as $banner)
    <div class="banner-slider-item" style="background-image: url('{{ asset("uploads/banners/{$banner->image}") }}');">
        <div class="slider-table">
            <div class="slider-table-cell">
                <div class="container">
                    <div class="row align-items-center">
                        <div class="col-lg-{{ $banner->alignment_col_class }}">
                            <div class="banner-content text-{{ $banner->alignment_text }}">
                                @if($banner->title)
                                <h2>{{ $banner->title }}</h2>
                                @endif
                                @if($banner->subtitle)
                                <p>{{ $banner->subtitle }}</p>
                                @endif
                                @if($banner->link_url && $banner->link_text)
                                <div class="xs-btn-wraper">
                                    <a href="{{ $banner->link_url }}" class="xs-btn">
                                        {{ $banner->link_text }}
                                    </a>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</section>




<section class="promo-area-sec">
    <div class="container">
        <div class="promo-content-item">
            <div class="row">
                @foreach($homepageData['homePages'] as $index => $page)
                <div class="col-md-4 wow fadeInUp" data-wow-duration="1.5s" data-wow-delay="{{ 300 + ($index * 100) }}ms">
                    <div class="single-promo-content">
                        <i class="{{ $page->icon ?? 'icon-service_1' }}"></i>
                        <h3 class="xs-service-title">{{ $page->title }}</h3>
                        <p>{{ $page->short_description ?? limit_description(strip_tags($page->content), 100) }}</p>
                        <a href="{{ route('content', $page->slug) }}" class="xs-btn sm-btn">Learn More</a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>




<section class="about-sec section-padding">
    <div class="container">
        <div class="row">
            <div class="col-md-5 wow fadeInUp align-self-center" data-wow-duration="1.5s" data-wow-delay="300ms">
                <div class="about-content">

                    <h2 class="column-title">Welcome to Digital Bangladesh Legal Decisions</h2>
                    <p>{{ $homepageData['homePageContent'] ? limit_description($homepageData['homePageContent']->content, 150) : '' }}</p>
                    <a href="#" class="xs-btn sm-btn">Read More</a>
                </div>
            </div>
            <div class="col-md-7 align-self-center wow fadeInUp" data-wow-duration="1.5s" data-wow-delay="400ms">
                <div class="about-video-item">
                    <div class="about-video-img">
                        <img src="{{ asset('frontend/assets/images/about_front.jpg') }}')}}" alt="">
                        <a href="https://www.youtube.com/watch?v=Vn_FGpZJqUs" class="xs-video"><i class="fa fa-play"></i></a>
                    </div>
                    <img class="about-img2" src="{{ asset('frontend/assets/images/about_back.jpg')}}" alt="">
                </div>
            </div>
        </div>
    </div>
</section>

<section class="recent-work-sec section-padding">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="section-title-item">

                    <h2 class="section-title">
                        <span class="xs-title">Our projects</span>
                        Running Project
                    </h2>
                </div>

            </div>
        </div>
        <div class="row" id="mixcontent">
            @foreach($homepageData['projects'] as $index => $project)
            <div class="col-lg-4 mix {{ $project->category }} col-sm-6">
                <a href="#popup_{{ $index }}" class="xs-image-popup" data-effect="mfp-zoom-in">
                    <div class="single-recent-work">
                        <img src="{{ asset('uploads/projects/thumbnail/' . $project->thumbnail) }}" alt="{{ $project->title }}">
                        <div class="recet-work-footer">
                            <h3>{{ $project->title }}</h3>
                        </div>
                    </div>
                </a>
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
                            <a href="{{ route('investment.form', $project->slug) }}" class="xs-btn">INVEST NOW</a>
                        </div>

                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="load-more-btn">
                    <a href="{{ route('project') }}" class="xs-btn fill">More View</a>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="service-sec section-padding">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="section-title-item">
                    <h2 class="section-title">
                        <span class="xs-title">Service we provide</span>
                        Our Services
                    </h2>
                </div>
            </div>
        </div>
        <div class="row">
            @foreach($homepageData['services'] as $index => $service)
            <div class="col-lg-3 col-sm-6 wow fadeInUp p-0" data-wow-duration="1.5s" data-wow-delay="{{ 300 + $index * 100 }}ms">
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
                    <p>{{ limit_description(strip_tags($service->description), 100) }}</p>
                    <a href="{{ route('service.details', $service->slug) }}" class="readMore">Read more <i class="icon icon-arrow-right"></i></a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>







<section class="why-choose-us-sec section-padding section-bg">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 wow fadeInUp" data-wow-duration="1.5s" data-wow-delay="300ms">
                <div class="why-choose-img">
                    <img src="{{ asset('frontend/assets/images/why-choose-img.jpg')}}" alt="">
                </div>
            </div>
            <div class="col-lg-6 wow fadeInUp align-self-center" data-wow-duration="1.5s" data-wow-delay="400ms">
                <div class="why-choose-content">

                    <h2 class="column-title">
                        <span class="xs-title">Our special support</span>
                        Why Choose Us
                    </h2>
                    <p>
                        Fruit is their fill meat, hath abundantly place meat don't stars so and which signs third second
                        after seasons under.
                    </p>
                    <div class="row">
                        @foreach ($homepageData['whyChooses'] as $page)
                        <div class="col-sm-6">
                            <div class="single-why-choose-list">
                                <h3>
                                    <img src="{{ asset('uploads/pages/icon/' . $page->icon) }}" alt="{{ $page->title }} Icon" class="why-choose-icon">
                                    {{ $page->title }}
                                </h3>
                                <p>{{ limit_description(strip_tags($page->content), 50) }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>


<section class="funfact-sec section-padding" style="background: url(assets/images/funfact-bg.jpg);">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="section-title-item white">

                    <h2 class="section-title">
                        <span class="xs-title">Our fun fact</span>
                        10 years of Experience
                    </h2>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-6 col-md-3 wow fadeInUp" data-wow-duration="1.5s" data-wow-delay="300ms">
                <div class="single-funfact">
                    <i class="icon-fun_fact_1"></i>
                    <h3 class="funfact-title" data-counter="45">45</h3>
                    <p>Successful Projects</p>
                </div>
            </div>
            <div class="col-sm-6 col-md-3 wow fadeInUp" data-wow-duration="1.5s" data-wow-delay="400ms">
                <div class="single-funfact">
                    <i class="icon-fun-fact-02"></i>
                    <h3 class="funfact-title" data-counter="320">320</h3>
                    <p>Active Subscribers</p>
                </div>
            </div>
            <div class="col-sm-6 col-md-3 wow fadeInUp" data-wow-duration="1.5s" data-wow-delay="500ms">
                <div class="single-funfact">
                    <i class="icon-fun_fact_3"></i>
                    <h3 class="funfact-title" data-counter="500">500</h3>
                    <p>Projects Funded:</p>
                </div>
            </div>
            <div class="col-sm-6 col-md-3 wow fadeInUp" data-wow-duration="1.5s" data-wow-delay="600ms">
                <div class="single-funfact">
                    <i class="icon-fun_fact_4"></i>
                    <h3 class="funfact-title" data-counter="90">90</h3>
                    <p>Verified Partners:</p>
                </div>
            </div>
        </div>
    </div>
</section>


<section class="pricing-plan-sec section-padding">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="section-title-item">

                    <h2 class="section-title">
                        <span class="xs-title">Projects</span>
                        Upcomming Project
                    </h2>
                </div>
            </div>
        </div>

        <div class="row align-items-center pricing-plan-item">
            @foreach($homepageData['projects'] as $project)
            @php
            $profitRangeString = $project->profit_rang ?? '';

            // Check if there's a dash in the string
            if (strpos($profitRangeString, '-') !== false) {
            // Split into two parts
            [$min, $max] = explode('-', $profitRangeString, 2);
            $min = trim($min);
            $max = trim($max);
            } else {
            // No dash found — treat entire string as either min or max
            $min = trim($profitRangeString);
            $max = null;
            }

            $minValue = is_numeric($min) ? (float) $min : 0;
            $maxValue = is_numeric($max) ? (float) $max : 0;

            @endphp
            <div class="col-md-4 single-pricing-item wow fadeInUp" data-wow-duration="1.5s" data-wow-delay="{{ 300 + $loop->index * 100 }}ms">
                <div class="single-pricing-plan">
                    <img src="{{ asset('uploads/projects/thumbnail/' . $project->thumbnail) }}" alt="{{ $project->title }}" style="width: 200px; height: auto;">
                    <h3>{{ $project->title }}</h3>
                    <div class="pricing-bar"></div>
                    <p>Project Cost: Tk {{ number_format($project->goal_amount) }}</p>
                    <p>Unit Cost: Tk {{ number_format($project->unit_price) }}</p>
                    <p>Profit/Unit: {{ $minValue }}% - {{ $maxValue }}%</p>
                    <p>Duration: {{ $project->duration }} Month</p>
                    <p>Total Unit: {{ $project->total_unit }}</p>
                    <a href="{{ route('project.details', $project->slug) }}" class="xs-btn sm-btn">Book Now</a>
                </div>
            </div>
            @endforeach
        </div>

    </div>
</section>


<section class="testmonial-sec">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6 wow fadeInUp" data-wow-duration="1.5s" data-wow-delay="300ms">
                <div class="call-back-content">
                    <p class="call-contact-text">We will contact</p>
                    <h3>Get a <span>call back</span></h3>
                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <a href="javascript:void()" class="text-danger" data-dismiss="alert"><i class="fa fa-close fa-xs"></i></a>
                    </div>
                    @endif
                    <form class="call-back-form" method="POST" action="{{ route('inquiry.submit') }}" id="inquiryForm">
                        @csrf
                        <div class="form-group">
                            <input type="text" name="name" placeholder="Your Name" class="call-back-inp" required>
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <input type="email" name="email" placeholder="Email" class="call-back-inp" required>
                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <input type="tel" name="phone" placeholder="Phone no" class="call-back-inp">
                            @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <select name="subject" class="call-back-inp">
                                <option value="">Service</option>
                                @foreach($homepageData['services'] as $svc)
                                <option value="{{ $svc->name }}">{{ $svc->name }}</option>
                                @endforeach
                            </select>
                            @error('subject')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group xs-mb-40">
                            <textarea name="message" placeholder="Message" class="call-back-inp call-back-msg" required></textarea>
                            @error('message')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <button type="submit" class="xs-btn">Submit</button>
                            <label class="call-us-number">Or Call US - <span>098 2639 6257</span></label>
                        </div>
                    </form>

                </div>
            </div>
            <div class="col-md-6 wow fadeInUp" data-wow-duration="1.5s" data-wow-delay="400ms">
                <div class="owl-carousel" id="testmonial-slider">
                    @foreach ($homepageData['feedbacks'] as $feedback)
                    <div class="testmonial-content">
                        <i class="testmonial_icon icon-client_review"></i>
                        <h3 class="testmonial-title">Client review</h3>
                        <p>{{ $feedback->feedback }}</p>
                        <div class="testmonial-author">
                            @if ($feedback->client_photo && file_exists(public_path('uploads/feedbacks/' . $feedback->client_photo)))
                            <img src="{{ asset('uploads/feedbacks/' . $feedback->client_photo) }}"
                                alt="{{ $feedback->client_name }}"
                                class="rounded-circle" />
                            @else
                            <i class="fa fa-user-circle fa-3x text-primary"></i>
                            @endif

                            <h4>{{ $feedback->client_name }}@if($feedback->client_position), {{ $feedback->client_position }}@endif</h4>
                            <div class="author-rating">
                                @for ($i = 0; $i < 5; $i++)
                                    <i class="fa fa-star{{ $i < $feedback->rating ? '' : '-o' }}"></i>
                                    @endfor
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</section>


<section class="latest-news-sec section-padding">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 align-self-center wow fadeInUp" data-wow-duration="1.5s" data-wow-delay="300ms">
                <div class="latest-news-content">

                    <h2 class="column-title"> <span class="xs-title">From our blog</span>Latest News & Updates</h2>
                    <p>
                        Fruit is their fill meat hath abundantly place meat don't stars so and which signs third second
                        after seasons under.
                    </p>
                    <a href="#" class="xs-btn">View All</a>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="row">
                    @foreach($homepageData['blogs'] as $index => $blog)
                    <div class="col-md-6 wow fadeInUp" data-wow-duration="1.5s" data-wow-delay="{{ 400 + $index * 100 }}ms">
                        <div class="single-latest-news">
                            <div class="latest-news-img">
                                <a href="{{ route('blogs.show', $blog->slug) }}">
                                    <img src="{{ asset('uploads/blogs/' . $blog->featured_image) }}" alt="{{ $blog->title }}">
                                </a>
                            </div>
                            <div class="single-news-content">
                                <span class="date-info">{{ $blog->created_at->format('d M Y') }}</span>
                                <h3 class="xs-post-title">
                                    <a href="{{ route('blogs.show', $blog->slug) }}">{{ $blog->title }}</a>
                                </h3>
                                <p>{{ limit_description(strip_tags($blog->content), 100) }}</p>
                                <div class="blog-author">
                                    <img src="{{ asset('frontend/assets/images/blog-author.jpg') }}" alt="{{ $blog->author }}">
                                    <label>{{ $blog->author ?? 'Admin' }}</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

            </div>
        </div>
    </div>
</section>


<section class="call-to-action-sec">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-6 offset-lg-6 xs-padding-0 call-action-item">
                <div class="call-to-action-content">
                    <h2 class="column-title white"><span>Get start</span> your <br />first lawn</h2>
                    <p>Fruit is their fill meat hath abundantly place meat don't stars so <br />and which signs third
                        second
                        after seasons under.
                    </p>
                    <div class="call-to-btn">
                        <a href="#" class="xs-btn fill">Our Services</a>
                        <a href="#" class="xs-btn">Contact Us</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<div class="xs-map-sec">
    <div class="xs-maps-wraper">
        <div class="map">
            <iframe src="https://maps.google.com/maps?width=100&amp;height=600&amp;hl=en&amp;q=New%20York%2C%20USA+(My%20Business%20Name)&amp;ie=UTF8&amp;t=&amp;z=14&amp;iwloc=B&amp;output=embed"></iframe>
        </div>
    </div>
</div>
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