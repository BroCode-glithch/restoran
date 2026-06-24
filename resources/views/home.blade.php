@extends('layouts.app')

@section('title', 'Home | ' . getSetting('site_title'))

@section('content')
@php
    $orderUrl = auth()->check() && auth()->user()->isCustomer()
        ? route('catalog.index')
        : route('login', ['next' => 'catalog.index']);
@endphp
<div class="container-xxl bg-white p-0">
    <!-- Service Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="row g-4">
                @foreach ($services as $service)
                <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="service-item rounded pt-3">
                        <div class="p-4">
                            <i class="{{ $service->icon }} text-primary mb-4"></i>
                            <h5>{{ $service->title }}</h5>
                            <p>{{ $service->description }}</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    <!-- Service End -->

    <!-- About Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="row g-5 align-items-center">
                <div class="col-lg-6">
                    <div class="row g-3">
                        <div class="col-6 text-start">
                            <img class="img-fluid rounded w-100 wow zoomIn" data-wow-delay="0.1s" src="{{ asset('assets/img/about-1.jpg') }}">
                        </div>
                        <div class="col-6 text-start">
                            <img class="img-fluid rounded w-75 wow zoomIn" data-wow-delay="0.3s" src="{{ asset('assets/img/about-2.jpg') }}" style="margin-top: 25%;">
                        </div>
                        <div class="col-6 text-end">
                            <img class="img-fluid rounded w-75 wow zoomIn" data-wow-delay="0.5s" src="{{ asset('assets/img/about-3.jpg') }}">
                        </div>
                        <div class="col-6 text-end">
                            <img class="img-fluid rounded w-100 wow zoomIn" data-wow-delay="0.7s" src="{{ asset('assets/img/about-4.jpg') }}">
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <h5 class="section-title ff-secondary text-start text-primary fw-normal">About Us</h5>
                    <h1 class="mb-4">Welcome to <i class="fa fa-utensils text-primary me-2"></i>{{ getSetting('site_title') }}</h1>
                    <p class="mb-4">Tempor erat elitr rebum at clita. Diam dolor diam ipsum sit. Aliqu diam amet diam et eos erat ipsum et lorem et sit, sed stet lorem sit.</p>
                    <p class="mb-4">Tempor erat elitr rebum at clita. Diam dolor diam ipsum sit. Aliqu diam amet diam et eos. Clita erat ipsum et lorem et sit, sed stet lorem sit clita duo justo magna dolore erat amet</p>
                    <div class="row g-4 mb-4">
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center border-start border-5 border-primary px-3">
                                <h1 class="flex-shrink-0 display-5 text-primary mb-0" data-toggle="counter-up">15</h1>
                                <div class="ps-4">
                                    <p class="mb-0">Years of</p>
                                    <h6 class="text-uppercase mb-0">Experience</h6>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center border-start border-5 border-primary px-3">
                                <h1 class="flex-shrink-0 display-5 text-primary mb-0" data-toggle="counter-up">50</h1>
                                <div class="ps-4">
                                    <p class="mb-0">Popular</p>
                                    <h6 class="text-uppercase mb-0">Master Chefs</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                    <a class="btn btn-primary py-3 px-5 mt-2 me-2" href="{{ route('menu') }}">Browse Menu</a>
                    <a class="btn btn-outline-primary py-3 px-5 mt-2" href="{{ route('login', ['next' => 'catalog.index']) }}">Start Order</a>
                </div>
            </div>
        </div>
    </div>
    <!-- About End -->



    <!-- Menu Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                <h5 class="section-title ff-secondary text-center text-primary fw-normal">Food Menu</h5>
                <h1 class="mb-5">Most Popular Items</h1>
            </div>
            <div class="tab-class text-center wow fadeInUp" data-wow-delay="0.1s">
                <ul class="nav nav-pills d-inline-flex justify-content-center border-bottom mb-5">
                    <li class="nav-item">
                        <a class="d-flex align-items-center text-start mx-3 ms-0 pb-3 active" data-bs-toggle="pill" href="#tab-1">
                            <i class="fa fa-coffee fa-2x text-primary"></i>
                            <div class="ps-3">
                                <small class="text-body">Popular</small>
                                <h6 class="mt-n1 mb-0">Breakfast</h6>
                            </div>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="d-flex align-items-center text-start mx-3 pb-3" data-bs-toggle="pill" href="#tab-2">
                            <i class="fa fa-hamburger fa-2x text-primary"></i>
                            <div class="ps-3">
                                <small class="text-body">Special</small>
                                <h6 class="mt-n1 mb-0">Lunch</h6>
                            </div>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="d-flex align-items-center text-start mx-3 me-0 pb-3" data-bs-toggle="pill" href="#tab-3">
                            <i class="fa fa-utensils fa-2x text-primary"></i>
                            <div class="ps-3">
                                <small class="text-body">Lovely</small>
                                <h6 class="mt-n1 mb-0">Dinner</h6>
                            </div>
                        </a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div id="tab-1" class="tab-pane fade show p-0 active">
                        <div class="row g-4">
                            @foreach ($breakfasts as $item)
                            <div class="col-lg-6">
                                <a href="{{ $orderUrl }}" class="menu-card d-flex align-items-center text-decoration-none text-dark h-100 rounded-4 border bg-white p-3 shadow-sm">
                                    <img class="flex-shrink-0 img-fluid rounded-4" src="{{ mediaUrl($item->image, asset('assets/img/menu-1.jpg')) }}" alt="{{ $item->name }}" style="width: 80px; height: 80px; object-fit: cover;">
                                    <div class="w-100 d-flex flex-column text-start ps-4">
                                        <h5 class="d-flex justify-content-between border-bottom pb-2">
                                            <span>{{ $item->name }}</span>
                                            <span class="text-primary">{{ moneyFormat($item->price) }}</span>
                                        </h5>
                                        <small class="fst-italic">{{ $item->description }}</small>
                                    </div>
                                </a>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <div id="tab-2" class="tab-pane fade show p-0">
                        <div class="row g-4">
                            @foreach ($lunches as $item)
                            <div class="col-lg-6">
                                <a href="{{ $orderUrl }}" class="menu-card d-flex align-items-center text-decoration-none text-dark h-100 rounded-4 border bg-white p-3 shadow-sm">
                                    <img class="flex-shrink-0 img-fluid rounded-4" src="{{ mediaUrl($item->image, asset('assets/img/menu-2.jpg')) }}" alt="{{ $item->name }}" style="width: 80px; height: 80px; object-fit: cover;">
                                    <div class="w-100 d-flex flex-column text-start ps-4">
                                        <h5 class="d-flex justify-content-between border-bottom pb-2">
                                            <span>{{ $item->name }}</span>
                                            <span class="text-primary">{{ moneyFormat($item->price) }}</span>
                                        </h5>
                                        <small class="fst-italic">{{ $item->description }}</small>
                                    </div>
                                </a>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <div id="tab-3" class="tab-pane fade show p-0">
                        <div class="row g-4">
                            @foreach ($dinners as $item)
                            <div class="col-lg-6">
                                <a href="{{ $orderUrl }}" class="menu-card d-flex align-items-center text-decoration-none text-dark h-100 rounded-4 border bg-white p-3 shadow-sm">
                                    <img class="flex-shrink-0 img-fluid rounded-4" src="{{ mediaUrl($item->image, asset('assets/img/menu-3.jpg')) }}" alt="{{ $item->name }}" style="width: 80px; height: 80px; object-fit: cover;">
                                    <div class="w-100 d-flex flex-column text-start ps-4">
                                        <h5 class="d-flex justify-content-between border-bottom pb-2">
                                            <span>{{ $item->name }}</span>
                                            <span class="text-primary">{{ moneyFormat($item->price) }}</span>
                                        </h5>
                                        <small class="fst-italic">{{ $item->description }}</small>
                                    </div>
                                </a>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Menu End -->



    <!-- Reservation Start -->
    <div class="container-xxl py-5 px-0 wow fadeInUp" data-wow-delay="0.1s">
        <div class="row g-0">
            <div class="col-md-6">
                <div class="video">
                    <button type="button" class="btn-play" data-bs-toggle="modal" data-src="https://www.youtube.com/embed/DWRcNpR6Kdc" data-bs-target="#videoModal">
                        <span></span>
                    </button>
                </div>
            </div>
            <div class="col-md-6 bg-dark d-flex align-items-center">
                <div class="p-5 wow fadeInUp" data-wow-delay="0.2s">
                    <h5 class="section-title ff-secondary text-start text-primary fw-normal">Reservation</h5>
                    <h1 class="text-white mb-4">Book A Table Online</h1>
                    <form>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="name" placeholder="Your Name">
                                    <label for="name">Your Name</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="email" class="form-control" id="email" placeholder="Your Email">
                                    <label for="email">Your Email</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating date" id="date3" data-target-input="nearest">
                                    <input type="text" class="form-control datetimepicker-input" id="datetime" placeholder="Date & Time" data-target="#date3" data-toggle="datetimepicker" />
                                    <label for="datetime">Date & Time</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select" id="select1">
                                      <option value="1">People 1</option>
                                      <option value="2">People 2</option>
                                      <option value="3">People 3</option>
                                    </select>
                                    <label for="select1">No Of People</label>
                                  </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating">
                                    <textarea class="form-control" placeholder="Special Request" id="message" style="height: 100px"></textarea>
                                    <label for="message">Special Request</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <button class="btn btn-primary w-100 py-3" type="submit">Book Now</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="videoModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content rounded-0">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Youtube Video</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- 16:9 aspect ratio -->
                    <div class="ratio ratio-16x9">
                        <iframe class="embed-responsive-item" src="" id="video" allowfullscreen allowscriptaccess="always"
                            allow="autoplay"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <!-- Team Start -->
    <div class="container-xxl pt-5 pb-3">
        <div class="container">
            <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                <h5 class="section-title ff-secondary text-center text-primary fw-normal">Team Members</h5>
                <h1 class="mb-5">Our Active Team</h1>
            </div>
            <div class="row g-4">
                @forelse($teamMembers as $member)
                    @php
                        $parts = preg_split('/\s+/', trim($member->name));
                        $initials = strtoupper(substr($parts[0] ?? 'T', 0, 1) . substr($parts[1] ?? '', 0, 1));
                    @endphp
                    <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="team-item text-center rounded overflow-hidden h-100 cursor-pointer">
                            <div class="rounded-circle overflow-hidden m-4 d-flex align-items-center justify-content-center mx-auto" style="width:140px;height:140px;background:linear-gradient(135deg, rgba(254,161,22,.18), rgba(15,23,43,.08));">
                                <span class="display-6 fw-bold text-dark">{{ $initials }}</span>
                            </div>
                            <h5 class="mb-0">{{ $member->name }}</h5>
                            <small>{{ roleLabel($member->role) }}</small>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-light border">No active team members are visible yet.</div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
    <!-- Team End -->




    <!-- Testimonial Start -->
    <div class="container-xxl py-5 wow fadeInUp" data-wow-delay="0.1s">
        <div class="container">
            <div class="text-center">
                <h5 class="section-title ff-secondary text-center text-primary fw-normal">Customer Stories</h5>
                <h1 class="mb-5">Recent completed orders</h1>
            </div>
            <div class="owl-carousel testimonial-carousel">
                @forelse($customerStories as $story)
                    @php
                        $parts = preg_split('/\s+/', trim($story->customer_name));
                        $initials = strtoupper(substr($parts[0] ?? 'C', 0, 1) . substr($parts[1] ?? '', 0, 1));
                    @endphp
                    <div class="testimonial-item bg-transparent border rounded p-4">
                        <div class="d-flex align-items-start justify-content-between gap-3 mb-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-dark" style="width:50px;height:50px;background:rgba(254,161,22,.14);">
                                    {{ $initials }}
                                </div>
                                <div>
                                    <h5 class="mb-1">{{ $story->customer_name }}</h5>
                                    <small>{{ $story->order_number }}</small>
                                </div>
                            </div>
                            <span class="badge bg-success">Completed</span>
                        </div>
                        <p class="mb-3">{{ $story->notes ?: 'Delivered smoothly with live WhatsApp updates and a clean order flow.' }}</p>
                        <div class="d-flex align-items-center justify-content-between small text-muted">
                            <span>{{ ucfirst($story->delivery_type) }}</span>
                            <span>{{ moneyFormat($story->total, $story->currency) }}</span>
                        </div>
                    </div>
                @empty
                    <div class="testimonial-item bg-transparent border rounded p-4">
                        <p class="mb-0">Testimonials will appear here after completed orders start coming in.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
    <!-- Testimonial End -->


    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>
</div>

@endsection
