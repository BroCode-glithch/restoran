@extends('layouts.dashboard')

@section('title', 'Services | ' . getSetting('branding.business_name', getSetting('site_title', config('app.name'))))

@section('content')
<div class="ops-hero mb-4">
    <div class="row g-3 align-items-end">
        <div class="col-lg-8">
            <div class="badge bg-white text-dark px-3 py-2 rounded-pill mb-3">Service Management</div>
            <h1 class="fw-bold mb-2">{{ isset($editingService) && $editingService ? 'Edit Service' : 'Manage Services' }}</h1>
            <p class="mb-0 text-white-50">Update icons, titles and descriptions from the dashboard without touching code.</p>
        </div>
        <div class="col-lg-4 text-lg-end">
            <span class="badge bg-white text-dark px-3 py-2 rounded-pill">Visible on public pages</span>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="ops-setting-card p-4 h-100">
            <h4 class="mb-3">{{ isset($editingService) && $editingService ? 'Edit Service' : 'New Service' }}</h4>
            <form action="{{ isset($editingService) && $editingService ? route('admin.services.update', $editingService) : route('admin.services.store') }}" method="POST" class="vstack gap-3">
                @csrf
                @if(isset($editingService) && $editingService)
                    @method('PUT')
                @endif
                <input type="text" name="icon" class="form-control" placeholder="Icon class e.g. fa-solid fa-bowl-food" value="{{ old('icon', optional($editingService)->icon) }}" required>
                <input type="text" name="title" class="form-control" placeholder="Service title" value="{{ old('title', optional($editingService)->title) }}" required>
                <textarea name="description" class="form-control" rows="5" placeholder="Service description">{{ old('description', optional($editingService)->description) }}</textarea>
                <button class="btn btn-primary" type="submit">{{ isset($editingService) && $editingService ? 'Update Service' : 'Save Service' }}</button>
            </form>

            <div class="alert alert-light border-0 mt-4 mb-0">
                <div class="fw-semibold mb-1">Tip</div>
                <div class="small text-muted">Use Font Awesome class names like <code>fa-solid fa-truck-fast</code> or <code>fa-solid fa-bowl-food</code>.</div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="ops-card p-4">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
                <div>
                    <h4 class="mb-1">Services</h4>
                    <p class="text-muted mb-0">These cards are reused on the public pages and home page.</p>
                </div>

                <form method="GET" class="d-flex align-items-center gap-2">
                    <label for="servicePerPage" class="small text-muted mb-0">Per page</label>
                    <select id="servicePerPage" name="per_page" class="form-select form-select-sm" style="width:auto;" onchange="this.form.submit()">
                        @foreach([5, 10, 20, 50] as $option)
                            <option value="{{ $option }}" {{ (int) ($perPage ?? 10) === $option ? 'selected' : '' }}>{{ $option }}</option>
                        @endforeach
                    </select>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table align-middle ops-table">
                    <thead>
                        <tr>
                            <th>Icon</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($services as $service)
                            <tr>
                                <td style="width:84px;">
                                    <span class="ops-nav-icon" style="margin:0;">
                                        <i class="{{ $service->icon }}"></i>
                                    </span>
                                </td>
                                <td class="fw-semibold">{{ $service->title }}</td>
                                <td class="text-muted">{{ $service->description }}</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.services.edit', $service) }}" class="btn btn-outline-primary btn-sm mb-2">Edit</a>
                                    <form action="{{ route('admin.services.destroy', $service) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-outline-danger btn-sm" type="submit">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-5">No services found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $services->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
