<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\ProductSize; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PosController extends Controller
{
    public function index()
    {
        $products = Product::with('sizes')->where('stok', '>', 0)->get();
        return view('admin.pos.index', compact('products'));
    }

    // --- SINKRONISASI KASIR POS (BYPASS FILLABLE UNTUK RESI #POS) ---
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

            $metode_pembayaran = $request->metode_pembayaran ?? 'Tunai';
            $tipe_pesanan_text = 'POS Offline (' . $metode_pembayaran . ')';

            // BYPASS METODE CREATE MENJADI INSTANSIASI AGAR RESI PASTI TERSIMPAN
            $order = new Order();
            $order->user_id = Auth::id() ?? 1;
            $order->nama_depan = 'Pelanggan Toko';
            $order->nama_belakang = '(Offline)';
            $order->no_hp = '-';
            $order->wilayah = 'Pembelian Langsung di Toko Fisik';
            $order->alamat_jalan = '-';
            $order->total_harga = $totalHarga;
            $order->ongkir = 0; 
            $order->status = 'Dikirim'; 
            $order->tipe_pesanan = $tipe_pesanan_text; 
            $order->resi = '#POS-' . strtoupper(uniqid()); // PASTI #POS
            $order->save();

            foreach ($cart as $item) {
                $detail = new OrderDetail();
                $detail->order_id = $order->id;
                $detail->product_id = $item['id'];
                $detail->jumlah = $item['qty'];
                $detail->harga_satuan = $item['price'];
                $detail->ukuran = $item['ukuran'] ?? '-';
                $detail->save();

                $product = Product::find($item['id']);
                if ($product) {
                    $product->stok -= $item['qty'];
                    $product->save();
                }

                if (isset($item['ukuran'])) {
                    $productSize = ProductSize::where('product_id', $item['id'])
                                                ->where('ukuran', $item['ukuran'])
                                                ->first();
                                                
                    if ($productSize) {
                        $productSize->stok -= $item['qty'];
                        $productSize->save();
                    }
                }
            }

            DB::commit(); 

            $order->load('details.product');

            return redirect()->back()
                ->with('success', 'Transaksi Kasir Berhasil! Resi #POS telah otomatis masuk ke Laporan dan Dashboard.')
                ->with('print_order', $order);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem saat memproses transaksi: ' . $e->getMessage());
        }
    }
}