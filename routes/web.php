<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MessageController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\PromotionController;
use App\Http\Controllers\Admin\PurchaseOrderController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController as CustomerOrderController;

/*
|--------------------------------------------------------------------------
| Storefront
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/contact', [ContactController::class, 'index'])->name('contact.index');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');
Route::get('/orders', [CustomerOrderController::class, 'index'])->name('orders.index');
Route::get('/orders/{id}', [CustomerOrderController::class, 'show'])->name('orders.show');


/*
|--------------------------------------------------------------------------
| Cart (session based — works for guests and signed-in customers alike)
|--------------------------------------------------------------------------
*/
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::get('/cart/data', [CartController::class, 'data'])->name('cart.data');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/set', [CartController::class, 'set'])->name('cart.set');
Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');

Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

/*
|--------------------------------------------------------------------------
| Auth
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);
});
Route::post('/logout', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');

/*
|--------------------------------------------------------------------------
| Admin dashboard (auth + admin role required)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('index');

    Route::post('/api/products', [AdminProductController::class, 'store'])->name('products.store');
    Route::put('/api/products/{product}', [AdminProductController::class, 'update'])->name('products.update');
    Route::delete('/api/products/{product}', [AdminProductController::class, 'destroy'])->name('products.destroy');
    Route::post('/api/products/{product}/discount', [AdminProductController::class, 'discount'])->name('products.discount');
    Route::delete('/api/products/{product}/discount', [AdminProductController::class, 'removeDiscount'])->name('products.removeDiscount');
    Route::post('/api/products/{product}/reorder', [AdminProductController::class, 'reorder'])->name('products.reorder');

    Route::post('/api/purchase-orders', [PurchaseOrderController::class, 'store'])->name('purchaseOrders.store');
    Route::post('/api/purchase-orders/{purchaseOrder}/receive', [PurchaseOrderController::class, 'receive'])->name('purchaseOrders.receive');
    Route::delete('/api/purchase-orders/{purchaseOrder}', [PurchaseOrderController::class, 'destroy'])->name('purchaseOrders.destroy');

    Route::post('/api/promotions', [PromotionController::class, 'store'])->name('promotions.store');
    Route::delete('/api/promotions/{promotion}', [PromotionController::class, 'destroy'])->name('promotions.destroy');

    Route::post('/api/orders/{order}/approve', [OrderController::class, 'approve'])->name('orders.approve');
    Route::post('/api/orders/{order}/deliver', [OrderController::class, 'deliver'])->name('orders.deliver');
    Route::post('/api/orders/{order}/assign', [OrderController::class, 'assign'])->name('orders.assign');

    Route::post('/api/staff', [StaffController::class, 'store'])->name('staff.store');
    Route::put('/api/staff/{staff}', [StaffController::class, 'update'])->name('staff.update');
    Route::delete('/api/staff/{staff}', [StaffController::class, 'destroy'])->name('staff.destroy');

    Route::post('/api/messages/{message}/read', [MessageController::class, 'read'])->name('messages.read');
    Route::post('/api/messages/read-all', [MessageController::class, 'readAll'])->name('messages.readAll');
    Route::post('/api/messages/{message}/reply', [MessageController::class, 'reply'])->name('messages.reply');
    Route::delete('/api/messages/{message}', [MessageController::class, 'destroy'])->name('messages.destroy');

    Route::put('/api/settings', [SettingController::class, 'update'])->name('settings.update');
});
