<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\ProductSize; // Tambahkan pemanggilan model ProductSize
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $carts = Cart::with('product')->where('user_id', Auth::id())->get();
        $totalHarga = 0;
        foreach ($carts as $cart) {
            $totalHarga += $cart->product->harga * $cart->jumlah;
        }

        return view('cart', compact('carts', 'totalHarga'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'ukuran' => 'required',
            'jumlah' => 'required|integer|min:1'
        ]);

        $ukuranTersedia = ProductSize::where('product_id', $request->product_id)
                                     ->where('ukuran', $request->ukuran)
                                     ->first();

        if (!$ukuranTersedia || $ukuranTersedia->stok < $request->jumlah) {
            $sisa = $ukuranTersedia ? $ukuranTersedia->stok : 0;
            return redirect()->back()->with('error', 'Maaf, stok untuk ukuran ' . $request->ukuran . ' tidak mencukupi. Sisa stok: ' . $sisa . ' pcs.');
        }

        $cart = Cart::where('user_id', Auth::id())
                    ->where('product_id', $request->product_id)
                    ->where('ukuran', $request->ukuran)
                    ->first();

        if ($cart) {
            if ($ukuranTersedia->stok < ($cart->jumlah + $request->jumlah)) {
                return redirect()->back()->with('error', 'Maaf, total pesanan Anda melebihi sisa stok di gudang (' . $ukuranTersedia->stok . ' pcs).');
            }
            
            $cart->jumlah += $request->jumlah;
            $cart->save();
        } else {
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
        if ($cart->user_id !== Auth::id()) {
            return abort(403);
        }

        $request->validate([
            'jumlah' => 'required|integer|min:1',
            'ukuran' => 'required'
        ]);

        $ukuranTersedia = ProductSize::where('product_id', $cart->product_id)
                                     ->where('ukuran', $request->ukuran)
                                     ->first();

        if (!$ukuranTersedia || $ukuranTersedia->stok < $request->jumlah) {
            $sisa = $ukuranTersedia ? $ukuranTersedia->stok : 0;
            return redirect()->back()->with('error', 'Maaf, stok untuk ukuran ' . $request->ukuran . ' sudah habis atau tidak mencukupi. Sisa stok saat ini: ' . $sisa . ' pcs.');
        }

        $cart->update([
            'jumlah' => $request->jumlah,
            'ukuran' => $request->ukuran
        ]);

        return redirect()->back()->with('success', 'Keranjang berhasil diperbarui!');
    }

    public function destroy(Cart $cart)
    {
        if ($cart->user_id !== Auth::id()) {
            return abort(403);
        }

        $cart->delete();
        return redirect()->back()->with('success', 'Produk berhasil dihapus dari keranjang.');
    }
}