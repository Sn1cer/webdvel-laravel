<?php

namespace App\Http\Controllers;

use App\Models\Product; // Memanggil model Product
use Illuminate\Http\Request;

class FrontController extends Controller
{
    public function index()
    {
        // Mengambil semua produk dari database, diurutkan dari yang paling baru
        $products = Product::latest()->get();
        
        // Mengirim data produk ke halaman depan (welcome.blade.php)
        return view('welcome', compact('products'));
    }
    public function show($id)
    {
        // Mencari produk berdasarkan ID. Jika tidak ada, munculkan error 404
        $product = Product::findOrFail($id);
        
        // Membuka halaman detail dan mengirimkan data produk tersebut
        return view('product-detail', compact('product'));
    }
}
