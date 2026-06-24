@extends('layouts.app')

@section('title', 'Our Testimonials | ' . getSetting('site_title'))

@section('content')
<div class="container-xxl py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h5 class="section-title ff-secondary text-center text-primary fw-normal">Customer Stories</h5>
            <h1 class="mb-3">Recent completed orders and service snapshots.</h1>
            <p class="text-muted mb-0">This section uses live order data so it stays relevant to the current business state.</p>
        </div>

        <div class="row g-4">
            @forelse($customerStories as $story)
                @php
                    $parts = preg_split('/\s+/', trim($story->customer_name));
                    $initials = strtoupper(substr($parts[0] ?? 'C', 0, 1) . substr($parts[1] ?? '', 0, 1));
                @endphp
                <div class="col-lg-4 col-md-6">
                    <div class="testimonial-item bg-white border rounded-4 p-4 h-100 cursor-pointer">
                        <div class="d-flex align-items-start justify-content-between gap-3 mb-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-dark" style="width:56px;height:56px;background:rgba(254,161,22,.14);">
                                    {{ $initials }}
                                </div>
                                <div>
                                    <h5 class="mb-1">{{ $story->customer_name }}</h5>
                                    <small class="text-muted">{{ $story->order_number }}</small>
                                </div>
                            </div>
                            <span class="badge bg-success">Completed</span>
                        </div>

                        <p class="mb-3 text-muted">
                            {{ $story->notes ?: 'Completed ' . ucfirst($story->delivery_type) . ' order with live WhatsApp updates.' }}
                        </p>

                        <div class="d-flex justify-content-between align-items-center small text-muted">
                            <span>{{ ucfirst($story->delivery_type) }}</span>
                            <span>{{ moneyFormat($story->total, $story->currency) }}</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-light border mb-0">
                        No completed orders yet. Testimonials will appear here after the first orders are fulfilled.
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
