<?php

namespace App\Http\Controllers;

use App\Models\Product; 
use App\Models\Banner;
use Illuminate\Http\Request;

class FrontController extends Controller
{
    public function index()
    {
        // dari database
        $products = Product::latest()->get();
        
        // send to (welcome.blade.php)
        $banners = Banner::where('is_active', true)->latest()->get();
        return view('welcome', compact('products', 'banners'));
    }

    // --- FUNGSI BARU UNTUK HALAMAN KATALOG & FILTER KATEGORI ---
    public function katalog(Request $request)
    {
        // 1. Mulai persiapan query ke tabel produk
        $query = Product::query();

        // 2. LOGIKA FILTER: Cek apakah ada parameter 'kategori' di URL
        // Kolom di database bernama 'kategori_gender' (berisi 'Men' atau 'Women')
        if ($request->has('kategori') && in_array($request->kategori, ['Men', 'Women'])) {
            $query->where('kategori_gender', $request->kategori);
        }

        // 3. Tarik data dari database (diurutkan dari yang terbaru, 12 produk per halaman)
        $products = $query->latest()->paginate(12);

        // 4. Kirim data yang sudah disaring ke resources/views/katalog.blade.php
        return view('katalog', compact('products'));
    }

    public function show($id)
    {
        $product = Product::findOrFail($id);

        return view('product-detail', compact('product'));
    }
}