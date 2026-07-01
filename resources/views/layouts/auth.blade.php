<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', getSetting('site_title') . ' | Auth')</title>

    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600;700;800&family=Nunito:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/cb417788eb.js" crossorigin="anonymous"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/foodops.css') }}" rel="stylesheet">

    <style>
        :root {
            --auth-primary: {{ getSetting('branding.primary_color', '#FEA116') }};
            --auth-secondary: {{ getSetting('branding.secondary_color', '#0F172B') }};
            --auth-surface: rgba(255, 255, 255, 0.95);
            --auth-border: rgba(15, 23, 43, 0.08);
        }

        body.auth-body {
            min-height: 100vh;
            margin: 0;
            font-family: {{ getSetting('branding.font_family', '"Nunito", sans-serif') }};
            background:
                radial-gradient(circle at top left, rgba(254, 161, 22, 0.14), transparent 28%),
                radial-gradient(circle at bottom right, rgba(15, 23, 43, 0.08), transparent 26%),
                linear-gradient(180deg, #f8fafc 0%, #eef2ff 48%, #ffffff 100%);
            color: #0f172b;
            overflow-x: hidden;
        }

        .auth-shell {
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 1.5rem;
            position: relative;
            overflow: hidden;
        }

        .auth-shell::before,
        .auth-shell::after {
            content: '';
            position: fixed;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.45);
            filter: blur(24px);
            pointer-events: none;
            z-index: 0;
        }

        .auth-shell::before {
            width: 240px;
            height: 240px;
            top: -50px;
            right: 5%;
        }

        .auth-shell::after {
            width: 180px;
            height: 180px;
            bottom: -40px;
            left: 6%;
        }

        .auth-orb {
            position: absolute;
            z-index: 0;
            width: clamp(120px, 18vw, 180px);
            height: clamp(120px, 18vw, 180px);
            border-radius: 999px;
            overflow: hidden;
            box-shadow: 0 18px 40px rgba(15, 23, 43, 0.14);
            border: 6px solid rgba(255, 255, 255, 0.85);
            animation: authFloat 5.5s ease-in-out infinite;
        }

        .auth-orb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .auth-orb-left {
            top: 7%;
            left: 5%;
        }

        .auth-orb-right {
            right: 6%;
            bottom: 7%;
            animation-delay: 0.9s;
        }

        .auth-card {
            position: relative;
            z-index: 1;
            width: min(100%, 560px);
            padding: clamp(1.35rem, 4vw, 2.5rem);
            border-radius: 32px;
            border: 1px solid var(--auth-border);
            background: var(--auth-surface);
            box-shadow: 0 28px 72px rgba(15, 23, 43, 0.12);
            border-top: 4px solid var(--auth-primary);
            animation: authRiseIn 0.45s ease both;
            backdrop-filter: blur(14px);
            margin-inline: auto;
        }

        .auth-card .form-label {
            margin-bottom: 0.6rem;
            color: #334155;
        }

        .auth-card .form-floating > .form-control {
            height: 58px;
            border-radius: 16px;
        }

        .auth-card .form-floating > label {
            padding-left: 1rem;
        }

        .auth-card .form-control,
        .auth-card .form-select {
            border-radius: 16px;
            border-color: rgba(15, 23, 43, 0.10);
            box-shadow: none;
        }

        .auth-card .input-group > .form-control {
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
        }

        .auth-card .input-group > .auth-icon-btn {
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
            border-color: rgba(15, 23, 43, 0.10);
            background: #fff;
        }

        .auth-card .form-control:focus,
        .auth-card .form-select:focus {
            border-color: rgba(254, 161, 22, 0.55);
            box-shadow: 0 0 0 0.25rem rgba(254, 161, 22, 0.12);
        }

        .auth-btn {
            min-height: 54px;
            border-radius: 16px;
            font-weight: 800;
            letter-spacing: 0.01em;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.55rem;
            box-shadow: 0 12px 24px rgba(254, 161, 22, 0.18);
        }

        .auth-link {
            color: var(--auth-primary);
            font-weight: 700;
            text-decoration: none;
        }

        .auth-link:hover {
            color: var(--auth-secondary);
        }

        .auth-muted,
        .auth-note {
            color: #64748b;
        }

        .auth-divider {
            display: flex;
            align-items: center;
            gap: 1rem;
            color: #94a3b8;
            font-size: 0.82rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .auth-divider::before,
        .auth-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: rgba(15, 23, 43, 0.10);
        }

        .auth-preloader {
            position: fixed;
            inset: 0;
            z-index: 1000;
            display: grid;
            place-items: center;
            background: rgba(248, 250, 252, 0.72);
            backdrop-filter: blur(12px);
            transition: opacity 0.25s ease, visibility 0.25s ease;
        }

        .auth-preloader.is-hidden {
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
        }

        .auth-preloader-card {
            padding: 1.5rem 1.75rem;
            border-radius: 24px;
            background: #fff;
            border: 1px solid rgba(15, 23, 43, 0.08);
            box-shadow: 0 20px 50px rgba(15, 23, 43, 0.10);
            min-width: 220px;
        }

        @keyframes authRiseIn {
            from {
                opacity: 0;
                transform: translateY(14px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes authFloat {
            0%, 100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        @media (max-width: 575.98px) {
            .auth-shell {
                padding: 0.85rem;
            }

            .auth-card {
                border-radius: 24px;
                padding: 1.25rem;
            }

            .auth-orb {
                display: none;
            }
        }
    </style>
    @stack('auth-styles')
</head>
<body class="auth-body">
@php
    $authTitle = getSetting('site_title');
    $authDescription = getSetting('site_description', 'Role-based restaurant operations with fast, mobile-friendly workflows.');
@endphp
<div class="auth-preloader" id="authPreloader" aria-hidden="true">
    <div class="auth-preloader-card text-center">
        <div class="spinner-border text-primary" role="status" aria-label="Loading"></div>
        <div class="fw-bold mt-3">{{ $authTitle }}</div>
        <div class="text-muted small">Preparing the sign-in experience...</div>
    </div>
</div>

<main class="auth-shell">
    <div class="auth-orb auth-orb-left">
        <img src="{{ mediaUrl(getSetting('branding.logo_url'), asset('assets/img/hero.png')) }}" alt="{{ $authTitle }}">
    </div>
    <div class="auth-orb auth-orb-right">
        <img src="{{ asset('assets/img/about-1.jpg') }}" alt="{{ $authTitle }}">
    </div>

    <section class="auth-card">
        @yield('content')
    </section>
</main>

<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('assets/lib/wow/wow.min.js') }}"></script>
<script src="{{ asset('assets/lib/easing/easing.min.js') }}"></script>
<script src="{{ asset('assets/lib/waypoints/waypoints.min.js') }}"></script>
<script src="{{ asset('assets/lib/counterup/counterup.min.js') }}"></script>
<script src="{{ asset('assets/lib/owlcarousel/owl.carousel.min.js') }}"></script>
<script src="{{ asset('assets/lib/tempusdominus/js/moment.min.js') }}"></script>
<script src="{{ asset('assets/lib/tempusdominus/js/moment-timezone.min.js') }}"></script>
<script src="{{ asset('assets/lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js') }}"></script>
<script src="{{ asset('assets/js/main.js') }}"></script>
<script>
    (function () {
        var preloader = document.getElementById('authPreloader');

        window.addEventListener('load', function () {
            if (preloader) {
                preloader.classList.add('is-hidden');
            }
        });
    }());
</script>
@stack('auth-scripts')
</body>
</html>
