<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(CartService $cartService)
    {
        return view('customer.cart', [
            'items' => $cartService->items(),
            'subtotal' => $cartService->subtotal(),
            'deliveryFee' => $cartService->deliveryFee(),
            'total' => $cartService->total(),
            'cartCount' => app(CartService::class)->count(),
        ]);
    }

    public function update(Request $request, $productId, CartService $cartService)
    {
        $cartService->update($productId, (int) $request->input('quantity', 1));

        toastr()->success('Cart updated.', 'Success', ['timeOut' => 3000]);

        return redirect()->route('cart.index');
    }

    public function destroy($productId, CartService $cartService)
    {
        $cartService->remove($productId);

        toastr()->success('Item removed from cart.', 'Removed', ['timeOut' => 3000]);

        return redirect()->route('cart.index');
    }

    public function clear(CartService $cartService)
    {
        $cartService->clear();

        toastr()->success('Cart cleared.', 'Success', ['timeOut' => 3000]);

        return redirect()->route('cart.index');
    }
}
