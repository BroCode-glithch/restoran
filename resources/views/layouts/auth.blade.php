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
            --auth-surface: rgba(255, 255, 255, 0.96);
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
        }

        .auth-shell {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.25rem;
            position: relative;
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
            width: 220px;
            height: 220px;
            top: -40px;
            right: 3%;
        }

        .auth-shell::after {
            width: 180px;
            height: 180px;
            bottom: -50px;
            left: 5%;
        }

        .auth-layout {
            position: relative;
            z-index: 1;
            width: min(100%, 1120px);
            display: grid;
            grid-template-columns: minmax(0, 1.05fr) minmax(0, 0.95fr);
            gap: 1.5rem;
            align-items: stretch;
        }

        .auth-spotlight,
        .auth-card {
            position: relative;
            border-radius: 32px;
            border: 1px solid var(--auth-border);
            box-shadow: 0 24px 60px rgba(15, 23, 43, 0.10);
            overflow: hidden;
            animation: authRiseIn 0.45s ease both;
        }

        .auth-spotlight {
            padding: clamp(1.5rem, 3vw, 2.5rem);
            background:
                linear-gradient(135deg, rgba(15, 23, 43, 0.98), rgba(15, 23, 43, 0.88)),
                radial-gradient(circle at top right, rgba(254, 161, 22, 0.20), transparent 34%);
            color: #fff;
        }

        .auth-spotlight::before {
            content: '';
            position: absolute;
            inset: auto -20% -35% auto;
            width: 320px;
            height: 320px;
            border-radius: 999px;
            background: rgba(254, 161, 22, 0.08);
            filter: blur(0);
            pointer-events: none;
        }

        .auth-spotlight-inner {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
            height: 100%;
            justify-content: space-between;
        }

        .auth-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            width: fit-content;
            padding: 0.45rem 0.8rem;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.10);
            color: rgba(255, 255, 255, 0.92);
            font-size: 0.78rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .auth-spotlight h1 {
            margin: 0;
            font-size: clamp(2rem, 4vw, 3.35rem);
            line-height: 1.02;
            letter-spacing: -0.04em;
        }

        .auth-spotlight p {
            margin: 0;
            color: rgba(255, 255, 255, 0.75);
            font-size: 1rem;
            line-height: 1.7;
            max-width: 40rem;
        }

        .auth-feature-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.85rem;
        }

        .auth-feature {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            padding: 0.95rem 1rem;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .auth-feature i {
            color: var(--auth-primary);
            margin-top: 0.1rem;
        }

        .auth-feature strong {
            display: block;
            font-size: 0.96rem;
            margin-bottom: 0.25rem;
        }

        .auth-feature span {
            display: block;
            font-size: 0.82rem;
            color: rgba(255, 255, 255, 0.70);
            line-height: 1.5;
        }

        .auth-trust {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            padding-top: 0.25rem;
        }

        .auth-trust span {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.45rem 0.8rem;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.10);
            color: rgba(255, 255, 255, 0.92);
            font-size: 0.82rem;
            font-weight: 700;
        }

        .auth-card {
            background: var(--auth-surface);
            border-top: 4px solid var(--auth-primary);
            padding: clamp(1.5rem, 4vw, 2.5rem);
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

        .auth-social-btn {
            min-height: 52px;
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.6rem;
            font-weight: 700;
            border: 1px solid rgba(15, 23, 43, 0.10);
            background: #ffffff;
            color: #111827;
            box-shadow: 0 10px 22px rgba(15, 23, 43, 0.06);
        }

        .auth-social-btn:disabled {
            opacity: 1;
            color: #111827;
            background: #ffffff;
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

        @media (max-width: 991.98px) {
            .auth-layout {
                grid-template-columns: 1fr;
            }

            .auth-spotlight {
                order: -1;
            }
        }

        @media (max-width: 575.98px) {
            .auth-shell {
                padding: 0.85rem;
            }

            .auth-card,
            .auth-spotlight {
                border-radius: 24px;
                padding: 1.25rem;
            }

            .auth-feature-grid {
                grid-template-columns: 1fr;
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
    <div class="auth-layout">
        {{-- <section class="auth-spotlight">
            <div class="auth-spotlight-inner">
                <div>
                    <div class="auth-eyebrow mb-3">
                        <i class="fa-solid fa-layer-group"></i>
                        Modern operations
                    </div>
                    <h1>{{ $authTitle }}</h1>
                    <p class="mt-3">{{ $authDescription }}</p>
                </div>

                <div class="auth-feature-grid">
                    <div class="auth-feature">
                        <i class="fa-solid fa-shield-halved fa-lg"></i>
                        <div>
                            <strong>Secure access</strong>
                            <span>Protected sign-in, password recovery, and role-aware redirects.</span>
                        </div>
                    </div>
                    <div class="auth-feature">
                        <i class="fa-solid fa-chart-line fa-lg"></i>
                        <div>
                            <strong>Live dashboards</strong>
                            <span>Track orders, revenue, logs, and staff activity in one place.</span>
                        </div>
                    </div>
                    <div class="auth-feature">
                        <i class="fa-solid fa-mobile-screen-button fa-lg"></i>
                        <div>
                            <strong>Mobile ready</strong>
                            <span>Responsive portals that stay usable on smaller screens.</span>
                        </div>
                    </div>
                    <div class="auth-feature">
                        <i class="fa-solid fa-bowl-food fa-lg"></i>
                        <div>
                            <strong>Operational flow</strong>
                            <span>Clear paths from auth into the correct customer or staff workspace.</span>
                        </div>
                    </div>
                </div>

                <div class="auth-trust">
                    <span><i class="fa-solid fa-check"></i> Fast sign-in</span>
                    <span><i class="fa-solid fa-check"></i> Balanced spacing</span>
                    <span><i class="fa-solid fa-check"></i> No palette changes</span>
                </div>
            </div>
        </section> --}}

        <section class="auth-card">
            @yield('content')
        </section>
    </div>
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
