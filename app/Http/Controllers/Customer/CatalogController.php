<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Services\CartService;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index()
    {
        $businessId = currentBusinessId();

        $categories = ProductCategory::query()
            ->where('business_id', $businessId)
            ->where('is_visible', true)
            ->withCount('products')
            ->orderBy('sort_order')
            ->get();

        $products = Product::query()
            ->where('business_id', $businessId)
            ->available()
            ->with('category')
            ->latest()
            ->get();

        return view('customer.catalog', [
            'categories' => $categories,
            'products' => $products,
            'cartCount' => app(CartService::class)->count(),
        ]);
    }

    public function add(Request $request, Product $product, CartService $cartService)
    {
        $this->authorizeProduct($product);

        $quantity = (int) $request->input('quantity', 1);
        $cartService->add($product, $quantity);

        toastr()->success($product->name . ' added to cart.', ['timeOut' => 3000], 'Cart updated');

        return redirect()->route('catalog.index');
    }

    protected function authorizeProduct(Product $product)
    {
        if ($product->business_id !== currentBusinessId()) {
            abort(404);
        }
    }
}
