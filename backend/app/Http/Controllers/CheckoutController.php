<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Setting;
use App\Services\TelegramService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    /**
     * Enforce authentication for all checkout actions.
     */
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

        // The cart stores each line as ['qty' => ..., 'price' => ...]; read the real quantity.
        // (Casting the whole line to float used to turn every quantity into 1.)
        $wanted = [];
        foreach ($cart as $id => $line) {
            $qty = is_array($line) ? (float) ($line['qty'] ?? 0) : (float) $line;
            if ($qty > 0) {
                $wanted[(int) $id] = $qty;
            }
        }

        if (empty($wanted)) {
            return response()->json(['message' => 'Your basket is empty.'], 422);
        }

        $settings = Setting::current();
        $user = $request->user(); // Guaranteed authenticated via middleware

        try {
            $order = DB::transaction(function () use ($wanted, $data, $settings, $user) {
                // Re-read products inside the transaction so stock and prices are the current ones.
                $products = Product::with('promotions')
                    ->whereIn('id', array_keys($wanted))
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                $subtotal = 0;
                foreach ($wanted as $id => $qty) {
                    $product = $products->get($id);

                    if (! $product) {
                        throw new \DomainException('An item in your basket is no longer available.');
                    }

                    if ($qty > (float) $product->qty) {
                        $left = (float) $product->qty;
                        throw new \DomainException(
                            $left <= 0
                                ? "{$product->name} is out of stock."
                                : "Only {$left} {$product->unit} of {$product->name} left in stock."
                        );
                    }

                    $subtotal += $product->sale_price * $qty;
                }

                // Flat fee set by the admin (Staff Delivery > Delivery price), by transport type.
                $deliveryFee = round($data['transport_type'] === 'truck' ? $settings->truck_price : $settings->moto_price, 2);

                $order = Order::create([
                    'user_id' => $user->id,
                    'customer_name' => $user->name,
                    'customer_email' => $data['email'] ?? $user->email,
                    'customer_phone' => $data['phone'] ?? $user->phone,
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

                foreach ($wanted as $id => $qty) {
                    $product = $products->get($id);

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'unit' => $product->unit,
                        'price' => $product->sale_price,
                        'qty' => $qty,
                        'line_total' => round($product->sale_price * $qty, 2),
                    ]);

                    // Stock decrement supports decimal quantities (validated above, so never negative).
                    $product->decrement('qty', $qty);
                }

                return $order;
            });
        } catch (\DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        session()->forget(CartController::SESSION_KEY);

        $e = fn ($v) => TelegramService::e($v);
        $lines = $order->items->map(fn ($i) => '• ' . $e($i->product_name) . ' × ' . (float) $i->qty . ' = $' . number_format($i->line_total, 2))->implode("\n");
        TelegramService::send(
            "🛒 <b>New order #{$order->id}</b>\n"
            . "👤 {$e($order->customer_name)}\n"
            . "📞 {$e($order->customer_phone)}\n"
            . "📍 {$e($order->location)} {$e($order->address)}\n"
            . "💳 {$e($order->payment_method)}\n\n"
            . $lines . "\n\n"
            . "<b>Total: $" . number_format($order->total, 2) . "</b>"
        );

        return response()->json([
            'message' => 'Order placed successfully!',
            'redirect_url' => route('orders.show', $order->id),
            'order' => [
                'number' => $order->id,
                'date' => $order->placed_at->toDayDateTimeString(),
                'mode' => $order->mode_label,
                'pay' => $order->payment_method,
                'items' => $order->items->map(fn ($i) => [
                    'name' => $i->product_name,
                    'qty' => (float) $i->qty,
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