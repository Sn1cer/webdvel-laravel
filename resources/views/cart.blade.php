<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja - D'Vel Jeans</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=DM+Serif+Display&display=swap" rel="stylesheet">
    <style>
        /* CSS Dasar */
        :root { --accent: #d97706; --text: #1e293b; --bg: #f8fafc; --border: #e2e8f0; --red: #ef4444; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg); color: var(--text); margin: 0; padding: 0; }
        .navbar { background: white; padding: 20px 50px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 10px rgba(0,0,0,0.05); position: sticky; top: 0; z-index: 100; }
        .logo { font-family: 'DM Serif Display', serif; font-size: 28px; color: var(--text); text-decoration: none; }
        .nav-links a { margin-left: 24px; text-decoration: none; color: var(--text); font-weight: 600; transition: color 0.2s; }
        .nav-links a:hover { color: var(--accent); }
        .btn-login { border: 2px solid var(--accent); padding: 8px 20px; border-radius: 20px; color: var(--accent) !important; display: inline-block;}

        /* Layout Keranjang */
        .container { max-width: 1200px; margin: 40px auto; padding: 0 20px; display: flex; gap: 30px; align-items: flex-start;}
        .cart-items { flex: 1; }
        .cart-summary { width: 340px; background: white; padding: 25px; border-radius: 12px; border: 1px solid var(--border); box-shadow: 0 4px 10px rgba(0,0,0,0.02); position: sticky; top: 100px;}

        /* Item Keranjang */
        .page-title { font-size: 28px; font-weight: 800; margin-bottom: 24px; }
        .item-card { background: white; padding: 20px; border-radius: 12px; border: 1px solid var(--border); margin-bottom: 15px; display: flex; gap: 20px; align-items: flex-start;}
        .item-img { width: 100px; height: 100px; border-radius: 8px; object-fit: cover; background: #f1f5f9; display: flex; align-items: center; justify-content: center; font-size: 30px;}
        .item-details { flex: 1; }
        .item-title { font-size: 18px; font-weight: 700; margin-bottom: 5px; }
        .item-price { font-size: 16px; font-weight: 800; color: var(--accent); margin-bottom: 15px;}
        
        /* Elemen Interaktif (Dropdown & Input) */
        .interactive-form { display: flex; gap: 10px; align-items: center; flex-wrap: wrap;}
        .form-select, .form-input { padding: 8px 12px; border: 1px solid var(--border); border-radius: 6px; font-family: inherit; font-size: 14px; outline: none; transition: 0.2s; background: white;}
        .form-select:focus, .form-input:focus { border-color: var(--accent); }
        .form-input { width: 70px; text-align: center; }

        /* Tombol Hapus */
        .btn-delete { background: none; border: none; color: var(--red); font-size: 14px; font-weight: 600; cursor: pointer; padding: 8px 12px; border-radius: 6px; transition: 0.2s; display: flex; align-items: center; gap: 5px;}
        .btn-delete:hover { background: #fee2e2; }

        /* Ringkasan Belanja */
        .summary-title { font-size: 18px; font-weight: 800; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid var(--border);}
        .summary-row { display: flex; justify-content: space-between; margin-bottom: 15px; font-size: 15px; color: #475569;}
        .summary-total { display: flex; justify-content: space-between; margin-top: 20px; padding-top: 20px; border-top: 1px dashed var(--border); font-size: 18px; font-weight: 800; color: var(--text);}
        
        /* TOMBOL CHECKOUT MINIMALIS */
        .btn-checkout { display: block; width: 100%; text-align: center; background: var(--text); color: white; padding: 12px; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 14px; margin-top: 25px; transition: 0.2s; letter-spacing: 0.5px;}
        .btn-checkout:hover { background: var(--accent); transform: translateY(-2px);}

        /* Tampilan Kosong */
        .empty-cart { text-align: center; padding: 60px 20px; background: white; border-radius: 12px; border: 1px dashed var(--border); }
        .btn-shop { background: var(--text); color: white; padding: 10px 24px; text-decoration: none; border-radius: 20px; font-weight: 600; display: inline-block; margin-top: 15px;}
        .alert { background: #dcfce3; color: #166534; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-weight: 600; border: 1px solid #bbf7d0;}
    </style>
</head>
<body>

    <nav class="navbar">
        <a href="/" class="logo">D'Vel Jeans</a>
        <div class="nav-links" style="display: flex; align-items: center;">
            <a href="/">Beranda</a>
            <a href="/#katalog">Katalog Produk</a>

            @auth
                <a href="{{ route('cart.index') }}" style="position: relative; margin-left: 24px; margin-right: 10px; color: var(--text); font-size: 22px; text-decoration: none;">
                    🛒
                    @php $cartCount = \App\Models\Cart::where('user_id', Auth::id())->sum('jumlah'); @endphp
                    @if($cartCount > 0)
                        <span style="position: absolute; top: -8px; right: -12px; background: #ef4444; color: white; font-size: 11px; font-weight: 800; padding: 2px 6px; border-radius: 50%; border: 2px solid white;">{{ $cartCount }}</span>
                    @endif
                </a>

                @if(Auth::user()->email === 'admin@dveljeans.com')
                    <a href="{{ route('products.index') }}" class="btn-login" style="background: var(--accent); color: white !important;">Panel Admin</a>
                @else
                    <span style="margin-left: 24px; font-weight: 700; color: var(--text);">Halo, {{ Auth::user()->name }}</span>
                @endif
                <form method="POST" action="{{ route('logout') }}" style="margin: 0; padding: 0;">
                    @csrf
                    <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();" class="btn-login" style="margin-left: 16px; border-color: #ef4444; color: #ef4444 !important;">Keluar</a>
                </form>
            @endauth
        </div>
    </nav>

    <div class="container">
        <div class="cart-items">
            <h1 class="page-title">Keranjang Belanja</h1>

            @if(session('success'))
                <div class="alert">{{ session('success') }}</div>
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
                                
                                <select name="ukuran" class="form-select" onchange="this.form.submit()">
                                    <option value="27" {{ $cart->ukuran == '27' ? 'selected' : '' }}>Ukuran: 27</option>
                                    <option value="28" {{ $cart->ukuran == '28' ? 'selected' : '' }}>Ukuran: 28</option>
                                    <option value="30" {{ $cart->ukuran == '30' ? 'selected' : '' }}>Ukuran: 30</option>
                                    <option value="32" {{ $cart->ukuran == '32' ? 'selected' : '' }}>Ukuran: 32</option>
                                    <option value="34" {{ $cart->ukuran == '34' ? 'selected' : '' }}>Ukuran: 34</option>
                                </select>

                                <input type="number" name="jumlah" value="{{ $cart->jumlah }}" min="1" max="10" class="form-input" onchange="this.form.submit()">
                            </form>
                        </div>

                        <form action="{{ route('cart.destroy', $cart->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-delete" onclick="return confirm('Yakin ingin menghapus produk ini dari keranjang?')">
                                🗑️ Hapus
                            </button>
                        </form>
                    </div>
                @endforeach
            @else
                <div class="empty-cart">
                    <span style="font-size: 60px;">🛒</span>
                    <h3>Keranjang Anda Masih Kosong</h3>
                    <p>Yuk, temukan celana jeans impianmu sekarang!</p>
                    <a href="/#katalog" class="btn-shop">Mulai Belanja</a>
                </div>
            @endif
        </div>

        @if(count($carts) > 0)
        <div class="cart-summary">
            <h2 class="summary-title">Ringkasan Belanja</h2>
            <div class="summary-row">
                <span>Total Barang</span>
                <span>{{ $cartCount }} Produk</span>
            </div>
            
            <div class="summary-total">
                <span>Total Harga</span>
                <span>Rp {{ number_format($totalHarga, 0, ',', '.') }}</span>
            </div>

            <a href="{{ route('checkout.index') }}" class="btn-checkout">Lanjut ke Checkout</a>
        </div>
        @endif
    </div>

</body>
</html>