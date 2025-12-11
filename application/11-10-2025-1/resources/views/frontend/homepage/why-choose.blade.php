<section id="about" class="how-it-works section about section">
        <div class="container">
            <div class="row position-relative">
                @php
                $firstChoose = $homepageData['whyChooses']->first();
                $otherChooses = $homepageData['whyChooses']->skip(1);
                @endphp

                <div class="col-lg-7">
                    <h2 class="inner-title"><a href="{{ getPageUrl($firstChoose) }}">{{ $firstChoose->title ?? '' }}</a></h2>
                    <p>{!! limit_description($firstChoose->content ? $firstChoose->content : '', 200) ?? '' !!}</p>

                    <div class="col-lg-12 about-img">
                        @if($firstChoose->image)
                        <img src="{{ asset('uploads/pages/image/'.$firstChoose->image) }}" style="max-height: 300px;">
                        @else
                        <img src="{{ asset('frontend/assets/img/about.jpg') }}" style="max-height: 300px;">
                        @endif
                    </div>
                </div>

                <div class="col-lg-5 about-img">
                    <div class="row">
                        @foreach($otherChooses as $choose)
                        <div class="col-sm-6 col-12">
                            <div class="works-item card-radius {{ $loop->first ? 'active' : '' }}">
                                <a href="{{ getPageUrl($choose) }}">
                                    @if(!empty($choose->icon))
                                    <img src="{{ asset('uploads/pages/icon/'.$choose->icon) }}">
                                    @else
                                    <div class="number-circle large {{ $loop->first ? 'disable' : '' }}"><i class="fa fa-balance-scale"></i></div>
                                    @endif
                                    <h3 style="min-height: 50px;">{{ $choose->title ?? '' }}</h3>
                                    <p>{{ limit_description(html_entity_decode($choose->content ? $choose->content : '', ENT_QUOTES | ENT_HTML5), 100) }}</p>
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

            </div>

        </div>
    </section>