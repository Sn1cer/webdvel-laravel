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
    // Menampilkan halaman Checkout
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
        $carts = Cart::with('product')->where('user_id', Auth::id())->get();
        if ($carts->count() == 0) {
            return redirect('/');
        }

        $totalHarga = 0;
        foreach ($carts as $cart) {
            $totalHarga += $cart->product->harga * $cart->jumlah;
        }

        
        $order = Order::create([
            'user_id' => Auth::id(),
            'nama_depan' => $request->nama_depan,
            'nama_belakang' => $request->nama_belakang,
            'alamat_jalan' => $request->alamat_jalan,
            'wilayah' => $request->wilayah,
            'no_hp' => $request->no_hp,
            'alamat_lengkap' => $request->alamat_lengkap,
            'total_harga' => $totalHarga,
            'status' => 'Belum Bayar'
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

        // Arahkan ke halaman instruksi pembayaran
        return redirect()->route('checkout.success', $order->id);
    }

    // Menampilkan halaman Instruksi Pembayaran (Struk)
    public function success($id)
    {
        $order = Order::where('user_id', Auth::id())->findOrFail($id);
        return view('checkout-success', compact('order'));
    }

    // Memproses unggahan foto bukti transfer
    public function uploadBukti(Request $request, $id)
    {
        $request->validate([
            'bukti_pembayaran' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $order = Order::where('user_id', Auth::id())->findOrFail($id);

        if ($request->hasFile('bukti_pembayaran')) {
            $file = $request->file('bukti_pembayaran');
            $nama_file = time() . "_" . $file->getClientOriginalName();
            
            $file->move(public_path('images/bukti'), $nama_file);

            $order->update([
                'bukti_pembayaran' => $nama_file,
                'status' => 'Diproses'
            ]);
        }

        return redirect()->back()->with('success', 'Bukti pembayaran berhasil diunggah! Pesanan Anda segera kami proses.');
    }
    // Menampilkan riwayat pesanan khusus untuk pelanggan yang login
    public function history()
    {
        // Ambil pesanan milik user ini saja, urutkan dari yang paling baru
        $orders = Order::where('user_id', Auth::id())->latest()->get();
        
        return view('order-history', compact('orders'));
    }
}