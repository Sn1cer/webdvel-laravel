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
        // Panggil relasi details.product untuk mencegah N+1 Query
        $query = Order::with('details.product')->latest();

        // Filter berdasarkan Status Pesanan
        if ($request->has('status') && $request->status != 'Semua') {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan Tipe Pesanan (Online, Booking, POS)
        if ($request->has('tipe') && $request->tipe != 'Semua') {
            $query->where('tipe_pesanan', $request->tipe);
        }

        $orders = $query->get();

        return view('admin.orders.index', compact('orders'));
    }

    public function updateStatus(Request $request, $id)
    {
        $order = Order::with('details')->findOrFail($id);
        $oldStatus = $order->status;
        $newStatus = $request->status;

        $updateData = ['status' => $newStatus];

        // Jika form mengirim nomor resi yang tidak kosong
        if ($request->has('resi') && $request->resi != null) {
            $updateData['resi'] = $request->resi;
        }

        // LOGIKA PENGEMBALIAN STOK (JIKA ADMIN MEMBATALKAN PESANAN MANUAL)
        if ($newStatus == 'Dibatalkan' && $oldStatus != 'Dibatalkan') {
            foreach ($order->details as $detail) {
                // 1. Kembalikan stok global produk
                $produk = Product::find($detail->product_id);
                if ($produk) {
                    $produk->stok += $detail->jumlah;
                    $produk->save();
                }

                // 2. Kembalikan stok spesifik pada varian ukuran
                $productSize = ProductSize::where('product_id', $detail->product_id)
                                ->where('ukuran', $detail->ukuran)->first();
                if ($productSize) {
                    $productSize->stok += $detail->jumlah;
                    $productSize->save();
                }
            }
        }

        // Simpan perubahan ke database
        $order->update($updateData);

        return redirect()->back()->with('success', 'Status pesanan berhasil diperbarui!');
    }
}