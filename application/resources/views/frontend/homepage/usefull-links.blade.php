<section id="clients" class="clients section about">

        <div class="container section-title" data-aos="fade-up">
            <h2 class="inner-title"><a href="#">{{ $section['title'] ?? 'Useful Links' }}</a></h2>
        </div>

        <div class="container" data-aos="fade-up" data-aos-delay="100">

            <div class="row g-0 clients-wrap">

                @foreach($homepageData['usefulllinks'] as $link)
                <div class="col-xl-2 col-md-3 client-logo">
                    <a href="{{ $link->website }}" target="_blank" rel="noopener noreferrer">
                        <img src="{{ asset('uploads/link/' . $link->logo) }}" class="img-fluid" alt="{{ $link->name ?? 'Useful Link' }}">
                    </a>
                </div>
                @endforeach

            </div>

        </div>

    </section>