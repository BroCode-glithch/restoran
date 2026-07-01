<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use App\Models\InventoryMovement;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class InventoryController extends Controller
{
    public function index()
    {
        $businessId = currentBusinessId();

        $items = InventoryItem::query()
            ->where('business_id', $businessId)
            ->with('supplier')
            ->latest()
            ->get();

        $suppliers = Supplier::query()
            ->where('business_id', $businessId)
            ->latest()
            ->get();

        $summary = [
            'total_items' => $items->count(),
            'low_stock' => $items->filter(fn ($item) => $item->isLowStock())->count(),
            'expiring_soon' => $items->filter(fn ($item) => $item->isExpiringSoon())->count(),
            'stock_value' => $items->sum(fn ($item) => (float) $item->current_stock * (float) $item->cost_price),
        ];

        return view('admin.inventory.index', compact('items', 'suppliers', 'summary'));
    }

    public function storeItem(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['nullable', 'string', 'max:100'],
            'unit' => ['nullable', 'string', 'max:50'],
            'category' => ['nullable', 'string', 'max:100'],
            'current_stock' => ['required', 'numeric', 'min:0'],
            'reorder_level' => ['required', 'numeric', 'min:0'],
            'cost_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'expiry_date' => ['nullable', 'date'],
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'notes' => ['nullable', 'string'],
        ]);

        $item = InventoryItem::create($data);

        if ((float) $data['current_stock'] > 0) {
            InventoryMovement::create([
                'inventory_item_id' => $item->id,
                'movement_type' => 'stock_in',
                'quantity' => $data['current_stock'],
                'unit_cost' => $data['cost_price'],
                'reference' => 'opening-stock',
                'notes' => 'Opening stock entry',
            ]);
        }

        toastr()->success('Stock item added.', ['timeOut' => 3000], 'Inventory');
        session()->flash('swal', [
            'type' => 'success',
            'title' => 'Inventory',
            'message' => 'Stock item added.',
            'ok_text' => 'OK',
        ]);

        return redirect()->route('admin.inventory.index');
    }

    public function storeSupplier(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        Supplier::create($data);

        toastr()->success('Supplier saved.', ['timeOut' => 3000], 'Inventory');
        session()->flash('swal', [
            'type' => 'success',
            'title' => 'Inventory',
            'message' => 'Supplier saved.',
            'ok_text' => 'OK',
        ]);

        return redirect()->route('admin.inventory.index');
    }

    public function storeMovement(Request $request)
    {
        $data = $request->validate([
            'inventory_item_id' => ['required', 'exists:inventory_items,id'],
            'movement_type' => ['required', 'in:stock_in,stock_out,waste,adjustment'],
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'reference' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $item = InventoryItem::findOrFail($data['inventory_item_id']);

        $quantity = (float) $data['quantity'];

        switch ($data['movement_type']) {
            case 'stock_in':
                $item->current_stock = (float) $item->current_stock + $quantity;
                break;
            case 'stock_out':
            case 'waste':
                $item->current_stock = max(0, (float) $item->current_stock - $quantity);
                break;
            case 'adjustment':
                $item->current_stock = $quantity;
                break;
        }

        $item->save();

        InventoryMovement::create([
            'inventory_item_id' => $item->id,
            'movement_type' => $data['movement_type'],
            'quantity' => $quantity,
            'unit_cost' => $item->cost_price,
            'reference' => $data['reference'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);

        toastr()->success('Stock movement recorded.', ['timeOut' => 3000], 'Inventory');
        session()->flash('swal', [
            'type' => 'success',
            'title' => 'Inventory',
            'message' => 'Stock movement recorded.',
            'ok_text' => 'OK',
        ]);

        return redirect()->route('admin.inventory.index');
    }
}
