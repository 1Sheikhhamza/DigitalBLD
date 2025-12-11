@extends('layouts.app')
@section('seodetails')
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
@endsection
@section('title', "Welcome to Digital Bangladesh Legal Decisions")

@section('content')
<section class="service-sec service-v2-sec service-inner-sec section-padding">
    <div class="container">
        <div class="registration-container">
            <div class="bld-header">
                <div class="logo-text">BLD</div>
                <div class="sub-text">Digital Bangladesh Legal Decisions</div>
                <p class="register-steps">Register to BLD platform by following 3 steps</p>
            </div>

            <!-- Progress Indicator -->
            <div class="progress-indicator">
                <div class="progress-step active" data-step="1">
                    <div class="progress-step-number">1</div>
                    <div class="progress-step-label">User Information</div>
                </div>
                <div class="progress-step active" data-step="2">
                    <div class="progress-step-number">2</div>
                    <div class="progress-step-label">Verification</div>
                </div>
                <div class="progress-step active" data-step="3">
                    <div class="progress-step-number">3</div>
                    <div class="progress-step-label">Password Set</div>
                </div>
                <div class="progress-line">
                    <div class="progress-line-fill" style="width: 0%;"></div>
                </div>
            </div>

            <div class="form-step-content">
                <form id="registrationForm" method="POST" action="{{ route('subscriber.completeRegistration') }}">
                    @csrf
                    <div id="step3Content">
                        <div class="row g-3 mb-4">
                            <div class="col-md-7">
                                <div class="col-sm-12 mb-4">
                                    <div class="password-input-group">
                                        <input type="password" class="form-control custom-form-control @error('password') is-invalid @enderror" name="password" id="password" placeholder="New password" required>
                                        <button type="button" class="password-toggle" id="togglePassword">
                                            <i class="bi bi-eye-slash"></i>
                                        </button>
                                    </div>
                                    @error('password')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-sm-12">
                                    <div class="password-input-group">
                                        <input type="password" class="form-control custom-form-control @error('password_confirm') is-invalid @enderror" name="password_confirmation" id="confirmPassword" placeholder="Confirm new password" required>
                                        <button type="button" class="password-toggle" id="toggleConfirmPassword">
                                            <i class="bi bi-eye-slash"></i>
                                        </button>
                                    </div>
                                    @error('password_confirm')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-5">
                                <ul class="password-rules-list" id="passwordRules">
                                    <li id="ruleLength"><i class="bi bi-exclamation-circle-fill"></i> Must be at least 8 characters long</li>
                                    <li id="ruleLowercase"><i class="bi bi-exclamation-circle-fill"></i> Must contain a lowercase letter</li>
                                    <li id="ruleCapital"><i class="bi bi-exclamation-circle-fill"></i> Must contain a capital letter</li>
                                    <li id="ruleNumber"><i class="bi bi-exclamation-circle-fill"></i> Must contain a number</li>
                                    <li id="ruleSpecial"><i class="bi bi-exclamation-circle-fill"></i> Must contain a special Character</li>
                                </ul>
                            </div>
                        </div>

                        <div class="d-flex justify-content-center mt-4">
                            <button type="submit" class="btn btn-next">Submit</button>
                        </div>
                    </div>
                </form>



                @include('auth.subscribers._register-footer')
            </div>
        </div>
    </div>

</section>


@endsection
@push('scripts')
<script>
    document.getElementById('togglePassword').addEventListener('click', function() {
        const password = document.getElementById('password');
        password.type = password.type === 'password' ? 'text' : 'password';
        this.querySelector('i').classList.toggle('bi-eye');
        this.querySelector('i').classList.toggle('bi-eye-slash');
    });

    document.getElementById('toggleConfirmPassword').addEventListener('click', function() {
        const confirmPassword = document.getElementById('confirmPassword');
        confirmPassword.type = confirmPassword.type === 'password' ? 'text' : 'password';
        this.querySelector('i').classList.toggle('bi-eye');
        this.querySelector('i').classList.toggle('bi-eye-slash');
    });

    const passwordInput = document.getElementById('password');
    const rulesList = document.getElementById('passwordRules');
    const ruleLength = document.getElementById('ruleLength');
    const ruleLowercase = document.getElementById('ruleLowercase');
    const ruleCapital = document.getElementById('ruleCapital');
    const ruleSpecial = document.getElementById('ruleSpecial');

    passwordInput.addEventListener('input', function() {
        rulesList.classList.remove('d-none');

        const val = passwordInput.value;

        ruleLength.classList.toggle('text-success', val.length >= 8); // Minimum 8 characters
        ruleLowercase.classList.toggle('text-success', /[a-z]/.test(val)); // At least one lowercase
        ruleCapital.classList.toggle('text-success', /[A-Z]/.test(val)); // At least one uppercase
        ruleNumber.classList.toggle('text-success', /[0-9]/.test(val)); // At least one number
        ruleSpecial.classList.toggle('text-success', /[!@#$%^&*(),.?":{}|<>]/.test(val)); // At least one special char

    });
</script>

@endpush