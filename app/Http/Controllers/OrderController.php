<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user(); // guaranteed non-null — this route requires 'auth'
        $status = $request->query('status');
        $categories = Category::orderBy('name')->get();

        // Fetch a few products to browse while looking at order history.
        $products = Product::latest()->take(8)->get();

        $query = Order::with(['items.product', 'deliveryStaff'])
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    // Legacy guest checkouts (placed before login was required)
                    // only count as "theirs" if the contact info actually matches —
                    // never show every guest order to every customer.
                    ->orWhere(function ($q2) use ($user) {
                        $q2->whereNull('user_id')
                            ->where(function ($q3) use ($user) {
                                $q3->where('customer_email', $user->email)
                                    ->orWhere('customer_phone', $user->phone);
                            });
                    });
            });

        if ($status && in_array($status, ['pending', 'out', 'done'])) {
            $query->where('status', $status);
        }

        $orders = $query->orderBy('id', 'desc')->get();

        return view('orders.index', compact('orders', 'categories', 'products'));
    }

    public function show($id, Request $request): View
    {
        $user = $request->user(); // guaranteed non-null — this route requires 'auth'
        $categories = Category::orderBy('name')->get();

        $order = Order::with(['items.product', 'deliveryStaff'])->findOrFail($id);

        $ownsOrder = $order->user_id === $user->id
            || ($order->user_id === null && (
                $order->customer_email === $user->email
                || $order->customer_phone === $user->phone
            ));

        abort_unless($ownsOrder || $user->isAdmin(), 403, "You don't have access to this order.");

        return view('orders.show', compact('order', 'categories'));
    }
}