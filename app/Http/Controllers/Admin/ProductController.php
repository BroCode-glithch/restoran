<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemLog;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index()
    {
        $businessId = currentBusinessId();
        $perPage = $this->perPage(request());

        return view('admin.products.index', [
            'products' => Product::query()->where('business_id', $businessId)->with('category')->latest()->paginate($perPage)->withQueryString(),
            'categories' => ProductCategory::query()->where('business_id', $businessId)->orderBy('sort_order')->get(),
            'editingProduct' => null,
            'perPage' => $perPage,
        ]);
    }

    public function edit(Product $product)
    {
        $this->authorizeProduct($product);

        $businessId = currentBusinessId();
        $perPage = $this->perPage(request());

        return view('admin.products.index', [
            'products' => Product::query()->where('business_id', $businessId)->with('category')->latest()->paginate($perPage)->withQueryString(),
            'categories' => ProductCategory::query()->where('business_id', $businessId)->orderBy('sort_order')->get(),
            'editingProduct' => $product,
            'perPage' => $perPage,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validatePayload($request);
        $data['slug'] = !empty($data['slug']) ? $data['slug'] : Str::slug($data['name']);
        $data['image'] = $this->storeImage($request);
        $data['availability'] = $request->boolean('availability', true);
        $data['is_featured'] = $request->boolean('is_featured', false);

        Product::create($data);

        SystemLog::create([
            'business_id' => currentBusinessId(),
            'actor_user_id' => auth()->id(),
            'level' => 'info',
            'category' => 'products',
            'message' => 'Product created: ' . $data['name'],
        ]);

        toastr()->success('Product created.', ['timeOut' => 3000], 'Saved');
        session()->flash('swal', [
            'type' => 'success',
            'title' => 'Saved',
            'message' => 'Product created.',
            'ok_text' => 'OK',
        ]);

        return redirect()->route('admin.products.index');
    }

    public function update(Request $request, Product $product)
    {
        $this->authorizeProduct($product);

        $data = $this->validatePayload($request, $product->id);
        $data['slug'] = !empty($data['slug']) ? $data['slug'] : Str::slug($data['name']);
        $data['availability'] = $request->boolean('availability', false);
        $data['is_featured'] = $request->boolean('is_featured', false);

        if ($request->hasFile('image')) {
            $this->deleteImage($product->image);
            $data['image'] = $this->storeImage($request);
        }

        $product->update($data);

        SystemLog::create([
            'business_id' => currentBusinessId(),
            'actor_user_id' => auth()->id(),
            'level' => 'info',
            'category' => 'products',
            'message' => 'Product updated: ' . $product->name,
        ]);

        toastr()->success('Product updated.', ['timeOut' => 3000], 'Saved');
        session()->flash('swal', [
            'type' => 'success',
            'title' => 'Saved',
            'message' => 'Product updated.',
            'ok_text' => 'OK',
        ]);

        return redirect()->route('admin.products.index');
    }

    public function destroy(Product $product)
    {
        $this->authorizeProduct($product);

        $this->deleteImage($product->image);
        $product->delete();

        SystemLog::create([
            'business_id' => currentBusinessId(),
            'actor_user_id' => auth()->id(),
            'level' => 'warning',
            'category' => 'products',
            'message' => 'Product deleted: ' . $product->name,
        ]);

        toastr()->success('Product deleted.', ['timeOut' => 3000], 'Removed');
        session()->flash('swal', [
            'type' => 'success',
            'title' => 'Removed',
            'message' => 'Product deleted.',
            'ok_text' => 'OK',
        ]);

        return redirect()->route('admin.products.index');
    }

    public function bulkAvailability(Request $request)
    {
        $data = $request->validate([
            'product_ids' => ['required', 'array', 'min:1'],
            'product_ids.*' => ['integer'],
            'availability_action' => ['required', 'in:activate,hide'],
        ]);

        $availability = $data['availability_action'] === 'activate';

        Product::query()
            ->where('business_id', currentBusinessId())
            ->whereIn('id', $data['product_ids'])
            ->update(['availability' => $availability]);

        SystemLog::create([
            'business_id' => currentBusinessId(),
            'actor_user_id' => auth()->id(),
            'level' => 'info',
            'category' => 'products',
            'message' => ($availability ? 'Activated' : 'Hidden') . ' multiple products from the bulk menu action.',
        ]);

        toastr()->success(
            $availability ? 'Selected products are now available.' : 'Selected products are now hidden.',
            ['timeOut' => 3000],
            'Bulk update'
        );
        session()->flash('swal', [
            'type' => 'success',
            'title' => 'Bulk update',
            'message' => $availability ? 'Selected products are now available.' : 'Selected products are now hidden.',
            'ok_text' => 'OK',
        ]);

        return redirect()->route('admin.products.index');
    }

    protected function validatePayload(Request $request, $ignoreId = null)
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'category_id' => ['nullable', 'exists:product_categories,id'],
            'description' => ['nullable', 'string', 'max:2000'],
            'price' => ['required', 'numeric', 'min:0'],
            'type' => ['required', 'in:meal,drink,catering'],
            'availability' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'preparation_time_minutes' => ['nullable', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'max:4096'],
        ]);
    }

    protected function storeImage(Request $request)
    {
        if (!$request->hasFile('image')) {
            return null;
        }

        return $request->file('image')->store('products', 'public');
    }

    protected function deleteImage($path)
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    protected function authorizeProduct(Product $product)
    {
        if ($product->business_id !== currentBusinessId()) {
            abort(404);
        }
    }

    protected function perPage(Request $request)
    {
        $allowed = [5, 10, 20, 50];
        $perPage = (int) $request->query('per_page', 10);

        return in_array($perPage, $allowed, true) ? $perPage : 10;
    }
}
