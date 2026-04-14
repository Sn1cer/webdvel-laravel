<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        // Ambil semua data keranjang milik user yang sedang login
        $carts = Cart::with('product')->where('user_id', Auth::id())->get();
        
        //total harga
        $totalHarga = 0;
        foreach ($carts as $cart) {
            $totalHarga += $cart->product->harga * $cart->jumlah;
        }

        // tampilan halaman keranjang dan data keranjang
        return view('cart', compact('carts', 'totalHarga'));
    }
    public function store(Request $request)
    {
        // Validasi
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'ukuran' => 'required',
            'jumlah' => 'required|integer|min:1'
        ]);

        // valadasi ukuran sesuai atau tidak
        $cart = Cart::where('user_id', Auth::id())
                    ->where('product_id', $request->product_id)
                    ->where('ukuran', $request->ukuran)
                    ->first();
        // pengecekan ukuran
        if ($cart) {
            $cart->jumlah += $request->jumlah;
            $cart->save();
        } else {
            // Jika BELUM ADA, buat data pesanan baru di keranjang
            Cart::create([
                'user_id' => Auth::id(),
                'product_id' => $request->product_id,
                'ukuran' => $request->ukuran,
                'jumlah' => $request->jumlah,
            ]);
        }

        return redirect()->back()->with('success', 'Yey! Produk berhasil ditambahkan ke keranjang Anda 🛒');
    }
    
    public function update(Request $request, Cart $cart)
    {
        // untuk orang yang sudah login
        if ($cart->user_id !== Auth::id()) {
            return abort(403);
        }

        $request->validate([
            'jumlah' => 'required|integer|min:1',
            'ukuran' => 'required'
        ]);

        $cart->update([
            'jumlah' => $request->jumlah,
            'ukuran' => $request->ukuran
        ]);

        return redirect()->back()->with('success', 'Keranjang berhasil diperbarui!');
    }

    // Fungsi untuk menghapus barang dari keranjang
    public function destroy(Cart $cart)
    {
        if ($cart->user_id !== Auth::id()) {
            return abort(403);
        }

        $cart->delete();
        return redirect()->back()->with('success', 'Produk berhasil dihapus dari keranjang.');
    }
}