<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    const SESSION_KEY = 'mm_cart';

    public function index(): View
    {
        $categories = Category::orderBy('name')->get();

        return view('cart.index', compact('categories'));
    }

    /** Current cart as {id: qty} — used to prime the page and after every mutation. */
    public function data(): JsonResponse
    {
        return response()->json($this->summary());
    }

    public function add(Request $request): JsonResponse
    {
        $data = $request->validate([
            'id' => 'required|integer|exists:products,id',
            'qty' => 'nullable|integer|min:1',
        ]);
        $qty = $data['qty'] ?? 1;

        $cart = session(self::SESSION_KEY, []);
        $cart[$data['id']] = ($cart[$data['id']] ?? 0) + $qty;
        session([self::SESSION_KEY => $cart]);

        return response()->json($this->summary());
    }

    public function set(Request $request): JsonResponse
    {
        $data = $request->validate([
            'id' => 'required|integer|exists:products,id',
            'qty' => 'required|integer|min:0',
        ]);

        $cart = session(self::SESSION_KEY, []);
        if ($data['qty'] <= 0) {
            unset($cart[$data['id']]);
        } else {
            $cart[$data['id']] = $data['qty'];
        }
        session([self::SESSION_KEY => $cart]);

        return response()->json($this->summary());
    }

    public function remove(Request $request): JsonResponse
    {
        $data = $request->validate(['id' => 'required|integer']);
        $cart = session(self::SESSION_KEY, []);
        unset($cart[$data['id']]);
        session([self::SESSION_KEY => $cart]);

        return response()->json($this->summary());
    }

    /** {lines:[{id,qty}], count, subtotal} built against live product prices/stock. */
    private function summary(): array
    {
        $cart = session(self::SESSION_KEY, []);
        $ids = array_keys($cart);
        $products = Product::whereIn('id', $ids)->get()->keyBy('id');

        $lines = [];
        $subtotal = 0;
        $count = 0;

        foreach ($cart as $id => $qty) {
            $product = $products->get($id);
            if (! $product) {
                continue;
            }
            $lines[] = ['id' => (int) $id, 'qty' => (int) $qty];
            $subtotal += $product->sale_price * $qty;
            $count += $qty;
        }

        return [
            'lines' => $lines,
            'count' => $count,
            'subtotal' => round($subtotal, 2),
        ];
    }
}
