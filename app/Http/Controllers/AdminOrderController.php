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
        $query = Order::with('details.product')->latest();

        if ($request->has('status') && $request->status != 'Semua') {
            $query->where('status', $request->status);
        }

        // Filter 
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

        if ($request->has('resi') && $request->resi != null) {
            $updateData['resi'] = $request->resi;
        }

        // pembatalan manual by admin
        if ($newStatus == 'Dibatalkan' && $oldStatus != 'Dibatalkan') {
            foreach ($order->details as $detail) {
                $produk = Product::find($detail->product_id);
                if ($produk) {
                    $produk->stok += $detail->jumlah;
                    $produk->save();
                }

                $productSize = ProductSize::where('product_id', $detail->product_id)
                                ->where('ukuran', $detail->ukuran)->first();
                if ($productSize) {
                    $productSize->stok += $detail->jumlah;
                    $productSize->save();
                }
            }
        }

        $order->update($updateData);

        return redirect()->back()->with('success', 'Status pesanan berhasil diperbarui!');
    }
}