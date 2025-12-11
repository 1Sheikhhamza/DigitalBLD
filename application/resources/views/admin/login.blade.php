    @extends('admin.layouts.app')
    @section('title', 'Login Page')
    @section('content')
    <div class="login-page bg-body-secondary">
        <div class="login-box">
            <div class="login-logo">
                <a href="{{ url('/') }}" class="text-white">
                    <img src="{{ asset('frontend/assets/img/logo.png') }}" alt="Digital Bangladesh Legal Decisions Logo">
                    <b>Digital Bangladesh Legal Decisions</b>
                </a>
            </div>
            <div class="card">
                <div class="card-body login-card-body">
                    <p class="login-box-msg">Sign in to start your session</p>
                    @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                    @endif

                    <form action="{{ route('admin.login.submit') }}" method="POST">
                        @csrf
                        <div class="input-group mb-3">
                            <input
                                type="email"
                                name="email"
                                class="form-control @error('email') is-invalid @enderror"
                                placeholder="Email"
                                value="{{ old('email') }}"
                                required>
                            <div class="input-group-text">
                                <span class="bi bi-envelope"></span>
                            </div>
                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="input-group mb-3">
                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="form-control @error('password') is-invalid @enderror"
                                placeholder="Password"
                                required>
                            <div class="input-group-text">
                                <span id="togglePassword">
                                    <i class="fa fa-eye"></i>
                                </span>
                            </div>
                            @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="row">
                            <div class="col-8">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="flexCheckDefault">
                                    <label class="form-check-label" for="flexCheckDefault">
                                        Remember Me
                                    </label>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary">Sign In</button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- <p class="mb-1">
                        <a href="{{ route('password.request') }}">I forgot my password</a>
                    </p> -->

                </div>
            </div>
        </div>
    </div>

    <script>
        const passwordField = document.getElementById("password");
        const togglePassword = document.getElementById("togglePassword");
        const icon = togglePassword.querySelector("i");

        togglePassword.addEventListener("click", function() {
            const type = passwordField.getAttribute("type") === "password" ? "text" : "password";
            passwordField.setAttribute("type", type);

            // toggle icon
            if (type === "password") {
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            } else {
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            }
        });
    </script>
    @endsection