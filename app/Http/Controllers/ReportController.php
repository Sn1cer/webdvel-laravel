<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf; 

class ReportController extends Controller
{
    // Menampilkan Halaman Filter Laporan di Admin
    public function index(Request $request)
    {
        // Hanya ambil pesanan yang sudah dibayar (Diproses / Dikirim)
        $query = Order::whereIn('status', ['Diproses', 'Dikirim']);

        // Jika Admin memasukkan rentang tanggal, filter datanya!
        if ($request->tanggal_awal && $request->tanggal_akhir) {
            $query->whereBetween('created_at', [
                $request->tanggal_awal . ' 00:00:00', 
                $request->tanggal_akhir . ' 23:59:59'
            ]);
        }

        $orders = $query->latest()->get();
        $totalPendapatan = $orders->sum('total_harga');

        return view('admin.reports.index', compact('orders', 'totalPendapatan', 'request'));
    }

    // 2. Fungsi untuk Mengunduh PDF
    public function exportPdf(Request $request)
    {
        $query = Order::whereIn('status', ['Diproses', 'Dikirim']);

        if ($request->tanggal_awal && $request->tanggal_akhir) {
            $query->whereBetween('created_at', [
                $request->tanggal_awal . ' 00:00:00', 
                $request->tanggal_akhir . ' 23:59:59'
            ]);
        }

        $orders = $query->latest()->get();
        $totalPendapatan = $orders->sum('total_harga');

        // Render desain HTML ke dalam bentuk PDF
        $pdf = Pdf::loadView('admin.reports.pdf', compact('orders', 'totalPendapatan', 'request'));
        
        // Unduh file dengan nama ini
        return $pdf->download('Laporan_Penjualan_DVel_Jeans.pdf');
    }
}