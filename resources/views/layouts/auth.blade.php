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

        .auth-card {
            position: relative;
            z-index: 1;
            width: min(100%, 560px);
            background: var(--auth-surface);
            border: 1px solid var(--auth-border);
            border-top: 4px solid var(--auth-primary);
            border-radius: 30px;
            box-shadow: 0 24px 60px rgba(15, 23, 43, 0.10);
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

        @media (max-width: 575.98px) {
            .auth-shell {
                padding: 0.85rem;
            }

            .auth-card {
                border-radius: 24px;
                padding: 1.25rem;
            }
        }
    </style>
    @stack('auth-styles')
</head>
<body class="auth-body">
    <main class="auth-shell">
        <div class="auth-card">
            @yield('content')
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
    @stack('auth-scripts')
</body>
</html>
