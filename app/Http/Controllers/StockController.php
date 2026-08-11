<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB; 

class StockController extends Controller
{
    // Tampilkan Halaman Manajemen Stok
    public function index()
    {
        // Ambil semua produk beserta relasi ukurannya, urutkan dari stok yang paling sedikit ke paling banyak
        $products = Product::with('sizes')->orderBy('stok', 'asc')->get();
        return view('admin.stocks.index', compact('products'));
    }

    // Fitur Quick Restock (Tambah Stok Cepat)
    public function addStock(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        
        // Tambahkan stok lama dengan stok baru yang diinput
        $product->stok += $request->tambahan_stok;
        $product->save();

        return redirect()->back()->with('success', 'Stok untuk ' . $product->nama_produk . ' berhasil ditambah sebanyak ' . $request->tambahan_stok . ' Pcs!');
    }

    // Fitur Cetak PDF Laporan Sisa Gudang (Stok Fisik Saat Ini)
    public function exportPdf()
    {
        // Ambil data produk beserta detail varian ukurannya
        $products = Product::with('sizes')->orderBy('stok', 'asc')->get();
        
        // Render ke PDF 
        $pdf = Pdf::loadView('admin.stocks.pdf', compact('products'));
        
        return $pdf->download('Laporan_Stok_Gudang_DVel_Jeans.pdf');
    }

    // Fitur Baru: Cetak PDF Laporan Log Aktivitas Shopee
    public function exportShopeeLogPdf()
    {
        // Mengambil riwayat log khusus pengurangan (minus) dari Shopee beserta nama produk dan ukurannya
        $logs = DB::table('shopee_logs')
            ->join('products', 'shopee_logs.product_id', '=', 'products.id')
            ->join('product_sizes', 'shopee_logs.product_size_id', '=', 'product_sizes.id')
            ->select(
                'shopee_logs.*', 
                'products.nama_produk', 
                'product_sizes.ukuran'
            )
            ->where('shopee_logs.jumlah_penyesuaian', '<', 0)
            ->orderBy('shopee_logs.created_at', 'desc')
            ->get();

        // Render ke PDF menggunakan view baru
        $pdf = Pdf::loadView('admin.stocks.shopee_pdf', compact('logs'));
        
        return $pdf->download('Laporan_Log_Shopee_DVel_Jeans.pdf');
    }
}