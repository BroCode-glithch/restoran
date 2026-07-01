<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\WalletTransaction;
use App\Services\CartService;
use App\Services\KorapayService;
use App\Services\OrderWorkflowService;
use App\Services\WalletService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $perPage = $this->perPage(request());

        $orders = Order::query()
            ->where('business_id', currentBusinessId())
            ->when($user->isCustomer(), function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->when($user->isStaff(), function ($query) use ($user) {
                $query->where('assigned_staff_id', $user->id);
            })
            ->when($user->isKitchenStaff(), function ($query) {
                $query->whereIn('status', ['placed', 'confirmed', 'preparing', 'ready']);
            })
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return view('customer.orders.index', compact('orders', 'user', 'perPage'));
    }

    public function show(Request $request, Order $order)
    {
        $this->authorizeOrder($order);

        $order->load(['items.product', 'statusHistories.actor']);

        if ($order->payment_method === 'korapay' && $request->filled('reference') && $order->payment_status !== 'paid') {
            session()->flash('swal', [
                'type' => 'info',
                'title' => 'Payment in progress',
                'message' => 'Korapay returned your order reference. We are waiting for the payment confirmation.',
                'ok_text' => 'OK',
            ]);
        }

        $kitchenContact = getSetting('notifications.kitchen_whatsapp_number', getSetting('contact.whatsapp_number'));
        $kitchenWhatsappMessage = 'Greetings! New order alert for kitchen: ' . $order->order_number . ' | Customer: ' . $order->customer_name . ' | Status: ' . orderStatusLabel($order->status) . ' | Total: ' . moneyFormat($order->total, $order->currency);

        return view('customer.orders.show', compact('order', 'kitchenContact', 'kitchenWhatsappMessage'));
    }

    public function korapayCheckout(Order $order, KorapayService $korapayService)
    {
        $this->authorizeOrder($order);

        if ($order->payment_method !== 'korapay') {
            return redirect()->route('orders.show', $order);
        }

        $checkoutUrl = $korapayService->initializeCheckout(
            $order,
            route('orders.show', $order),
            route('payments.korapay.webhook')
        );

        if (!$checkoutUrl) {
            toastr()->error('Korapay checkout could not be started. Check your Korapay keys in settings.', ['timeOut' => 4000], 'Checkout unavailable');
            session()->flash('swal', [
                'type' => 'error',
                'title' => 'Checkout unavailable',
                'message' => 'Korapay checkout could not be started. Check your Korapay keys in settings.',
                'ok_text' => 'OK',
            ]);

            return redirect()->route('orders.show', $order);
        }

        return redirect()->away($checkoutUrl);
    }

    public function korapayWebhook(Request $request, WalletService $walletService)
    {
        $payload = $request->all();
        $event = data_get($payload, 'event');

        if ($event !== 'charge.success') {
            return response()->json(['ok' => true]);
        }

        $reference = data_get($payload, 'data.payment_reference') ?: data_get($payload, 'data.reference');

        if (empty($reference)) {
            return response()->json(['ok' => false, 'message' => 'Missing reference'], 422);
        }

        $order = Order::query()->where('payment_reference', $reference)->first();

        if ($order) {
            if ($order->payment_status !== 'paid') {
                $order->payment_status = 'paid';
                $order->save();
            }

            return response()->json(['ok' => true]);
        }

        $walletTransaction = WalletTransaction::query()->where('reference', $reference)->first();

        if (!$walletTransaction) {
            return response()->json(['ok' => false, 'message' => 'Payment target not found'], 404);
        }

        if ($walletTransaction->status !== 'completed') {
            $walletService->completeTopup($walletTransaction);
        }

        return response()->json(['ok' => true]);
    }

    public function store(Request $request, CartService $cartService, OrderWorkflowService $orderWorkflowService)
    {
        $items = $cartService->items();

        if (empty($items)) {
            toastr()->error('Your cart is empty.', ['timeOut' => 3000], 'Checkout blocked');
            session()->flash('swal', [
                'type' => 'error',
                'title' => 'Checkout blocked',
                'message' => 'Your cart is empty.',
                'ok_text' => 'OK',
            ]);
            return redirect()->route('catalog.index');
        }

        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['nullable', 'email', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:30'],
            'delivery_type' => ['required', 'in:pickup,delivery'],
            'delivery_address' => ['nullable', 'string', 'max:1000'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'payment_method' => ['required', 'in:korapay,wallet'],
            'payment_reference' => ['nullable', 'string', 'max:255'],
        ]);

        $order = $orderWorkflowService->placeOrder($validated, $items, auth()->user());
        $cartService->clear();

        if (($validated['payment_method'] ?? '') === 'korapay') {
            return redirect()->route('orders.korapay.checkout', $order);
        }

        toastr()->success('Order paid from wallet balance.', ['timeOut' => 3000], 'Order received');
        session()->flash('swal', [
            'type' => 'success',
            'title' => 'Order received',
            'message' => 'Order paid from wallet balance.',
            'ok_text' => 'OK',
        ]);

        return redirect()->route('orders.show', $order);
    }

    public function updateStatus(Request $request, Order $order, OrderWorkflowService $orderWorkflowService)
    {
        $this->authorizeOrder($order, true);

        $validated = $request->validate([
            'status' => ['required', 'string'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        $orderWorkflowService->advanceStatus($order, $validated['status'], auth()->user(), isset($validated['note']) ? $validated['note'] : null);

        toastr()->success('Order status updated.', ['timeOut' => 3000], 'Status saved');
        session()->flash('swal', [
            'type' => 'success',
            'title' => 'Status saved',
            'message' => 'Order status updated.',
            'ok_text' => 'OK',
        ]);

        return redirect()->back();
    }

    protected function authorizeOrder(Order $order, $canManage = false)
    {
        if ($order->business_id !== currentBusinessId()) {
            abort(404);
        }

        $user = auth()->user();

        if ($canManage) {
            if (!$user->canAccessRole('staff')) {
                abort(403);
            }

            return;
        }

        if ($user->isCustomer() && $order->user_id !== $user->id) {
            abort(403);
        }
    }

    protected function perPage(Request $request)
    {
        $allowed = [10, 12, 20, 50];
        $perPage = (int) $request->query('per_page', 12);

        return in_array($perPage, $allowed, true) ? $perPage : 12;
    }
}
