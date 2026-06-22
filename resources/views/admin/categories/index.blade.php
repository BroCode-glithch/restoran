@extends('layouts.dashboard')

@section('title', 'Categories | ' . getSetting('branding.business_name', getSetting('site_title', config('app.name'))))

@section('content')
<div class="ops-hero mb-4">
    <div class="row g-3 align-items-end">
        <div class="col-lg-8">
            <div class="badge bg-white text-dark px-3 py-2 rounded-pill mb-3">Category Management</div>
            <h1 class="fw-bold mb-2">{{ isset($editingCategory) && $editingCategory ? 'Edit Category' : 'Manage Categories' }}</h1>
            <p class="mb-0 text-white-50">Organize products into visible categories for the customer menu.</p>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="ops-setting-card p-4">
            <h4 class="mb-3">{{ isset($editingCategory) && $editingCategory ? 'Edit Category' : 'New Category' }}</h4>
            <form action="{{ isset($editingCategory) && $editingCategory ? route('admin.categories.update', $editingCategory) : route('admin.categories.store') }}" method="POST" class="vstack gap-3">
                @csrf
                @if(isset($editingCategory) && $editingCategory)
                    @method('PUT')
                @endif
                <input type="text" name="name" class="form-control" placeholder="Category name" value="{{ old('name', optional($editingCategory)->name) }}" required>
                <input type="text" name="slug" class="form-control" placeholder="Slug" value="{{ old('slug', optional($editingCategory)->slug) }}">
                <input type="number" name="sort_order" class="form-control" placeholder="Sort order" value="{{ old('sort_order', optional($editingCategory)->sort_order) }}">
                <textarea name="description" class="form-control" rows="4" placeholder="Description">{{ old('description', optional($editingCategory)->description) }}</textarea>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="is_visible" value="1" id="is_visible" {{ old('is_visible', optional($editingCategory)->is_visible ?? true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_visible">Visible on menu</label>
                </div>
                <button class="btn btn-primary" type="submit">{{ isset($editingCategory) && $editingCategory ? 'Update Category' : 'Save Category' }}</button>
            </form>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="ops-card p-4">
            <h4 class="mb-3">Categories</h4>
            <div class="table-responsive">
                <table class="table align-middle ops-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Slug</th>
                            <th>Products</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                            <tr>
                                <td>
                                    <div class="fw-bold">{{ $category->name }}</div>
                                    <small class="text-muted">{{ $category->description }}</small>
                                </td>
                                <td>{{ $category->slug }}</td>
                                <td>{{ $category->products_count }}</td>
                                <td>
                                    <span class="badge {{ $category->is_visible ? 'bg-success' : 'bg-secondary' }}">{{ $category->is_visible ? 'Visible' : 'Hidden' }}</span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-outline-primary btn-sm">Edit</a>
                                    <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-outline-danger btn-sm" type="submit">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted py-5">No categories found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
