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
            <div class="login-box {{ $transparent ? 'transparent-login' : 'solid-login' }}" style="max-width: 450px;">
                    <p class="text-center mb-4">Enter the 6-digit that we have sent via the email address</p>

                    <div class="otp-timer">
                        <i class="bi bi-clock-fill"></i>
                        <span id="timerDisplay">00:00</span>
                    </div>
                    <form id="registrationForm" method="POST" action="{{ route('subscriber.otp.check') }}">
                        @csrf
                        <div class="otp-input-group">
                            <input type="text" class="form-control otp-input" maxlength="1" inputmode="numeric">
                            <input type="text" class="form-control otp-input" maxlength="1" inputmode="numeric">
                            <input type="text" class="form-control otp-input" maxlength="1" inputmode="numeric">
                            <input type="text" class="form-control otp-input" maxlength="1" inputmode="numeric">
                            <input type="text" class="form-control otp-input" maxlength="1" inputmode="numeric">
                            <input type="text" class="form-control otp-input" maxlength="1" inputmode="numeric">
                        </div>
                        @if($errors->any())
                        <div style="color: red; text-align:center">
                            <ul>
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                        <!-- Hidden field to combine OTP -->
                        <input type="hidden" name="otp" id="combinedOtp">

                        <div class="d-flex justify-content-center mt-3">
                            <button type="submit" class="btn btn-next">Next</button>
                        </div>
                    </form>
                    <div class="text-center mt-3 d-none" id="resendOtpArea">
                        <form action="{{ route('subscriber.otp.resend') }}" method="POST" id="resendOtpForm">
                            @csrf
                            <input type="hidden" name="redirect" value="subscriber.otp.verify">
                            <input type="hidden" name="type" value="forgot-password">
                            <div id="resendOtpBtn" class="see-more d-block mb-2">
                                Didn`t receive the code?
                                <button type="submit" id="resendBtn" class="text-primary"
                                    style="background:none; border:none; font-weight:bold">
                                    Resend OTP
                                </button>
                            </div>
                        </form>
                    </div>
            </div>

        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
    // console.log('times', {{ $remainingSeconds}})
    let timeLeft = {{ $remainingSeconds ?? 300 }};

    function otpTimes() {
        function formatTime(seconds) {
            const m = String(Math.floor(seconds / 60)).padStart(2, '0');
            const s = String(seconds % 60).padStart(2, '0');
            return `${m}:${s}`;
        }

        const timerDisplay = document.getElementById('timerDisplay');
        const resendOtpArea = document.getElementById('resendOtpArea');

        if (timeLeft <= 0) {
            timerDisplay.textContent = '00:00';
        } else {
            timerDisplay.textContent = formatTime(timeLeft);
        }

        const countdown = setInterval(() => {
            timeLeft--;

            if (timeLeft <= 0) {
                clearInterval(countdown);
                timerDisplay.textContent = "Expired";
                if (resendOtpArea) {
                    resendOtpArea.classList.remove('d-none');
                    resendOtpArea.classList.add('d-block');
                }
            } else {
                timerDisplay.textContent = formatTime(timeLeft);
            }
        }, 1000);
    }

    let resendFunc = () => {
        const form = document.getElementById('resendOtpForm');
        const button = document.getElementById('resendBtn');

        form.addEventListener('submit', function() {
            button.disabled = true;
            button.innerText = 'Sending...'; // Optional
        });
    }

    document.addEventListener("DOMContentLoaded", function() {

        otpTimes()
        resendFunc()
        const inputs = document.querySelectorAll(".otp-input");
        const hiddenInput = document.getElementById("combinedOtp");

        inputs.forEach((input, index) => {
            input.addEventListener("input", function(e) {
                const value = input.value.trim();

                // Handle paste (fallback for some browsers)
                if (e.inputType === "insertFromPaste" || value.length > 1) {
                    const pasted = value;
                    if (pasted.length === 6 && /^\d+$/.test(pasted)) {
                        pasted.split("").forEach((char, i) => {
                            if (inputs[i]) inputs[i].value = char;
                        });
                        updateHiddenOtp();
                        inputs[5].focus();
                    }
                    return;
                }

                // Move to next input
                if (value.length === 1 && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }

                updateHiddenOtp();
            });

            input.addEventListener("keydown", function(e) {
                if (e.key === "Backspace" && input.value === "" && index > 0) {
                    inputs[index - 1].focus();
                }
            });

            // Better paste handling for all browsers
            input.addEventListener("paste", function(e) {
                e.preventDefault();
                const paste = (e.clipboardData || window.clipboardData).getData("text").trim();
                if (/^\d{6}$/.test(paste)) {
                    paste.split("").forEach((char, i) => {
                        if (inputs[i]) inputs[i].value = char;
                    });
                    updateHiddenOtp();
                    inputs[5].focus();
                }
            });
        });

        function updateHiddenOtp() {
            const otp = Array.from(inputs).map(input => input.value).join("");
            if (hiddenInput) hiddenInput.value = otp;
        }
    });
</script>



@endpush