<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class AdminOrderController extends Controller
{
    // Fungsi untuk menampilkan daftar semua pesanan ke Admin
    public function index(Request $request)
    {
        // Menambahkan with('details') untuk optimasi kecepatan pembacaan jumlah celana
        $query = Order::with('details');

        // LOGIKA FILTER (misal: ?status=Diproses)
        if ($request->filled('status') && $request->status != 'Semua') {
            $query->where('status', $request->status);
        }

        // selesai
        $orders = $query->latest()->get();

        return view('admin.orders.index', compact('orders'));
    }

    // Fungsi untuk mengubah status pesanan (Misal: dari Belum Bayar -> Dikirim)
    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        
        $order->update([
            'status' => $request->status,
            'resi' => $request->resi 
        ]);

        // Pesan notifikasi sukses yang dinamis (Cerdas mengenali tipe pesanan)
        if ($order->tipe_pesanan == 'Booking' && $request->status == 'Dikirim') {
            $pesanSukses = 'Status berhasil! Pesanan Booking telah Lunas dan selesai diambil.';
        } else {
            $pesanSukses = 'Status & Resi pesanan berhasil diperbarui!';
        }

        return redirect()->back()->with('success', $pesanSukses);
    }
}