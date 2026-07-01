@extends('layouts.dashboard')

@section('title', $product->name . ' | ' . getSetting('branding.business_name', getSetting('site_title', config('app.name'))))

@section('content')
<div class="ops-hero ops-hero-soft mb-4">
    <div class="row g-3 align-items-end">
        <div class="col-lg-8">
            <div class="badge px-3 py-2 rounded-pill mb-3">Product Details</div>
            <h1 class="fw-bold mb-2">{{ $product->name }}</h1>
            <p class="mb-0 text-muted">View the full product details before adding it to your cart.</p>
        </div>
        <div class="col-lg-4">
            <div class="d-flex flex-wrap gap-2 justify-content-lg-end">
                <a href="{{ route('dashboard') }}" class="btn btn-outline-light btn-lg">Back to Dashboard</a>
                <a href="{{ route('catalog.index') }}" class="btn btn-primary btn-lg">Back to Menu</a>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 align-items-start">
    <div class="col-lg-7">
        <div class="ops-product-card h-100">
            <img src="{{ mediaUrl($product->image, asset('assets/img/menu-1.jpg')) }}" alt="{{ $product->name }}" class="ops-product-image" style="height: 360px;">
            <div class="p-4">
                <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                    <span class="ops-kpi-chip">{{ $product->type }}</span>
                    @if(optional($product->category)->name)
                        <span class="badge bg-light text-dark">{{ $product->category->name }}</span>
                    @endif
                    <span class="badge {{ $product->availability ? 'bg-success' : 'bg-secondary' }}">{{ $product->availability ? 'Available now' : 'Unavailable' }}</span>
                </div>
                <p class="text-muted mb-4">{{ $product->description ?: 'No description has been added for this item yet.' }}</p>

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="ops-insight-card p-3 h-100">
                            <div class="small text-uppercase text-muted fw-semibold mb-1">Price</div>
                            <div class="h3 fw-bold mb-0">{{ moneyFormat($product->price) }}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="ops-insight-card p-3 h-100">
                            <div class="small text-uppercase text-muted fw-semibold mb-1">Prep Time</div>
                            <div class="h3 fw-bold mb-0">{{ $product->preparation_time_minutes ? $product->preparation_time_minutes . ' mins' : 'Quick serve' }}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="ops-insight-card p-3 h-100">
                            <div class="small text-uppercase text-muted fw-semibold mb-1">Category</div>
                            <div class="h3 fw-bold mb-0">{{ optional($product->category)->name ?: 'Uncategorized' }}</div>
                        </div>
                    </div>
                </div>

                <form action="{{ route('catalog.add', $product) }}" method="POST" class="row g-3 align-items-end">
                    @csrf
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Quantity</label>
                        <input type="number" name="quantity" value="1" min="1" class="form-control" required>
                    </div>
                    <div class="col-md-8 d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-lg flex-grow-1">
                            <i class="fa-solid fa-cart-plus"></i>
                            Add to Cart
                        </button>
                        <a href="{{ route('cart.index') }}" class="btn btn-outline-primary btn-lg">Cart ({{ $cartCount }})</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="ops-card p-4 mb-4">
            <h4 class="mb-3">Quick Actions</h4>
            <div class="d-grid gap-2">
                <a href="{{ route('catalog.index') }}" class="btn btn-outline-primary">Browse More Items</a>
                <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">View My Orders</a>
                <a href="{{ route('dashboard') }}" class="btn btn-outline-dark">Return to Dashboard</a>
            </div>
        </div>

        @if($relatedProducts->count())
            <div class="ops-card p-4">
                <h4 class="mb-3">Related Items</h4>
                <div class="vstack gap-3">
                    @foreach($relatedProducts as $relatedProduct)
                        <a href="{{ route('catalog.show', $relatedProduct) }}" class="d-flex align-items-center gap-3 text-decoration-none text-reset">
                            <img src="{{ mediaUrl($relatedProduct->image, asset('assets/img/menu-1.jpg')) }}" alt="{{ $relatedProduct->name }}" style="width:72px;height:72px;object-fit:cover;" class="rounded-4">
                            <div class="flex-grow-1">
                                <div class="fw-semibold">{{ $relatedProduct->name }}</div>
                                <small class="text-muted">{{ moneyFormat($relatedProduct->price) }}</small>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
@endsection