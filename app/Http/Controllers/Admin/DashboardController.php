<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\ContactMessage;
use App\Models\DeliveryStaff;
use App\Models\Order;
use App\Models\Product;
use App\Models\Promotion;
use App\Models\PurchaseOrder;
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
        $purchaseOrders = PurchaseOrder::with('items')->orderByDesc('id')->get();
        $promotions = Promotion::with('products')->orderByDesc('id')->get();

        $data = [
            'products' => $products->map(fn ($p) => $p->toStorefrontArray() + ['discountPercent' => $p->discount_percent])->values(),
            'categories' => $categories->pluck('name')->values(),
            'orders' => $orders->map(fn ($o) => [
                'id' => $o->id,
                'customer' => $o->customer_name,
                'items' => $o->items->sum('qty'),
                'total' => (float) $o->total,
                'status' => $o->status,
                'mode' => $o->deliveryStaff?->vehicle ?? $o->mode_label,
                'staffId' => $o->delivery_staff_id,
                'placed' => optional($o->placed_at)->diffForHumans(),
            ])->values(),
            'staff' => $staff->map(fn ($s) => $s->only('id', 'name', 'phone', 'vehicle', 'status'))->values(),
            'customers' => $customers->map(fn ($c) => [
                'id' => $c->id,
                'name' => $c->name,
                'phone' => $c->phone,
                'email' => $c->email,
                'orders' => $c->orders_count,
            ])->values(),
            'messages' => $messages->map(fn ($m) => [
                'id' => $m->id,
                'name' => $m->name,
                'email' => $m->email,
                'message' => $m->message,
                'read' => (bool) $m->read,
                'reply' => $m->reply,
                'repliedAt' => optional($m->replied_at)->toIso8601String(),
                'date' => $m->created_at->toIso8601String(),
            ])->values(),
            'settings' => [
                'storeName' => $settings->store_name,
                'hours' => $settings->hours,
                'deliveryRate' => (float) $settings->delivery_rate,
                'lowStockThreshold' => $settings->low_stock_threshold,
            ],
            'purchaseOrders' => $purchaseOrders->map(fn ($po) => $po->toAdminArray())->values(),
            'promotions' => $promotions->map(fn ($p) => $p->toAdminArray())->values(),
        ];

        return view('admin.dashboard', compact('data', 'categories'));
    }
}
