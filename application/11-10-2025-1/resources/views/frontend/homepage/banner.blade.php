<section id="hero" class="hero">
        <div id="hero-carousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="5000">

            @foreach($homepageData['banners'] as $key => $banner)
            <div class="carousel-item {{ $key === 0 ? 'active' : '' }}">
                <img src="{{ asset('uploads/banners/'.$banner->image) }}" alt="">
                <div class="image-overlay"></div>
                <div class="container d-flex align-items-center">
                    <div class="row w-100">

                        <div class="@auth('subscriber') col-lg-12 @else col-lg-6 @endauth  d-flex flex-column justify-content-center">
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

                        @guest('subscriber')
                        <div class="col-lg-6 d-flex justify-content-end align-items-center">
                            <div class="form-area w-100 d-none d-sm-none d-md-none d-lg-block" style="max-width: 400px; margin-right:30px">
                                @include('auth.subscribers._login', ['transparent' => true])
                            </div>
                        </div>
                        @endguest
                    </div>
                </div>
            </div>
            @endforeach

            <a class="carousel-control-prev" href="#hero-carousel" role="button" data-bs-slide="prev">
                <span class="carousel-control-prev-icon bi bi-chevron-left" aria-hidden="true"></span>
            </a>
            <a class="carousel-control-next" href="#hero-carousel" role="button" data-bs-slide="next">
                <span class="carousel-control-next-icon bi bi-chevron-right" aria-hidden="true"></span>
            </a>
        </div>
    </section>

    @guest('subscriber')
    <div class="container mt-5 d-block d-sm-block d-md-block d-lg-none">
        <div class="row justify-content-end">

            <div class="col-lg-4 text-end">
                @include('auth.subscribers._login', ['transparent' => false])
            </div>
        </div>
    </div>
    @endguest
    