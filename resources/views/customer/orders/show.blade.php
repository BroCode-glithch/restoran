@extends('layouts.dashboard')

@section('title', $order->order_number . ' | ' . getSetting('branding.business_name', getSetting('site_title', config('app.name'))))

@section('content')
<div class="ops-hero mb-4">
    <div class="row g-3 align-items-end">
        <div class="col-lg-8">
            <div class="badge bg-white text-dark px-3 py-2 rounded-pill mb-3">Order {{ $order->order_number }}</div>
            <h1 class="fw-bold mb-2">{{ orderStatusLabel($order->status) }}</h1>
            <p class="mb-0 text-white-50">{{ $order->customer_name }} - {{ ucfirst($order->delivery_type) }} - {{ moneyFormat($order->total, $order->currency) }}</p>
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
                                <td>{{ moneyFormat($item->unit_price, $order->currency) }}</td>
                                <td class="fw-bold">{{ moneyFormat($item->total_price, $order->currency) }}</td>
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

            @php
                $customerWhatsappUrl = whatsappChatUrl(
                    $order->customer_phone,
                    'Hello ' . $order->customer_name . ', your order ' . $order->order_number . ' is currently ' . orderStatusLabel($order->status) . '.'
                );
            @endphp

            @if($customerWhatsappUrl)
                <a href="{{ $customerWhatsappUrl }}" target="_blank" rel="noopener" class="btn btn-success w-100 mt-3">
                    <i class="fa-brands fa-whatsapp me-1"></i>Send WhatsApp update
                </a>
            @endif
        </div>

        <div class="ops-card p-4 mb-4">
            <h4 class="mb-3">Order Summary</h4>
            <div class="d-flex justify-content-between mb-2"><span>Subtotal</span><strong>{{ moneyFormat($order->subtotal, $order->currency) }}</strong></div>
            <div class="d-flex justify-content-between mb-2"><span>Delivery Fee</span><strong>{{ moneyFormat($order->delivery_fee, $order->currency) }}</strong></div>
            <div class="d-flex justify-content-between mb-2"><span>Discount</span><strong>{{ moneyFormat($order->discount, $order->currency) }}</strong></div>
            <hr>
            <div class="d-flex justify-content-between fs-5"><span>Total</span><strong>{{ moneyFormat($order->total, $order->currency) }}</strong></div>
            <div class="mt-3 small text-muted">
                Payment method:
                <span class="fw-semibold text-dark">{{ ucfirst(str_replace('_', ' ', $order->payment_method ?: 'demo_card')) }}</span>
            </div>
            <div class="small text-muted">
                Payment status:
                <span class="fw-semibold text-dark">{{ ucfirst($order->payment_status) }}</span>
            </div>
            @if($order->payment_reference)
                <div class="small text-muted">
                    Reference:
                    <span class="fw-semibold text-dark">{{ $order->payment_reference }}</span>
                </div>
            @endif
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
                    <select name="status" class="form-select mb-4">
                        @foreach($statusOptions as $statusKey => $statusConfig)
                            @if($statusKey && $statusConfig && $statusKey !== $order->status)
                                <option value="{{ $statusKey }}">{{ $statusConfig['label'] }}</option>
                            @endif
                        @endforeach
                    </select>
                    <textarea name="note" class="form-control mb-4" rows="3" placeholder="Optional note"></textarea>
                    <button type="submit" class="btn btn-primary">Save Status</button>
                </form>
            </div>
        @endif
    </div>
</div>
@endsection
