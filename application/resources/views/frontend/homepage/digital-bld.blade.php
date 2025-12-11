<section id="services" class="services section light-background">
    <div class="container">
        <div class="row gy-4 about">
            <h2 class="inner-title"><a href="#">{{ $section['title'] ?? 'A Preview of Digital BLD' }}</a></h2>
            @foreach($homepageData['legalDecision'] as $decision)
            @php
            // Take judgment text
            $judgment = $decision->judgment ?? '';

            // Match Judge's name at start until ":"
            $judgmentFormatted = preg_replace( '/^([^:]+:)/', // everything before first colon
            '<strong>$1</strong>',
            e(limit_description($judgment, 300)) // limit to safe length
            );
            @endphp

            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                <div class="service-item item-cyan position-relative p-3 rounded shadow-sm d-flex flex-column h-100">
                    <div class="icon mb-2 text-center">
                        <img src="{{ asset('frontend/assets/img/book.png') }}" alt="Case Icon" class="w-12 h-12">
                    </div>

                    <a href="{{ route('subscriber.login') }}"
                        class="stretched-link text-decoration-none text-dark d-flex flex-column flex-grow-1">

                        <!-- Parties (max 3 lines, bold) -->
                        <div class="clamp-3 mb-2 fw-bold">
                            {!! $decision->parties !!}
                        </div>

                        <!-- Case No (max 1 line, lighter text) -->
                        <div class="text-sm text-gray-500 clamp-1 text-center mb-2">
                            {!! $decision->case_no !!}
                        </div>

                        <!-- Judgment (max 4 lines, fills remaining space) -->
                        <p class="text-sm text-gray-700 clamp-4 flex-grow-1 mb-0 text-justify">
                            <!-- {!! $judgmentFormatted !!} -->
                            {!! html_entity_decode($judgmentFormatted, ENT_QUOTES | ENT_HTML5) !!}
                        </p>

                    </a>
                </div>

            </div>

            @endforeach
        </div>
    </div>
</section>