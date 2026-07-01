@extends('layouts.dashboard')

@section('title', 'Cart | ' . getSetting('branding.business_name', getSetting('site_title', config('app.name'))))

@section('content')
<div class="ops-hero mb-4">
    <div class="row g-3 align-items-end">
        <div class="col-lg-8">
            <div class="badge bg-white text-dark px-3 py-2 rounded-pill mb-3">Cart and Checkout</div>
            <h1 class="fw-bold mb-2">Review your order and submit checkout.</h1>
            <p class="mb-0 text-white-50">Keep pickup and delivery details updated, then complete a Korapay checkout before placing the order.</p>
        </div>
        <div class="col-lg-4 text-lg-end">
            <a href="{{ route('catalog.index') }}" class="btn btn-outline-light btn-lg me-2 m-2">Continue Shopping</a>
            <a href="{{ route('orders.index') }}" class="btn btn-warning btn-lg">Order History</a>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="ops-card p-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    <h4 class="mb-1">Cart Items</h4>
                    <p class="text-muted mb-0">{{ count($items) }} items in your cart</p>
                </div>
                <form action="{{ route('cart.clear') }}" method="POST">
                    @csrf
                    @method('DELETE')
                    @if ($cartCount > 0)
                        <button class="btn btn-outline-danger" type="submit">Clear Cart</button>
                    @else
                        <button class="btn btn-outline-danger" type="submit" disabled>Clear Cart</button>
                    @endif
                </form>
            </div>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Qty</th>
                            <th>Price</th>
                            <th>Total</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $item)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="{{ mediaUrl($item['image'], asset('assets/img/menu-1.jpg')) }}" alt="{{ $item['name'] }}" style="width:68px;height:68px;object-fit:cover;" class="rounded-3">
                                        <div>
                                            <div class="fw-bold">{{ $item['name'] }}</div>
                                            <small class="text-muted">{{ $item['category'] ?? ucfirst($item['type']) }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td style="width:120px;">
                                    <form action="{{ route('cart.update', $item['product_id']) }}" method="POST" class="d-flex gap-2">
                                        @csrf
                                        @method('PATCH')
                                        <input type="number" min="1" name="quantity" value="{{ $item['quantity'] }}" class="form-control">
                                        <button class="btn btn-light" type="submit"><i class="fa-solid fa-arrows-rotate"></i></button>
                                    </form>
                                </td>
                                <td>{{ moneyFormat($item['price']) }}</td>
                                <td class="fw-bold">{{ moneyFormat($item['price'] * $item['quantity']) }}</td>
                                <td>
                                    <form action="{{ route('cart.remove', $item['product_id']) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-outline-danger" type="submit">Remove</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">Your cart is empty.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="ops-card p-4 mb-4">
            <h4 class="mb-3">Order Summary</h4>
            <div class="d-flex justify-content-between mb-2"><span>Subtotal</span><strong>{{ moneyFormat($subtotal) }}</strong></div>
            <div class="d-flex justify-content-between mb-2">
                <span>Delivery Fee</span>
                <strong id="deliveryFeePreview">{{ moneyFormat($deliveryFee) }}</strong>
            </div>
            <hr>
            <div class="d-flex justify-content-between fs-5">
                <span>Total</span>
                <strong id="cartTotalPreview">{{ moneyFormat($total) }}</strong>
            </div>
            <div class="alert alert-light border-0 mt-3 mb-0">
                <div class="fw-semibold mb-1">Payment ready</div>
                <div class="small text-muted">Use Korapay for direct payment or your wallet if you already topped up funds.</div>
            </div>
        </div>

        <div class="ops-card p-4">
            <h4 class="mb-3">Checkout</h4>
            <form action="{{ route('orders.store') }}" method="POST" class="vstack gap-3">
                @csrf
                <input type="text" name="customer_name" class="form-control mb-4" placeholder="Full name" value="{{ old('customer_name', auth()->user()->name) }}" required>
                <input type="email" name="customer_email" class="form-control mb-4" placeholder="Email address" value="{{ old('customer_email', auth()->user()->email) }}">
                <input type="tel" name="customer_phone" class="form-control mb-4" placeholder="WhatsApp number" value="{{ old('customer_phone', auth()->user()->phone) }}" required>
                <div class="small text-muted mt-n3 mb-2">Please use a WhatsApp number so we can send live order updates.</div>
                <select name="delivery_type" class="form-select mb-4" required>
                    <option value="pickup">Pickup</option>
                    <option value="delivery">Delivery</option>
                </select>
                <div id="deliveryAreaWrap" class="border rounded-4 p-3 bg-light mb-4 {{ old('delivery_type', 'pickup') === 'delivery' ? '' : 'd-none' }}">
                    <div class="fw-bold mb-2">Delivery area</div>
                    <select name="delivery_area" class="form-select mb-2" id="deliveryAreaSelect">
                        <option value="inside_school" {{ old('delivery_area', $deliveryArea) === 'inside_school' ? 'selected' : '' }}>Within school premises - {{ moneyFormat($deliveryFeeInside) }}</option>
                        <option value="outside_school" {{ old('delivery_area') === 'outside_school' ? 'selected' : '' }}>Outside school premises - {{ moneyFormat($deliveryFeeOutside) }}</option>
                    </select>
                    <div class="small text-muted" id="deliveryAreaNote">{{ $deliveryAreaNote }}</div>
                </div>
                <textarea name="delivery_address" class="form-control mb-4" rows="3" placeholder="Delivery address if needed">{{ old('delivery_address') }}</textarea>
                <textarea name="notes" class="form-control mb-4" rows="3" placeholder="Special instructions">{{ old('notes') }}</textarea>

                <div class="border rounded-4 p-3 bg-light mb-4">
                    <div class="fw-bold mb-2">Payment method</div>
                    <select name="payment_method" class="form-select mb-2" required>
                        <option value="korapay" {{ old('payment_method', 'korapay') === 'korapay' ? 'selected' : '' }}>Korapay</option>
                        <option value="wallet" {{ old('payment_method') === 'wallet' ? 'selected' : '' }}>Wallet balance</option>
                    </select>
                    <input type="text" name="payment_reference" class="form-control" placeholder="Payment reference (optional)" value="{{ old('payment_reference') }}">
                    <div class="small text-muted mt-2">Wallet balance: {{ moneyFormat(auth()->user()->walletBalance()) }}. Korapay is used when you need to pay directly.</div>
                </div>
            
                @if ($cartCount > 0)
                    <button type="submit" class="btn btn-primary btn-sm">Pay {{ moneyFormat($total) }} and Place Order</button>
                @else
                    <button type="submit" class="btn btn-primary btn-sm" disabled>Pay {{ moneyFormat($total) }} and Place Order</button>
                @endif
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
    @media (max-width: 575.98px) {
        .checkout-action-row .btn {
            width: 100%;
        }

        .checkout-action-row {
            flex-direction: column;
        }
    }
</style>
@endpush

<script>
    (function () {
        const deliveryTypeSelect = document.querySelector('select[name="delivery_type"]');
        const deliveryAreaWrap = document.getElementById('deliveryAreaWrap');
        const deliveryAreaSelect = document.getElementById('deliveryAreaSelect');
        const deliveryAreaNote = document.getElementById('deliveryAreaNote');
        const deliveryFeePreview = document.getElementById('deliveryFeePreview');
        const cartTotalPreview = document.getElementById('cartTotalPreview');
        const subtotal = {{ (float) $subtotal }};
        const deliveryFees = {
            inside_school: {{ (float) $deliveryFeeInside }},
            outside_school: {{ (float) $deliveryFeeOutside }},
        };
        const deliveryNotes = {
            inside_school: 'For customers receiving orders inside the school premises.',
            outside_school: 'For addresses outside the school gate and surrounding area.',
        };

        function formatMoney(amount) {
            return amount.toFixed(2) + ' {{ getSetting('operations.currency', 'NGN') }}';
        }

        function updateSummary() {
            const deliveryType = deliveryTypeSelect ? deliveryTypeSelect.value : 'pickup';
            const deliveryArea = deliveryAreaSelect ? deliveryAreaSelect.value : 'inside_school';
            const fee = deliveryType === 'delivery' ? (deliveryFees[deliveryArea] || 0) : 0;

            if (deliveryAreaWrap) {
                deliveryAreaWrap.classList.toggle('d-none', deliveryType !== 'delivery');
            }

            if (deliveryAreaNote) {
                deliveryAreaNote.textContent = deliveryNotes[deliveryArea] || deliveryNotes.inside_school;
            }

            if (deliveryFeePreview) {
                deliveryFeePreview.textContent = formatMoney(fee);
            }

            if (cartTotalPreview) {
                cartTotalPreview.textContent = formatMoney(subtotal + fee);
            }
        }

        if (deliveryTypeSelect) {
            deliveryTypeSelect.addEventListener('change', updateSummary);
        }

        if (deliveryAreaSelect) {
            deliveryAreaSelect.addEventListener('change', updateSummary);
        }

        updateSummary();
    }());
</script>
@endsection
