<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product; 
use App\Models\ProductSize; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class OrderController extends Controller
{
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

    public function store(Request $request)
    {
        $rules = [
            'nama_depan' => 'required|string|max:255',
            'nama_belakang' => 'required|string|max:255',
            'no_hp' => 'required|string|max:20',
            'tipe_pesanan' => 'required|in:Online,Booking',
        ];

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

        $ongkir = $request->has('ongkir') ? (int)$request->ongkir : 0;
        $totalTagihan = $totalHarga + $ongkir;

        $alamatJalan = $request->tipe_pesanan === 'Booking' ? 'Ambil di Toko' : $request->alamat_jalan;
        $wilayah = $request->tipe_pesanan === 'Booking' ? 'Ambil di Toko' : $request->wilayah;
        $alamatLengkap = $request->tipe_pesanan === 'Booking' ? 'Pesanan Booking - Ambil di Toko D Vel Jeans' : $request->alamat_lengkap;

        $prefix = $request->tipe_pesanan === 'Booking' ? 'BKG-' : 'ONL-';
        $kodePesanan = $prefix . strtoupper(uniqid());

        $order = Order::create([
            'user_id' => Auth::id(),
            'nama_depan' => $request->nama_depan,
            'nama_belakang' => $request->nama_belakang,
            'alamat_jalan' => $alamatJalan,
            'wilayah' => $wilayah,
            'no_hp' => $request->no_hp,
            'alamat_lengkap' => $alamatLengkap,
            'total_harga' => $totalTagihan, 
            'ongkir' => $ongkir, 
            'status' => 'Belum Bayar',
            'tipe_pesanan' => $request->tipe_pesanan,
            'resi' => $kodePesanan 
        ]);

        foreach ($carts as $cart) {
            OrderDetail::create([
                'order_id' => $order->id, 
                'product_id' => $cart->product_id,
                'ukuran' => $cart->ukuran,
                'jumlah' => $cart->jumlah,
                'harga_satuan' => $cart->product->harga
            ]);

            $produk = Product::find($cart->product_id);
            if ($produk) {
                $produk->stok -= $cart->jumlah;
                $produk->save();
            }

            $productSize = ProductSize::where('product_id', $cart->product_id)
                            ->where('ukuran', $cart->ukuran)
                            ->first();
            if ($productSize) {
                $productSize->stok -= $cart->jumlah;
                $productSize->save();
            }
        }

        Cart::where('user_id', Auth::id())->delete();

        if ($request->tipe_pesanan === 'Booking') {
            return redirect()->route('booking.success', $order->id);
        }

        return redirect()->route('checkout.success', $order->id);
    }

    public function success($id)
    {
        $order = Order::with('details.product')->where('user_id', Auth::id())->findOrFail($id);

        if ($order->tipe_pesanan == 'Online') {
            \Midtrans\Config::$serverKey = config('midtrans.server_key');
            \Midtrans\Config::$isProduction = config('midtrans.is_production');
            \Midtrans\Config::$isSanitized = config('midtrans.is_sanitized');
            \Midtrans\Config::$is3ds = config('midtrans.is_3ds');

            if ($order->status == 'Belum Bayar' && $order->snap_token) {
                $isExpired = false;
                
                try {
                    $statusResponse = \Midtrans\Transaction::status($order->id . '-' . $order->created_at->timestamp);
                    
                    if (in_array($statusResponse->transaction_status, ['capture', 'settlement'])) {
                        $order->update(['status' => 'Diproses', 'bukti_pembayaran' => 'midtrans_verified']);
                        $order->refresh();
                    } 
                    elseif (in_array($statusResponse->transaction_status, ['expire', 'cancel', 'deny'])) {
                        $isExpired = true;
                    } 
                    elseif ($statusResponse->transaction_status == 'pending') {
                        if (Carbon::now()->greaterThanOrEqualTo(Carbon::parse($order->created_at)->addHours(24))) {
                            $isExpired = true;
                        }
                    }
                } catch (\Exception $e) { 
                    if (Carbon::now()->greaterThanOrEqualTo(Carbon::parse($order->created_at)->addHours(24))) {
                        $isExpired = true;
                    }
                }

                if ($isExpired) {
                    $this->batalkanDanKembalikanStok($order);
                    $order->refresh();
                }
            }

            if ($order->status == 'Belum Bayar' && empty($order->snap_token)) {
                $params = [
                    'transaction_details' => [
                        'order_id' => $order->id . '-' . $order->created_at->timestamp,
                        'gross_amount' => $order->total_harga, 
                    ],
                    'customer_details' => [
                        'first_name' => $order->nama_depan,
                        'last_name' => $order->nama_belakang,
                        'phone' => $order->no_hp,
                    ],
                    'expiry' => [
                        'start_time' => $order->created_at->format("Y-m-d H:i:s O"),
                        'unit' => 'hours',
                        'duration' => 24
                    ],
                ];

                $snapToken = \Midtrans\Snap::getSnapToken($params);
                $order->snap_token = $snapToken;
                $order->save();
            }
        }

        return view('checkout-success', compact('order'));
    }

    public function bookingSuccess($id)
    {
        $order = Order::with('details.product')->where('user_id', Auth::id())->findOrFail($id);
        return view('booking-success', compact('order'));
    }
    
    public function history()
    {
        $expiredBookings = Order::with('details')
            ->where('user_id', Auth::id())
            ->where('tipe_pesanan', 'Booking')
            ->where('status', 'Belum Bayar')
            ->where('created_at', '<', Carbon::now()->subHours(24))
            ->get();

        foreach ($expiredBookings as $expiredBooking) {
            $this->batalkanDanKembalikanStok($expiredBooking);
        }

        $orders = Order::where('user_id', Auth::id())->latest()->get();
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production');

        foreach($orders as $order) {
            if ($order->tipe_pesanan == 'Online' && $order->status == 'Belum Bayar' && $order->snap_token) {
                $isExpired = false;

                try {
                    $statusResponse = \Midtrans\Transaction::status($order->id . '-' . $order->created_at->timestamp);
                    
                    if (in_array($statusResponse->transaction_status, ['capture', 'settlement'])) {
                        $order->update(['status' => 'Diproses', 'bukti_pembayaran' => 'midtrans_verified']);
                    } 
                    elseif (in_array($statusResponse->transaction_status, ['expire', 'cancel', 'deny'])) {
                        $isExpired = true;
                    } 
                    elseif ($statusResponse->transaction_status == 'pending') {
                        if (Carbon::now()->greaterThanOrEqualTo(Carbon::parse($order->created_at)->addHours(24))) {
                            $isExpired = true;
                        }
                    }
                } catch (\Exception $e) { 
                    if (Carbon::now()->greaterThanOrEqualTo(Carbon::parse($order->created_at)->addHours(24))) {
                        $isExpired = true;
                    }
                }

                if ($isExpired) {
                    $this->batalkanDanKembalikanStok($order);
                }
            }
        }

        $orders = Order::where('user_id', Auth::id())->latest()->get();
        return view('order-history', compact('orders'));
    }

    public function callback(Request $request)
    {
        $serverKey = config('midtrans.server_key');
        $hashed = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $serverKey);
        
        if ($hashed == $request->signature_key) {
            $realOrderId = explode('-', $request->order_id)[0];
            $order = Order::with('details')->find($realOrderId);
            
            if ($order) {
                if ($request->transaction_status == 'capture' || $request->transaction_status == 'settlement') {
                    if ($order->status == 'Belum Bayar') {
                        $order->update(['status' => 'Diproses', 'bukti_pembayaran' => 'midtrans_verified']);
                    }
                } 
                elseif (in_array($request->transaction_status, ['cancel', 'deny', 'expire'])) {
                    $this->batalkanDanKembalikanStok($order);
                }
            }
            return response()->json(['message' => 'Callback received']);
        }

        return response()->json(['message' => 'Invalid signature'], 403);
    }

    private function batalkanDanKembalikanStok($order)
    {
        if ($order->status != 'Dibatalkan') {
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
            $order->update(['status' => 'Dibatalkan']);
        }
    }
}