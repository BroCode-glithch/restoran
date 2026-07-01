<?php

namespace App\Services;

use App\Models\Order;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class KorapayService
{
    protected string $initializeEndpoint = 'https://api.korapay.com/merchant/api/v1/charges/initialize';

    public function initializeCharge(array $payload): ?string
    {
        $publicKey = trim((string) getSetting('integrations.korapay_public_key'));
        $secretKey = trim((string) getSetting('integrations.korapay_secret_key'));

        if ($publicKey === '' || $secretKey === '') {
            return null;
        }

        $response = Http::withToken($secretKey)
            ->acceptJson()
            ->asJson()
            ->post($this->initializeEndpoint, $payload);

        if (!$response->successful()) {
            Log::warning('Korapay checkout initialization failed.', [
                'status' => $response->status(),
                'body' => $response->body(),
                'payload' => $payload,
            ]);

            return null;
        }

        $checkoutUrl = data_get($response->json(), 'data.checkout_url');

        if (empty($checkoutUrl)) {
            Log::warning('Korapay checkout initialization returned no checkout URL.', [
                'response' => $response->json(),
                'payload' => $payload,
            ]);

            return null;
        }

        return $checkoutUrl;
    }

    public function initializeCheckout(Order $order, ?string $redirectUrl = null, ?string $notificationUrl = null): ?string
    {
        return $this->initializeCharge([
            'amount' => (int) round((float) $order->total),
            'currency' => strtoupper((string) ($order->currency ?: getSetting('operations.currency', 'NGN'))),
            'reference' => $order->payment_reference ?: $order->order_number,
            'redirect_url' => $redirectUrl ?: route('orders.show', $order),
            'notification_url' => $notificationUrl ?: route('payments.korapay.webhook'),
            'narration' => 'Payment for order ' . $order->order_number,
            'customer' => [
                'name' => $order->customer_name,
                'email' => $order->customer_email ?: optional($order->customerUser)->email,
            ],
            'merchant_bears_cost' => true,
        ]);
    }

    public function initializeTopup(WalletTransaction $transaction, string $customerName, string $customerEmail, ?string $redirectUrl = null, ?string $notificationUrl = null): ?string
    {
        return $this->initializeCharge([
            'amount' => (int) round((float) $transaction->amount),
            'currency' => strtoupper((string) getSetting('operations.currency', 'NGN')),
            'reference' => $transaction->reference,
            'redirect_url' => $redirectUrl ?: route('wallet.index'),
            'notification_url' => $notificationUrl ?: route('payments.korapay.webhook'),
            'narration' => 'Wallet top-up for ' . $customerName,
            'customer' => [
                'name' => $customerName,
                'email' => $customerEmail,
            ],
            'merchant_bears_cost' => true,
        ]);
    }
}





