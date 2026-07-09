<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\FrontController; 
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AdminOrderController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PosController;
use App\Models\Product;
use App\Http\Controllers\AdminManagementController;
use App\Http\Controllers\BannerController; 

// --- RUTE BERANDA ---
Route::get('/', [FrontController::class, 'index'])->name('home');

// --- RUTE KATALOG ---
Route::get('/katalog', function () {
    $products = Product::latest()->paginate(12); 
    return view('katalog', compact('products'));
})->name('katalog');

Route::get('/produk/{id}', [FrontController::class, 'show'])->name('produk.detail');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// RUTE PANEL ADMIN (Bisa diakses Admin & Owner)
Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
Route::resource('products', ProductController::class);

Route::get('/admin/pesanan', [AdminOrderController::class, 'index'])->name('admin.orders.index');
Route::patch('/admin/pesanan/{id}/status', [AdminOrderController::class, 'updateStatus'])->name('admin.orders.updateStatus');

// --- MANAJEMEN STOK GUDANG ---
Route::get('/admin/stok', [StockController::class, 'index'])->name('admin.stocks.index');
Route::patch('/admin/stok/{id}', [StockController::class, 'addStock'])->name('admin.stocks.add');
Route::get('/admin/stok/cetak', [StockController::class, 'exportPdf'])->name('admin.stocks.pdf');

// --- DATA PELANGGAN (CRM) ---
Route::get('/admin/pelanggan', [CustomerController::class, 'index'])->name('admin.customers.index');

// --- KASIR OFFLINE (POS) ---
Route::get('/admin/kasir', [PosController::class, 'index'])->name('admin.pos.index');
Route::post('/admin/kasir/checkout', [PosController::class, 'checkout'])->name('admin.pos.checkout');

// Rute Manajemen Banner
Route::get('/admin/banners', [BannerController::class, 'index'])->name('admin.banners.index');
Route::post('/admin/banners', [BannerController::class, 'store'])->name('admin.banners.store');
Route::delete('/admin/banners/{id}', [BannerController::class, 'destroy'])->name('admin.banners.destroy');

// RUTE KHUSUS OWNER (Admin biasa akan diblokir)
Route::middleware(['auth', 'owner'])->group(function () {
    // Fitur Pengelolaan Akun Admin
    Route::get('/admin/user-management', [AdminManagementController::class, 'index'])->name('admin.user-management.index');
    Route::post('/admin/user-management', [AdminManagementController::class, 'store'])->name('admin.user-management.store'); // <-- Rute Tambah Admin Baru
    Route::patch('/admin/user-management/{user}', [AdminManagementController::class, 'updateRole'])->name('admin.user-management.update-role');
    
    // Fitur Laporan Penjualan (Hanya Owner yang boleh cetak laporan)
    Route::get('/admin/laporan', [ReportController::class, 'index'])->name('admin.reports.index');
    Route::get('/admin/laporan/cetak', [ReportController::class, 'exportPdf'])->name('admin.reports.pdf');
});

// RUTE KHUSUS PELANGGAN YANG SUDAH LOGIN
Route::middleware('auth')->group(function () {
    // Rute Keranjang
    Route::get('/keranjang', [CartController::class, 'index'])->name('cart.index');
    Route::post('/keranjang/tambah', [CartController::class, 'store'])->name('cart.store');
    Route::patch('/keranjang/{cart}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/keranjang/{cart}', [CartController::class, 'destroy'])->name('cart.destroy');

    // Rute Checkout
    Route::get('/checkout', [OrderController::class, 'create'])->name('checkout.index');
    Route::post('/checkout', [OrderController::class, 'store'])->name('checkout.store');
    Route::get('/checkout/sukses/{id}', [OrderController::class, 'success'])->name('checkout.success');
    Route::post('/checkout/upload-bukti/{id}', [OrderController::class, 'uploadBukti'])->name('checkout.uploadBukti');
    Route::get('/booking-success/{id}', [OrderController::class, 'bookingSuccess'])->name('booking.success');
    
    // Rute Riwayat Pesanan
    Route::get('/pesanan-saya', [OrderController::class, 'history'])->name('orders.history');
}); 

// --- RUTE WEBHOOK MIDTRANS ---
// Harus di luar middleware auth agar server Midtrans bisa mengirim data kemari tanpa perlu login
Route::post('/midtrans/callback', [OrderController::class, 'callback']);

require __DIR__.'/auth.php';