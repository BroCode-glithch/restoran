@extends('layouts.dashboard')

@section('title', 'Thank You | ' . $order->order_number)

@section('content')
<div class="ops-hero mb-4">
    <div class="row g-3 align-items-end">
        <div class="col-lg-8">
            <div class="badge bg-white text-dark px-3 py-2 rounded-pill mb-3">Thank you</div>
            <h1 class="fw-bold mb-2">Thank you for patronizing us.</h1>
            <p class="mb-0 text-white-50">Your order {{ $order->order_number }} has been received and is now moving through the kitchen flow.</p>
        </div>
        <div class="col-lg-4 text-lg-end">
            <a href="{{ route('catalog.index') }}" class="btn btn-outline-light btn-lg me-2 m-2">Continue Shopping</a>
            <a href="{{ route('orders.index') }}" class="btn btn-warning btn-lg">View Orders</a>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="ops-card p-4 mb-4">
            <h4 class="mb-3">Order Summary</h4>
            <div class="d-flex justify-content-between mb-2"><span>Order Number</span><strong>{{ $order->order_number }}</strong></div>
            <div class="d-flex justify-content-between mb-2"><span>Total</span><strong>{{ moneyFormat($order->total, $order->currency) }}</strong></div>
            <div class="d-flex justify-content-between mb-2"><span>Payment Method</span><strong>{{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</strong></div>
            <div class="d-flex justify-content-between mb-2"><span>Payment Status</span><strong>{{ ucfirst($order->payment_status) }}</strong></div>
            @if($order->delivery_type === 'delivery')
                <div class="d-flex justify-content-between mb-2"><span>Delivery Area</span><strong>{{ $order->delivery_area ? str_replace('_', ' ', $order->delivery_area) : 'Delivery' }}</strong></div>
            @endif
        </div>

        <div class="ops-card p-4">
            <h4 class="mb-3">What happens next</h4>
            <div class="vstack gap-3">
                <div class="p-3 rounded-4 bg-light border">We’ve saved your order and sent it to the kitchen.</div>
                <div class="p-3 rounded-4 bg-light border">If you paid with Korapay, confirmation may take a few moments.</div>
                <div class="p-3 rounded-4 bg-light border">You can track updates from your order history at any time.</div>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="ops-card p-4 mb-4">
            <h4 class="mb-3">Your Next Steps</h4>
            <div class="d-grid gap-2 checkout-action-row">
                <a href="{{ route('orders.show', $order) }}" class="btn btn-primary">Open Order Details</a>
                <a href="{{ route('catalog.index') }}" class="btn btn-outline-secondary">Back to Menu</a>
                <a href="{{ route('wallet.index') }}" class="btn btn-outline-dark">Open Wallet</a>
            </div>
        </div>

        <div class="ops-card p-4">
            <h4 class="mb-3">Need help?</h4>
            <p class="text-muted mb-0">If something looks off, open the order page and reach the kitchen or support team from there.</p>
        </div>
    </div>
</div>
@endsection