@extends('layouts.auth')

@section('title', 'Sign In')

@section('content')
<div class="mb-4">
    <span class="badge rounded-pill px-3 py-2 mb-3" style="background:rgba(254,161,22,.12);color:var(--auth-primary);">
        Welcome back
    </span>
    <h2 class="fw-bold mb-2">Sign in to your workspace</h2>
    <p class="auth-note mb-0">
        Manage orders, kitchen updates, and customer activity from one secure dashboard.
    </p>
</div>

<div class="mb-4">
    <div class="auth-divider"><span>or use email</span></div>
</div>

<form method="POST" action="{{ route('login') }}" class="vstack gap-3">
    @csrf

    <div class="form-floating">
        <input
            type="email"
            class="form-control @error('email') is-invalid @enderror"
            id="email"
            name="email"
            value="{{ old('email') }}"
            placeholder="Email address"
            required
            autofocus>
        <label for="email">Email address</label>

        @error('email')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label for="password" class="form-label fw-semibold text-dark">Password</label>
        <div class="input-group">
            <input
                type="password"
                class="form-control @error('password') is-invalid @enderror"
                id="password"
                name="password"
                placeholder="Password"
                required>
            <button
                type="button"
                class="btn btn-outline-secondary auth-icon-btn"
                id="togglePassword"
                aria-label="Toggle password visibility">
                <i class="fas fa-eye"></i>
            </button>
        </div>

        @error('password')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div class="form-check mb-0">
            <input class="form-check-input" type="checkbox" name="remember" id="remember">
            <label class="form-check-label" for="remember">Remember me</label>
        </div>

        @if(Route::has('password.request'))
            <a href="{{ route('password.request') }}" class="auth-link">Forgot password?</a>
        @endif
    </div>

    <button type="submit" class="btn btn-primary auth-btn w-100">
        <i class="fa-solid fa-right-to-bracket"></i>
        Sign in
    </button>

    <div class="text-center pt-2">
        <span class="auth-note">Need an account?</span>
        <a href="{{ route('register', request()->only('next')) }}" class="auth-link ms-1">Create one</a>
    </div>
</form>
@endsection

@push('auth-scripts')
<script>
    (function () {
        var toggle = document.getElementById('togglePassword');
        if (!toggle) {
            return;
        }

        toggle.addEventListener('click', function () {
            var password = document.getElementById('password');
            var icon = this.querySelector('i');

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
    }());
</script>
@endpush
