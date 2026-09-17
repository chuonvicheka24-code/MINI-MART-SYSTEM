<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function index(): View
    {
        $categories = Category::orderBy('name')->get();

        return view('checkout.index', compact('categories'));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'truck_number' => 'nullable|string|max:50',
            'location' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:255',
            'transport_type' => 'required|in:truck,moto',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'payment_method' => 'required|string|max:50',
        ]);

        $cart = session(CartController::SESSION_KEY, []);
        if (empty($cart)) {
            return response()->json(['message' => 'Your basket is empty.'], 422);
        }

        $products = Product::whereIn('id', array_keys($cart))->get()->keyBy('id');
        if ($products->isEmpty()) {
            return response()->json(['message' => 'Your basket is empty.'], 422);
        }

        $settings = Setting::current();
        $user = $request->user();

        $order = DB::transaction(function () use ($cart, $products, $data, $settings, $user) {
            $subtotal = 0;
            foreach ($cart as $id => $qty) {
                if ($product = $products->get($id)) {
                    $subtotal += $product->sale_price * $qty;
                }
            }
            $deliveryFee = round($subtotal * ($settings->delivery_rate / 100), 2);

            $order = Order::create([
                'user_id' => $user?->id,
                'customer_name' => $user?->name ?? 'Guest customer',
                'customer_email' => $data['email'] ?? $user?->email,
                'customer_phone' => $data['phone'] ?? $user?->phone,
                'location' => $data['location'] ?? null,
                'address' => $data['address'] ?? null,
                'truck_number' => $data['transport_type'] === 'truck' ? ($data['truck_number'] ?? null) : null,
                'transport_type' => $data['transport_type'],
                'payment_method' => $data['payment_method'],
                'subtotal' => round($subtotal, 2),
                'delivery_fee' => $deliveryFee,
                'total' => round($subtotal + $deliveryFee, 2),
                'status' => 'pending',
                'placed_at' => now(),
            ]);

            foreach ($cart as $id => $qty) {
                $product = $products->get($id);
                if (! $product) {
                    continue;
                }
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'unit' => $product->unit,
                    'price' => $product->sale_price,
                    'qty' => $qty,
                    'line_total' => round($product->sale_price * $qty, 2),
                ]);

                // Stock is real now — decrement it (never below zero).
                $product->decrement('qty', min($qty, $product->qty));
            }

            return $order;
        });

        session()->forget(CartController::SESSION_KEY);

        return response()->json([
            'order' => [
                'number' => $order->id,
                'date' => $order->placed_at->toDayDateTimeString(),
                'mode' => $order->mode_label,
                'pay' => $order->payment_method,
                'items' => $order->items->map(fn ($i) => [
                    'name' => $i->product_name,
                    'qty' => $i->qty,
                    'price' => (float) $i->price,
                    'lineTotal' => (float) $i->line_total,
                ]),
                'subtotal' => (float) $order->subtotal,
                'delivery' => (float) $order->delivery_fee,
                'total' => (float) $order->total,
            ],
        ]);
    }
}
