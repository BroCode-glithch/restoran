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
            font-family: {{ getSetting('branding.font_family', '"Nunito", sans-serif') }};
            background:
                radial-gradient(circle at top left, rgba(254, 161, 22, 0.14), transparent 32%),
                radial-gradient(circle at bottom right, rgba(15, 23, 43, 0.08), transparent 28%),
                linear-gradient(180deg, #f8fafc 0%, #eef2ff 48%, #ffffff 100%);
            color: #0f172b;
        }

        .auth-shell {
            min-height: 100vh;
        }

        .auth-showcase {
            position: relative;
            overflow: hidden;
            background:
                linear-gradient(135deg, rgba(15, 23, 43, 0.98), rgba(15, 23, 43, 0.88)),
                radial-gradient(circle at top right, rgba(254, 161, 22, 0.26), transparent 34%);
            color: #fff;
            display: flex;
            align-items: center;
            padding: clamp(1.75rem, 3vw, 3rem);
        }

        .auth-showcase::before,
        .auth-showcase::after {
            content: '';
            position: absolute;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.08);
            filter: blur(8px);
        }

        .auth-showcase::before {
            width: 240px;
            height: 240px;
            top: -80px;
            right: -70px;
        }

        .auth-showcase::after {
            width: 160px;
            height: 160px;
            bottom: -40px;
            left: 8%;
        }

        .auth-showcase-inner {
            position: relative;
            z-index: 1;
            max-width: 720px;
            width: 100%;
            margin: 0 auto;
        }

        .auth-brand-row {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .auth-logo {
            width: 70px;
            height: 70px;
            border-radius: 22px;
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(16px);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            color: #fff;
            flex: none;
            box-shadow: 0 14px 30px rgba(0, 0, 0, 0.18);
        }

        .auth-kicker {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 0.9rem;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.14);
            color: rgba(255, 255, 255, 0.92);
            font-size: 0.82rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .auth-showcase h1 {
            font-size: clamp(2.2rem, 4vw, 4rem);
            line-height: 1.02;
            letter-spacing: -0.04em;
            margin-top: 1rem;
            margin-bottom: 1rem;
            max-width: 11ch;
            color: #f8fafc;
            text-shadow: 0 16px 36px rgba(0, 0, 0, 0.28);
        }

        .auth-showcase p {
            color: rgba(255, 255, 255, 0.82);
            max-width: 58ch;
            line-height: 1.7;
        }

        .auth-feature-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .auth-feature {
            display: flex;
            gap: 0.9rem;
            padding: 1rem 1.1rem;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.09);
            border: 1px solid rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(10px);
        }

        .auth-feature i {
            width: 42px;
            height: 42px;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(254, 161, 22, 0.18);
            color: #fff;
            flex: none;
        }

        .auth-feature small {
            display: block;
            color: rgba(255, 255, 255, 0.62);
            margin-bottom: 0.15rem;
            letter-spacing: 0.02em;
        }

        .auth-feature .fw-semibold {
            color: #ffffff;
        }

        .auth-proof {
            margin-top: 1.35rem;
            padding: 1.15rem 1.25rem;
            border-radius: 22px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(14px);
        }

        .auth-panel {
            background: rgba(248, 250, 252, 0.88);
            backdrop-filter: blur(16px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: clamp(1.25rem, 3vw, 2.5rem);
        }

        .auth-card {
            width: min(100%, 540px);
            background: var(--auth-surface);
            border: 1px solid var(--auth-border);
            border-top: 4px solid var(--auth-primary);
            border-radius: 28px;
            box-shadow: 0 24px 60px rgba(15, 23, 43, 0.10);
            padding: clamp(1.5rem, 3vw, 2.25rem);
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

        .auth-icon-btn {
            min-width: 54px;
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .auth-link {
            color: var(--auth-primary);
            font-weight: 700;
            text-decoration: none;
        }

        .auth-link:hover {
            color: var(--auth-secondary);
        }

        .auth-muted {
            color: #64748b;
        }

        .auth-note {
            font-size: 0.92rem;
            color: #64748b;
        }

        @media (max-width: 991.98px) {
            .auth-showcase {
                padding-bottom: 1.5rem;
            }

            .auth-brand-row {
                align-items: flex-start;
            }

            .auth-feature-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 575.98px) {
            .auth-showcase {
                padding: 1.25rem;
            }

            .auth-logo {
                width: 58px;
                height: 58px;
                border-radius: 18px;
                font-size: 24px;
            }

            .auth-showcase h1 {
                max-width: none;
                font-size: clamp(1.9rem, 8vw, 2.45rem);
                line-height: 1.08;
            }

            .auth-showcase p {
                font-size: 0.96rem;
            }

            .auth-feature-grid,
            .auth-proof {
                display: none !important;
            }

            .auth-panel {
                padding: 1rem;
            }
        }
    </style>
    @stack('auth-styles')
</head>
<body class="auth-body">
    <div class="container-fluid auth-shell">
        <div class="row g-0 min-vh-100">
            {{-- <div class="col-lg-7 order-2 order-lg-1 auth-showcase">
                <div class="auth-showcase-inner">
                    <div class="auth-brand-row mb-4">
                        <div class="auth-logo">
                            <i class="{{ getSetting('site_icon') }}"></i>
                        </div>

                        <div>
                            <span class="auth-kicker">Food ordering and catering SaaS</span>
                            <h5 class="mb-1 mt-3 fw-bold text-white">{{ getSetting('site_title') }}</h5>
                            <div class="text-white-50">Built for fast-moving catering teams</div>
                        </div>
                    </div>

                    <h1 class="fw-bold">
                        Orders, kitchen flow, and customer updates in one place.
                    </h1>

                    <p class="lead mb-0">
                        A modern workflow for Nigerian food businesses that want a sharper customer experience,
                        tighter operations, and a clean path to SaaS growth.
                    </p>

                    <div class="auth-feature-grid">
                        <div class="auth-feature">
                            <i class="fa-solid fa-bag-shopping"></i>
                            <div>
                                <small>Customer portal</small>
                                <div class="fw-semibold">Browse, checkout, and track live orders.</div>
                            </div>
                        </div>

                        <div class="auth-feature">
                            <i class="fa-solid fa-kitchen-set"></i>
                            <div>
                                <small>Kitchen operations</small>
                                <div class="fw-semibold">Move orders from placed to ready faster.</div>
                            </div>
                        </div>

                        <div class="auth-feature">
                            <i class="fa-solid fa-users-gear"></i>
                            <div>
                                <small>Role control</small>
                                <div class="fw-semibold">Staff, managers, and admins stay in sync.</div>
                            </div>
                        </div>

                        <div class="auth-feature">
                            <i class="fa-brands fa-whatsapp"></i>
                            <div>
                                <small>Notifications</small>
                                <div class="fw-semibold">In-app and WhatsApp updates at every step.</div>
                            </div>
                        </div>
                    </div>

                    <div class="auth-proof d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div>
                            <small class="text-white-50 d-block">Powered by</small>
                            <div class="fw-bold">{{ getSetting('branding.business_name', getSetting('site_title')) }}</div>
                        </div>

                        <div class="text-end">
                            <small class="text-white-50 d-block">Support</small>
                            <div class="fw-bold">{{ getSetting('contact.email', 'info@dailydewtech.com.ng') }}</div>
                        </div>
                    </div>
                </div>
            </div> --}}

            <div class="col-lg-5 order-1 order-lg-2 auth-panel">
                <div class="auth-card">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>

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
