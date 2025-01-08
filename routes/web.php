<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
// use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('register', [AdminAuthController::class, 'registerFrom'])->name('register');
Route::post('register', [AdminAuthController::class, 'register']);
Route::get('login', [AdminAuthController::class, 'loginFrom'])->name('login');
Route::post('login', [AdminAuthController::class, 'login']);


Route::middleware(['is_admin'])->group(function () {
    Route::get('logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
    // Display product management page
    Route::get('/products', [ProductController::class, 'index'])->name('admin.products.index');
    
    // Handle product CRUD operations via AJAX
    Route::get('/products/data', [ProductController::class, 'data'])->name('admin.products.data');
    Route::get('/products/{id}', [ProductController::class, 'getByid'])->name('admin.products.getbyid');
    Route::post('/products', [ProductController::class, 'store'])->name('admin.products.store');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('admin.products.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('admin.products.destroy');
});