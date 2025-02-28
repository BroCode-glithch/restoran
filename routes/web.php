<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\ServicesController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TestimonialController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/logout', [LogoutController::class, 'logout'])->name('logout');

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('about', [AboutController::class, 'index'])->name('about');

Route::get('services', [ServicesController::class, 'index'])->name('services');

Route::get('menu', [MenuController::class, 'index'])->name('menu');

Route::get('booking', [BookingController::class, 'index'])->name('booking');

Route::get('team', [TeamController::class, 'index'])->name('team');

Route::get('testimonial', [TestimonialController::class, 'index'])->name('testimonial');
