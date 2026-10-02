<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * The full catalog + category list is handed to the page as JSON and
     * filtered/sorted client-side, exactly like the original products.html —
     * this keeps the instant, no-reload filtering UX intact.
     */
    public function index(): View
    {
        $products = Product::with('category')->get();
        $categories = Category::orderBy('name')->get();

        return view('products.index', compact('products', 'categories'));
    }

    /** Live catalog snapshot used by the storefront's auto-refresh polling. */
    public function data(): JsonResponse
    {
        return response()->json(
            Product::with('category')->get()->map->toStorefrontArray()->values()
        )->header('Cache-Control', 'no-store');
    }
}
