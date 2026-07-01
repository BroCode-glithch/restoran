@extends('layouts.dashboard')

@section('title', 'Sales Reports | ' . getSetting('branding.business_name', getSetting('site_title', config('app.name'))))

@section('content')
<div class="ops-hero mb-4">
    <div class="row g-3 align-items-end">
        <div class="col-lg-8">
            <div class="badge bg-white text-dark px-3 py-2 rounded-pill mb-3">Sales Reporting</div>
            <h1 class="fw-bold mb-2">Manager Sales & Performance</h1>
            <p class="mb-0 text-white-50">Review revenue, order flow, payment mix and top products in one place.</p>
        </div>
        <div class="col-lg-4 d-flex justify-content-lg-end align-items-center">
            <a href="{{ route('manager.dashboard') }}" class="btn btn-outline-light btn-lg">
                <i class="fa-solid fa-arrow-left me-2"></i>Back to dashboard
            </a>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="ops-card p-4">
            <div class="text-muted small">Revenue Today</div>
            <div class="display-6 fw-bold">{{ moneyFormat($revenueToday) }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="ops-card p-4">
            <div class="text-muted small">Orders Completed</div>
            <div class="display-6 fw-bold">{{ $ordersToday }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="ops-card p-4">
            <div class="text-muted small">Last 7 days</div>
            <div class="display-6 fw-bold">{{ moneyFormat($revenueLast7Days) }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="ops-card p-4">
            <div class="text-muted small">Average Ticket</div>
            <div class="display-6 fw-bold">{{ moneyFormat($averageTicket) }}</div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="ops-card p-4 h-100">
            <div class="d-flex align-items-start justify-content-between gap-3 mb-3">
                <div>
                    <h4 class="mb-1">Revenue trend</h4>
                    <p class="text-muted mb-0">Completed order value for the last 7 days.</p>
                </div>
                <span class="ops-kpi-chip">Revenue</span>
            </div>
            <div class="ops-chart-wrap" style="height: 320px;">
                <canvas id="report-revenue-trend"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="ops-card p-4 mb-4 h-100">
            <div class="d-flex align-items-start justify-content-between gap-3 mb-3">
                <div>
                    <h4 class="mb-1">Payment mix</h4>
                    <p class="text-muted mb-0">Completed orders by payment channel.</p>
                </div>
                <span class="ops-kpi-chip">Payments</span>
            </div>
            <div class="ops-chart-wrap" style="height: 260px;">
                <canvas id="report-payment-breakdown"></canvas>
            </div>
        </div>
        <div class="ops-card p-4 h-100">
            <div class="d-flex align-items-start justify-content-between gap-3 mb-3">
                <div>
                    <h4 class="mb-1">Order status</h4>
                    <p class="text-muted mb-0">Pipeline distribution across all orders.</p>
                </div>
                <span class="ops-kpi-chip">Status</span>
            </div>
            <div class="ops-chart-wrap" style="height: 260px;">
                <canvas id="report-status-breakdown"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="ops-card p-4">
            <div class="d-flex align-items-center justify-content-between gap-3 mb-3">
                <div>
                    <h4 class="mb-1">Best sellers</h4>
                    <p class="text-muted mb-0">Products with the highest quantity sold.</p>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table align-middle ops-table mb-0">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Qty Sold</th>
                            <th>Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bestSellers as $item)
                            <tr>
                                <td>{{ $item->product_name ?? 'Unknown' }}</td>
                                <td>{{ $item->quantity_sold }}</td>
                                <td>{{ moneyFormat($item->sales) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center text-muted py-5">No completed product sales yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="ops-card p-4">
            <div class="d-flex align-items-center justify-content-between gap-3 mb-3">
                <div>
                    <h4 class="mb-1">Slow movers</h4>
                    <p class="text-muted mb-0">Available products with the lowest completed sales.</p>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table align-middle ops-table mb-0">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Qty Sold</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($slowMovers as $item)
                            <tr>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->quantity_sold }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="text-center text-muted py-5">No product sales data available.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.9/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var revenueSeries = @json($trendSeries);
            var paymentOptions = @json($chartOptions['payment']);
            var statusOptions = @json($chartOptions['status']);

            var currency = @json(getSetting('operations.currency', 'NGN'));

            if (typeof Chart !== 'undefined') {
                var revenueCanvas = document.getElementById('report-revenue-trend');
                if (revenueCanvas) {
                    new Chart(revenueCanvas, {
                        type: 'line',
                        data: {
                            labels: revenueSeries.labels,
                            datasets: [{
                                label: 'Revenue',
                                data: revenueSeries.values,
                                borderColor: '#FEA116',
                                backgroundColor: 'rgba(254, 161, 22, 0.16)',
                                fill: true,
                                tension: 0.35,
                                pointRadius: 3,
                                pointBackgroundColor: '#FEA116',
                                pointBorderColor: '#ffffff',
                                pointBorderWidth: 2,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                tooltip: {
                                    callbacks: {
                                        label: function (context) {
                                            return context.dataset.label + ': ' + Number(context.parsed.y).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' ' + currency;
                                        }
                                    }
                                }
                            },
                            scales: {
                                x: { grid: { display: false }, ticks: { color: '#64748b' } },
                                y: { beginAtZero: true, grid: { color: 'rgba(15, 23, 43, 0.08)' }, ticks: { color: '#64748b', precision: 0 } }
                            }
                        }
                    });
                }

                var paymentCanvas = document.getElementById('report-payment-breakdown');
                if (paymentCanvas) {
                    new Chart(paymentCanvas, {
                        type: 'doughnut',
                        data: {
                            labels: paymentOptions.labels,
                            datasets: [{ data: paymentOptions.values, backgroundColor: paymentOptions.colors, borderWidth: 0 }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { position: 'bottom' } }
                        }
                    });
                }

                var statusCanvas = document.getElementById('report-status-breakdown');
                if (statusCanvas) {
                    new Chart(statusCanvas, {
                        type: 'doughnut',
                        data: {
                            labels: statusOptions.labels,
                            datasets: [{ data: statusOptions.values, backgroundColor: statusOptions.colors, borderWidth: 0 }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { position: 'bottom' } }
                        }
                    });
                }
            }
        });
    </script>
@endpush
