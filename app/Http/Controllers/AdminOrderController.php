<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class AdminOrderController extends Controller
{
    // Fungsi untuk menampilkan daftar semua pesanan ke Admin
    public function index(Request $request)
    {
        // pencarian data 
        $query = Order::query();

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

        return redirect()->back()->with('success', 'Status & Resi pesanan berhasil diperbarui!');
    }
}