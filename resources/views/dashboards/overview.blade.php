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
        <div class="col-lg-4">
            @if(!empty($actions))
                <div class="d-flex flex-wrap gap-2 justify-content-lg-end">
                    @foreach($actions as $action)
                        <a href="{{ route($action['route']) }}" class="btn {{ $action['variant'] ?? 'btn-outline-light' }} btn-lg">
                            @if(!empty($action['icon']))
                                <i class="{{ $action['icon'] }}"></i>
                            @endif
                            {{ $action['label'] }}
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

@if(!empty($insights))
    <div class="row g-3 mb-4">
        @foreach($insights as $insight)
            <div class="col-md-4">
                <div class="ops-insight-card h-100 p-4">
                    <div class="small text-uppercase text-muted fw-semibold mb-2">{{ $insight['label'] }}</div>
                    <div class="display-6 fw-bold mb-2">{{ $insight['value'] }}</div>
                    <p class="text-muted mb-0">{{ $insight['note'] ?? '' }}</p>
                </div>
            </div>
        @endforeach
    </div>
@endif

@if(!empty($stats))
    <div class="ops-stats-grid mb-4">
        @foreach($stats as $stat)
            <div class="ops-stat">
                <div class="d-flex justify-content-between align-items-start gap-3">
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

@if(!empty($charts))
    <div class="row g-4 mb-4">
        @foreach($charts as $chart)
            <div class="{{ $chart['column_class'] ?? 'col-lg-6' }}">
                <div class="ops-card ops-chart-card h-100 p-4">
                    <div class="d-flex align-items-start justify-content-between gap-3 mb-3">
                        <div>
                            <div class="small text-uppercase text-muted fw-semibold mb-2">Trend</div>
                            <h4 class="mb-1">{{ $chart['title'] }}</h4>
                            <p class="text-muted mb-0">{{ $chart['subtitle'] ?? '' }}</p>
                        </div>
                        <span class="ops-kpi-chip">{{ $chart['type'] === 'doughnut' ? 'Distribution' : 'Trend' }}</span>
                    </div>

                    <div class="ops-chart-wrap" style="height: {{ $chart['height'] ?? 280 }}px;">
                        <canvas id="{{ $chart['id'] }}"></canvas>
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
                    <div class="p-4 d-flex flex-column h-100">
                        <div class="d-flex justify-content-between align-items-start gap-3 mb-2">
                            <div>
                                @if(!empty($card['meta']))
                                    <div class="ops-kpi-chip mb-2">{{ $card['meta'] }}</div>
                                @endif
                                <h5 class="mb-1">{{ $card['title'] }}</h5>
                                <small class="text-muted">{{ $card['subtitle'] }}</small>
                            </div>
                            @if(isset($card['price']))
                                <div class="fw-bold text-primary text-nowrap">{{ moneyFormat($card['price']) }}</div>
                            @endif
                        </div>
                        <p class="text-muted flex-grow-1 mb-3">{{ $card['description'] }}</p>
                        @if(isset($card['action_route']))
                            <a href="{{ route($card['action_route']) }}" class="btn btn-primary w-100">
                                {{ $card['action_label'] ?? 'Open' }}
                            </a>
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

@push('scripts')
    @if(!empty($charts))
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.9/dist/chart.umd.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var charts = @json($charts ?? []);
                var currency = @json(getSetting('operations.currency', 'NGN'));

                if (!charts.length || typeof Chart === 'undefined') {
                    return;
                }

                charts.forEach(function (chart) {
                    var canvas = document.getElementById(chart.id);

                    if (!canvas) {
                        return;
                    }

                    new Chart(canvas, {
                        type: chart.type,
                        data: {
                            labels: chart.labels,
                            datasets: chart.datasets.map(function (dataset) {
                                var defaults = {
                                    borderColor: dataset.borderColor || 'rgba(254, 161, 22, 0.9)',
                                    backgroundColor: dataset.backgroundColor || 'rgba(254, 161, 22, 0.16)',
                                    tension: chart.type === 'line' ? 0.35 : 0,
                                    fill: chart.type === 'line',
                                    borderWidth: chart.type === 'line' ? 3 : 0,
                                    pointRadius: chart.type === 'line' ? 3 : 0,
                                    pointHoverRadius: chart.type === 'line' ? 5 : 0,
                                    pointBackgroundColor: dataset.pointBackgroundColor || '#FEA116',
                                    pointBorderColor: dataset.pointBorderColor || '#ffffff',
                                    pointBorderWidth: dataset.pointBorderWidth || 2,
                                    hoverOffset: 8
                                };

                                return Object.assign(defaults, dataset);
                            })
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: chart.type === 'doughnut' ? (chart.cutout || '68%') : undefined,
                            plugins: {
                                legend: {
                                    display: chart.legend !== false,
                                    position: 'bottom',
                                    labels: {
                                        usePointStyle: true,
                                        pointStyle: 'circle',
                                        boxWidth: 10,
                                        boxHeight: 10,
                                        padding: 16,
                                        color: '#64748b'
                                    }
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function (context) {
                                            var value = Number(context.parsed);
                                            var label = context.dataset.label ? context.dataset.label + ': ' : '';

                                            if (chart.format === 'currency') {
                                                return label + value.toLocaleString(undefined, {
                                                    minimumFractionDigits: 2,
                                                    maximumFractionDigits: 2
                                                }) + ' ' + currency;
                                            }

                                            return label + value.toLocaleString();
                                        }
                                    }
                                }
                            },
                            scales: chart.type === 'line' ? {
                                x: {
                                    grid: {
                                        display: false
                                    },
                                    ticks: {
                                        color: '#64748b'
                                    }
                                },
                                y: {
                                    beginAtZero: true,
                                    grid: {
                                        color: 'rgba(15, 23, 43, 0.08)'
                                    },
                                    ticks: {
                                        color: '#64748b',
                                        precision: 0
                                    }
                                }
                            } : {}
                        }
                    });
                });
            });
        </script>
    @endif
@endpush
