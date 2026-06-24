@extends('layouts.app')

@section('title', 'Menu | ' . getSetting('site_title'))

@section('content')
@php
    $orderUrl = auth()->check() && auth()->user()->isCustomer()
        ? route('catalog.index')
        : route('login', ['next' => 'catalog.index']);
@endphp

<div class="container-xxl py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h5 class="section-title ff-secondary text-center text-primary fw-normal">Food Menu</h5>
            <h1 class="mb-3">Tap a menu item to start your order.</h1>
            <p class="text-muted mb-0">Public menu items now route customers into the ordering flow, with login fallback for guests.</p>
        </div>

        <div class="tab-class text-center">
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
                        @forelse($breakfasts as $item)
                            <div class="col-lg-6">
                                <a href="{{ $orderUrl }}" class="menu-card d-flex align-items-center text-decoration-none text-dark h-100 rounded-4 border bg-white p-3 shadow-sm">
                                    <img class="flex-shrink-0 img-fluid rounded-4" src="{{ mediaUrl($item->image, asset('assets/img/menu-1.jpg')) }}" alt="{{ $item->name }}" style="width: 90px; height: 90px; object-fit: cover;">
                                    <div class="w-100 d-flex flex-column text-start ps-4">
                                        <h5 class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                                            <span>{{ $item->name }}</span>
                                            <span class="text-primary">{{ moneyFormat($item->price) }}</span>
                                        </h5>
                                        <small class="fst-italic text-muted">{{ $item->description }}</small>
                                        <span class="small text-primary mt-2 fw-semibold">Tap to order</span>
                                    </div>
                                </a>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="alert alert-light border">No breakfast items are available right now.</div>
                            </div>
                        @endforelse
                    </div>
                </div>

                <div id="tab-2" class="tab-pane fade show p-0">
                    <div class="row g-4">
                        @forelse($lunches as $item)
                            <div class="col-lg-6">
                                <a href="{{ $orderUrl }}" class="menu-card d-flex align-items-center text-decoration-none text-dark h-100 rounded-4 border bg-white p-3 shadow-sm">
                                    <img class="flex-shrink-0 img-fluid rounded-4" src="{{ mediaUrl($item->image, asset('assets/img/menu-2.jpg')) }}" alt="{{ $item->name }}" style="width: 90px; height: 90px; object-fit: cover;">
                                    <div class="w-100 d-flex flex-column text-start ps-4">
                                        <h5 class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                                            <span>{{ $item->name }}</span>
                                            <span class="text-primary">{{ moneyFormat($item->price) }}</span>
                                        </h5>
                                        <small class="fst-italic text-muted">{{ $item->description }}</small>
                                        <span class="small text-primary mt-2 fw-semibold">Tap to order</span>
                                    </div>
                                </a>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="alert alert-light border">No lunch items are available right now.</div>
                            </div>
                        @endforelse
                    </div>
                </div>

                <div id="tab-3" class="tab-pane fade show p-0">
                    <div class="row g-4">
                        @forelse($dinners as $item)
                            <div class="col-lg-6">
                                <a href="{{ $orderUrl }}" class="menu-card d-flex align-items-center text-decoration-none text-dark h-100 rounded-4 border bg-white p-3 shadow-sm">
                                    <img class="flex-shrink-0 img-fluid rounded-4" src="{{ mediaUrl($item->image, asset('assets/img/menu-3.jpg')) }}" alt="{{ $item->name }}" style="width: 90px; height: 90px; object-fit: cover;">
                                    <div class="w-100 d-flex flex-column text-start ps-4">
                                        <h5 class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                                            <span>{{ $item->name }}</span>
                                            <span class="text-primary">{{ moneyFormat($item->price) }}</span>
                                        </h5>
                                        <small class="fst-italic text-muted">{{ $item->description }}</small>
                                        <span class="small text-primary mt-2 fw-semibold">Tap to order</span>
                                    </div>
                                </a>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="alert alert-light border">No dinner items are available right now.</div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
