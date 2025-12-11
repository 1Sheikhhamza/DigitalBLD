@extends('layouts.app')
@section('seodetails')
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
@endsection
@section('title', "Welcome to Digital Bangladesh Legal Decisions")

@section('content')

<section class="service-sec service-v2-sec service-inner-sec section-padding gray-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                @include('auth.subscribers._login', ['transparent' => false])
            </div>
        </div>
    </div>
</section>
@endsection