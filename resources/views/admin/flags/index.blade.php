@extends('layouts.dashboard')

@section('title', 'Feature Flags | ' . getSetting('branding.business_name', getSetting('site_title', config('app.name'))))

@section('content')
<div class="ops-hero mb-4">
    <div class="row g-3 align-items-end">
        <div class="col-lg-8">
            <div class="badge bg-white text-dark px-3 py-2 rounded-pill mb-3">Feature Flags</div>
            <h1 class="fw-bold mb-2">{{ isset($editingFlag) && $editingFlag ? 'Edit Feature Flag' : 'Manage Toggles' }}</h1>
            <p class="mb-0 text-white-50">Turn modules on and off from the database in real time.</p>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="ops-setting-card p-4">
            <h4 class="mb-3">{{ isset($editingFlag) && $editingFlag ? 'Edit Toggle' : 'New Toggle' }}</h4>
            <form action="{{ isset($editingFlag) && $editingFlag ? route('admin.flags.update', $editingFlag) : route('admin.flags.store') }}" method="POST" class="vstack gap-3">
                @csrf
                @if(isset($editingFlag) && $editingFlag)
                    @method('PUT')
                @endif
                <input type="text" name="key" class="form-control mb-4" placeholder="Flag key" value="{{ old('key', optional($editingFlag)->key) }}" required>
                <input type="text" name="label" class="form-control mb-4" placeholder="Label" value="{{ old('label', optional($editingFlag)->label) }}" required>
                <textarea name="description" class="form-control mb-4" rows="4" placeholder="Description">{{ old('description', optional($editingFlag)->description) }}</textarea>
                <div class="form-check mb-4">
                    <input type="hidden" name="enabled" value="0">
                    <input class="form-check-input" type="checkbox" name="enabled" value="1" id="enabled" {{ old('enabled', optional($editingFlag)->enabled) ? 'checked' : '' }}>
                    <label class="form-check-label" for="enabled">Enabled</label>
                </div>
                <button class="btn btn-primary" type="submit">{{ isset($editingFlag) && $editingFlag ? 'Update Flag' : 'Save Flag' }}</button>
            </form>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="ops-card p-4">
            <h4 class="mb-3">Toggles</h4>
            <div class="table-responsive">
                <table class="table align-middle ops-table">
                    <thead>
                        <tr>
                            <th>Flag</th>
                            <th>Status</th>
                            <th>Description</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($flags as $flag)
                            <tr>
                                <td>
                                    <div class="fw-bold">{{ $flag->label }}</div>
                                    <small class="text-muted">{{ $flag->key }}</small>
                                </td>
                                <td><span class="badge {{ $flag->enabled ? 'bg-success' : 'bg-secondary' }}">{{ $flag->enabled ? 'Enabled' : 'Disabled' }}</span></td>
                                <td>{{ $flag->description }}</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.flags.edit', $flag) }}" class="btn btn-outline-primary btn-sm m-2">Edit</a>
                                    <form action="{{ route('admin.flags.destroy', $flag) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-outline-danger btn-sm" type="submit">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted py-5">No feature flags found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
