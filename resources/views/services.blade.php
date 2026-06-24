@extends('layouts.app')

@section('title', 'Our Services | ' . getSetting('site_title'))

@section('content')
@php
    $orderUrl = auth()->check() && auth()->user()->isCustomer()
        ? route('catalog.index')
        : route('login', ['next' => 'catalog.index']);
@endphp

<div class="container-xxl py-5">
    <div class="container">
        <div class="row align-items-end g-4 mb-5">
            <div class="col-lg-8">
                <h5 class="section-title ff-secondary text-start text-primary fw-normal">Our Services</h5>
                <h1 class="mb-3">Flexible catering, dine-in support, and fast online ordering.</h1>
                <p class="mb-0 text-muted">Every service card below is editable from the dashboard so the public site always reflects the current business offering.</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="{{ route('booking') }}" class="btn btn-primary py-3 px-4 me-2">Book A Table</a>
                <a href="{{ $orderUrl }}" class="btn btn-outline-primary py-3 px-4">Start Order</a>
            </div>
        </div>

        <div class="row g-4">
            @forelse($services as $service)
                <div class="col-lg-3 col-md-6">
                    <div class="service-item rounded-4 h-100 p-4 cursor-pointer">
                        <div class="mb-3 text-primary fs-1">
                            <i class="{{ $service->icon }}"></i>
                        </div>
                        <h5 class="mb-2">{{ $service->title }}</h5>
                        <p class="mb-0 text-muted">{{ $service->description }}</p>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-light border">
                        No services have been configured yet. Add them from the services manager in the dashboard.
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
