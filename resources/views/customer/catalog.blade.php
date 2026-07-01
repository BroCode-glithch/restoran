@extends('layouts.dashboard')

@section('title', 'Menu | ' . getSetting('branding.business_name', getSetting('site_title', config('app.name'))))

@section('content')
<div class="ops-hero ops-hero-soft mb-4">
    <div class="row g-3 align-items-end">
        <div class="col-lg-8">
            <div class="badge px-3 py-2 rounded-pill mb-3">Customer Menu</div>
            <h1 class="fw-bold mb-2">Browse menu and add items to your cart.</h1>
            <p class="mb-0 text-muted">Fresh meals, drinks and catering packages are ready for quick ordering.</p>
        </div>
        <div class="col-lg-4 text-lg-end">
            <a href="{{ route('cart.index') }}" class="btn btn-warning btn-lg">Cart ({{ $cartCount }})</a>
        </div>
    </div>
</div>

@if($categories->count())
    <div class="d-flex flex-wrap gap-2 mb-4">
        @foreach($categories as $category)
            <a href="#category-{{ $category->id }}" class="btn btn-outline-primary rounded-pill">{{ $category->name }}</a>
        @endforeach
    </div>
@endif

@forelse($categories as $category)
    <div id="category-{{ $category->id }}" class="mb-4">
        <div class="d-flex align-items-end justify-content-between mb-3">
            <div>
                <h3 class="mb-1">{{ $category->name }}</h3>
                <p class="text-muted mb-0">{{ $category->description }}</p>
            </div>
            <span class="badge bg-light text-dark">{{ $category->products_count }} items</span>
        </div>
        <div class="row g-4">
            @foreach($products->where('category_id', $category->id) as $product)
                <div class="col-md-6 col-xl-4">
                    <div class="ops-product-card h-100">
                        <img src="{{ mediaUrl($product->image, asset('assets/img/menu-1.jpg')) }}" alt="{{ $product->name }}" class="ops-product-image">
                        <div class="p-4">
                            <div class="d-flex justify-content-between align-items-start gap-3 mb-2">
                                <div>
                                    <h5 class="mb-1">{{ $product->name }}</h5>
                                    <small class="text-muted">{{ $product->type }} @if($product->preparation_time_minutes) - {{ $product->preparation_time_minutes }} mins @endif</small>
                                </div>
                                <div class="fw-bold text-primary">{{ moneyFormat($product->price) }}</div>
                            </div>
                            <p class="text-muted">{{ $product->description }}</p>
                            <form action="{{ route('catalog.add', $product) }}" method="POST" class="d-flex gap-2">
                                @csrf
                                <input type="number" name="quantity" value="1" min="1" class="form-control" style="max-width: 92px;">
                                <button class="btn btn-primary flex-grow-1" type="submit">
                                    <i class="fa-solid fa-cart-plus me-1"></i>Add to Cart
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@empty
    <div class="ops-card p-5 text-center">
        <h4 class="mb-2">No menu items yet.</h4>
        <p class="text-muted mb-0">Add products in the admin panel to start accepting orders.</p>
    </div>
@endforelse
@endsection
