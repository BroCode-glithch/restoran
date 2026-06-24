@extends('layouts.app')

@section('title', 'About Us | ' . getSetting('site_title'))

@section('content')
@php
    $orderUrl = auth()->check() && auth()->user()->isCustomer()
        ? route('catalog.index')
        : route('login', ['next' => 'catalog.index']);
@endphp

<div class="container-xxl py-5">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-6">
                <div class="row g-3">
                    <div class="col-6 text-start">
                        <img class="img-fluid rounded w-100 wow zoomIn" data-wow-delay="0.1s" src="{{ asset('assets/img/about-1.jpg') }}" alt="About image 1">
                    </div>
                    <div class="col-6 text-start">
                        <img class="img-fluid rounded w-75 wow zoomIn" data-wow-delay="0.3s" src="{{ asset('assets/img/about-2.jpg') }}" alt="About image 2" style="margin-top: 25%;">
                    </div>
                    <div class="col-6 text-end">
                        <img class="img-fluid rounded w-75 wow zoomIn" data-wow-delay="0.5s" src="{{ asset('assets/img/about-3.jpg') }}" alt="About image 3">
                    </div>
                    <div class="col-6 text-end">
                        <img class="img-fluid rounded w-100 wow zoomIn" data-wow-delay="0.7s" src="{{ asset('assets/img/about-4.jpg') }}" alt="About image 4">
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <h5 class="section-title ff-secondary text-start text-primary fw-normal">About Us</h5>
                <h1 class="mb-4">Built for fast food ordering, catering, and operations control.</h1>
                <p class="mb-4">{{ getSetting('site_description', getSetting('branding.business_name', getSetting('site_title'))) }} The dashboard keeps service, menu, and customer workflows in sync.</p>

                <div class="row g-4 mb-4">
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center border-start border-5 border-primary px-3 py-2">
                            <h1 class="flex-shrink-0 display-5 text-primary mb-0">{{ $serviceCount }}</h1>
                            <div class="ps-4">
                                <p class="mb-0">Published</p>
                                <h6 class="text-uppercase mb-0">Services</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center border-start border-5 border-primary px-3 py-2">
                            <h1 class="flex-shrink-0 display-5 text-primary mb-0">{{ $teamCount }}</h1>
                            <div class="ps-4">
                                <p class="mb-0">Active</p>
                                <h6 class="text-uppercase mb-0">Team Members</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center border-start border-5 border-primary px-3 py-2">
                            <h1 class="flex-shrink-0 display-5 text-primary mb-0">{{ $menuCount }}</h1>
                            <div class="ps-4">
                                <p class="mb-0">Menu</p>
                                <h6 class="text-uppercase mb-0">Items</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center border-start border-5 border-primary px-3 py-2">
                            <h1 class="flex-shrink-0 display-5 text-primary mb-0">{{ $completedOrders }}</h1>
                            <div class="ps-4">
                                <p class="mb-0">Orders</p>
                                <h6 class="text-uppercase mb-0">Completed</h6>
                            </div>
                        </div>
                    </div>
                </div>

                <a class="btn btn-primary py-3 px-5 mt-2 me-2" href="{{ route('booking') }}">Book A Table</a>
                <a class="btn btn-outline-primary py-3 px-5 mt-2" href="{{ $orderUrl }}">Start Order</a>
            </div>
        </div>
    </div>
</div>

@if(!empty($services) && $services->count())
    <div class="container-xxl py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h5 class="section-title ff-secondary text-center text-primary fw-normal">Key Services</h5>
                <h1>What the business can manage right now</h1>
            </div>
            <div class="row g-4">
                @foreach($services as $service)
                    <div class="col-lg-3 col-md-6">
                        <div class="service-item rounded-4 h-100 p-4 cursor-pointer">
                            <div class="mb-3 text-primary fs-1"><i class="{{ $service->icon }}"></i></div>
                            <h5 class="mb-2">{{ $service->title }}</h5>
                            <p class="mb-0 text-muted">{{ $service->description }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif
@endsection
