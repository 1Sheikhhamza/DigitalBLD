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
          <h2 class="banner-inner-title">Our Subscriptions</h2>
          <!-- <ul class="xs-breadcumb">
            <li><a href="{{ route('package') }}"> Home / </a> Packages</li>
          </ul> -->
        </div>
      </div>
    </div>
  </div>
</section>

<section id="pricing" class="pricing section-padding light-background">
  <div class="container">
    <div class="row gy-4">
      @foreach($packages as $package)
      <?php
      // Decode features JSON into array
      $features = json_decode($package->features_mask, true) ?: [];

      // Determine classes and badge
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
      ];
      $durationText = $durationTextMap[$package->duration_type] ?? '/ month';
      $originalPrice = $package->price;
      $discount = $package->discount;
      $type = $package->discount_type;

      $finalPrice = $originalPrice; // default

      if (!empty($discount)) {
        if ($type === '%') {
          $finalPrice = $originalPrice - ($originalPrice * ($discount / 100));
        } elseif ($type === 'Tk') {
          $finalPrice = $originalPrice - $discount;
        }
      }
      ?>

      <div class="{{ $colClass }}" data-aos="zoom-in" data-aos-delay="{{ 100 + ($loop->index * 100) }}">
        <div class="{{ $pricingItemClass }}">
          {!! $popularBadge !!}
          <h3>{{ $package->name }}</h3>
          <p class="description">{{ $package->description }}</p>
          @if($package->id != 3)
          <h4>

            {{-- If discount exists show sale text --}}
            @if(!empty($discount))
            <!-- <small style="color: #ffffffff; font-weight: bold; display:block; font-size:25px">
                            Special Sale Price
                        </small> -->

            {{-- Final Price --}}
            <sup>{{ $currency }}</sup>
            {{ number_format($finalPrice, 0) }}

            {{-- Duration Text --}}
            <span> {{ $durationText }} </span>
            <br>

            {{-- Original price line-through --}}
            <span style="text-decoration: line-through; color: #6c757d;">
              {{ $currency }} {{ number_format($originalPrice, 0) }}
            </span>

            {{-- Discount label --}}
            <span style="color: #28a745; font-weight:bold; margin-left:10px">
              -{{ $discount }}{{ $type }}
            </span>

            @else
            {{-- No discount: normal price --}}
            <sup>{{ $currency }}</sup>{{ number_format($originalPrice, 0) }}
            <span> {{ $durationText }}</span>
            @endif

          </h4>
          @endif
          <div class="pricing-content">
            <ul>
              @foreach($features as $feature)
              @php
              // If feature string starts with "na:" consider it as not available feature, else available
              $isAvailable = true;
              $featureText = $feature;
              if (stripos($feature, 'na:') === 0) {
              $isAvailable = false;
              $featureText = substr($feature, 3); // remove 'na:' prefix
              }
              @endphp
              <li class="{{ $isAvailable ? '' : 'na' }}">
                @if($isAvailable)
                <span class="number-circle small"><i class="bi bi-check"></i></span>
                @else
                <i class="bi bi-x"></i>
                @endif
                <span>{{ $featureText }}</span>
              </li>
              @endforeach
            </ul>
            @if($package->id != 3)
            <a href="{{ route('subscriber.subscription.form', $package->id) }}" class="cta-btn">{{ $buttonText }}</a>
            @else
            <a href="{{ route('content', 'contact') }}" class="cta-btn">{{ $buttonText }}</a>
            @endif
          </div>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</section>


@endsection
@section('footer')

@parent
@endsection

@push('scripts')
@endpush