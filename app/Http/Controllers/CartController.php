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
        $products = Product::all();
        $user = $request->user();

        if ($user) {
            $pastOrders = Order::with(['items.product'])
                ->where(function ($q) use ($user) {
                    $q->where('user_id', $user->id)
                      ->orWhere('customer_email', $user->email)
                      ->orWhere('customer_phone', $user->phone)
                      ->orWhereNull('user_id');
                })
                ->orderBy('id', 'desc')
                ->get();
        } else {
            $pastOrders = Order::with(['items.product'])
                ->whereNull('user_id')
                ->orderBy('id', 'desc')
                ->get();
        }

        return view('cart.index', compact('categories', 'products', 'pastOrders'));
    }

    /** Return current cart session data */
    public function data(): JsonResponse
    {
        $cart = session()->get('cart', []);
        $lines = [];
        $subtotal = 0;

        foreach ($cart as $id => $item) {
            $price = (float)($item['price'] ?? 0);
            $qty = (float)($item['qty'] ?? 1);
            $subtotal += $price * $qty;

            $lines[] = [
                'id' => (int)$id,
                'qty' => $qty,
                'price' => $price,
                'name' => $item['name'] ?? '',
                'img' => $item['img'] ?? '',
                'unit' => $item['unit'] ?? 'item'
            ];
        }

        return response()->json([
            'lines' => $lines,
            'count' => count($lines),
            'subtotal' => $subtotal,
        ]);
    }

    /** Add item to session cart */
    public function add(Request $request): JsonResponse
    {
        $id = $request->input('id');
        $qty = (float) $request->input('qty', 1);

        $product = Product::find($id);

        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            $cart[$id]['qty'] += $qty;
        } else {
            $cart[$id] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->sale_price ?? $product->price ?? 0,
                'qty' => $qty,
                'unit' => $product->unit ?? 'item',
                'img' => $product->image ?? 'images/placeholder.jpg',
            ];
        }

        session()->put('cart', $cart);

        return $this->data();
    }

    /** Update item quantity or delete if 0 */
    public function set(Request $request): JsonResponse
    {
        $id = $request->input('id');
        $qty = (float) $request->input('qty', 0);

        $cart = session()->get('cart', []);

        if ($qty <= 0) {
            unset($cart[$id]);
        } else if (isset($cart[$id])) {
            $cart[$id]['qty'] = $qty;
        }

        session()->put('cart', $cart);

        return $this->data();
    }
}