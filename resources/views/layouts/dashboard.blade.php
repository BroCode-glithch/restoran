<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', getSetting('branding.business_name', getSetting('site_title', config('app.name'))))</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600;700;800&family=Nunito:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/foodops.css') }}" rel="stylesheet">
    <style>
        :root {
            --ops-primary: {{ getSetting('branding.primary_color', '#FEA116') }};
            --ops-secondary: {{ getSetting('branding.secondary_color', '#0F172B') }};
        }

        body.ops-body {
            font-family: {{ getSetting('branding.font_family', '"Nunito", sans-serif') }};
        }
    </style>
</head>
<body class="ops-body">
@php
    $user = auth()->user();
    $role = $user ? $user->role : 'customer';
    $navItems = dashboardNavigation($role);
    $bottomNav = customerBottomNavigation();
    $unreadNotifications = $user ? $user->unreadNotifications()->count() : 0;
    $businessName = getSetting('branding.business_name', getSetting('site_title', config('app.name')));
    $logoUrl = mediaUrl(getSetting('branding.logo_url'), asset('assets/img/hero.png'));
@endphp
<div class="ops-shell">
    @if($role !== 'customer')
        <aside class="ops-sidebar">
            <div class="ops-sidebar-brand p-4">
                <div class="d-flex align-items-center gap-3">
                    <img src="{{ $logoUrl }}" alt="{{ $businessName }}" style="width:52px;height:52px;object-fit:cover;" class="rounded-4">
                    <div>
                        <div class="fw-bold fs-5">{{ $businessName }}</div>
                        <small class="text-muted">{{ roleLabel($role) }} workspace</small>
                    </div>
                </div>
            </div>

            <nav class="p-3">
                @foreach($navItems as $item)
                    <a href="{{ route($item['route']) }}" class="{{ request()->routeIs($item['route']) ? 'active' : '' }}">
                        <i class="{{ $item['icon'] }}"></i>
                        <span>{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </nav>
        </aside>
    @endif

    <div class="ops-main">
        <header class="ops-topbar px-3 px-lg-4 py-3">
            <div class="d-flex align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3">
                    @if($role !== 'customer')
                        <button type="button" class="btn btn-light d-lg-none" id="opsSidebarToggle">
                            <i class="fa-solid fa-bars"></i>
                        </button>
                    @endif
                    <div>
                        <div class="text-uppercase small text-muted fw-semibold">{{ roleLabel($role) }}</div>
                        <h5 class="mb-0">{{ $businessName }}</h5>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2">
                    @if($user)
                        <div class="dropdown">
                            <button class="btn btn-light position-relative dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="fa-regular fa-bell"></i>
                                @if($unreadNotifications > 0)
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">{{ $unreadNotifications }}</span>
                                @endif
                            </button>
                            <div class="dropdown-menu dropdown-menu-end p-0 shadow border-0" style="min-width: 320px;">
                                <div class="px-3 py-2 border-bottom">
                                    <div class="fw-bold">Notifications</div>
                                    <small class="text-muted">{{ $unreadNotifications }} unread</small>
                                </div>
                                <div style="max-height: 320px; overflow-y: auto;">
                                    @forelse($user->notifications()->latest()->take(5)->get() as $notification)
                                        @php($data = $notification->data)
                                        <a href="{{ isset($data['action_url']) ? $data['action_url'] : '#' }}" class="dropdown-item py-3 border-bottom">
                                            <div class="fw-semibold">{{ $data['title'] ?? 'Notification' }}</div>
                                            <small class="text-muted">{{ $data['message'] ?? '' }}</small>
                                        </a>
                                    @empty
                                        <div class="px-3 py-4 text-center text-muted">No notifications yet.</div>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <div class="dropdown">
                            <button class="btn btn-light dropdown-toggle d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                                <span class="avatar rounded-circle d-inline-flex align-items-center justify-content-center" style="width:36px;height:36px;background:rgba(254,161,22,.14);color:var(--ops-primary);">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </span>
                                <span class="d-none d-md-inline">{{ $user->name }}</span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <span class="dropdown-item-text text-muted">{{ roleLabel($role) }}</span>
                                <a class="dropdown-item" href="{{ route('dashboard') }}">Dashboard</a>
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    Logout
                                </a>
                            </div>
                        </div>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                    @endif
                </div>
            </div>
        </header>

        <main class="p-3 p-lg-4">
            @yield('content')
        </main>
    </div>

    @if($role === 'customer')
        <nav class="ops-bottom-nav">
            @foreach($bottomNav as $item)
                <a href="{{ route($item['route']) }}" class="{{ request()->routeIs($item['route']) ? 'active' : '' }}">
                    <div class="mb-1"><i class="{{ $item['icon'] }}"></i></div>
                    <div>{{ $item['label'] }}</div>
                </a>
            @endforeach
        </nav>
    @endif
</div>

<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const toggle = document.getElementById('opsSidebarToggle');
    if (toggle) {
        toggle.addEventListener('click', function () {
            document.body.classList.toggle('sidebar-open');
        });
    }
</script>
@stack('scripts')
</body>
</html>
