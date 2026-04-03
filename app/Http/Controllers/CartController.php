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
        // 'with('product')' digunakan untuk memanggil relasi data celananya sekaligus
        $carts = Cart::with('product')->where('user_id', Auth::id())->get();
        
        // Hitung Total Harga Belanja
        $totalHarga = 0;
        foreach ($carts as $cart) {
            $totalHarga += $cart->product->harga * $cart->jumlah;
        }

        // Tampilkan halaman keranjang sambil membawa data tersebut
        return view('cart', compact('carts', 'totalHarga'));
    }
    public function store(Request $request)
    {
        // Validasi: Pastikan data yang dikirim dari form lengkap dan benar
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'ukuran' => 'required',
            'jumlah' => 'required|integer|min:1'
        ]);

        // Cek apakah celana dengan ukuran tersebut sudah ada di keranjang pelanggan ini
        $cart = Cart::where('user_id', Auth::id())
                    ->where('product_id', $request->product_id)
                    ->where('ukuran', $request->ukuran)
                    ->first();

        if ($cart) {
            // Jika SUDAH ADA, cukup tambahkan jumlah kuantitasnya
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

        // 3. Kembalikan pelanggan ke halaman sebelumnya dengan pesan sukses!
        return redirect()->back()->with('success', 'Yey! Produk berhasil ditambahkan ke keranjang Anda 🛒');
    }
    // Fungsi untuk mengubah jumlah atau ukuran
    public function update(Request $request, Cart $cart)
    {
        // Pastikan keranjang ini benar-benar milik orang yang sedang login
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