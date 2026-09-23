<?php

namespace App\Providers;

use App\Models\Category;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Automatically share $categories with all blade views
        View::composer('*', function ($view) {
            $view->with('categories', Category::orderBy('name')->get());
        });
    }
}