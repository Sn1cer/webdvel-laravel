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

            // Membuat data Order Induk
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

            // Memproses rincian barang, memotong stok global, dan memotong stok ukuran
            foreach ($cart as $item) {
                
                // 1. Simpan ke OrderDetail
                OrderDetail::create([
                    'order_id' => $order->id,
                    'product_id' => $item['id'],
                    'jumlah' => $item['qty'],
                    'harga_satuan' => $item['price'], 
                    'ukuran' => $item['ukuran'] ?? '-' // Simpan ukuran spesifik ke nota
                ]);

                // 2. Potong Stok Global (Tabel Products)
                $product = Product::find($item['id']);
                if ($product) {
                    $product->stok -= $item['qty'];
                    $product->save();
                }

                // 3. Potong Stok Spesifik per Ukuran (Tabel Product_Sizes)
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
            return redirect()->back()->with('success', 'Pembayaran Offline Berhasil! Stok varian ukuran telah otomatis dikurangi.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }
}