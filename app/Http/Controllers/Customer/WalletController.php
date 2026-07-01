<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\WalletTransaction;
use App\Services\KorapayService;
use App\Services\WalletService;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function index(Request $request, WalletService $walletService)
    {
        $user = auth()->user();
        $wallet = $walletService->walletFor($user);

        if ($request->filled('reference')) {
            $transaction = WalletTransaction::query()
                ->where('reference', $request->string('reference'))
                ->where('wallet_id', $wallet->id)
                ->first();

            if ($transaction && $transaction->status !== 'completed') {
                session()->flash('swal', [
                    'type' => 'info',
                    'title' => 'Top-up pending',
                    'message' => 'Korapay returned your wallet reference. We are waiting for the payment confirmation.',
                    'ok_text' => 'OK',
                ]);
            }
        }

        $transactions = $wallet->transactions()
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('customer.wallet.index', [
            'wallet' => $wallet,
            'transactions' => $transactions,
            'balance' => $walletService->balanceFor($user),
        ]);
    }

    public function topUp(Request $request, KorapayService $korapayService, WalletService $walletService)
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:100'],
        ]);

        $user = auth()->user();
        $wallet = $walletService->walletFor($user);
        $amount = round((float) $validated['amount'], 2);
        $transaction = $walletService->createTopupTransaction($user, $amount, null, [
            'source' => 'wallet-topup',
        ]);

        $checkoutUrl = $korapayService->initializeCharge([
            'amount' => (int) round($amount),
            'currency' => strtoupper((string) $wallet->currency),
            'reference' => $transaction->reference,
            'redirect_url' => route('wallet.index'),
            'notification_url' => route('payments.korapay.webhook'),
            'narration' => 'Wallet top-up for ' . $user->name,
            'customer' => [
                'name' => $user->name,
                'email' => $user->email,
            ],
            'merchant_bears_cost' => true,
        ]);

        if (!$checkoutUrl) {
            $transaction->delete();

            toastr()->error('Wallet top-up could not be started. Check your Korapay keys in settings.', ['timeOut' => 4000], 'Top-up unavailable');
            session()->flash('swal', [
                'type' => 'error',
                'title' => 'Top-up unavailable',
                'message' => 'Wallet top-up could not be started. Check your Korapay keys in settings.',
                'ok_text' => 'OK',
            ]);

            return redirect()->route('wallet.index');
        }

        return redirect()->away($checkoutUrl);
    }
}