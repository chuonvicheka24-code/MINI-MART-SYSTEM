<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class CartController extends Controller
{
    public const SESSION_KEY = 'cart';
    public function index(Request $request): View
    {
        $categories = Category::orderBy('name')->get();
        $products = Product::with(['category', 'promotions'])->get();
        $user = $request->user();

        if ($user) {
            $pastOrders = Order::with(['items.product'])
                ->where(function ($q) use ($user) {
                    $q->where('user_id', $user->id)
                        // Legacy guest checkouts only count as "theirs" if the
                        // contact info actually matches — never show every
                        // guest order to every logged-in customer.
                        ->orWhere(function ($q2) use ($user) {
                            $q2->whereNull('user_id')
                                ->where(function ($q3) use ($user) {
                                    $q3->where('customer_email', $user->email)
                                        ->orWhere('customer_phone', $user->phone);
                                });
                        });
                })
                ->orderBy('id', 'desc')
                ->get();
        } else {
            // Guests have no way to prove which past orders are theirs —
            // show none rather than leaking every anonymous order in the store.
            $pastOrders = collect();
        }

        return view('cart.index', compact('categories', 'products', 'pastOrders'));
    }

    /** One cart line's quantity, whatever shape the session holds (['qty'=>..] or a bare number). */
    private function lineQty(mixed $line): float
    {
        return is_array($line) ? (float) ($line['qty'] ?? 0) : (float) $line;
    }

    private function stockError(Product $product, float $wanted = 0): JsonResponse
    {
        $left = (float) $product->qty;

        $message = $left <= 0
            ? "{$product->name} is out of stock."
            : "Only " . (float) $left . " {$product->unit} of {$product->name} in stock.";

        return response()->json(['error' => $message, 'stock' => $left], 422);
    }

    /**
     * Return current cart session data.
     * Prices are always re-read from the database (admin discounts / promotions can change
     * while an item sits in the cart) and deleted products are dropped.
     */
    public function data(): JsonResponse
    {
        $cart = session()->get('cart', []);
        $products = Product::with('promotions')->whereIn('id', array_keys($cart))->get()->keyBy('id');

        $lines = [];
        $clean = [];
        $subtotal = 0;

        foreach ($cart as $id => $item) {
            $product = $products->get((int) $id);
            $qty = $this->lineQty($item);

            if (! $product || $qty <= 0) {
                continue;
            }

            $price = $product->sale_price;
            $subtotal += $price * $qty;
            $clean[$id] = is_array($item) ? $item : ['id' => $product->id, 'qty' => $qty];

            $lines[] = [
                'id' => (int) $id,
                'qty' => $qty,
                'price' => $price,
                'name' => $product->name,
                'img' => $product->image_url,
                'unit' => $product->unit ?? 'item',
                'stock' => (float) $product->qty,
            ];
        }

        if (count($clean) !== count($cart)) {
            session()->put('cart', $clean);
        }

        return response()->json([
            'lines' => $lines,
            'count' => count($lines),
            'subtotal' => $subtotal,
        ]);
    }

    /** Add item to session cart — never more than what is in stock. */
    public function add(Request $request): JsonResponse
    {
        $id = (int) $request->input('id');
        $qty = (float) $request->input('qty', 1);

        $product = Product::with('promotions')->find($id);

        if (! $product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        if ($qty <= 0) {
            return response()->json(['error' => 'Quantity must be greater than zero.'], 422);
        }

        $cart = session()->get('cart', []);
        $inCart = isset($cart[$id]) ? $this->lineQty($cart[$id]) : 0;

        if ($product->isOutOfStock() || $inCart + $qty > (float) $product->qty) {
            return $this->stockError($product, $inCart + $qty);
        }

        if (isset($cart[$id]) && is_array($cart[$id])) {
            $cart[$id]['qty'] = $inCart + $qty;
        } else {
            $cart[$id] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->sale_price,
                'qty' => $inCart + $qty,
                'unit' => $product->unit ?? 'item',
                'img' => $product->image ?? 'images/placeholder.jpg',
            ];
        }

        session()->put('cart', $cart);

        return $this->data();
    }

    /** Update item quantity (0 removes it) — capped by stock. */
    public function set(Request $request): JsonResponse
    {
        $id = (int) $request->input('id');
        $qty = (float) $request->input('qty', 0);

        $cart = session()->get('cart', []);

        if ($qty <= 0) {
            unset($cart[$id]);
        } elseif (isset($cart[$id])) {
            $product = Product::find($id);

            if (! $product) {
                unset($cart[$id]);
            } elseif ($qty > (float) $product->qty) {
                return $this->stockError($product, $qty);
            } else {
                if (is_array($cart[$id])) {
                    $cart[$id]['qty'] = $qty;
                } else {
                    $cart[$id] = ['id' => $product->id, 'qty' => $qty];
                }
            }
        }

        session()->put('cart', $cart);

        return $this->data();
    }
}
