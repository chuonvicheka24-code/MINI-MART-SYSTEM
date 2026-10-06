<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $products = Product::with('category')->get();
        $categories = Category::orderBy('name')->get();

        // Same six IDs / discount percentages the original flash-deals section used.
        $dealIds = [2, 1, 13, 22, 11, 18];
        $dealPct = [2 => 20, 1 => 15, 13 => 20, 22 => 15, 11 => 30, 18 => 25];

        $newArrivals = Product::with('category')->orderByDesc('date_added')->limit(8)->get();

        $heroImage = Product::resolveImageUrl(Setting::current()->hero_image ?: 'Photo/150a7d6ac15444d505c789b0c017f9f1.jpg');

        return view('home.index', compact('products', 'categories', 'dealIds', 'dealPct', 'newArrivals', 'heroImage'));
    }
}
