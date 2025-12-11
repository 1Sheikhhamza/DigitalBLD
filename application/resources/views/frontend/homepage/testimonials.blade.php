<section id="testimonials" class="testimonials section">

        <section id="testimonials" class="section about">
            <div class="container">
                <h2 class="inner-title"><a href="#">{{ $section['title'] ?? 'Testimonials' }}</a></h2>
                <div class="row gy-4">
                    @foreach($homepageData['feedbacks'] as $feedback)
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
                                <span>{{ limit_description($feedback->feedback ? strip_tags($feedback->feedback) : '', 150) }}</span>
                                <i class="bi bi-quote quote-icon-right"></i>
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>


    </section>