<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', getSetting('site_title') . ' | Auth')</title>

        <!-- Scripts -->
    {{--  <script src="{{ asset('assets/js/app.js') }}" defer></script>  --}}

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <script src="https://kit.fontawesome.com/cb417788eb.js" crossorigin="anonymous"></script>

    {{--  My files  --}}

    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600&family=Nunito:wght@600;700;800&family=Pacifico&display=swap" rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="{{ asset('assets/lib/animate/animate.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/lib/owlcarousel/assets/owl.carousel.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css') }}" rel="stylesheet" />

    <!-- Customized Bootstrap Stylesheet -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">

    <!-- Styles -->
    {{--  <link href="{{ asset('assets/css/app.css') }}" rel="stylesheet">  --}}

    <style>

        body{
            background: linear-gradient(
                135deg,
                #f8fafc 0%,
                #eef2ff 50%,
                #ffffff 100%
            );
            font-family: 'Nunito', sans-serif;
        }

        .auth-branding{
            background: linear-gradient(
                135deg,
                #0d6efd,
                #2563eb,
                #1e40af
            );
        }

        /* subtle floating effect */
        .auth-branding::before,
        .auth-branding::after{
            animation: float 8s ease-in-out infinite;
        }

        @keyframes float{
            0% { transform: translateY(0px); }
            50% { transform: translateY(20px); }
            100% { transform: translateY(0px); }
        }

        .feature-item i{
            color: rgba(255,255,255,0.9);
            width:20px;
        }

        .branding-content{
            margin:auto;
            max-width:500px;
            padding:60px;
        }

        .logo-circle{
            width:80px;
            height:80px;
            border-radius:50%;
            background:white;
            color:#0d6efd;
            display:flex;
            justify-content:center;
            align-items:center;
            font-size:32px;
        }

        .feature-list{
            display:flex;
            flex-direction:column;
            gap:18px;
        }

        .feature-item{
            display:flex;
            align-items:center;
            gap:12px;
            color:white;
            font-size:17px;
        }

        .auth-card{
            background:white;

            border-radius:24px;

            box-shadow:
            0 25px 60px rgba(0,0,0,.08);

            padding:50px;
        }

        .auth-btn{
            height:54px;
            border-radius:12px;
            font-weight:600;
            transition:.3s;
        }

        .auth-btn:hover{
            transform:translateY(-2px);
        }

        .form-control{
            border-radius:12px;
        }

        .form-floating>.form-control{
            height:58px;
        }
        

        @media(max-width:991px){

            .auth-card{
                margin:20px;
                padding:30px;
            }

        }

    </style>
</head>
<body class="auth-body">

    <div class="container-fluid min-vh-100">
        <div class="row min-vh-100">

            <!-- Branding Section -->
            <div class="branding-content text-white">

                <!-- Logo -->
                <div class="d-flex align-items-center mb-4">

                    <div class="logo-circle me-3">
                        <i class="{{ getSetting('site_icon') }}"></i>
                    </div>

                    <div>
                        <h4 class="mb-0 fw-bold">
                            {{ getSetting('site_title') }}
                        </h4>

                        <small class="text-white-50">
                            Smart Food & Catering Platform
                        </small>
                    </div>

                </div>

                <!-- Main headline -->
                <h1 class="fw-bold mb-3" style="line-height:1.2;">
                    Manage Orders.
                    <br>
                    Deliver Faster.
                    <br>
                    Grow Sales.
                </h1>

                <p class="text-white-50 mb-4" style="font-size:16px;">
                    A modern system for catering businesses to take orders, manage staff,
                    track deliveries and scale operations seamlessly.
                </p>

                <!-- Feature list -->
                <div class="feature-list">

                    <div class="feature-item">
                        <i class="fas fa-bolt"></i>
                        <span>Instant order management</span>
                    </div>

                    <div class="feature-item">
                        <i class="fas fa-utensils"></i>
                        <span>Built for food & catering businesses</span>
                    </div>

                    <div class="feature-item">
                        <i class="fas fa-truck"></i>
                        <span>Delivery & tracking workflow</span>
                    </div>

                    <div class="feature-item">
                        <i class="fas fa-users-cog"></i>
                        <span>Role-based staff system</span>
                    </div>

                </div>

                <!-- Bottom highlight card -->
                <div class="mt-5 p-3 rounded-4"
                    style="background:rgba(255,255,255,0.08); backdrop-filter: blur(10px);">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>
                            <small class="text-white-50">Powered by</small>
                            <div class="fw-bold">DailyDew Tech</div>
                        </div>

                        <div class="text-end">
                            <small class="text-white-50">Version</small>
                            <div class="fw-bold">v2.0</div>
                        </div>

                    </div>

                </div>

            </div>

            <!-- Page Content -->
            <div class="col-lg-6 d-flex align-items-center justify-content-center">

                @yield('content')

            </div>

        </div>
    </div>

    <!-- JavaScript Libraries -->
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

    <!-- Template Javascript -->
    <script src="{{ asset('assets/js/main.js') }}"></script>
</body>
</html>
