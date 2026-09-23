<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Display list of orders (Filtered by status tab if provided)
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $status = $request->query('status');
        $categories = Category::orderBy('name')->get();

        $query = Order::with(['items.product', 'deliveryStaff']);

        if ($user) {
            $query->where('user_id', $user->id);
        }

        if ($status && in_array($status, ['pending', 'out', 'done'])) {
            $query->where('status', $status);
        }

        $orders = $query->orderBy('id', 'desc')->get();

        return view('orders.index', compact('orders', 'categories'));
    }

    /**
     * Display order details page with progress timeline
     */
    public function show($id, Request $request): View
    {
        $user = $request->user();
        $categories = Category::orderBy('name')->get();

        $query = Order::with(['items.product', 'deliveryStaff']);
        if ($user) {
            $query->where('user_id', $user->id);
        }

        $order = $query->findOrFail($id);

        return view('orders.show', compact('order', 'categories'));
    }
}