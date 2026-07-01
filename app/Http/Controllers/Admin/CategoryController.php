<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemLog;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $businessId = currentBusinessId();
        $perPage = $this->perPage(request());

        return view('admin.categories.index', [
            'categories' => ProductCategory::query()->where('business_id', $businessId)->withCount('products')->orderBy('sort_order')->latest()->paginate($perPage)->withQueryString(),
            'editingCategory' => null,
            'perPage' => $perPage,
        ]);
    }

    public function edit(ProductCategory $category)
    {
        $this->authorizeCategory($category);

        $businessId = currentBusinessId();
        $perPage = $this->perPage(request());

        return view('admin.categories.index', [
            'categories' => ProductCategory::query()->where('business_id', $businessId)->withCount('products')->orderBy('sort_order')->latest()->paginate($perPage)->withQueryString(),
            'editingCategory' => $category,
            'perPage' => $perPage,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validatePayload($request);
        $data['slug'] = !empty($data['slug']) ? $data['slug'] : Str::slug($data['name']);
        $data['is_visible'] = $request->boolean('is_visible', true);

        ProductCategory::create($data);

        SystemLog::create([
            'business_id' => currentBusinessId(),
            'actor_user_id' => auth()->id(),
            'level' => 'info',
            'category' => 'catalog',
            'message' => 'Category created: ' . $data['name'],
        ]);

        toastr()->success('Category created.', ['timeOut' => 3000], 'Saved');

        return redirect()->route('admin.categories.index');
    }

    public function update(Request $request, ProductCategory $category)
    {
        $this->authorizeCategory($category);

        $data = $this->validatePayload($request);
        $data['slug'] = !empty($data['slug']) ? $data['slug'] : Str::slug($data['name']);
        $data['is_visible'] = $request->boolean('is_visible', true);

        $category->update($data);

        SystemLog::create([
            'business_id' => currentBusinessId(),
            'actor_user_id' => auth()->id(),
            'level' => 'info',
            'category' => 'catalog',
            'message' => 'Category updated: ' . $category->name,
        ]);

        toastr()->success('Category updated.', ['timeOut' => 3000], 'Saved');

        return redirect()->route('admin.categories.index');
    }

    public function destroy(ProductCategory $category)
    {
        $this->authorizeCategory($category);

        $category->delete();

        SystemLog::create([
            'business_id' => currentBusinessId(),
            'actor_user_id' => auth()->id(),
            'level' => 'warning',
            'category' => 'catalog',
            'message' => 'Category deleted: ' . $category->name,
        ]);

        toastr()->success('Category deleted.', ['timeOut' => 3000], 'Removed');

        return redirect()->route('admin.categories.index');
    }

    protected function validatePayload(Request $request)
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);
    }

    protected function authorizeCategory(ProductCategory $category)
    {
        if ($category->business_id !== currentBusinessId()) {
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
