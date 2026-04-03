<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Data Statistik Atas
        $pendapatanBulanIni = Order::whereIn('status', ['Diproses', 'Dikirim'])
                                    ->whereMonth('created_at', Carbon::now()->month)
                                    ->whereYear('created_at', Carbon::now()->year)
                                    ->sum('total_harga');

        $pesananMenunggu = Order::where('status', 'Belum Bayar')->count();
        $pesananDiproses = Order::where('status', 'Diproses')->count();
        $totalStok = Product::sum('stok');

        // Data Aktivitas & Stok Menipis
        $aktivitasTerkini = Order::latest()->take(5)->get();
        $stokMenipis = Product::where('stok', '<', 5)->get();

        //Data Grafik Pendapatan 7 Hari Terakhir
        $chartDates = [];
        $chartRevenues = [];

        // Looping mundur dari 6 hari lalu sampai hari ini (0)
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            // Simpan format tanggal (Contoh: "29 Mar")
            $chartDates[] = $date->translatedFormat('d M'); 

            // Hitung pendapatan khusus di tanggal tersebut
            $revenue = Order::whereIn('status', ['Diproses', 'Dikirim'])
                            ->whereDate('created_at', $date->toDateString())
                            ->sum('total_harga');
            
            // Simpan hasil pendapatannya
            $chartRevenues[] = $revenue;
        }

        return view('admin.dashboard', compact(
            'pendapatanBulanIni', 
            'pesananMenunggu', 
            'pesananDiproses', 
            'totalStok',
            'aktivitasTerkini',
            'stokMenipis',
            'chartDates',      
            'chartRevenues'    
        ));
    }
}