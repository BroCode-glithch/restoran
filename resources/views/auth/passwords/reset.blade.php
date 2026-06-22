@extends('layouts.auth')

@section('title', 'Reset Password')

@section('content')
<div class="mb-4">
    <span class="badge rounded-pill px-3 py-2 mb-3" style="background:rgba(254,161,22,.12);color:var(--auth-primary);">
        Password reset
    </span>
    <h2 class="fw-bold mb-2">Send a reset link</h2>
    <p class="auth-note mb-0">
        We will email a secure link so you can create a new password.
    </p>
</div>

@if (session('status'))
    <div class="alert alert-success border-0" role="alert">
        {{ session('status') }}
    </div>
@endif

<form method="POST" action="{{ route('password.email') }}" class="vstack gap-3">
    @csrf

    <div class="form-floating">
        <input
            id="email"
            type="email"
            class="form-control @error('email') is-invalid @enderror"
            name="email"
            value="{{ old('email') }}"
            placeholder="Email address"
            required
            autocomplete="email"
            autofocus>
        <label for="email">Email address</label>

        @error('email')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <button type="submit" class="btn btn-primary auth-btn w-100">
        <i class="fa-solid fa-paper-plane"></i>
        Send reset link
    </button>

    <div class="text-center pt-2">
        <a href="{{ route('login') }}" class="auth-link">Back to sign in</a>
    </div>
</form>
@endsection
