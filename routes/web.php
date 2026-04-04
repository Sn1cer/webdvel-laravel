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

Route::get('/', [FrontController::class, 'index'])->name('home');
Route::get('/', function () {
    $products = Product::latest()->take(4)->get(); 
    return view('welcome', compact('products'));
});
Route::get('/katalog', function () {
    // Mengambil semua produk, tapi dibagi per halaman (misal: 12 produk per halaman)
    $products = Product::latest()->paginate(12); 
    return view('katalog', compact('products'));
})->name('katalog');
Route::get('/produk/{id}', [App\Http\Controllers\FrontController::class, 'show'])->name('produk.detail');
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
// --- RUTE PANEL ADMIN ---
Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
Route::resource('products', ProductController::class);
Route::get('/admin/pesanan', [AdminOrderController::class, 'index'])->name('admin.orders.index');
Route::patch('/admin/pesanan/{id}/status', [AdminOrderController::class, 'updateStatus'])->name('admin.orders.updateStatus');
// --- RUTE LAPORAN PENJUALAN ---
    Route::get('/admin/laporan', [ReportController::class, 'index'])->name('admin.reports.index');
    Route::get('/admin/laporan/cetak', [ReportController::class, 'exportPdf'])->name('admin.reports.pdf');
// --- MANAJEMEN STOK GUDANG ---
    Route::get('/admin/stok', [StockController::class, 'index'])->name('admin.stocks.index');
    Route::patch('/admin/stok/{id}', [StockController::class, 'addStock'])->name('admin.stocks.add');
    Route::get('/admin/stok/cetak', [StockController::class, 'exportPdf'])->name('admin.stocks.pdf');
// --- DATA PELANGGAN (CRM) ---
    Route::get('/admin/pelanggan', [CustomerController::class, 'index'])->name('admin.customers.index');
    // --- KASIR OFFLINE (POS) ---
    Route::get('/admin/kasir', [PosController::class, 'index'])->name('admin.pos.index');
    Route::post('/admin/kasir/checkout', [PosController::class, 'checkout'])->name('admin.pos.checkout');
    
// --- RUTE KHUSUS PELANGGAN YANG SUDAH LOGIN ---
Route::middleware('auth')->group(function () {
    Route::get('/keranjang', [CartController::class, 'index'])->name('cart.index');
    Route::post('/keranjang/tambah', [CartController::class, 'store'])->name('cart.store');

    // Rute untuk Update dan Delete 
    Route::patch('/keranjang/{cart}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/keranjang/{cart}', [CartController::class, 'destroy'])->name('cart.destroy');

    // Rute untuk menampilkan halaman keranjang
    Route::get('/keranjang', [CartController::class, 'index'])->name('cart.index');

    // Rute untuk memproses tombol "Masukkan Keranjang"
    Route::post('/keranjang/tambah', [CartController::class, 'store'])->name('cart.store');
    
    // Rute Checkout
    Route::get('/checkout', [OrderController::class, 'create'])->name('checkout.index');
    Route::post('/checkout', [OrderController::class, 'store'])->name('checkout.store');
    // rute sukses agar membawa ID pesanan
    Route::get('/checkout/sukses/{id}', [OrderController::class, 'success'])->name('checkout.success');
    // Rute baru untuk memproses form upload foto
    Route::post('/checkout/upload-bukti/{id}', [OrderController::class, 'uploadBukti'])->name('checkout.uploadBukti');
    // Rute untuk melihat riwayat pesanan pelanggan
    Route::get('/pesanan-saya', [App\Http\Controllers\OrderController::class, 'history'])->name('orders.history');
});
require __DIR__.'/auth.php';
