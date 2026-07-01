<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Customer\CatalogController;
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\OrderController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\FeatureFlagController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Developer\SystemLogController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\ServicesController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TestimonialController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::get('about', [AboutController::class, 'index'])->name('about');
Route::get('services', [ServicesController::class, 'index'])->name('services');
Route::get('menu', [MenuController::class, 'index'])->name('menu');
Route::get('booking', [BookingController::class, 'index'])->name('booking');
Route::get('team', [TeamController::class, 'index'])->name('team');
Route::get('testimonial', [TestimonialController::class, 'index'])->name('testimonial');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/customer/dashboard', [DashboardController::class, 'customer'])->name('customer.dashboard')->middleware('role:customer');
    Route::get('/staff/dashboard', [DashboardController::class, 'staff'])->name('staff.dashboard')->middleware('role:staff');
    Route::get('/staff/tasks', [DashboardController::class, 'staff'])->name('staff.tasks.index')->middleware('role:staff');
    Route::get('/kitchen/dashboard', [DashboardController::class, 'kitchen'])->name('kitchen.dashboard')->middleware('role:kitchen_staff');
    Route::get('/manager/dashboard', [DashboardController::class, 'manager'])->name('manager.dashboard')->middleware('role:manager');
    Route::get('/super-admin/dashboard', [DashboardController::class, 'superAdmin'])->name('super-admin.dashboard')->middleware('role:super_admin');
    Route::get('/developer/dashboard', [DashboardController::class, 'developer'])->name('developer.dashboard')->middleware('role:developer');

    Route::get('/catalog', [CatalogController::class, 'index'])->name('catalog.index')->middleware('role:customer');
    Route::post('/catalog/{product}/add', [CatalogController::class, 'add'])->name('catalog.add')->middleware('role:customer');

    Route::get('/cart', [CartController::class, 'index'])->name('cart.index')->middleware('role:customer');
    Route::patch('/cart/{productId}', [CartController::class, 'update'])->name('cart.update')->middleware('role:customer');
    Route::delete('/cart/{productId}', [CartController::class, 'destroy'])->name('cart.remove')->middleware('role:customer');
    Route::delete('/cart', [CartController::class, 'clear'])->name('cart.clear')->middleware('role:customer');

    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index')->middleware('role:customer');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store')->middleware('role:customer');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show')->middleware('role:customer');
    Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status.update')->middleware('role:staff');

    Route::get('/staff/orders', [OrderController::class, 'index'])->name('staff.orders.index')->middleware('role:staff');
    Route::get('/kitchen/orders', [OrderController::class, 'index'])->name('kitchen.orders.index')->middleware('role:kitchen_staff');
    Route::get('/kitchen/orders/completed', [OrderController::class, 'index'])->name('kitchen.orders.completed')->middleware('role:kitchen_staff');
    Route::get('/manager/orders', [OrderController::class, 'index'])->name('manager.orders.index')->middleware('role:manager');

    Route::prefix('admin')->name('admin.')->middleware('role:manager')->group(function () {
        Route::resource('services', ServiceController::class)->except(['show', 'create']);
        Route::resource('products', ProductController::class)->except(['show', 'create']);
        Route::post('products/bulk-availability', [ProductController::class, 'bulkAvailability'])->name('products.bulk-availability');
        Route::resource('categories', CategoryController::class)->except(['show', 'create']);
    });

    Route::prefix('admin')->name('admin.')->middleware('role:manager,super_admin,developer')->group(function () {
        Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::put('settings', [SettingsController::class, 'update'])->name('settings.update');
    });

    Route::prefix('admin')->name('admin.')->middleware('role:super_admin')->group(function () {
        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');

        Route::resource('flags', FeatureFlagController::class)->except(['show', 'create']);
    });

    Route::prefix('developer')->name('developer.')->middleware('role:developer')->group(function () {
        Route::get('logs', [SystemLogController::class, 'index'])->name('logs.index');
    });
});
