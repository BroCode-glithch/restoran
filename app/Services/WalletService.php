<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class WalletService
{
    public function walletFor(User $user): Wallet
    {
        return Wallet::query()->firstOrCreate(
            [
                'business_id' => $user->business_id ?: currentBusinessId(),
                'user_id' => $user->id,
            ],
            [
                'currency' => getSetting('operations.currency', 'NGN'),
                'balance' => 0,
                'status' => 'active',
            ]
        );
    }

    public function balanceFor(User $user): float
    {
        $wallet = $user->wallet;

        return $wallet ? (float) $wallet->balance : 0.0;
    }

    public function createTopupTransaction(User $user, float $amount, ?string $reference = null, array $meta = []): WalletTransaction
    {
        $wallet = $this->walletFor($user);
        $reference = $reference ?: 'WLT-' . Str::upper(Str::random(10));

        return WalletTransaction::query()->create([
            'business_id' => $wallet->business_id,
            'wallet_id' => $wallet->id,
            'reference' => $reference,
            'type' => 'topup',
            'method' => 'korapay',
            'status' => 'pending',
            'amount' => $amount,
            'balance_before' => $wallet->balance,
            'balance_after' => $wallet->balance,
            'narration' => 'Wallet top-up for ' . $user->name,
            'meta' => $meta,
        ]);
    }

    public function payOrderFromWallet(Order $order, User $customer): WalletTransaction
    {
        return DB::transaction(function () use ($order, $customer) {
            $wallet = Wallet::query()
                ->where('business_id', $customer->business_id ?: currentBusinessId())
                ->where('user_id', $customer->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ((float) $wallet->balance < (float) $order->total) {
                throw ValidationException::withMessages([
                    'payment_method' => 'Wallet balance is insufficient. Top up your wallet or choose Korapay.',
                ]);
            }

            $balanceBefore = (float) $wallet->balance;
            $balanceAfter = round($balanceBefore - (float) $order->total, 2);
            $reference = $order->payment_reference ?: 'WLT-' . Str::upper(Str::random(10));

            $wallet->balance = $balanceAfter;
            $wallet->save();

            $transaction = WalletTransaction::query()->create([
                'business_id' => $wallet->business_id,
                'wallet_id' => $wallet->id,
                'order_id' => $order->id,
                'reference' => $reference,
                'type' => 'payment',
                'method' => 'wallet',
                'status' => 'completed',
                'amount' => $order->total,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'narration' => 'Order payment for ' . $order->order_number,
                'meta' => [
                    'order_number' => $order->order_number,
                ],
            ]);

            $order->payment_status = 'paid';
            $order->payment_reference = $reference;
            $order->save();

            return $transaction;
        });
    }

    public function completeTopup(WalletTransaction $transaction): WalletTransaction
    {
        return DB::transaction(function () use ($transaction) {
            $transaction = WalletTransaction::query()
                ->whereKey($transaction->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($transaction->status === 'completed') {
                return $transaction;
            }

            $wallet = Wallet::query()->whereKey($transaction->wallet_id)->lockForUpdate()->firstOrFail();
            $balanceBefore = (float) $wallet->balance;
            $balanceAfter = round($balanceBefore + (float) $transaction->amount, 2);

            $wallet->balance = $balanceAfter;
            $wallet->save();

            $transaction->status = 'completed';
            $transaction->balance_before = $balanceBefore;
            $transaction->balance_after = $balanceAfter;
            $transaction->save();

            return $transaction;
        });
    }
}