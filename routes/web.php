<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| GrocerEase Web Routes
|--------------------------------------------------------------------------
|
| Route structure:
| 1. Public routes — accessible to everyone
| 2. Guest routes — login/register (redirect if already authenticated)
| 3. Authenticated routes — cart, checkout, orders, logout
| 4. Admin routes — dashboard, product/category/brand/order management
|
*/

// ──────────────────────────────────────────────
// 1. PUBLIC ROUTES
// ──────────────────────────────────────────────
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;

/*
|--------------------------------------------------------------------------
| GrocerEase Web Routes
|--------------------------------------------------------------------------
|
| Route structure:
| 1. Public routes — accessible to everyone
| 2. Guest routes — login/register (redirect if already authenticated)
| 3. Authenticated routes — cart, checkout, orders, logout
| 4. Admin routes — dashboard, product/category/brand/order management
|
*/

// ──────────────────────────────────────────────
// 1. PUBLIC ROUTES
// ──────────────────────────────────────────────
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/search', [ProductController::class, 'search'])->name('search');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');

// Cart (Public/Guest allowed)
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::put('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');

// ──────────────────────────────────────────────
// 2. GUEST ROUTES (redirect away if logged in)
// ──────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.store');

    Route::get('/register', [RegisterController::class, 'showForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->name('register.store');
});

// ──────────────────────────────────────────────
// 3. AUTHENTICATED ROUTES
// ──────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Checkout
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout/place-order', [CheckoutController::class, 'placeOrder'])->name('checkout.placeOrder');

    // Orders
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');

    // Payments
    Route::get('/payment/{order}/esewa/initiate', [PaymentController::class, 'initiateEsewa'])->name('payment.esewa.initiate');
    Route::get('/payment/esewa/success', [PaymentController::class, 'esewaSuccess'])->name('payment.esewa.success');
    Route::get('/payment/esewa/failure', [PaymentController::class, 'esewaFailure'])->name('payment.esewa.failure');
    Route::get('/payment/{order}/khalti/initiate', [PaymentController::class, 'initiateKhalti'])->name('payment.khalti.initiate');
    Route::get('/payment/khalti/success', [PaymentController::class, 'khaltiSuccess'])->name('payment.khalti.success');
});

// Redirect /admin to /admin/dashboard
Route::redirect('/admin', '/admin/dashboard');

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

    // Product management (resource routes)
    Route::resource('products', \App\Http\Controllers\Admin\ProductController::class)
        ->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);

    // Category management (resource routes)
    Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class)
        ->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);

    // Brand management (resource routes)
    Route::resource('brands', \App\Http\Controllers\Admin\BrandController::class)
        ->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);

    // Admin order management
    // Admin order management
    Route::get('/orders', [\App\Http\Controllers\Admin\OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [\App\Http\Controllers\Admin\OrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/status', [\App\Http\Controllers\Admin\OrderController::class, 'updateStatus'])->name('orders.updateStatus');
});
