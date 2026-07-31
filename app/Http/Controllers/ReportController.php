<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // Panggil relasi details.product untuk mencegah load lambat dan memungkinkan rincian barang muncul
        $query = Order::with('details.product')->latest();

        // 1. Filter Rentang Tanggal
        if ($request->filled('tanggal_awal') && $request->filled('tanggal_akhir')) {
            $query->whereBetween('created_at', [
                $request->tanggal_awal . ' 00:00:00', 
                $request->tanggal_akhir . ' 23:59:59'
            ]);
        }

        // 2. Filter Pencarian Cepat (Cari ID Pesanan / Nama Pelanggan)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                // Asumsi ID pesanan disimpan di kolom resi atau nomor_pesanan
                $q->where('resi', 'like', "%{$search}%")
                  ->orWhere('nomor_pesanan', 'like', "%{$search}%")
                  ->orWhere('nama_depan', 'like', "%{$search}%")
                  ->orWhere('nama_belakang', 'like', "%{$search}%");
            });
        }

        // 3. Filter Sumber / Tipe Pesanan (Online, Booking, POS)
        if ($request->filled('tipe_pesanan') && $request->tipe_pesanan != 'Semua') {
            $query->where('tipe_pesanan', $request->tipe_pesanan);
        }

        // 4. Filter Status Pesanan
        if ($request->filled('status') && $request->status != 'Semua') {
            $query->where('status', $request->status);
        }

        // 5. Filter Metode Pembayaran
        if ($request->filled('pembayaran') && $request->pembayaran != 'Semua') {
            if ($request->pembayaran == 'Midtrans') {
                $query->where('bukti_pembayaran', 'midtrans_verified');
            } elseif ($request->pembayaran == 'Tunai') {
                $query->where(function($q) {
                    $q->whereNull('bukti_pembayaran')
                      ->orWhere('bukti_pembayaran', '!=', 'midtrans_verified');
                });
            }
        }

        $orders = $query->get();
        
        // Kalkulasi Total Pendapatan Bersih (Tidak menghitung pesanan yang dibatalkan)
        $totalPendapatan = $orders->where('status', '!=', 'Dibatalkan')->sum('total_harga');

        return view('admin.reports.index', compact('orders', 'totalPendapatan'));
    }

    public function exportPdf(Request $request)
    {
        // PENTING: Gunakan logika query yang SAMA PERSIS dengan index
        // Agar data yang di-print di PDF sama persis dengan tabel yang sedang difilter Admin
        $query = Order::with('details.product')->latest();

        if ($request->filled('tanggal_awal') && $request->filled('tanggal_akhir')) {
            $query->whereBetween('created_at', [
                $request->tanggal_awal . ' 00:00:00', 
                $request->tanggal_akhir . ' 23:59:59'
            ]);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('resi', 'like', "%{$search}%")
                  ->orWhere('nomor_pesanan', 'like', "%{$search}%")
                  ->orWhere('nama_depan', 'like', "%{$search}%")
                  ->orWhere('nama_belakang', 'like', "%{$search}%");
            });
        }

        if ($request->filled('tipe_pesanan') && $request->tipe_pesanan != 'Semua') {
            $query->where('tipe_pesanan', $request->tipe_pesanan);
        }

        if ($request->filled('status') && $request->status != 'Semua') {
            $query->where('status', $request->status);
        }

        if ($request->filled('pembayaran') && $request->pembayaran != 'Semua') {
            if ($request->pembayaran == 'Midtrans') {
                $query->where('bukti_pembayaran', 'midtrans_verified');
            } elseif ($request->pembayaran == 'Tunai') {
                $query->where(function($q) {
                    $q->whereNull('bukti_pembayaran')
                      ->orWhere('bukti_pembayaran', '!=', 'midtrans_verified');
                });
            }
        }

        $orders = $query->get();
        $totalPendapatan = $orders->where('status', '!=', 'Dibatalkan')->sum('total_harga');

        // Kembalikan ke halaman view PDF Anda
        // (Pastikan file view admin.reports.pdf Anda juga melakukan foreach pada $order->details jika ingin menampilkan detail di PDF)
        return view('admin.reports.pdf', compact('orders', 'totalPendapatan', 'request'));
    }
}