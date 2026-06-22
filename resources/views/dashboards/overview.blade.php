@extends('layouts.dashboard')

@section('title', $title . ' | ' . getSetting('branding.business_name', getSetting('site_title', config('app.name'))))

@section('content')
<div class="ops-hero mb-4">
    <div class="row g-3 align-items-end">
        <div class="col-lg-8">
            <div class="badge bg-white text-dark px-3 py-2 rounded-pill mb-3">{{ roleLabel($role) }}</div>
            <h1 class="fw-bold mb-2">{{ $title }}</h1>
            <p class="mb-0 text-white-50">{{ $subtitle }}</p>
        </div>
        <div class="col-lg-4 text-lg-end">
            @if($role === 'customer')
                <a href="{{ route('catalog.index') }}" class="btn btn-warning btn-lg me-2">Browse Menu</a>
                <a href="{{ route('cart.index') }}" class="btn btn-outline-light btn-lg">Open Cart</a>
            @else
                <a href="{{ route('orders.index') }}" class="btn btn-warning btn-lg me-2">Open Orders</a>
                @if(in_array($role, ['manager', 'super_admin', 'developer'], true))
                    <a href="{{ route('admin.settings.index') }}" class="btn btn-outline-light btn-lg">Settings</a>
                @endif
            @endif
        </div>
    </div>
</div>

@if(!empty($stats))
    <div class="ops-stats-grid mb-4">
        @foreach($stats as $stat)
            <div class="ops-stat">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="small text-muted fw-semibold">{{ $stat['label'] }}</div>
                        <div class="display-6 fw-bold mb-1">{{ $stat['value'] }}</div>
                        <div class="small text-muted">{{ $stat['note'] ?? '' }}</div>
                    </div>
                    <div class="ops-stat-icon">
                        <i class="{{ $stat['icon'] }}"></i>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif

@if(!empty($cards))
    <div class="row g-4 mb-4">
        @foreach($cards as $card)
            <div class="col-md-6 col-xl-4">
                <div class="ops-product-card h-100">
                    <img src="{{ $card['image'] }}" alt="{{ $card['title'] }}" class="ops-product-image">
                    <div class="p-4">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h5 class="mb-1">{{ $card['title'] }}</h5>
                                <small class="text-muted">{{ $card['subtitle'] }}</small>
                            </div>
                            @if(isset($card['price']))
                                <div class="fw-bold text-primary">{{ number_format($card['price'], 2) }}</div>
                            @endif
                        </div>
                        <p class="text-muted mb-3">{{ $card['description'] }}</p>
                        @if(isset($card['action_route']))
                            <a href="{{ route($card['action_route']) }}" class="btn btn-primary w-100">Open</a>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif

@if(!empty($tables))
    <div class="row g-4">
        @foreach($tables as $table)
            <div class="col-12">
                <div class="ops-card p-4">
                    <div class="d-flex align-items-start justify-content-between gap-3 mb-3">
                        <div>
                            <h4 class="mb-1">{{ $table['title'] }}</h4>
                            <p class="text-muted mb-0">{{ $table['description'] ?? '' }}</p>
                        </div>
                        @if(isset($table['action_route']))
                            <a href="{{ route($table['action_route']) }}" class="btn btn-outline-primary">{{ $table['action_label'] ?? 'Open' }}</a>
                        @endif
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle ops-table mb-0">
                            <thead>
                                <tr>
                                    @foreach($table['columns'] as $column)
                                        <th>{{ $column }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($table['rows'] as $row)
                                    <tr>
                                        @foreach($row as $cell)
                                            <td>{!! $cell !!}</td>
                                        @endforeach
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ count($table['columns']) }}" class="text-center text-muted py-5">
                                            {{ $table['empty'] ?? 'No data available.' }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection
