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


                <div class="success-content">
                    <h4>Congratulation</h4>
                    <div>You registration process is complete</div>
                    <p>Use your registered Email ID & password to <br> login to <strong>BLD</strong></p>
                </div>
                @include('auth.subscribers._register-footer')
            </div>
        </div>
    </div>

</section>


@endsection
@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
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

    document.addEventListener("DOMContentLoaded", function() {
        const resendBtn = document.getElementById("resendOtpBtn");
        const resendStatus = document.getElementById("resendOtpStatus");

        resendBtn.addEventListener("click", function() {
            const email = '{{ old("email") ?? session("email") }}';

            if (!email) {
                alert("Email address not available.");
                return;
            }

            fetch("{{ route('subscriber.otp.resend') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        email: email
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.message) {
                        resendStatus.classList.remove("d-none");
                        setTimeout(() => {
                            resendStatus.classList.add("d-none");
                        }, 4000);
                    } else {
                        alert(data.error || "Something went wrong");
                    }
                })
                .catch(error => {
                    console.error(error);
                    alert("Failed to resend OTP.");
                });
        });
    });
</script>



@endpush