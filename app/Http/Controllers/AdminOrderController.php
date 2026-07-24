<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductSize;
use Illuminate\Http\Request;

class AdminOrderController extends Controller
{
    public function index(Request $request)
    {
        // 1. AUTO CANCEL BOOKING LEWAT 24 JAM
        $expiredBookings = Order::with('details')
            ->where('tipe_pesanan', 'Booking')
            ->where('status', 'Belum Bayar')
            ->where('created_at', '<', now()->subHours(24))
            ->get();

        foreach ($expiredBookings as $expired) {
            foreach ($expired->details as $detail) {
                // Kembalikan stok global
                $produk = Product::find($detail->product_id);
                if ($produk) { $produk->stok += $detail->jumlah; $produk->save(); }
                
                // Kembalikan stok ukuran spesifik
                $productSize = ProductSize::where('product_id', $detail->product_id)
                                ->where('ukuran', $detail->ukuran)->first();
                if ($productSize) { $productSize->stok += $detail->jumlah; $productSize->save(); }
            }
            $expired->update(['status' => 'Dibatalkan']);
        }

        // 2. LOAD DATA NORMAL KE TABEL
        $status = $request->query('status', 'Semua');
        if ($status == 'Semua') {
            $orders = Order::with('details.product')->latest()->get();
        } else {
            $orders = Order::with('details.product')->where('status', $status)->latest()->get();
        }

        return view('admin.orders.index', compact('orders'));
    }

    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        
        $order->update([
            'status' => $request->status,
            'resi' => $request->resi ?? $order->resi
        ]);

        return back()->with('success', 'Status pesanan berhasil diperbarui!');
    }
}