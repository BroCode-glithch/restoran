@extends('layouts.dashboard')

@section('title', 'Orders | ' . getSetting('branding.business_name', getSetting('site_title', config('app.name'))))

@section('content')
<div class="ops-hero mb-4">
    <div class="row g-3 align-items-end">
        <div class="col-lg-8">
            <div class="badge bg-white text-dark px-3 py-2 rounded-pill mb-3">Order Tracking</div>
            <h1 class="fw-bold mb-2">Track every order from one timeline.</h1>
            <p class="mb-0 text-white-50">Customer orders, staff assignments and kitchen progress all live here.</p>
        </div>
        <div class="col-lg-4 text-lg-end">
            <a href="{{ route('catalog.index') }}" class="btn btn-outline-light btn-lg me-2">Browse Menu</a>
            <a href="{{ route('cart.index') }}" class="btn btn-warning btn-lg">Cart</a>
        </div>
    </div>
</div>

<div class="ops-card p-4">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
        <div>
            <h4 class="mb-1">Orders</h4>
            <p class="text-muted mb-0">Showing {{ $orders->count() }} records on this page.</p>
        </div>
        <form method="GET" class="d-flex align-items-center gap-2">
            <label for="ordersPerPage" class="small text-muted mb-0">Per page</label>
            <select id="ordersPerPage" name="per_page" class="form-select form-select-sm" style="width:auto;" onchange="this.form.submit()">
                @foreach([10, 12, 20, 50] as $option)
                    <option value="{{ $option }}" {{ (int) ($perPage ?? 12) === $option ? 'selected' : '' }}>{{ $option }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table align-middle ops-table">
                    <thead>
                        <tr>
                            <th>Order</th>
                            <th>Customer</th>
                            <th>Status</th>
                            <th>Payment</th>
                            <th>Delivery</th>
                            <th>Total</th>
                            <th>Updated</th>
                            <th></th>
                        </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td class="fw-bold">{{ $order->order_number }}</td>
                        <td>
                            <div>{{ $order->customer_name }}</div>
                            <small class="text-muted">{{ $order->customer_phone }}</small>
                        </td>
                        <td><span class="badge {{ orderStatusBadge($order->status) }}">{{ orderStatusLabel($order->status) }}</span></td>
                        <td>
                            <div class="fw-semibold">{{ ucfirst(str_replace('_', ' ', $order->payment_status)) }}</div>
                            <small class="text-muted">{{ ucfirst(str_replace('_', ' ', $order->payment_method ?: 'demo_card')) }}</small>
                        </td>
                        <td>{{ ucfirst($order->delivery_type) }}</td>
                        <td class="fw-bold">{{ moneyFormat($order->total, $order->currency) }}</td>
                        <td>{{ optional($order->updated_at)->diffForHumans() }}</td>
                        <td class="text-end">
                            <a href="{{ route('orders.show', $order) }}" class="btn btn-outline-primary btn-sm">View</a>
                            @if(auth()->user()->canAccessRole('staff'))
                                @php
                                    $customerWhatsappUrl = whatsappChatUrl(
                                        $order->customer_phone,
                                        'Hello ' . $order->customer_name . ', your order ' . $order->order_number . ' is currently ' . orderStatusLabel($order->status) . '.'
                                    );

                                    $statusOptions = auth()->user()->canAccessRole('super_admin')
                                        ? config('foodops.order_status_pipeline')
                                        : array_filter([
                                            orderStatusNext($order->status) => config('foodops.order_status_pipeline.' . orderStatusNext($order->status)),
                                            'cancelled' => config('foodops.order_status_pipeline.cancelled'),
                                        ]);
                                @endphp
                                @if($customerWhatsappUrl)
                                    <a href="{{ $customerWhatsappUrl }}" target="_blank" rel="noopener" class="btn btn-success btn-sm ms-2 mt-2">
                                        <i class="fa-brands fa-whatsapp me-1"></i>WhatsApp
                                    </a>
                                @endif
                                <form action="{{ route('orders.status.update', $order) }}" method="POST" class="d-inline-flex gap-2 ms-2 mt-2">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" class="form-select form-select-sm">
                                        @foreach($statusOptions as $statusKey => $statusConfig)
                                            @if($statusKey && $statusConfig && $statusKey !== $order->status)
                                                <option value="{{ $statusKey }}">{{ $statusConfig['label'] }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                    <button class="btn btn-sm btn-primary" type="submit">Update</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-5">No orders found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $orders->links() }}
    </div>
</div>
@endsection
