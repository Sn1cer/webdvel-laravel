<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Order; 
use App\Models\OrderDetail; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('sizes')->get(); 
        
        $shopeeLogs = DB::table('shopee_logs')
            ->join('product_sizes', 'shopee_logs.product_size_id', '=', 'product_sizes.id')
            ->select('shopee_logs.*', 'product_sizes.ukuran')
            ->where('shopee_logs.jumlah_penyesuaian', '<', 0)
            ->orderBy('shopee_logs.created_at', 'desc')
            ->get()
            ->groupBy('product_id'); 
        
        return view('products.index', compact('products', 'shopeeLogs'));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_produk'     => 'required|string|max:255',
            'kategori_gender' => 'required|in:Men,Women',
            'deskripsi'       => 'nullable|string',
            'harga'           => 'required|integer|min:0',
            'gambar'          => 'required|image|mimes:jpeg,png,jpg|max:2048', 
            'gambar_2'        => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'gambar_3'        => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'gambar_4'        => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'gambar_5'        => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'gambar_6'        => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'gambar_7'        => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'gambar_8'        => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

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

        $product = new Product();
        $product->nama_produk = $request->nama_produk;
        $product->kategori_gender = $request->kategori_gender;
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

    public function edit(string $id)
    {
        $product = Product::findOrFail($id); 
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'nama_produk'     => 'required|string|max:255',
            'kategori_gender' => 'required|in:Men,Women', 
            'deskripsi'       => 'nullable|string',
            'harga'           => 'required|integer|min:0',
            'gambar'          => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'gambar_2'        => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $product = Product::findOrFail($id);

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

        $product->nama_produk = $request->nama_produk;
        $product->kategori_gender = $request->kategori_gender;
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

        if ($request->has('sizes')) {
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

    public function destroy(string $id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Data Celana Jeans berhasil dihapus dari sistem!');
    }

    // --- SINKRONISASI STOK SHOPEE TERINTEGRASI (+ MARKUP 30%) ---
    public function adjustStock(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        
        $adjustments = $request->input('adjustments', []);
        $ada_perubahan = false;

        DB::beginTransaction();
        try {
            foreach ($adjustments as $size_id => $qty) {
                if ($qty && $qty != 0) {
                    $size = \App\Models\ProductSize::where('product_id', $product->id)->where('id', $size_id)->first();
                    
                    if ($size) {
                        $stok_baru = $size->stok + $qty;

                        if ($stok_baru < 0) {
                            DB::rollBack();
                            return redirect()->back()->with('error', 'Gagal! Sisa stok untuk Ukuran ' . $size->ukuran . ' tidak mencukupi untuk dikurangi.');
                        }

                        $size->stok = $stok_baru;
                        $size->save();

                        if ($qty < 0) {
                            $jumlahTerjual = abs($qty); 
                            
                            // LOGIKA BARU: Harga Web dikali 1.3 (Markup 30%) untuk transaksi Shopee
                            $hargaShopee = $product->harga * 1.3;
                            $totalHarga = $hargaShopee * $jumlahTerjual;

                            $order = new Order();
                            $order->user_id = Auth::id() ?? 1;
                            $order->nama_depan = 'Pelanggan';
                            $order->nama_belakang = 'Shopee';
                            $order->no_hp = '-';
                            $order->wilayah = 'Marketplace Shopee';
                            $order->alamat_jalan = '-';
                            $order->total_harga = $totalHarga; // Menyimpan harga yang sudah dinaikkan 30%
                            $order->ongkir = 0;
                            $order->status = 'Dikirim';
                            $order->tipe_pesanan = 'Shopee';
                            $order->resi = '#SHP-' . strtoupper(uniqid()); 
                            $order->save();

                            $detail = new OrderDetail();
                            $detail->order_id = $order->id;
                            $detail->product_id = $product->id;
                            $detail->ukuran = $size->ukuran;
                            $detail->jumlah = $jumlahTerjual;
                            $detail->harga_satuan = $hargaShopee; // Menyimpan harga satuan yang sudah dinaikkan 30%
                            $detail->save();

                            DB::table('shopee_logs')->insert([
                                'product_id'         => $product->id,
                                'product_size_id'    => $size->id,
                                'jumlah_penyesuaian' => (int)$qty,
                                'keterangan'         => 'Terjual di Shopee',
                                'created_at'         => now(),
                                'updated_at'         => now(),
                            ]);
                        }

                        $ada_perubahan = true;
                    }
                }
            }

            if ($ada_perubahan) {
                $product->stok = $product->sizes()->sum('stok');
                $product->save();

                DB::commit();
                return redirect()->back()->with('success', 'Stok varian berhasil disesuaikan & Transaksi Shopee otomatis terekam dengan harga +30%!');
            }

            DB::rollBack();
            return redirect()->back();
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem saat sinkronisasi: ' . $e->getMessage());
        }
    }
}