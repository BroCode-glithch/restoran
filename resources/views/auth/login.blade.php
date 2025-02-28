@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-center align-items-center vh-100">
    <div class="card shadow-lg p-4 border-0 rounded-4" style="width: 400px;">
        <div class="text-center">
            <h3 class="fw-bold">{{ __('Login') }}</h3>
            <p class="text-muted">Welcome back! Please log in to continue.</p>
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email Input -->
                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold">{{ __('Email Address') }}</label>
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                           name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                    @error('email')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Password Input -->
                <div class="mb-3">
                    <label for="password" class="form-label fw-semibold">{{ __('Password') }}</label>
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                           name="password" required autocomplete="current-password">
                    @error('password')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Remember Me Checkbox -->
                <div class="mb-3 form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember"
                           {{ old('remember') ? 'checked' : '' }}>
                    <label class="form-check-label" for="remember">
                        {{ __('Remember Me') }}
                    </label>
                </div>

                <!-- Login Button -->
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg">
                        {{ __('Login') }}
                    </button>
                </div>

                <!-- Forgot Password & Register -->
                <div class="text-center mt-3">
                    @if (Route::has('password.request'))
                        <a class="text-decoration-none small" href="{{ route('password.request') }}">
                            {{ __('Forgot Password?') }}
                        </a>
                    @endif
                    <br>
                    <span class="small">Don't have an account?
                        <a href="{{ route('register') }}" class="text-primary text-decoration-none">Sign Up</a>
                    </span>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
