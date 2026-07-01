@extends('layouts.dashboard')

@section('title', 'Inventory | ' . getSetting('branding.business_name', getSetting('site_title', config('app.name'))))

@section('content')
<div class="ops-hero mb-4">
    <div class="row g-3 align-items-end">
        <div class="col-lg-8">
            <div class="badge bg-white text-dark px-3 py-2 rounded-pill mb-3">Inventory & Stock</div>
            <h1 class="fw-bold mb-2">Manage inventory with confidence</h1>
            <p class="mb-0 text-white-50">Track stock, monitor low items, record stock movement, and keep the canteen running smoothly.</p>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="ops-card p-4">
            <div class="text-muted small">Total Items</div>
            <div class="display-6 fw-bold">{{ $summary['total_items'] }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="ops-card p-4">
            <div class="text-muted small">Low Stock</div>
            <div class="display-6 fw-bold text-warning">{{ $summary['low_stock'] }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="ops-card p-4">
            <div class="text-muted small">Expiring Soon</div>
            <div class="display-6 fw-bold text-danger">{{ $summary['expiring_soon'] }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="ops-card p-4">
            <div class="text-muted small">Stock Value</div>
            <div class="display-6 fw-bold">{{ moneyFormat($summary['stock_value']) }}</div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="ops-setting-card p-4 mb-4">
            <h4 class="mb-3">Add stock item</h4>
            <form action="{{ route('admin.inventory.items.store') }}" method="POST" class="vstack gap-3">
                @csrf
                <input type="text" name="name" class="form-control mb-4" placeholder="Item name" required>
                <input type="text" name="sku" class="form-control mb-4" placeholder="SKU (optional)">
                <input type="text" name="unit" class="form-control mb-4" placeholder="Unit (e.g. bag, crate, carton)">
                <input type="text" name="category" class="form-control mb-4" placeholder="Category (e.g. staple, produce)">
                <input type="number" step="0.01" name="current_stock" class="form-control mb-4" placeholder="Opening stock" value="0" required>
                <input type="number" step="0.01" name="reorder_level" class="form-control mb-4" placeholder="Reorder level" value="0" required>
                <input type="number" step="0.01" name="cost_price" class="form-control mb-4" placeholder="Cost price" value="0" required>
                <input type="number" step="0.01" name="selling_price" class="form-control mb-4" placeholder="Selling price" value="0" required>
                <input type="date" name="expiry_date" class="form-control mb-4">
                <select name="supplier_id" class="form-select mb-4">
                    <option value="">No supplier</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                    @endforeach
                </select>
                <textarea name="notes" class="form-control mb-4" rows="3" placeholder="Notes"></textarea>
                <button class="btn btn-primary" type="submit">Save item</button>
            </form>
        </div>

        <div class="ops-setting-card p-4">
            <h4 class="mb-3">Add supplier</h4>
            <form action="{{ route('admin.inventory.suppliers.store') }}" method="POST" class="vstack gap-3">
                @csrf
                <input type="text" name="name" class="form-control mb-4" placeholder="Supplier name" required>
                <input type="text" name="contact_person" class="form-control mb-4" placeholder="Contact person">
                <input type="text" name="phone" class="form-control mb-4" placeholder="Phone">
                <input type="email" name="email" class="form-control mb-4" placeholder="Email">
                <textarea name="address" class="form-control mb-4" rows="2" placeholder="Address"></textarea>
                <textarea name="notes" class="form-control mb-4" rows="2" placeholder="Notes"></textarea>
                <button class="btn btn-outline-primary" type="submit">Save supplier</button>
            </form>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="ops-setting-card p-4 mb-4">
            <h4 class="mb-3">Record stock movement</h4>
            <form action="{{ route('admin.inventory.movements.store') }}" method="POST" class="row g-3">
                @csrf
                <div class="col-md-4">
                    <select name="inventory_item_id" class="form-select" required>
                        <option value="">Select item</option>
                        @foreach($items as $item)
                            <option value="{{ $item->id }}">{{ $item->name }} ({{ $item->current_stock }} {{ $item->unit }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="movement_type" class="form-select" required>
                        <option value="stock_in">Stock In</option>
                        <option value="stock_out">Stock Out</option>
                        <option value="waste">Waste</option>
                        <option value="adjustment">Adjustment</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="number" step="0.01" name="quantity" class="form-control" placeholder="Qty" required>
                </div>
                <div class="col-md-3">
                    <input type="text" name="reference" class="form-control" placeholder="Reference">
                </div>
                <div class="col-12">
                    <textarea name="notes" class="form-control" rows="2" placeholder="Notes"></textarea>
                </div>
                <div class="col-12">
                    <button class="btn btn-success" type="submit">Save movement</button>
                </div>
            </form>
        </div>

        <div class="ops-card p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h4 class="mb-1">Stock list</h4>
                    <p class="text-muted mb-0">Current stock, reorder level, and expiry alerts.</p>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table align-middle ops-table">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Stock</th>
                            <th>Reorder</th>
                            <th>Supplier</th>
                            <th>Expiry</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $item)
                            <tr>
                                <td>
                                    <div class="fw-bold">{{ $item->name }}</div>
                                    <small class="text-muted">{{ $item->sku ?: 'No SKU' }}</small>
                                </td>
                                <td>{{ number_format($item->current_stock, 2) }} {{ $item->unit }}</td>
                                <td>{{ number_format($item->reorder_level, 2) }} {{ $item->unit }}</td>
                                <td>{{ optional($item->supplier)->name ?? '—' }}</td>
                                <td>{{ $item->expiry_date ? $item->expiry_date->format('Y-m-d') : '—' }}</td>
                                <td>
                                    @if($item->isLowStock())
                                        <span class="badge bg-warning text-dark">Low stock</span>
                                    @elseif($item->isExpiringSoon())
                                        <span class="badge bg-danger">Expiring soon</span>
                                    @else
                                        <span class="badge bg-success">Healthy</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted py-5">No inventory items yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
