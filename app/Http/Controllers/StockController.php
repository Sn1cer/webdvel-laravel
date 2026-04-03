<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf; 

class StockController extends Controller
{
    // Tampilkan Halaman Manajemen Stok
    public function index()
    {
        // Ambil semua produk, urutkan dari stok yang paling sedikit ke paling banyak
        $products = Product::orderBy('stok', 'asc')->get();
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

    // itur Cetak PDF Laporan Sisa Gudang
    public function exportPdf()
    {
        $products = Product::orderBy('stok', 'asc')->get();
        
        // Render ke PDF 
        $pdf = Pdf::loadView('admin.stocks.pdf', compact('products'));
        
        return $pdf->download('Laporan_Stok_Gudang_DVel_Jeans.pdf');
    }
}