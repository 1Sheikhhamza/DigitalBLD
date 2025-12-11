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
                <div class="sub-text">Digital BLD</div>
                <p class="register-steps">Register to BLD platform by following 3 steps</p>
            </div>

            <!-- Progress Indicator -->
            <div class="progress-indicator">
                <div class="progress-step active" data-step="1">
                    <div class="progress-step-number">1</div>
                    <div class="progress-step-label">User Information</div>
                </div>
                <div class="progress-step" data-step="2">
                    <div class="progress-step-number">2</div>
                    <div class="progress-step-label">Verification</div>
                </div>
                <div class="progress-step" data-step="3">
                    <div class="progress-step-number">3</div>
                    <div class="progress-step-label">Password Set</div>
                </div>
                <div class="progress-line">
                    <div class="progress-line-fill" style="width: 0%;"></div>
                </div>
            </div>

            <div class="form-step-content">
                <!-- Form Content (Dynamically switch steps) -->
                <form id="registrationForm" method="POST" action="{{ route('subscriber.sendOtp') }}">
                    @csrf
                    <!-- Step 1: User Information -->
                    <div id="step1Content">
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="firstName" class="custom-form-label">First Name <span class="required">*</span></label>
                                <input type="text" class="form-control custom-form-control @error('first_name') is-invalid @enderror" name="first_name" id="firstName" value="{{ old('first_name') }}">
                                @error('first_name')
                                <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="lastName" class="custom-form-label">Last Name <span class="required">*</span></label>
                                <input type="text" class="form-control custom-form-control @error('last_name') is-invalid @enderror" name="last_name" id="lastName" value="{{ old('last_name') }}">
                                @error('last_name')
                                <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="dateOfBirth" class="custom-form-label">Date of Birth</label>
                                <div class="input-group">
                                    <input type="text" class="form-control custom-form-control datepicker @error('dob') is-invalid @enderror" name="dob" id="dateOfBirth" value="{{ old('dob') }}">
                                    <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                                </div>
                                @error('dob')
                                <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="registeredAs" class="custom-form-label">Registered as  <span class="required">*</span></label>
                                <select class="form-select custom-form-select @error('registration_as') is-invalid @enderror" id="registeredAs" name="registration_as">
                                    <option {{ old('registration_as') == 'Judiciary Person' ? 'selected' : '' }} value="Judiciary Person">Judiciary Person</option>
                                    <option {{ old('registration_as') == 'Lawyer' ? 'selected' : '' }} value="Lawyer">Lawyer</option>
                                    <option {{ old('registration_as') == 'Student' ? 'selected' : '' }} value="Student">Student</option>
                                    <option {{ old('registration_as') == 'Other' ? 'selected' : '' }} value="Other">Other</option>
                                </select>
                                @error('registration_as')
                                <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="mobileNumber" class="custom-form-label">Mobile Number <span class="required">*</span></label>

                                <input type="number"
                                    name="mobile"
                                    class="form-control custom-form-control @error('mobile') is-invalid @enderror"
                                    pattern="\d{11,13}"
                                    minlength="11"
                                    maxlength="13"
                                    required
                                    placeholder="Enter 11-digit mobile number"
                                    id="mobileNumber" value="{{ old('mobile') }}">


                                @error('mobile')
                                <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="emailAddress" class="custom-form-label">Email Address <span class="required">*</span></label>
                                <input type="email" class="form-control custom-form-control @error('email') is-invalid @enderror" name="email" id="emailAddress" value="{{ old('email') }}">
                                @error('email')
                                <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                        </div>
                        <div class="d-flex justify-content-center">
                            <button type="submit" class="btn btn-next">Next</button>
                        </div>
                    </div>

                </form>

                @include('auth.subscribers._register-footer')
            </div>
        </div>
    </div>

</section>


@endsection
@section('footer')
@parent
@endsection