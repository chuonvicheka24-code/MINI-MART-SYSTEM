<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Promotion;
use App\Models\Setting;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $products = Product::with(['category', 'promotions'])->get();
        $categories = Category::orderBy('name')->get();
        $settings = Setting::current();

        // Same threshold the admin uses for "Need to Order" / low stock.
        $lowStockThreshold = (int) ($settings->low_stock_threshold ?: 10);

        // Flash Deals = every product that really has a discount right now
        // (manual admin discount or an active promotion). In-stock first, then biggest discount.
        $deals = $products
            ->filter(fn ($p) => $p->effective_discount > 0)
            ->sortBy(fn ($p) => ($p->isOutOfStock() ? 1000 : 0) - $p->effective_discount)
            ->take(6)
            ->values();

        $maxDiscount = (float) ($products->max(fn ($p) => $p->effective_discount) ?? 0);

        // Banner text comes from the biggest promotion that is active today.
        $promo = Promotion::orderByDesc('discount_percent')->get()
            ->first(fn ($p) => $p->isActive());

        $newArrivals = Product::with(['category', 'promotions'])->orderByDesc('date_added')->limit(8)->get();

        $heroImage = Product::resolveImageUrl($settings->hero_image ?: 'Photo/150a7d6ac15444d505c789b0c017f9f1.jpg');

        return view('home.index', compact(
            'products', 'categories', 'deals', 'newArrivals', 'heroImage',
            'maxDiscount', 'promo', 'lowStockThreshold'
        ));
    }
}
