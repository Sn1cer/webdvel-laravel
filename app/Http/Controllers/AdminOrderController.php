<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class AdminOrderController extends Controller
{
    // Fungsi untuk menampilkan daftar semua pesanan ke Admin
    public function index(Request $request)
    {
        // Mulai pencarian data pesanan
        $query = Order::query();

        // LOGIKA FILTER: Jika ada permintaan tab status dari URL (misal: ?status=Diproses)
        if ($request->filled('status') && $request->status != 'Semua') {
            $query->where('status', $request->status);
        }

        // Ambil data yang sudah difilter, urutkan dari yang terbaru
        $orders = $query->latest()->get();

        return view('admin.orders.index', compact('orders'));
    }

    // Fungsi untuk mengubah status pesanan (Misal: dari Belum Bayar -> Dikirim)
    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        
        // Simpan status dan resi sekaligus
        $order->update([
            'status' => $request->status,
            'resi' => $request->resi // Ini baris barunya!
        ]);

        return redirect()->back()->with('success', 'Status & Resi pesanan berhasil diperbarui!');
    }
}