<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
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
            'unit' => 'each',
            'emoji' => '🧺',
            'qty' => $data['qty'],
            'image' => $data['image'] ?? null,
            'date_added' => now()->toDateString(),
        ]);

        return response()->json([
            'product' => $this->present($product->fresh('category')),
            'isNewCategory' => $isNewCategory,
            'category' => $category->only('id', 'name', 'icon', 'image'),
        ]);
    }

    /** Quick "edit price / quantity" action from the inventory table. */
    public function update(Request $request, Product $product): JsonResponse
    {
        $data = $request->validate([
            'price' => 'nullable|numeric|min:0',
            'qty' => 'nullable|integer|min:0',
        ]);

        $product->update(array_filter($data, fn ($v) => $v !== null));

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

    /** Bulk restock everything under 2x the low-stock threshold. */
    public function purchaseOrder(): JsonResponse
    {
        $threshold = Setting::current()->low_stock_threshold * 2;
        $low = Product::where('qty', '<', $threshold)->get();

        foreach ($low as $product) {
            $product->increment('qty', 20);
        }

        return response()->json([
            'count' => $low->count(),
            'products' => $low->fresh('category')->map(fn ($p) => $this->present($p)),
        ]);
    }

    private function present(Product $product): array
    {
        return $product->toStorefrontArray() + ['discountPercent' => $product->discount_percent];
    }
}
