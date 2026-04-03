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
        // 1. Validasi Data (Memastikan admin tidak memasukkan data yang salah)
        $request->validate([
            'nama_produk' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'harga' => 'required|integer|min:0',
            'stok' => 'required|integer|min:0',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Maksimal 2MB
        ]);

        // 2. Proses Mengamankan Upload Foto (Jika admin mengunggah foto)
        $namaGambar = null;
        if ($request->hasFile('gambar')) {
            $gambar = $request->file('gambar');
            $namaGambar = time() . '_' . $gambar->getClientOriginalName();
            // Foto akan otomatis disimpan ke folder public/images
            $gambar->move(public_path('images'), $namaGambar); 
        }

        // 3. Menyimpan Data ke Database MySQL
        // Ubah array ukuran dari checkbox menjadi teks yang dipisahkan koma
$ukuranString = $request->has('ukuran') ? implode(',', $request->ukuran) : null;
        Product::create([
            'nama_produk' => $request->nama_produk,
            'deskripsi' => $request->deskripsi,
            'ukuran' => $ukuranString,
            'harga' => $request->harga,
            'stok' => $request->stok,
            'gambar' => $namaGambar,
        ]);

        // 4. Mengembalikan halaman ke formulir dan memberikan pesan Sukses
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
        $request->validate([
            'nama_produk' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'harga' => 'required|integer|min:0',
            'stok' => 'required|integer|min:0',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $product = Product::findOrFail($id);
        $namaGambar = $product->gambar; // Simpan nama gambar lama dulu

        // Jika admin mengunggah gambar baru, ganti dengan yang baru
        if ($request->hasFile('gambar')) {
            $gambar = $request->file('gambar');
            $namaGambar = time() . '_' . $gambar->getClientOriginalName();
            $gambar->move(public_path('images'), $namaGambar);
        }

        // Simpan perubahan ke database
        $ukuranString = $request->has('ukuran') ? implode(',', $request->ukuran) : null;
        $product->update([
            'nama_produk' => $request->nama_produk,
            'deskripsi' => $request->deskripsi,
            'ukuran' => $ukuranString,
            'harga' => $request->harga,
            'stok' => $request->stok,
            'gambar' => $namaGambar,
        ]);

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
