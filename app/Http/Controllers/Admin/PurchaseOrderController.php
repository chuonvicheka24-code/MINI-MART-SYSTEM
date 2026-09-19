<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\PurchaseOrder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends Controller
{
    /** Create a manual purchase order — the admin picks the products, quantities and cost, nothing is auto-generated. */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'supplier_name' => 'required|string|max:150',
            'expected_date' => 'nullable|date',
            'notes' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.unit_cost' => 'nullable|numeric|min:0',
        ]);

        $purchaseOrder = DB::transaction(function () use ($data) {
            $po = PurchaseOrder::create([
                'supplier_name' => $data['supplier_name'],
                'status' => 'pending',
                'expected_date' => $data['expected_date'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($data['items'] as $line) {
                $product = Product::find($line['product_id']);
                $po->items()->create([
                    'product_id' => $product?->id,
                    'product_name' => $product?->name ?? 'Unknown product',
                    'qty' => $line['qty'],
                    'unit_cost' => $line['unit_cost'] ?? 0,
                ]);
            }

            return $po;
        });

        return response()->json([
            'purchaseOrder' => $purchaseOrder->load('items')->toAdminArray(),
        ]);
    }

    /** Mark a pending order as received — this is the only thing that actually adds stock. */
    public function receive(PurchaseOrder $purchaseOrder): JsonResponse
    {
        if ($purchaseOrder->status !== 'pending') {
            return response()->json(['message' => 'This purchase order has already been closed.'], 422);
        }

        $updatedProducts = DB::transaction(function () use ($purchaseOrder) {
            $products = collect();

            foreach ($purchaseOrder->items as $item) {
                if (! $item->product_id) {
                    continue;
                }
                $product = Product::find($item->product_id);
                if (! $product) {
                    continue;
                }
                $product->increment('qty', $item->qty);
                $products->push($product->fresh('category'));
            }

            $purchaseOrder->update([
                'status' => 'received',
                'received_at' => now(),
            ]);

            return $products;
        });

        return response()->json([
            'purchaseOrder' => $purchaseOrder->fresh('items')->toAdminArray(),
            'products' => $updatedProducts->map(fn ($p) => $p->toStorefrontArray() + ['discountPercent' => $p->discount_percent])->values(),
        ]);
    }

    /** Cancel an order that hasn't been received yet. */
    public function destroy(PurchaseOrder $purchaseOrder): JsonResponse
    {
        if ($purchaseOrder->status !== 'pending') {
            return response()->json(['message' => 'Only pending purchase orders can be cancelled.'], 422);
        }

        $purchaseOrder->update(['status' => 'cancelled']);

        return response()->json(['ok' => true]);
    }
}
