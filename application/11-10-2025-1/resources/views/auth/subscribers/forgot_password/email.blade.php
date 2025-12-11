@extends('layouts.app')
@section('seodetails')
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
@endsection
@section('title', "Welcome to Digital Bangladesh Legal Decisions")
@section('content')

@php
$transparent = $transparent ?? false;
@endphp
<section class="service-sec service-v2-sec service-inner-sec section-padding gray-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="login-box {{ $transparent ? 'transparent-login' : 'solid-login' }}" style="max-width: 600px;">
                @if($errors->any())
                <div style="color: red;">
                    <ul>
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                <h4>Verify your email to reset your password</h4>
                <form method="POST" action="{{ route('subscriber.password.email') }}">
                    @csrf
                    <input type="hidden" name="back" value="{{ request()->query('back') }}">

                    <div class="form-group">
                        <input type="email" name="email" class="form-control" placeholder="Email address" required>
                    </div>
                    <button type="submit" class="btn-submit">Send Mail</button>

                    <hr>

                    <div class="text-link">
                        <span style="color: black;">Remembered your password? </span>
                        <a href="{{ route('subscriber.login') }}">Login</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection