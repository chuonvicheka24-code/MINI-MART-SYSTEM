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
        $user = $request->user();
        $status = $request->query('status');
        $categories = Category::orderBy('name')->get();

        // Fetch products for ordering directly on this page
        $products = Product::where('status', 'active')->latest()->take(8)->get();

        $query = Order::with(['items.product', 'deliveryStaff']);

        if ($user) {
            $query->where(function ($q) use ($user) {
                $q->where('user_id', $user->id);

                if (!empty($user->phone)) {
                    $q->orWhere('customer_phone', $user->phone);
                }

                if (!empty($user->email)) {
                    $q->orWhere('customer_email', $user->email);
                }

                $q->orWhereNull('user_id');
            });
        }

        if ($status && in_array($status, ['pending', 'out', 'done'])) {
            $query->where('status', $status);
        }

        $orders = $query->orderBy('id', 'desc')->get();

        return view('orders.index', compact('orders', 'categories', 'products'));
    }

    public function show($id, Request $request): View
    {
        $categories = Category::orderBy('name')->get();
        $order = Order::with(['items.product', 'deliveryStaff'])->findOrFail($id);

        return view('orders.show', compact('order', 'categories'));
    }
}