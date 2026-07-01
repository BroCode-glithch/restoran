@extends('layouts.auth')

@section('title', 'Create Account | ' . config('app.name'))

@section('content')
<div class="mb-4">
    <span class="badge rounded-pill px-3 py-2 mb-3" style="background:rgba(254,161,22,.12);color:var(--auth-primary);">
        Get started
    </span>
    <h2 class="fw-bold mb-2">Create your account</h2>
    <p class="auth-note mb-0">
        Sign up once and the system will assign the right access automatically.
    </p>
</div>

<div class="mb-4">
    <div class="auth-divider"><span>Create your account</span></div>
</div>

<form method="POST" action="{{ route('register') }}" class="vstack gap-3">
    @csrf

    <label for="name" class="form-label fw-semibold text-dark">Full Name</label>
    <div class="form-floating">
        <input
            id="name"
            type="text"
            class="form-control @error('name') is-invalid @enderror mb-4"
            name="name"
            value="{{ old('name') }}"
            placeholder="Full name"
            required
            autocomplete="name"
            autofocus>
        <label for="name">Full name</label>

        @error('name')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <label for="email" class="form-label fw-semibold text-dark">Email Address</label>
    <div class="form-floating">
        <input
            id="email"
            type="email"
            class="form-control @error('email') is-invalid @enderror mb-4"
            name="email"
            value="{{ old('email') }}"
            placeholder="Email address"
            required
            autocomplete="email">
        <label for="email">Email address</label>

        @error('email')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label for="password" class="form-label fw-semibold text-dark">Password</label>
        <input
            id="password"
            type="password"
            class="form-control @error('password') is-invalid @enderror mb-4"
            name="password"
            placeholder="Create a password"
            required
            autocomplete="new-password">

        @error('password')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label for="password-confirm" class="form-label fw-semibold text-dark">Confirm password</label>
        <input
            id="password-confirm"
            type="password"
            class="form-control mb-4"
            name="password_confirmation"
            placeholder="Repeat your password"
            required
            autocomplete="new-password">
    </div>

    <button type="submit" class="btn btn-primary auth-btn w-100">
        <i class="fa-solid fa-user-plus"></i>
        Create account
    </button>

    <div class="text-center pt-2">
        <span class="auth-note">Already have an account?</span>
        <a href="{{ route('login', request()->only('next')) }}" class="auth-link ms-1">Sign in</a>
    </div>
</form>
@endsection
