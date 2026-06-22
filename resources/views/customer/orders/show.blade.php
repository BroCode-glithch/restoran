@extends('layouts.dashboard')

@section('title', $order->order_number . ' | ' . getSetting('branding.business_name', getSetting('site_title', config('app.name'))))

@section('content')
<div class="ops-hero mb-4">
    <div class="row g-3 align-items-end">
        <div class="col-lg-8">
            <div class="badge bg-white text-dark px-3 py-2 rounded-pill mb-3">Order {{ $order->order_number }}</div>
            <h1 class="fw-bold mb-2">{{ orderStatusLabel($order->status) }}</h1>
            <p class="mb-0 text-white-50">{{ $order->customer_name }} - {{ ucfirst($order->delivery_type) }} - {{ number_format($order->total, 2) }} {{ $order->currency }}</p>
        </div>
        <div class="col-lg-4 text-lg-end">
            <a href="{{ route('orders.index') }}" class="btn btn-outline-light btn-lg">Back to Orders</a>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="ops-card p-4 mb-4">
            <h4 class="mb-3">Order Items</h4>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Qty</th>
                            <th>Unit Price</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                            <tr>
                                <td>{{ $item->product_name }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ number_format($item->unit_price, 2) }}</td>
                                <td class="fw-bold">{{ number_format($item->total_price, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="ops-card p-4">
            <h4 class="mb-3">Status Timeline</h4>
            <div class="vstack gap-3">
                @foreach($order->statusHistories as $history)
                    <div class="p-3 rounded-4 bg-light border">
                        <div class="d-flex justify-content-between gap-3">
                            <div>
                                <div class="fw-bold">{{ orderStatusLabel($history->status) }}</div>
                                <small class="text-muted">{{ $history->note }}</small>
                            </div>
                            <small class="text-muted">{{ $history->created_at->diffForHumans() }}</small>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="ops-card p-4 mb-4">
            <h4 class="mb-3">Customer Details</h4>
            <div class="mb-2"><span class="text-muted">Name:</span> <strong>{{ $order->customer_name }}</strong></div>
            <div class="mb-2"><span class="text-muted">Email:</span> <strong>{{ $order->customer_email ?: 'N/A' }}</strong></div>
            <div class="mb-2"><span class="text-muted">Phone:</span> <strong>{{ $order->customer_phone }}</strong></div>
            <div class="mb-2"><span class="text-muted">Delivery:</span> <strong>{{ ucfirst($order->delivery_type) }}</strong></div>
            <div class="mb-2"><span class="text-muted">Address:</span> <strong>{{ $order->delivery_address ?: 'Pickup order' }}</strong></div>
        </div>

        <div class="ops-card p-4 mb-4">
            <h4 class="mb-3">Order Summary</h4>
            <div class="d-flex justify-content-between mb-2"><span>Subtotal</span><strong>{{ number_format($order->subtotal, 2) }}</strong></div>
            <div class="d-flex justify-content-between mb-2"><span>Delivery Fee</span><strong>{{ number_format($order->delivery_fee, 2) }}</strong></div>
            <div class="d-flex justify-content-between mb-2"><span>Discount</span><strong>{{ number_format($order->discount, 2) }}</strong></div>
            <hr>
            <div class="d-flex justify-content-between fs-5"><span>Total</span><strong>{{ number_format($order->total, 2) }}</strong></div>
        </div>

        @if(auth()->user()->canAccessRole('staff'))
            <div class="ops-card p-4">
                <h4 class="mb-3">Update Status</h4>
                @php
                    $statusOptions = auth()->user()->canAccessRole('super_admin')
                        ? config('foodops.order_status_pipeline')
                        : array_filter([
                            orderStatusNext($order->status) => config('foodops.order_status_pipeline.' . orderStatusNext($order->status)),
                            'cancelled' => config('foodops.order_status_pipeline.cancelled'),
                        ]);
                @endphp
                <form action="{{ route('orders.status.update', $order) }}" method="POST" class="vstack gap-3">
                    @csrf
                    @method('PATCH')
                    <select name="status" class="form-select">
                        @foreach($statusOptions as $statusKey => $statusConfig)
                            @if($statusKey && $statusConfig && $statusKey !== $order->status)
                                <option value="{{ $statusKey }}">{{ $statusConfig['label'] }}</option>
                            @endif
                        @endforeach
                    </select>
                    <textarea name="note" class="form-control" rows="3" placeholder="Optional note"></textarea>
                    <button type="submit" class="btn btn-primary">Save Status</button>
                </form>
            </div>
        @endif
    </div>
</div>
@endsection
