<section class="service-inner-sec single-service-sec section-padding py-5 bg-light about">
        <div class="container">
            <div class="section-title text-center">
                <h2 class="inner-title"><a href="{{ route('faq') }}">{{ $section['title'] ?? 'FAQ' }}</a></H2>
                <p class="text-muted">Find answers to the most common questions below</p>
                <div class="title-line mx-auto"></div>
            </div>
            @include('frontend._faq')
        </div>
    </section>