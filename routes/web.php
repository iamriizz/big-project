<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Models\Transaction;

// 1. Landing Page Katalog (Publik & Pelanggan)
Route::get('/', [HomeController::class, 'index'])->name('home');

// 2. Fitur Khusus Pengguna Login (Pelanggan)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [AuthController::class, 'editProfile'])->name('profile.edit');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
    
    // Fitur Keranjang
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
    Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');

    // Fitur Checkout, Pesanan & Riwayat
    Route::get('/checkout/{id?}', [HomeController::class, 'checkout'])->name('checkout');
    Route::post('/order', [HomeController::class, 'order'])->name('order');
    Route::get('/history', [HomeController::class, 'history'])->name('orders.history'); // <- Diubah ke orders.history
    
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// 3. Auth Guest (Belum Login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// 4. Fitur Cetak Struk Thermal (Admin Dashboard)
Route::get('/admin/transactions/{transaction}/print', function (Transaction $transaction) {
    return view('print.receipt', compact('transaction'));
})->middleware(['auth'])->name('transactions.print');