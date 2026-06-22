@extends('layouts.auth')

@section('title', 'Verify Email')

@section('content')
<div class="mb-4">
    <span class="badge rounded-pill px-3 py-2 mb-3" style="background:rgba(254,161,22,.12);color:var(--auth-primary);">
        Email verification
    </span>
    <h2 class="fw-bold mb-2">Check your inbox</h2>
    <p class="auth-note mb-0">
        Before you continue, please verify your email address using the link we sent.
    </p>
</div>

@if (session('resent'))
    <div class="alert alert-success border-0" role="alert">
        A fresh verification link has been sent to your email address.
    </div>
@endif

<div class="rounded-4 border bg-white p-3 mb-4 shadow-sm">
    <div class="fw-semibold mb-1">Need another link?</div>
    <div class="auth-note">
        If the email did not arrive, we can send a new one right away.
    </div>
</div>

<form method="POST" action="{{ route('verification.resend') }}" class="vstack gap-3">
    @csrf

    <button type="submit" class="btn btn-primary auth-btn w-100">
        <i class="fa-solid fa-paper-plane"></i>
        Resend verification link
    </button>

    <a href="{{ route('logout') }}"
       class="text-center auth-link"
       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
        Sign out
    </a>
</form>

<form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
    @csrf
</form>
@endsection
