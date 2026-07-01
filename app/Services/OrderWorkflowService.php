<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\SystemLog;
use App\Notifications\OrderPlacedNotification;
use App\Notifications\OrderStatusUpdatedNotification;
use App\Services\WalletService;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class OrderWorkflowService
{
    public function placeOrder(array $payload, array $cartItems, $customer = null)
    {
        return DB::transaction(function () use ($payload, $cartItems, $customer) {
            $businessId = app(BusinessContext::class)->currentId();
            $subtotal = 0;
            $paymentMethod = isset($payload['payment_method']) ? $payload['payment_method'] : 'korapay';
            $paymentMethod = in_array($paymentMethod, ['korapay', 'wallet'], true)
                ? $paymentMethod
                : 'korapay';
            $paymentReference = isset($payload['payment_reference']) && trim((string) $payload['payment_reference']) !== ''
                ? trim((string) $payload['payment_reference'])
                : ($paymentMethod === 'wallet' ? 'WLT-' . Str::upper(Str::random(8)) : 'KORA-' . Str::upper(Str::random(8)));

            foreach ($cartItems as $item) {
                $subtotal += ((float) $item['price']) * ((int) $item['quantity']);
            }

            $deliveryFee = !empty($payload['delivery_type']) && $payload['delivery_type'] === 'delivery'
                ? app(CartService::class)->deliveryFeeFor(isset($payload['delivery_area']) ? $payload['delivery_area'] : 'inside_school')
                : 0;

            $order = Order::query()->create([
                'business_id' => $businessId,
                'user_id' => $customer ? $customer->id : null,
                'order_number' => $this->generateOrderNumber(),
                'customer_name' => $payload['customer_name'],
                'customer_email' => $payload['customer_email'],
                'customer_phone' => $payload['customer_phone'],
                'delivery_type' => $payload['delivery_type'],
                'delivery_area' => isset($payload['delivery_area']) ? $payload['delivery_area'] : null,
                'status' => 'placed',
                'payment_status' => 'pending',
                'payment_method' => $paymentMethod,
                'currency' => getSetting('operations.currency', 'NGN'),
                'subtotal' => $subtotal,
                'delivery_fee' => $deliveryFee,
                'tax' => isset($payload['tax']) ? (float) $payload['tax'] : 0,
                'discount' => isset($payload['discount']) ? (float) $payload['discount'] : 0,
                'total' => max(0, $subtotal + $deliveryFee + (isset($payload['tax']) ? (float) $payload['tax'] : 0) - (isset($payload['discount']) ? (float) $payload['discount'] : 0)),
                'delivery_address' => isset($payload['delivery_address']) ? $payload['delivery_address'] : null,
                'notes' => isset($payload['notes']) ? $payload['notes'] : null,
                'payment_reference' => $paymentReference,
                'placed_at' => now(),
            ]);

            foreach ($cartItems as $item) {
                OrderItem::query()->create([
                    'business_id' => $businessId,
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['name'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'total_price' => ((float) $item['price']) * ((int) $item['quantity']),
                    'metadata' => isset($item['metadata']) ? $item['metadata'] : null,
                ]);
            }

            OrderStatusHistory::query()->create([
                'business_id' => $businessId,
                'order_id' => $order->id,
                'status' => 'placed',
                'note' => 'Order received and queued for confirmation.',
                'changed_by' => $customer ? $customer->id : null,
            ]);

            SystemLog::query()->create([
                'business_id' => $businessId,
                'actor_user_id' => $customer ? $customer->id : null,
                'level' => 'info',
                'category' => 'orders',
                'message' => 'New order placed: ' . $order->order_number,
                'context' => [
                    'order_id' => $order->id,
                    'status' => 'placed',
                ],
            ]);

            if ($paymentMethod === 'wallet') {
                if (!$customer) {
                    throw ValidationException::withMessages([
                        'payment_method' => 'Wallet payment requires a signed-in customer account.',
                    ]);
                }

                app(WalletService::class)->payOrderFromWallet($order, $customer);
            }

            try {
                $this->notifyOrderPlaced($order, $customer);
            } catch (\Throwable $e) {
                logger()->warning('Order placed notification failed.', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'error' => $e->getMessage(),
                ]);
            }

            return $order->load(['items', 'statusHistories']);
        });
    }

    public function advanceStatus(Order $order, $status, $actor = null, $note = null)
    {
        return DB::transaction(function () use ($order, $status, $actor, $note) {
            if (!$this->canTransition($order->status, $status, $actor)) {
                abort(422, 'This status transition is not allowed.');
            }

            $order->status = $status;
            $timestampField = $status . '_at';

            if (property_exists($order, $timestampField) || in_array($timestampField, $order->getFillable(), true)) {
                $order->{$timestampField} = now();
            }

            $order->save();

            OrderStatusHistory::query()->create([
                'business_id' => $order->business_id,
                'order_id' => $order->id,
                'status' => $status,
                'note' => $note ?: 'Status updated to ' . $status,
                'changed_by' => $actor ? $actor->id : null,
            ]);

            SystemLog::query()->create([
                'business_id' => $order->business_id,
                'actor_user_id' => $actor ? $actor->id : null,
                'level' => 'info',
                'category' => 'orders',
                'message' => 'Order status updated: ' . $order->order_number . ' => ' . $status,
                'context' => [
                    'order_id' => $order->id,
                    'status' => $status,
                ],
            ]);

            try {
                $this->notifyStatusUpdated($order, $actor, $note);
            } catch (\Throwable $e) {
                logger()->warning('Order status notification failed.', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'status' => $status,
                    'error' => $e->getMessage(),
                ]);
            }

            return $order->fresh(['items', 'statusHistories']);
        });
    }

    public function allowedNextStatuses($status)
    {
        $next = app(RoleManager::class)->statusNext($status);

        return $next ? [$next] : [];
    }

    public function canTransition($currentStatus, $nextStatus, $actor = null)
    {
        if ($currentStatus === $nextStatus) {
            return true;
        }

        if ($actor && in_array($actor->role, ['developer', 'super_admin'], true)) {
            return true;
        }

        $allowedNext = $this->allowedNextStatuses($currentStatus);

        return in_array($nextStatus, $allowedNext, true) || $nextStatus === 'cancelled';
    }

    protected function generateOrderNumber()
    {
        return 'FO-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(4));
    }

    protected function notifyOrderPlaced(Order $order, $customer = null)
    {
        if ($customer) {
            $customer->notify(new OrderPlacedNotification($order));
        }

        $teamRoles = ['staff', 'kitchen_staff', 'manager', 'super_admin', 'developer'];
        $teamMembers = User::query()
            ->where('business_id', $order->business_id)
            ->whereIn('role', $teamRoles)
            ->get();

        foreach ($teamMembers as $member) {
            $member->notify(new OrderPlacedNotification($order));
        }
    }

    protected function notifyStatusUpdated(Order $order, $actor = null, $note = null)
    {
        $recipient = $order->customerUser;

        if ($recipient) {
            $recipient->notify(new OrderStatusUpdatedNotification($order, $note));
        }

        if ($order->assignedStaff) {
            $order->assignedStaff->notify(new OrderStatusUpdatedNotification($order, $note));
        }
    }
}
