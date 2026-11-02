<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Promotion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PromotionController extends Controller
{
    /** Create a promotion and apply its discount to every product picked for it. */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title' => 'required|string|max:150',
            'description' => 'nullable|string|max:300',
            'discount_percent' => 'required|numeric|min:1|max:90',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'product_ids' => 'required|array|min:1',
            'product_ids.*' => 'integer|exists:products,id',
        ]);

        $promotion = DB::transaction(function () use ($data) {
            $promotion = Promotion::create([
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'discount_percent' => $data['discount_percent'],
                'starts_at' => $data['starts_at'] ?? null,
                'ends_at' => $data['ends_at'] ?? null,
            ]);

            // The discount is NOT copied onto the products any more: the storefront reads it
            // from the promotion, so starts_at / ends_at are respected automatically.
            $promotion->products()->sync($data['product_ids']);

            return $promotion;
        });

        $products = Product::whereIn('id', $data['product_ids'])->with(['category', 'promotions'])->get();

        return response()->json([
            'promotion' => $promotion->load('products')->toAdminArray(),
            'products' => $products->map(fn ($p) => $p->toAdminArray())->values(),
        ]);
    }

    /** End a promotion early. (Also clears a discount an older version copied onto the products, where it still matches.) */
    public function destroy(Promotion $promotion): JsonResponse
    {
        $productIds = $promotion->products()->pluck('products.id');

        Product::whereIn('id', $productIds)
            ->where('discount_percent', $promotion->discount_percent)
            ->update(['discount_percent' => null]);

        $promotion->update(['ended_at' => now()]);

        $products = Product::whereIn('id', $productIds)->with(['category', 'promotions'])->get();

        return response()->json([
            'products' => $products->map(fn ($p) => $p->toAdminArray())->values(),
        ]);
    }
}
