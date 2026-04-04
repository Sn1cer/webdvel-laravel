@extends('layouts.app')

@section('title', "Keranjang Belanja - D'Vel Jeans")

@push('styles')
<style>
    /* --- CSS KHUSUS KERANJANG BELANJA --- */
    
    /* Layout Keranjang */
    .container-cart { max-width: 1200px; margin: 40px auto; padding: 0 20px; display: flex; gap: 30px; align-items: flex-start;}
    .cart-items { flex: 1; }
    .cart-summary { width: 340px; background: white; padding: 25px; border-radius: 12px; border: 1px solid var(--border); box-shadow: 0 4px 10px rgba(0,0,0,0.02); position: sticky; top: 100px; box-sizing: border-box;}

    /* Item Keranjang */
    .page-title { font-size: 28px; font-weight: 800; margin-bottom: 24px; font-family: 'DM Serif Display', serif;}
    .item-card { background: white; padding: 20px; border-radius: 12px; border: 1px solid var(--border); margin-bottom: 15px; display: flex; gap: 20px; align-items: flex-start; position: relative;}
    .item-img { width: 100px; height: 100px; border-radius: 8px; object-fit: cover; background: #f1f5f9; display: flex; align-items: center; justify-content: center; font-size: 30px; flex-shrink: 0;}
    .item-details { flex: 1; }
    .item-title { font-size: 18px; font-weight: 700; margin-bottom: 5px; color: var(--text); padding-right: 30px;}
    .item-price { font-size: 16px; font-weight: 800; color: var(--accent); margin-bottom: 15px;}
    
    /* Elemen Interaktif (Dropdown & Input) */
    .interactive-form { display: flex; gap: 10px; align-items: center; flex-wrap: wrap;}
    .form-select, .form-input { padding: 8px 12px; border: 1px solid var(--border); border-radius: 6px; font-family: inherit; font-size: 14px; outline: none; transition: 0.2s; background: white; color: var(--text);}
    .form-select:focus, .form-input:focus { border-color: var(--accent); }
    .form-input { width: 70px; text-align: center; }

    /* Tombol Hapus */
    .btn-delete { background: none; border: none; color: var(--red); font-size: 14px; font-weight: 600; cursor: pointer; padding: 8px 12px; border-radius: 6px; transition: 0.2s; display: flex; align-items: center; gap: 5px;}
    .btn-delete:hover { background: #fee2e2; }

    /* Ringkasan Belanja */
    .summary-title { font-size: 18px; font-weight: 800; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid var(--border); color: var(--text);}
    .summary-row { display: flex; justify-content: space-between; margin-bottom: 15px; font-size: 15px; color: #475569;}
    .summary-total { display: flex; justify-content: space-between; margin-top: 20px; padding-top: 20px; border-top: 1px dashed var(--border); font-size: 18px; font-weight: 800; color: var(--text);}
    
    /* TOMBOL CHECKOUT MINIMALIS */
    .btn-checkout { display: block; width: 100%; text-align: center; background: var(--text); color: white; padding: 12px; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 14px; margin-top: 25px; transition: 0.2s; letter-spacing: 0.5px; box-sizing: border-box;}
    .btn-checkout:hover { background: var(--accent); transform: translateY(-2px);}

    /* Tampilan Kosong */
    .empty-cart { text-align: center; padding: 60px 20px; background: white; border-radius: 12px; border: 1px dashed var(--border); }
    .btn-shop { background: var(--text); color: white; padding: 10px 24px; text-decoration: none; border-radius: 20px; font-weight: 600; display: inline-block; margin-top: 15px;}
    .btn-shop:hover { background: var(--accent); }
    .alert-success { background: #dcfce3; color: #166534; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-weight: 600; border: 1px solid #bbf7d0;}

    /* --- RESPONSIVE MOBILE --- */
    @media (max-width: 768px) {
        .container-cart { 
            flex-direction: column; /* Mengubah layout kiri-kanan menjadi atas-bawah */
            gap: 20px; 
            margin: 20px auto; 
        }
        .cart-summary { 
            width: 100%; /* Lebar penuh di HP */
            position: static; /* Menghilangkan efek sticky di layar kecil */
        }
        .item-card { 
            gap: 15px; 
        }
        .item-img { 
            width: 80px; 
            height: 80px; 
            font-size: 24px; 
        }
        .btn-delete {
            position: absolute;
            top: 15px;
            right: 15px;
            padding: 5px;
            background: #f8fafc;
        }
        .btn-delete span { display: none; } /* Hanya menampilkan icon tong sampah di HP */
    }
</style>
@endpush

@section('content')
    <div class="container-cart">
        <div class="cart-items">
            <h1 class="page-title">Keranjang Belanja</h1>

            @if(session('success'))
                <div class="alert-success">✅ {{ session('success') }}</div>
            @endif

            @if(count($carts) > 0)
                @foreach($carts as $cart)
                    <div class="item-card">
                        @if($cart->product->gambar)
                            <img src="{{ asset('images/'.$cart->product->gambar) }}" alt="{{ $cart->product->nama_produk }}" class="item-img">
                        @else
                            <div class="item-img">👖</div>
                        @endif
                        
                        <div class="item-details">
                            <div class="item-title">{{ $cart->product->nama_produk }}</div>
                            <div class="item-price">Rp {{ number_format($cart->product->harga, 0, ',', '.') }}</div>
                            
                            <form action="{{ route('cart.update', $cart->id) }}" method="POST" class="interactive-form">
                                @csrf
                                @method('PATCH')
                                
                                @php
                                    // Membaca ukuran dari database produk secara dinamis
                                    $ukuranTersedia = $cart->product->ukuran ? explode(',', $cart->product->ukuran) : ['27', '28', '30', '32', '34'];
                                @endphp

                                <select name="ukuran" class="form-select" onchange="this.form.submit()">
                                    @foreach($ukuranTersedia as $uk)
                                        <option value="{{ trim($uk) }}" {{ $cart->ukuran == trim($uk) ? 'selected' : '' }}>
                                            Ukuran: {{ trim($uk) }}
                                        </option>
                                    @endforeach
                                </select>

                                <input type="number" name="jumlah" value="{{ $cart->jumlah }}" min="1" max="{{ $cart->product->stok }}" class="form-input" onchange="this.form.submit()">
                            </form>
                        </div>

                        <form action="{{ route('cart.destroy', $cart->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-delete" onclick="return confirm('Yakin ingin menghapus produk ini dari keranjang?')" title="Hapus Barang">
                                🗑️ <span>Hapus</span>
                            </button>
                        </form>
                    </div>
                @endforeach
            @else
                <div class="empty-cart">
                    <span style="font-size: 60px;">🛒</span>
                    <h3 style="margin-top: 15px; color: var(--text);">Keranjang Anda Masih Kosong</h3>
                    <p style="color: #64748b;">Yuk, temukan celana jeans impianmu sekarang!</p>
                    <a href="{{ route('katalog') }}" class="btn-shop">Mulai Belanja</a>
                </div>
            @endif
        </div>

        @if(count($carts) > 0)
        <div class="cart-summary">
            <h2 class="summary-title">Ringkasan Belanja</h2>
            <div class="summary-row">
                <span>Total Barang</span>
                <span style="font-weight: 700;">{{ $carts->sum('jumlah') }} Produk</span>
            </div>
            
            <div class="summary-total">
                <span>Total Harga</span>
                <span>Rp {{ number_format($totalHarga, 0, ',', '.') }}</span>
            </div>

            <a href="{{ route('checkout.index') }}" class="btn-checkout">Lanjut ke Checkout</a>
        </div>
        @endif
    </div>
@endsection