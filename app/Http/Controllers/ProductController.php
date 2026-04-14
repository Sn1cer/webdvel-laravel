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
        // Mengambil semua data dari tabel products
        $products = Product::all(); 
        
        // Mengirim data tersebut ke file tampilan bernama 'index.blade.php'
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
        // 1. Validasi Data (Termasuk ke-8 gambar dan ukuran)
        $request->validate([
            'nama_produk' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'ukuran'      => 'required|array', // Memastikan ukuran berbentuk array dari checkbox
            'harga'       => 'required|integer|min:0',
            'stok'        => 'required|integer|min:0',
            'gambar'      => 'required|image|mimes:jpeg,png,jpg|max:2048', // Gambar utama wajib
            'gambar_2'    => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'gambar_3'    => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'gambar_4'    => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'gambar_5'    => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'gambar_6'    => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'gambar_7'    => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'gambar_8'    => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // 2. Ubah array ukuran dari checkbox menjadi teks yang dipisahkan koma
        // Contoh: ['27', '28', '30'] menjadi "27, 28, 30"
        $ukuranString = $request->has('ukuran') ? implode(', ', $request->ukuran) : null;

        // 3. Buat Objek Produk Baru (Tanpa Gambar Dulu)
        $product = new Product();
        $product->nama_produk = $request->nama_produk;
        $product->deskripsi = $request->deskripsi;
        $product->ukuran = $ukuranString;
        $product->harga = $request->harga;
        $product->stok = $request->stok;

        // 4. Proses Mengamankan Upload Foto (Mengecek 8 kotak secara berurutan)
        $kolomGambar = ['gambar', 'gambar_2', 'gambar_3', 'gambar_4', 'gambar_5', 'gambar_6', 'gambar_7', 'gambar_8'];

        foreach ($kolomGambar as $kolom) {
            if ($request->hasFile($kolom)) {
                $file = $request->file($kolom);
                // Nama file unik: timestamp _ namaKolom . ekstensi
                $namaFile = time() . '_' . $kolom . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('images'), $namaFile);
                
                // Memasukkan nama file ke dalam property database
                $product->{$kolom} = $namaFile;
            }
        }

        // 5. Menyimpan Data ke Database MySQL
        $product->save(); 

        // 6. Mengembalikan halaman ke formulir dan memberikan pesan Sukses
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
        $product = Product::findOrFail($id); // Cari data berdasarkan ID
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
            'deskripsi' => 'nullable|string',
            'ukuran'      => 'required|array',
            'harga'       => 'required|integer|min:0',
            'stok'        => 'required|integer|min:0',
            'gambar'      => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Di Edit, gambar utama boleh kosong (artinya tidak diganti)
            'gambar_2'    => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'gambar_3'    => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'gambar_4'    => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'gambar_5'    => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'gambar_6'    => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'gambar_7'    => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'gambar_8'    => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $product = Product::findOrFail($id);

        // 2. Format Ukuran
        $ukuranString = $request->has('ukuran') ? implode(', ', $request->ukuran) : null;

        // 3. Update Data Teks
        $product->nama_produk = $request->nama_produk;
        $product->deskripsi = $request->deskripsi;
        $product->ukuran = $ukuranString;
        $product->harga = $request->harga;
        $product->stok = $request->stok;

        // 4. Proses Upload Gambar Baru (Hanya memproses kotak gambar yang diisi oleh admin)
        $kolomGambar = ['gambar', 'gambar_2', 'gambar_3', 'gambar_4', 'gambar_5', 'gambar_6', 'gambar_7', 'gambar_8'];

        foreach ($kolomGambar as $kolom) {
            if ($request->hasFile($kolom)) {
                $file = $request->file($kolom);
                $namaFile = time() . '_' . $kolom . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('images'), $namaFile);
                
                $product->{$kolom} = $namaFile;
            }
        }

        // 5. Simpan Perubahan ke Database
        $product->save();

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