@extends('layouts.auth')

@section('title', 'Sign In')

@section('content')

<div class="auth-card">

    <!-- Header -->
    <div class="text-center mb-4">

        <div class="mobile-logo d-lg-none mb-3">
            <div class="logo-circle mx-auto">
                <i class="{{ getSetting('site_icon') }}"></i>
            </div>
        </div>

        <h2 class="fw-bold">
            Welcome Back 👋
        </h2>

        <p class="text-muted">
            Sign in to continue
        </p>

    </div>

    <!-- Form START -->
    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email -->
        <div class="form-floating mb-3">
            <input
                type="email"
                class="form-control @error('email') is-invalid @enderror"
                id="email"
                name="email"
                value="{{ old('email') }}"
                placeholder="Email Address"
                required
                autofocus>

            <label for="email">Email Address</label>
        </div>

        <!-- Password -->
        <div class="mb-3">

            <div class="input-group">

                <div class="form-floating flex-grow-1">
                    <input
                        type="password"
                        class="form-control @error('password') is-invalid @enderror"
                        id="password"
                        name="password"
                        placeholder="Password"
                        required>

                    <label for="password">Password</label>
                </div>

                <button
                    type="button"
                    class="btn btn-outline-secondary"
                    id="togglePassword">

                    <i class="fas fa-eye"></i>

                </button>

            </div>

        </div>

        <!-- Remember + Forgot -->
        <div class="d-flex justify-content-between align-items-center mb-4">

            <div class="form-check">
                <input
                    class="form-check-input"
                    type="checkbox"
                    name="remember"
                    id="remember">

                <label class="form-check-label" for="remember">
                    Remember Me
                </label>
            </div>

            @if(Route::has('password.request'))
                <a href="{{ route('password.request') }}"
                   class="text-decoration-none">
                    Forgot Password?
                </a>
            @endif

        </div>

        <!-- Submit -->
        <button type="submit" class="btn btn-primary w-100 auth-btn">
            Sign In
        </button>

        <!-- Register -->
        <div class="text-center mt-4">

            <span class="text-muted">
                Don't have an account?
            </span>

            <a href="{{ route('register') }}"
               class="text-decoration-none fw-bold">
                Create Account
            </a>

        </div>

    </form>
    <!-- Form END -->

</div>

<script>
document.getElementById('togglePassword').addEventListener('click', function () {

    const password = document.getElementById('password');
    const icon = this.querySelector('i');

    if (password.type === 'password') {
        password.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        password.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }

});
</script>

@endsection