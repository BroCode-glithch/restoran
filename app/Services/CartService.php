<?php

namespace App\Services;

use App\Models\Product;

class CartService
{
    protected $sessionKey = 'foodops.cart';

    public function deliveryFeeFor($deliveryArea = null)
    {
        $deliveryArea = $deliveryArea ?: 'inside_school';
        $insideFee = (float) getSetting('operations.delivery_fee_inside_school', getSetting('operations.delivery_fee', 0));
        $outsideFee = (float) getSetting('operations.delivery_fee_outside_school', max($insideFee, 0));

        return $deliveryArea === 'outside_school' ? $outsideFee : $insideFee;
    }

    public function deliveryAreaLabel($deliveryArea = null)
    {
        $deliveryArea = $deliveryArea ?: 'inside_school';

        if ($deliveryArea === 'outside_school') {
            return 'Outside school premises';
        }

        return 'Within school premises';
    }

    public function deliveryAreaNote($deliveryArea = null)
    {
        $deliveryArea = $deliveryArea ?: 'inside_school';

        if ($deliveryArea === 'outside_school') {
            return 'For addresses outside the school gate and surrounding area.';
        }

        return 'For customers receiving orders inside the school premises.';
    }

    public function items()
    {
        return session()->get($this->sessionKey, []);
    }

    public function count()
    {
        return count($this->items());
    }

    public function add(Product $product, $quantity = 1)
    {
        $items = $this->items();
        $productId = (string) $product->id;

        if (!isset($items[$productId])) {
            $items[$productId] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => (float) $product->price,
                'quantity' => 0,
                'image' => $product->image,
                'type' => $product->type,
                'category' => optional($product->category)->name,
            ];
        }

        $items[$productId]['quantity'] += max(1, (int) $quantity);
        session()->put($this->sessionKey, $items);

        return $items[$productId];
    }

    public function update($productId, $quantity)
    {
        $items = $this->items();
        $productId = (string) $productId;

        if (!isset($items[$productId])) {
            return;
        }

        $quantity = (int) $quantity;

        if ($quantity <= 0) {
            unset($items[$productId]);
        } else {
            $items[$productId]['quantity'] = $quantity;
        }

        session()->put($this->sessionKey, $items);
    }

    public function remove($productId)
    {
        $items = $this->items();
        $productId = (string) $productId;

        if (isset($items[$productId])) {
            unset($items[$productId]);
            session()->put($this->sessionKey, $items);
        }
    }

    public function clear()
    {
        session()->forget($this->sessionKey);
    }

    public function subtotal()
    {
        $subtotal = 0;

        foreach ($this->items() as $item) {
            $subtotal += ((float) $item['price']) * ((int) $item['quantity']);
        }

        return $subtotal;
    }

    public function deliveryFee()
    {
        return $this->deliveryFeeFor('inside_school');
    }

    public function total($deliveryArea = null)
    {
        return $this->subtotal() + $this->deliveryFeeFor($deliveryArea);
    }
}
