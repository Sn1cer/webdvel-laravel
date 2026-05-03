<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PosController extends Controller
{
    public function index()
    {
        $products = Product::where('stok', '>', 0)->get();
        return view('admin.pos.index', compact('products'));
    }

    public function checkout(Request $request)
    {
        $cart = json_decode($request->cart_data, true);

        if (!$cart || count($cart) == 0) {
            return redirect()->back()->with('error', 'Keranjang masih kosong!');
        }

        DB::beginTransaction();
        try {
            $totalHarga = 0;
            foreach ($cart as $item) {
                $totalHarga += ($item['price'] * $item['qty']);
            }

            $order = Order::create([
                'user_id' => Auth::id() ?? 1,
                'nama_depan' => 'Pelanggan',
                'nama_belakang' => 'Toko (Offline)',
                'no_hp' => '-',
                'wilayah' => 'Pembelian Langsung di Toko Fisik',
                'alamat_jalan' => '-',
                'total_harga' => $totalHarga,
                'status' => 'Dikirim',
                'resi' => 'POS-OFFLINE-' . strtoupper(uniqid())
            ]);

            foreach ($cart as $item) {
                OrderDetail::create([
                    'order_id' => $order->id,
                    'product_id' => $item['id'],
                    'jumlah' => $item['qty'],
                    'harga_satuan' => $item['price'], 
                    'ukuran' => '-'
                ]);

                $product = Product::find($item['id']);
                $product->stok -= $item['qty'];
                $product->save();
            }

            DB::commit();
            return redirect()->back()->with('success', 'Pembayaran Offline Berhasil! Stok barang telah otomatis dikurangi.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }
}