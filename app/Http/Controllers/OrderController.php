<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    // halaman Checkout
    public function create()
    {
        $carts = Cart::with('product')->where('user_id', Auth::id())->get();
        
        if ($carts->count() == 0) {
            return redirect('/')->with('error', 'Keranjang Anda kosong!');
        }

        $totalHarga = 0;
        foreach ($carts as $cart) {
            $totalHarga += $cart->product->harga * $cart->jumlah;
        }

        return view('checkout', compact('carts', 'totalHarga'));
    }

    // Memproses form pesanan dan mengurangi stok
    public function store(Request $request)
    {
        // 1. Validasi dinamis berdasarkan tipe pesanan
        $rules = [
            'nama_depan' => 'required|string|max:255',
            'nama_belakang' => 'required|string|max:255',
            'no_hp' => 'required|string|max:20',
            'tipe_pesanan' => 'required|in:Online,Booking',
        ];

        // Jika Online, alamat wajib diisi
        if ($request->tipe_pesanan === 'Online') {
            $rules['alamat_jalan'] = 'required|string|max:255';
            $rules['wilayah'] = 'required|string|max:255';
        }

        $request->validate($rules);

        $carts = Cart::with('product')->where('user_id', Auth::id())->get();
        if ($carts->count() == 0) {
            return redirect('/');
        }

        $totalHarga = 0;
        foreach ($carts as $cart) {
            $totalHarga += $cart->product->harga * $cart->jumlah;
        }

        // 2. Set alamat default jika pelanggan memilih Booking
        $alamatJalan = $request->tipe_pesanan === 'Booking' ? 'Ambil di Toko' : $request->alamat_jalan;
        $wilayah = $request->tipe_pesanan === 'Booking' ? 'Ambil di Toko' : $request->wilayah;
        $alamatLengkap = $request->tipe_pesanan === 'Booking' ? 'Pesanan Booking - Ambil di Toko D Vel Jeans' : $request->alamat_lengkap;

        // 3. Simpan data pesanan
        $order = Order::create([
            'user_id' => Auth::id(),
            'nama_depan' => $request->nama_depan,
            'nama_belakang' => $request->nama_belakang,
            'alamat_jalan' => $alamatJalan,
            'wilayah' => $wilayah,
            'no_hp' => $request->no_hp,
            'alamat_lengkap' => $alamatLengkap,
            'total_harga' => $totalHarga,
            'status' => 'Belum Bayar',
            'tipe_pesanan' => $request->tipe_pesanan // Menyimpan tipe pesanan ke database
        ]);

        // Pindahkan ke Order Detail & Kurangi Stok
        foreach ($carts as $cart) {
            OrderDetail::create([
                'order_id' => $order->id, 
                'product_id' => $cart->product_id,
                'ukuran' => $cart->ukuran,
                'jumlah' => $cart->jumlah,
                'harga_satuan' => $cart->product->harga
            ]);

            // Logika Pengurangan Stok
            $produk = Product::find($cart->product_id);
            if ($produk) {
                $produk->stok -= $cart->jumlah;
                $produk->save();
            }
        }

        // Kosongkan keranjang
        Cart::where('user_id', Auth::id())->delete();

        if ($request->tipe_pesanan === 'Booking') {
            return redirect()->route('booking.success', $order->id);
        }

        return redirect()->route('checkout.success', $order->id);
    }

    public function success($id)
    {
        $order = Order::where('user_id', Auth::id())->findOrFail($id);

        if ($order->tipe_pesanan == 'Online' && $order->status == 'Belum Bayar' && empty($order->snap_token)) {
            
            \Midtrans\Config::$serverKey = config('midtrans.server_key');
            \Midtrans\Config::$isProduction = config('midtrans.is_production');
            \Midtrans\Config::$isSanitized = config('midtrans.is_sanitized');
            \Midtrans\Config::$is3ds = config('midtrans.is_3ds');

            $params = [
                'transaction_details' => [
                    'order_id' => $order->id,
                    'gross_amount' => $order->total_harga,
                ],
                'customer_details' => [
                    'first_name' => $order->nama_depan,
                    'last_name' => $order->nama_belakang,
                    'phone' => $order->no_hp,
                ],
            ];

            $snapToken = \Midtrans\Snap::getSnapToken($params);
            $order->snap_token = $snapToken;
            $order->save();
        }

        return view('checkout-success', compact('order'));
    }
    
    public function history()
    {
        $orders = Order::where('user_id', Auth::id())->latest()->get();
        
        return view('order-history', compact('orders'));
    }
    public function callback(Request $request)
    {
        $serverKey = config('midtrans.server_key');
        
        $hashed = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $serverKey);
        
        if ($hashed == $request->signature_key) {
            $order = Order::with('details')->find($request->order_id);
            
            if ($order) {
                if ($request->transaction_status == 'capture' || $request->transaction_status == 'settlement') {
                    $order->update([
                        'status' => 'Diproses',
                        'bukti_pembayaran' => 'midtrans_verified'
                    ]);
                } 
                elseif ($request->transaction_status == 'cancel' || $request->transaction_status == 'deny' || $request->transaction_status == 'expire') {
                    
                    if ($order->status !== 'Dibatalkan') {
                        foreach ($order->details as $detail) {
                            $produk = \App\Models\Product::find($detail->product_id);
                            if ($produk) {
                                $produk->stok += $detail->jumlah; 
                                $produk->save();
                            }
                        }
                    }
                    $order->update(['status' => 'Dibatalkan']);
                }
            }
        }
        
        return response()->json(['message' => 'Callback received']);
    }
}