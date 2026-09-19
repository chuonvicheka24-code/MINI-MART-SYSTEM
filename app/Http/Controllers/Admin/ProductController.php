<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /** "Add new item" form on the dashboard. */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'category' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'qty' => 'required|integer|min:0',
            'unit' => 'nullable|string|max:20',
            'expiry_date' => 'nullable|date',
            'image' => 'nullable|string',
        ]);

        $isNewCategory = ! Category::where('name', $data['category'])->exists();
        $category = Category::firstOrCreate(
            ['name' => $data['category']],
            ['icon' => 'fa-tag', 'image' => $data['image'] ?? null]
        );

        $product = Product::create([
            'category_id' => $category->id,
            'name' => $data['name'],
            'price' => $data['price'],
            'unit' => $data['unit'] ?: 'each',
            'emoji' => '🧺',
            'qty' => $data['qty'],
            'image' => $data['image'] ?? null,
            'date_added' => now()->toDateString(),
            'expiry_date' => $data['expiry_date'] ?? null,
        ]);

        return response()->json([
            'product' => $this->present($product->fresh('category')),
            'isNewCategory' => $isNewCategory,
            'category' => $category->only('id', 'name', 'icon', 'image'),
        ]);
    }

    /** Edit action from the inventory table — price, quantity, unit and expiry date. */
    public function update(Request $request, Product $product): JsonResponse
    {
        $data = $request->validate([
            'price' => 'nullable|numeric|min:0',
            'qty' => 'nullable|integer|min:0',
            'unit' => 'nullable|string|max:20',
            'expiry_date' => 'nullable|date',
            'clear_expiry_date' => 'nullable|boolean',
        ]);

        $update = array_filter($data, fn ($v) => $v !== null);
        unset($update['clear_expiry_date']);

        if ($request->boolean('clear_expiry_date')) {
            $update['expiry_date'] = null;
        }

        $product->update($update);

        return response()->json(['product' => $this->present($product->fresh('category'))]);
    }

    public function destroy(Product $product): JsonResponse
    {
        $product->delete();

        return response()->json(['ok' => true]);
    }

    public function discount(Request $request, Product $product): JsonResponse
    {
        $data = $request->validate(['percent' => 'required|numeric|min:0|max:90']);

        $product->update(['discount_percent' => $data['percent'] > 0 ? $data['percent'] : null]);

        return response()->json(['product' => $this->present($product->fresh('category'))]);
    }

    public function removeDiscount(Product $product): JsonResponse
    {
        $product->update(['discount_percent' => null]);

        return response()->json(['product' => $this->present($product->fresh('category'))]);
    }

    public function reorder(Product $product): JsonResponse
    {
        $product->increment('qty', 20);

        return response()->json(['product' => $this->present($product->fresh('category'))]);
    }

    private function present(Product $product): array
    {
        return $product->toStorefrontArray() + ['discountPercent' => $product->discount_percent];
    }
}
