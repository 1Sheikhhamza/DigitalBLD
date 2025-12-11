<section id="pricing" class="pricing section-padding light-background">
        <div class="container section-title" data-aos="fade-up">
            <h2>{{ $section['title'] }}</h2>
        </div>

        <div class="container">
            <div class="row gy-4">
                @foreach($homepageData['packages'] as $package)
                @php
                $colClass = 'col-lg-4';
                $pricingItemClass = 'pricing-item';
                $popularBadge = '';

                if ($package->is_featured) {
                $colClass .= ' active';
                $pricingItemClass .= ' featured';
                $popularBadge = '<p class="popular">' . ($package->highlight_badge ?? 'Popular') . '</p>';
                }

                $currency = $package->currency ?? '৳';
                $buttonText = $package->button_text ?? 'Sign up Now';
                $durationTextMap = [
                'monthly' => '/ month',
                'quarterly' => '/ quarter',
                'half_yearly' => '/ half year',
                'yearly' => '/ year',
                'lifetime' => '/ lifetime',
                ];
                $durationText = $durationTextMap[$package->duration_type] ?? '/ month';

                // Load features for this package
                $features = $package->features ?? collect();
                @endphp

                <div class="{{ $colClass }}" data-aos="zoom-in" data-aos-delay="{{ 100 + ($loop->index * 100) }}">
                    <div class="{{ $pricingItemClass }}">
                        {!! $popularBadge !!}
                        <h3>{{ $package->name }}</h3>
                        <p class="description">{{ $package->description }}</p>
                        <h4>
                            <sup>{{ $currency }}</sup>{{ number_format($package->price, 0) }}
                            <span>{{ $durationText }}</span>
                        </h4>
                        <div class="pricing-content">
                            <ul>
                                @foreach($features as $feature)
                                @php
                                // Check if feature is active
                                $isAvailable = $feature->status;
                                @endphp
                                <li class="{{ $isAvailable ? '' : 'na' }}">
                                    @if($isAvailable)
                                    <span class="number-circle small"><i class="bi bi-check"></i></span>
                                    @else
                                    <i class="bi bi-x"></i>
                                    @endif
                                    <span>{{ $feature->name }}</span>
                                </li>
                                @endforeach
                            </ul>
                            <a href="{{ route('subscriber.subscription.form', $package->id) }}" class="cta-btn">
                                {{ $buttonText }}
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>