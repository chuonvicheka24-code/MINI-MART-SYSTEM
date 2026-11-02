<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Category;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (config('app.env') !== 'local' || request()->server('HTTP_X_FORWARDED_PROTO') === 'https') {
            URL::forceScheme('https');
        }

        // Any page using the storefront layout (e.g. /returns) gets $categories
        // automatically if its controller/route did not pass it.
        View::composer('layouts.app', function ($view) {
            if (! array_key_exists('categories', $view->getData())) {
                $view->with('categories', Category::orderBy('name')->get());
            }
        });
    }
}