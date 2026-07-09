<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::all(); 
        
        return view('products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('products.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. Validasi Data (stok dan ukuran dihapus dari sini karena akan dihitung otomatis)
        $request->validate([
            'nama_produk' => 'required|string|max:255',
            'deskripsi'   => 'nullable|string',
            'harga'       => 'required|integer|min:0',
            'gambar'      => 'required|image|mimes:jpeg,png,jpg|max:2048', 
            'gambar_2'    => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'gambar_3'    => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'gambar_4'    => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'gambar_5'    => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'gambar_6'    => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'gambar_7'    => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'gambar_8'    => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // 2. Hitung Otomatis Total Stok & Ukuran dari input array 'sizes'
        $totalStok = 0;
        $ukuranTersedia = [];
        if ($request->has('sizes')) {
            foreach ($request->sizes as $ukuran => $stok) {
                if ($stok > 0) {
                    $totalStok += $stok;
                    $ukuranTersedia[] = $ukuran;
                }
            }
        }

        // 3. Simpan Data Produk
        $product = new Product();
        $product->nama_produk = $request->nama_produk;
        $product->deskripsi = $request->deskripsi;
        $product->harga = $request->harga;
        // Simpan data otomatis ke kolom lama agar sistem lain tidak error
        $product->stok = $totalStok; 
        $product->ukuran = implode(', ', $ukuranTersedia); 

        $kolomGambar = ['gambar', 'gambar_2', 'gambar_3', 'gambar_4', 'gambar_5', 'gambar_6', 'gambar_7', 'gambar_8'];

        foreach ($kolomGambar as $kolom) {
            if ($request->hasFile($kolom)) {
                $file = $request->file($kolom);
                $namaFile = time() . '_' . $kolom . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('images'), $namaFile);
                $product->{$kolom} = $namaFile;
            }
        }
        $product->save(); 

        // 4. Simpan Rincian Stok per Ukuran ke Tabel Baru (product_sizes)
        if ($request->has('sizes')) {
            foreach ($request->sizes as $ukuran => $stok) {
                if ($stok > 0) { 
                    \App\Models\ProductSize::create([
                        'product_id' => $product->id,
                        'ukuran' => (string)$ukuran,
                        'stok' => $stok
                    ]);
                }
            }
        }

        return redirect()->route('products.index')->with('success', 'Mantap! Data Celana Jeans berhasil ditambahkan ke Gudang.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $product = Product::findOrFail($id); 
        return view('products.edit', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // 1. Validasi Data
        $request->validate([
            'nama_produk' => 'required|string|max:255',
            'deskripsi'   => 'nullable|string',
            'harga'       => 'required|integer|min:0',
            'gambar'      => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'gambar_2'    => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'gambar_3'    => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'gambar_4'    => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'gambar_5'    => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'gambar_6'    => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'gambar_7'    => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'gambar_8'    => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $product = Product::findOrFail($id);

        // 2. Hitung Otomatis Total Stok & Ukuran
        $totalStok = 0;
        $ukuranTersedia = [];
        if ($request->has('sizes')) {
            foreach ($request->sizes as $ukuran => $stok) {
                if ($stok > 0) {
                    $totalStok += $stok;
                    $ukuranTersedia[] = $ukuran;
                }
            }
        }

        // 3. Update Data Produk
        $product->nama_produk = $request->nama_produk;
        $product->deskripsi = $request->deskripsi;
        $product->harga = $request->harga;
        $product->stok = $totalStok;
        $product->ukuran = implode(', ', $ukuranTersedia);

        $kolomGambar = ['gambar', 'gambar_2', 'gambar_3', 'gambar_4', 'gambar_5', 'gambar_6', 'gambar_7', 'gambar_8'];

        foreach ($kolomGambar as $kolom) {
            if ($request->hasFile($kolom)) {
                $file = $request->file($kolom);
                $namaFile = time() . '_' . $kolom . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('images'), $namaFile);
                
                $product->{$kolom} = $namaFile;
            }
        }
        $product->save();

        // 4. Update Rincian Stok per Ukuran di Tabel Baru
        if ($request->has('sizes')) {
            // Hapus data ukuran lama, ganti dengan yang baru
            $product->sizes()->delete(); 

            foreach ($request->sizes as $ukuran => $stok) {
                if ($stok > 0) {
                    \App\Models\ProductSize::create([
                        'product_id' => $product->id,
                        'ukuran' => (string)$ukuran,
                        'stok' => $stok
                    ]);
                }
            }
        }

        return redirect()->route('products.index')->with('success', 'Data Celana Jeans berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Data Celana Jeans berhasil dihapus dari sistem!');
    }
}