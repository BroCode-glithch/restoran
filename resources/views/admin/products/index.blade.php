@extends('layouts.dashboard')

@section('title', 'Products | ' . getSetting('branding.business_name', getSetting('site_title', config('app.name'))))

@section('content')
<div class="ops-hero mb-4">
    <div class="row g-3 align-items-end">
        <div class="col-lg-8">
            <div class="badge bg-white text-dark px-3 py-2 rounded-pill mb-3">Product Management</div>
            <h1 class="fw-bold mb-2">{{ isset($editingProduct) && $editingProduct ? 'Edit Product' : 'Manage Products' }}</h1>
            <p class="mb-0 text-white-50">Create, update and retire products without touching code.</p>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="ops-setting-card p-4">
            <h4 class="mb-3">{{ isset($editingProduct) && $editingProduct ? 'Edit Product' : 'New Product' }}</h4>
            <form action="{{ isset($editingProduct) && $editingProduct ? route('admin.products.update', $editingProduct) : route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="vstack gap-3">
                @csrf
                @if(isset($editingProduct) && $editingProduct)
                    @method('PUT')
                @endif
                <input type="text" name="name" class="form-control mb-4" placeholder="Product name" value="{{ old('name', optional($editingProduct)->name) }}" required>
                <input type="text" name="slug" class="form-control mb-4" placeholder="Slug" value="{{ old('slug', optional($editingProduct)->slug) }}">
                <select name="category_id" class="form-select mb-4">
                    <option value="">Select category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', optional($editingProduct)->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
                <select name="type" class="form-select mb-4" required>
                    <option value="meal" {{ old('type', optional($editingProduct)->type) === 'meal' ? 'selected' : '' }}>Meal</option>
                    <option value="drink" {{ old('type', optional($editingProduct)->type) === 'drink' ? 'selected' : '' }}>Drink</option>
                    <option value="catering" {{ old('type', optional($editingProduct)->type) === 'catering' ? 'selected' : '' }}>Catering</option>
                </select>
                <input type="number" step="0.01" name="price" class="form-control mb-4" placeholder="Price" value="{{ old('price', optional($editingProduct)->price) }}" required>
                <input type="number" name="preparation_time_minutes" class="form-control mb-4" placeholder="Prep time (minutes)" value="{{ old('preparation_time_minutes', optional($editingProduct)->preparation_time_minutes) }}">
                <textarea name="description" class="form-control mb-4" rows="4" placeholder="Description">{{ old('description', optional($editingProduct)->description) }}</textarea>
                <input type="file" name="image" class="form-control mb-4">
                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" name="availability" value="1" id="availability" {{ old('availability', optional($editingProduct)->availability) ? 'checked' : '' }}>
                    <label class="form-check-label" for="availability">Available</label>
                </div>
                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="is_featured" {{ old('is_featured', optional($editingProduct)->is_featured) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_featured">Featured product</label>
                </div>
                <button class="btn btn-primary" type="submit">{{ isset($editingProduct) && $editingProduct ? 'Update Product' : 'Save Product' }}</button>
            </form>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="ops-card p-4">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
                <div>
                    <h4 class="mb-1">Products</h4>
                    <p class="text-muted mb-0">Use the checkboxes to activate or hide items for today.</p>
                </div>
                <div class="d-flex flex-wrap align-items-center gap-3">
                    <form method="GET" class="d-flex align-items-center gap-2">
                        <label for="productsPerPage" class="small text-muted mb-0">Per page</label>
                        <select id="productsPerPage" name="per_page" class="form-select form-select-sm" style="width:auto;" onchange="this.form.submit()">
                            @foreach([5, 10, 20, 50] as $option)
                                <option value="{{ $option }}" {{ (int) ($perPage ?? 10) === $option ? 'selected' : '' }}>{{ $option }}</option>
                            @endforeach
                        </select>
                    </form>
                    <div class="d-flex gap-2">
                        <button form="bulk-products-form" name="availability_action" value="activate" type="submit" class="btn btn-success btn-sm">Mark Available</button>
                        <button form="bulk-products-form" name="availability_action" value="hide" type="submit" class="btn btn-outline-secondary btn-sm">Hide Selected</button>
                    </div>
                </div>
            </div>

            <form id="bulk-products-form" action="{{ route('admin.products.bulk-availability') }}" method="POST" class="d-none">
                @csrf
            </form>

            <div class="table-responsive">
                <table class="table align-middle ops-table">
                    <thead>
                        <tr>
                            <th style="width:44px;"></th>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Type</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                            <tr>
                                <td>
                                    <input type="checkbox" name="product_ids[]" value="{{ $product->id }}" form="bulk-products-form" class="form-check-input">
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="{{ mediaUrl($product->image, asset('assets/img/menu-1.jpg')) }}" alt="{{ $product->name }}" style="width:62px;height:62px;object-fit:cover;" class="rounded-3">
                                        <div>
                                            <div class="fw-bold">{{ $product->name }}</div>
                                            <small class="text-muted">{{ $product->description }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ optional($product->category)->name }}</td>
                                <td>{{ ucfirst($product->type) }}</td>
                                <td>{{ moneyFormat($product->price) }}</td>
                                <td>
                                    <span class="badge {{ $product->availability ? 'bg-success' : 'bg-secondary' }}">{{ $product->availability ? 'Available' : 'Hidden' }}</span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-outline-primary btn-sm mb-2">Edit</a>
                                    <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-outline-danger btn-sm" type="submit">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-muted py-5">No products found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $products->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
