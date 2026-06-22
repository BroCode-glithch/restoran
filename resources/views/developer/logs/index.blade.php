@extends('layouts.dashboard')

@section('title', 'Logs | ' . getSetting('branding.business_name', getSetting('site_title', config('app.name'))))

@section('content')
<div class="ops-hero mb-4">
    <div class="row g-3 align-items-end">
        <div class="col-lg-8">
            <div class="badge bg-white text-dark px-3 py-2 rounded-pill mb-3">System Logs</div>
            <h1 class="fw-bold mb-2">Developer observability and audit trail.</h1>
            <p class="mb-0 text-white-50">Track configuration changes, workflow actions and operational events.</p>
        </div>
    </div>
</div>

<div class="ops-card p-4 mb-4">
    <form class="row g-3" method="GET">
        <div class="col-md-4">
            <input type="text" name="category" class="form-control" placeholder="Category filter" value="{{ request('category') }}">
        </div>
        <div class="col-md-4">
            <input type="text" name="level" class="form-control" placeholder="Level filter" value="{{ request('level') }}">
        </div>
        <div class="col-md-4">
            <button class="btn btn-primary w-100" type="submit">Filter Logs</button>
        </div>
    </form>
</div>

<div class="ops-card p-4">
    <div class="table-responsive">
        <table class="table align-middle ops-table">
            <thead>
                <tr>
                    <th>Level</th>
                    <th>Category</th>
                    <th>Message</th>
                    <th>Actor</th>
                    <th>Time</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td><span class="badge bg-dark">{{ strtoupper($log->level) }}</span></td>
                        <td>{{ ucfirst($log->category) }}</td>
                        <td>{{ $log->message }}</td>
                        <td>{{ optional($log->actor)->name ?: 'System' }}</td>
                        <td>{{ optional($log->created_at)->diffForHumans() }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-5">No logs available.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $logs->links() }}
    </div>
</div>
@endsection
