@extends('layouts.auth')

@section('title', 'Confirm Password')

@section('content')
<div class="mb-4">
    <span class="badge rounded-pill px-3 py-2 mb-3" style="background:rgba(254,161,22,.12);color:var(--auth-primary);">
        Security check
    </span>
    <h2 class="fw-bold mb-2">Confirm your password</h2>
    <p class="auth-note mb-0">
        Please confirm your password before continuing to sensitive settings.
    </p>
</div>

<form method="POST" action="{{ route('password.confirm') }}" class="vstack gap-3">
    @csrf

    <div>
        <label for="password" class="form-label fw-semibold text-dark">Current password</label>
        <input
            id="password"
            type="password"
            class="form-control @error('password') is-invalid @enderror"
            name="password"
            placeholder="Current password"
            required
            autocomplete="current-password">

        @error('password')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <button type="submit" class="btn btn-primary auth-btn w-100">
        <i class="fa-solid fa-shield-halved"></i>
        Confirm password
    </button>

    @if (Route::has('password.request'))
        <div class="text-center pt-2">
            <a class="auth-link" href="{{ route('password.request') }}">Forgot your password?</a>
        </div>
    @endif
</form>
@endsection
