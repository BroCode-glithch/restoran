@extends('layouts.dashboard')

@section('title', 'Wallet | ' . getSetting('branding.business_name', getSetting('site_title', config('app.name'))))

@section('content')
<div class="ops-hero mb-4">
    <div class="row g-3 align-items-end">
        <div class="col-lg-8">
            <div class="badge bg-white text-dark px-3 py-2 rounded-pill mb-3">Customer Wallet</div>
            <h1 class="fw-bold mb-2">Keep money in your wallet and pay faster.</h1>
            <p class="mb-0 text-white-50">Top up with Korapay, then use the wallet to settle orders without reopening checkout every time.</p>
        </div>
        <div class="col-lg-4 text-lg-end">
            <a href="{{ route('cart.index') }}" class="btn btn-outline-light btn-lg me-2 m-2">Open Cart</a>
            <a href="{{ route('orders.index') }}" class="btn btn-warning btn-lg">Order History</a>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-5">
        <div class="ops-card p-4 mb-4">
            <h4 class="mb-3">Wallet Balance</h4>
            <div class="display-6 fw-bold mb-2">{{ moneyFormat($balance, $wallet->currency) }}</div>
            <div class="small text-muted">This is the live balance available for checkout payments.</div>
        </div>

        <div class="ops-card p-4">
            <h4 class="mb-3">Top Up Wallet</h4>
            <form action="{{ route('wallet.topup') }}" method="POST" class="vstack gap-3">
                @csrf
                <input type="number" name="amount" min="100" step="1" class="form-control mb-4 gap-2" placeholder="Top-up amount" value="{{ old('amount') }}" required>
                <button type="submit" class="btn btn-primary">Fund with Korapay</button>
            </form>
            <div class="small text-muted mt-3">Once payment completes, the wallet will be credited automatically from the Korapay webhook.</div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="ops-card p-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    <h4 class="mb-1">Recent Wallet Activity</h4>
                    <p class="text-muted mb-0">Top-ups and order payments are recorded here.</p>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Reference</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Amount</th>
                            <th>Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $transaction)
                            <tr>
                                <td class="fw-semibold">{{ $transaction->reference }}</td>
                                <td>{{ ucfirst($transaction->type) }}</td>
                                <td><span class="badge {{ $transaction->status === 'completed' ? 'bg-success text-white' : 'bg-warning text-dark' }}">{{ ucfirst($transaction->status) }}</span></td>
                                <td>{{ moneyFormat($transaction->amount, $wallet->currency) }}</td>
                                <td>{{ moneyFormat($transaction->balance_after, $wallet->currency) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">No wallet activity yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $transactions->links() }}
            </div>
        </div>
    </div>
</div>
@endsection