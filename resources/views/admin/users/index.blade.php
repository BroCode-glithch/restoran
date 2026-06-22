@extends('layouts.dashboard')

@section('title', 'Users | ' . getSetting('branding.business_name', getSetting('site_title', config('app.name'))))

@section('content')
<div class="ops-hero mb-4">
    <div class="row g-3 align-items-end">
        <div class="col-lg-8">
            <div class="badge bg-white text-dark px-3 py-2 rounded-pill mb-3">User Management</div>
            <h1 class="fw-bold mb-2">Assign roles and keep accounts active.</h1>
            <p class="mb-0 text-white-50">Only super admins and developers can change roles.</p>
        </div>
    </div>
</div>

<div class="ops-card p-4">
    <div class="table-responsive">
        <table class="table align-middle ops-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->phone }}</td>
                        <td>
                            @if($canManageRoles && $user->id !== auth()->id())
                                <form action="{{ route('admin.users.update', $user) }}" method="POST" class="d-flex gap-2 align-items-center">
                                    @csrf
                                    @method('PUT')
                                    <select name="role" class="form-select form-select-sm">
                                        @foreach($roles as $role)
                                            <option value="{{ $role }}" {{ $user->role === $role ? 'selected' : '' }}>{{ roleLabel($role) }}</option>
                                        @endforeach
                                    </select>
                                    <button class="btn btn-sm btn-outline-primary" type="submit">Save</button>
                                </form>
                            @else
                                <span class="badge {{ roleBadgeClass($user->role) }}">{{ roleLabel($user->role) }}</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $user->is_active ? 'bg-success' : 'bg-secondary' }}">{{ $user->is_active ? 'Active' : 'Inactive' }}</span>
                        </td>
                        <td>
                            @if($canManageRoles && $user->id !== auth()->id())
                                <form action="{{ route('admin.users.update', $user) }}" method="POST" class="d-flex gap-2">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="role" value="{{ $user->role }}">
                                    <input type="hidden" name="phone" value="{{ $user->phone }}">
                                    <input type="hidden" name="is_active" value="{{ $user->is_active ? 0 : 1 }}">
                                    <button class="btn btn-sm {{ $user->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}" type="submit">
                                        {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-5">No users found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
