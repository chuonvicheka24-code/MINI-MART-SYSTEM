<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\ContactMessage;
use App\Models\DeliveryStaff;
use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $products = Product::with('category')->orderBy('qty')->get();
        $categories = Category::orderBy('name')->get();
        $orders = Order::with(['items', 'deliveryStaff'])->orderByDesc('id')->get();
        $staff = DeliveryStaff::orderBy('name')->get();
        $customers = User::where('role', 'customer')->withCount('orders')->orderBy('first_name')->get();
        $messages = ContactMessage::orderByDesc('created_at')->get();
        $settings = Setting::current();
        return view('admin.dashboard');
        return view('admin.dashboard', compact(
            'products', 'categories', 'orders', 'staff', 'customers', 'messages', 'settings'
        ));
    }
}