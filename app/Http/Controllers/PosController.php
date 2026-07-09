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
                'ongkir' => 0, 
                'status' => 'Dikirim', 
                'tipe_pesanan' => 'POS Offline', 
                'resi' => 'POS-OFFLINE-' . strtoupper(uniqid())
            ]);

            foreach ($cart as $item) {
                OrderDetail::create([
                    'order_id' => $order->id,
                    'product_id' => $item['id'],
                    'jumlah' => $item['qty'],
                    'harga_satuan' => $item['price'], 
                    'ukuran' => $item['ukuran'] ?? '-' 
                ]);

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

            // PENTING: Load relasi detail & produk agar datanya bisa dicetak di struk
            $order->load('details.product');

            // Mengembalikan pesan sukses, sekaligus mengirimkan data $order ke dalam session 'print_order'
            return redirect()->back()
                ->with('success', 'Pembayaran Offline Berhasil! Stok varian ukuran telah otomatis dikurangi.')
                ->with('print_order', $order);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }
}