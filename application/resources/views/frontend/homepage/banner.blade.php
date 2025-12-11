<section id="hero" class="hero position-relative">
    <div id="hero-carousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="5000">

        {{-- Carousel banners --}}
        @foreach($homepageData['banners'] as $key => $banner)
        <div class="carousel-item {{ $key === 0 ? 'active' : '' }}">
            <img src="{{ asset('uploads/banners/'.$banner->image) }}" alt="">
            <div class="image-overlay"></div>
            <div class="container d-flex align-items-center">
                <div class="row w-100">
                    <div class="@auth('subscriber') col-lg-12 @else col-lg-6 @endauth d-flex flex-column justify-content-center">
                        <div class="slider-text" style="@auth('subscriber') max-width:95% @endauth">
                            <h2>{{ $banner->title }}</h2>
                            <p>{!! $banner->description !!}</p>
                            @if(!empty($banner->button1_text) && !empty($banner->button1_url))
                                <a href="{{ $banner->button1_url }}" class="btn btn-primary me-2" style="background-color: #003092; border-color: #003092;">
                                    {{ $banner->button1_text }}
                                </a>
                            @endif
                            @if(!empty($banner->button2_text) && !empty($banner->button2_url))
                                <a href="{{ $banner->button2_url }}" class="btn btn-outline-light">
                                    {{ $banner->button2_text }}
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach

        {{-- Carousel controls --}}
        <a class="carousel-control-prev" href="#hero-carousel" role="button" data-bs-slide="prev" style="z-index: 100;">
            <span class="carousel-control-prev-icon bi bi-chevron-left" aria-hidden="true"></span>
        </a>
        <a class="carousel-control-next" href="#hero-carousel" role="button" data-bs-slide="next" style="z-index: 100;">
            <span class="carousel-control-next-icon bi bi-chevron-right" aria-hidden="true"></span>
        </a>
    </div>

    @guest('subscriber')
    <div class="login-area position-absolute top-50 end-0 translate-middle-y me-lg-5 me-xl-7 d-none d-sm-none d-md-none d-lg-block" 
         style="width: 350px !important; margin-right: 180px !important; z-index: 10;">
        @include('auth.subscribers._login', ['transparent' => true])
    </div>
    @endguest
</section>

