<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title', getSetting('site_title'))</title>

        <link rel="dns-prefetch" href="//fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
        <script src="https://kit.fontawesome.com/cb417788eb.js" crossorigin="anonymous"></script>

        <link href="{{ asset('favicon.ico') }}" rel="icon">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600&family=Nunito:wght@600;700;800&family=Pacifico&display=swap" rel="stylesheet">

        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

        <link href="{{ asset('assets/lib/animate/animate.min.css') }}" rel="stylesheet">
        <link href="{{ asset('assets/lib/owlcarousel/assets/owl.carousel.min.css') }}" rel="stylesheet">
        <link href="{{ asset('assets/lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css') }}" rel="stylesheet" />

        <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
        <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
        @stack('styles')
    </head>
    <body class="site-body">
        <div id="app">
            <div class="container-xxl bg-white p-0">
                <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
                    <div class="spinner-card text-center">
                        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status" aria-label="Loading">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <div class="fw-bold mt-3">{{ getSetting('site_title') }}</div>
                        <div class="text-muted small">Preparing your experience...</div>
                    </div>
                </div>

                <div class="container-xxl position-relative p-0">
                    <nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4 px-lg-5 py-3 py-lg-0">
                        <a href="{{ base_url() }}" class="navbar-brand p-0">
                            <h1 class="text-primary m-0"><i class="fa fa-utensils me-3"></i>{{ getSetting('site_title') }}</h1>
                        </a>
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                            <span class="fa fa-bars"></span>
                        </button>

                        @php
                            $pagesActive = request()->routeIs('booking') || request()->routeIs('team') || request()->routeIs('testimonial');
                        @endphp

                        <div class="collapse navbar-collapse" id="navbarCollapse">
                            <div class="navbar-nav ms-auto py-0 pe-4">
                                <a href="{{ url('/home') }}" class="nav-item nav-link {{ request()->routeIs('home') ? 'active' : '' }}">Home</a>
                                <a href="{{ url('/about') }}" class="nav-item nav-link {{ request()->routeIs('about') ? 'active' : '' }}">About</a>
                                <a href="{{ url('/services') }}" class="nav-item nav-link {{ request()->routeIs('services') ? 'active' : '' }}">Services</a>
                                <a href="{{ url('/menu') }}" class="nav-item nav-link {{ request()->routeIs('menu') ? 'active' : '' }}">Menu</a>
                                <div class="nav-item dropdown">
                                    <a href="#" class="nav-link dropdown-toggle {{ $pagesActive ? 'active' : '' }}" data-bs-toggle="dropdown">Pages</a>
                                    <div class="dropdown-menu m-0">
                                        <a href="{{ url('/booking') }}" class="dropdown-item {{ request()->routeIs('booking') ? 'active' : '' }}">Booking</a>
                                        <a href="{{ url('/team') }}" class="dropdown-item {{ request()->routeIs('team') ? 'active' : '' }}">Our Team</a>
                                        <a href="{{ url('/testimonial') }}" class="dropdown-item {{ request()->routeIs('testimonial') ? 'active' : '' }}">Testimonials</a>
                                    </div>
                                </div>
                                <a href="mailto:{{ getSetting('contact.email') }}" class="nav-item nav-link">Contact</a>
                            </div>

                            @guest
                                @if (Route::has('login'))
                                    <a href="{{ url('login') }}" class="nav-link nav-item">Login</a>
                                @endif

                                @if (Route::has('register'))
                                    <a href="{{ url('register') }}" class="nav-link nav-item">Register</a>
                                @endif
                            @else
                                <div class="nav-item dropdown">
                                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                        {{ Auth::user()->name }}
                                    </a>

                                    <div class="dropdown-menu m-0">
                                        <a class="dropdown-item" href="{{ route('dashboard') }}">Dashboard</a>
                                        <a class="dropdown-item" href="{{ route('logout') }}"
                                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                            {{ __('Logout') }}
                                        </a>
                                    </div>
                                </div>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            @endguest
                            <a href="{{ route('booking') }}" class="btn btn-primary py-2 px-4">Book A Table</a>
                        </div>
                    </nav>

                    <div class="container-xxl py-5 bg-dark hero-header mb-5">
                        <div class="container my-5 py-5">
                            <div class="row align-items-center g-5">
                                <div class="col-lg-6 text-center text-lg-start">
                                    <h1 class="display-3 text-white animated slideInLeft">Enjoy Our<br>Delicious Meal</h1>
                                    <p class="text-white animated slideInLeft mb-4 pb-2">Tempor erat elitr rebum at clita. Diam dolor diam ipsum sit. Aliqu diam amet diam et eos. Clita erat ipsum et lorem et sit, sed stet lorem sit clita duo justo magna dolore erat amet</p>
                                    <a href="{{ route('booking') }}" class="btn btn-primary py-sm-3 px-sm-5 me-3 animated slideInLeft">Book A Table</a>
                                </div>
                                <div class="col-lg-6 text-center text-lg-end overflow-hidden">
                                    <img class="img-fluid" src="{{ asset('assets/img/hero.png') }}" alt="">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <main class="py-4">
                    @yield('content')
                </main>
            </div>

            <div class="container-fluid bg-dark text-light footer pt-5 mt-5 wow fadeIn" data-wow-delay="0.1s">
                <div class="container py-5">
                    <div class="row g-5">
                        <div class="col-lg-3 col-md-6">
                            <h4 class="section-title ff-secondary text-start text-primary fw-normal mb-4">{{ getSetting('footer.company_title', 'Company') }}</h4>
                            <a class="btn btn-link" href="{{ route('about') }}">About Us</a>
                            <a class="btn btn-link" href="{{ route('services') }}">Services</a>
                            <a class="btn btn-link" href="{{ route('booking') }}">Reservation</a>
                            <a class="btn btn-link" href="{{ route('menu') }}">Menu</a>
                            <a class="btn btn-link" href="{{ route('testimonial') }}">Testimonials</a>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <h4 class="section-title ff-secondary text-start text-primary fw-normal mb-4">Contact</h4>
                            <p class="mb-2"><i class="fa fa-map-marker-alt me-3"></i>{{ getSetting('contact.address') }}</p>
                            <p class="mb-2"><i class="fa fa-phone-alt me-3"></i>{{ getSetting('contact.phone') }}</p>
                            <p class="mb-2"><i class="fa fa-envelope me-3"></i>{{ getSetting('contact.email') }}</p>
                            <div class="d-flex pt-2 flex-wrap gap-2">
                                @if(getSetting('footer.twitter_url'))<a class="btn btn-outline-light btn-social" href="{{ getSetting('footer.twitter_url') }}" target="_blank" rel="noopener"><i class="fab fa-twitter"></i></a>@endif
                                @if(getSetting('footer.facebook_url'))<a class="btn btn-outline-light btn-social" href="{{ getSetting('footer.facebook_url') }}" target="_blank" rel="noopener"><i class="fab fa-facebook-f"></i></a>@endif
                                @if(getSetting('footer.youtube_url'))<a class="btn btn-outline-light btn-social" href="{{ getSetting('footer.youtube_url') }}" target="_blank" rel="noopener"><i class="fab fa-youtube"></i></a>@endif
                                @if(getSetting('footer.linkedin_url'))<a class="btn btn-outline-light btn-social" href="{{ getSetting('footer.linkedin_url') }}" target="_blank" rel="noopener"><i class="fab fa-linkedin-in"></i></a>@endif
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <h4 class="section-title ff-secondary text-start text-primary fw-normal mb-4">Opening</h4>
                            <h5 class="text-light fw-normal">Monday - Saturday</h5>
                            <p>{{ getSetting('operations.business_hours', '09AM - 09PM') }}</p>
                            <h5 class="text-light fw-normal">Sunday</h5>
                            <p>{{ getSetting('operations.business_hours', '10AM - 08PM') }}</p>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <h4 class="section-title ff-secondary text-start text-primary fw-normal mb-4">Quick Links</h4>
                            <p>{{ getSetting('footer.quick_links_text', 'Browse the menu, reserve a table, or check the latest testimonials.') }}</p>
                            <div class="footer-menu">
                                <a href="{{ route('home') }}">Home</a>
                                <a href="{{ route('services') }}">Services</a>
                                <a href="{{ route('menu') }}">Menu</a>
                                <a href="{{ route('booking') }}">Booking</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="container">
                    <div class="copyright">
                        <div class="row">
                            <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                                &copy; <a class="border-bottom" href="{{ base_url() }}">{{ getSetting('site_title') }}</a>, {{ getSetting('footer.copyright_text', 'All Rights Reserved.') }}
                                <span class="d-inline-block mt-1 mt-md-0">{{ getSetting('footer.credit_text', 'Designed by DailyDew Tech Innovations') }} <a class="border-bottom" href="{{ getSetting('footer.credit_url', 'https://dailydewtech.com.ng') }}" target="_blank" rel="noopener">{{ getSetting('footer.credit_url', 'https://dailydewtech.com.ng') }}</a></span><br><br>
                            </div>
                            <div class="col-md-6 text-center text-md-end">
                                <div class="footer-menu">
                                    <a href="{{ route('home') }}">Home</a>
                                    <a href="{{ route('menu') }}">Menu</a>
                                    <a href="{{ route('services') }}">Services</a>
                                    <a href="{{ route('about') }}">About</a>
                                </div>
                            </div>
                        </div>
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
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        @flasher_render()
        <script src="{{ asset('vendor/flasher/flasher-toastr.min.js') }}"></script>

        @if(session()->has('swal'))
            <script>
                (function() {
                    try {
                        var payload = @json(session('swal'));
                        if (payload) {
                            if (payload.confirm) {
                                Swal.fire({
                                    icon: payload.type || 'warning',
                                    title: payload.title || '',
                                    html: payload.message || '',
                                    showCancelButton: true,
                                    confirmButtonText: payload.confirmText || 'Yes',
                                    cancelButtonText: payload.cancelText || 'Cancel',
                                    allowOutsideClick: payload.allowOutsideClick ?? false,
                                }).then(function(result){
                                    if (result.isConfirmed && typeof window[payload.onConfirm] === 'function') {
                                        try { window[payload.onConfirm](); } catch (e) { console.error(e); }
                                    }
                                });
                            } else {
                                Swal.fire({
                                    icon: payload.type || 'success',
                                    title: payload.title || '',
                                    html: payload.message || '',
                                    confirmButtonText: payload.ok_text || 'OK',
                                    allowOutsideClick: payload.allowOutsideClick ?? false,
                                });
                            }
                        }
                    } catch (e) {
                        console.error('SweetAlert payload error', e);
                    }
                }());
            </script>
        @endif
        <script>
            (function() {
                try {
                    document.addEventListener('DOMContentLoaded', function () {
                        var processed = new WeakSet();
                        document.querySelectorAll('.flasher-progress').forEach(function(pb) {
                            var container = pb.closest('div');
                            if (!container) return;
                            var alertEl = container.querySelector('.alert');
                            if (!alertEl || processed.has(alertEl)) return;
                            processed.add(alertEl);
                            var type = alertEl.classList.contains('alert-success') ? 'success' : (alertEl.classList.contains('alert-danger') ? 'error' : (alertEl.classList.contains('alert-warning') ? 'warning' : 'info'));
                            var message = alertEl.innerText.trim();
                            try {
                                if (type === 'success' || type === 'info') {
                                    Swal.fire({
                                        icon: type,
                                        title: '',
                                        html: message,
                                        timer: 3000,
                                        showConfirmButton: false,
                                        allowOutsideClick: true
                                    });
                                } else {
                                    Swal.fire({
                                        icon: type,
                                        title: '',
                                        html: message,
                                        confirmButtonText: 'OK',
                                        allowOutsideClick: false
                                    });
                                }
                            } catch (e) {
                                console.error('Swal display error', e);
                            }
                            alertEl.remove();
                        });
                    });
                } catch (e) {
                    console.error('Flasher->Swal hook error', e);
                }
            }());
        </script>
        @stack('scripts')
    </body>
</html>
