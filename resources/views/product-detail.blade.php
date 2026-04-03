<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $product->nama_produk }} - D'Vel Jeans</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=DM+Serif+Display&display=swap" rel="stylesheet">
    <style>
        /* CSS Dasar & Navigasi (Sama dengan Halaman Utama) */
        :root { --accent: #d97706; --text: #1e293b; --bg: #f8fafc; --border: #e2e8f0; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg); color: var(--text); margin: 0; padding: 0; }
        .navbar { background: white; padding: 20px 50px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 10px rgba(0,0,0,0.05); position: sticky; top: 0; z-index: 100; }
        .logo { font-family: 'DM Serif Display', serif; font-size: 28px; color: var(--text); text-decoration: none; }
        .nav-links a { margin-left: 24px; text-decoration: none; color: var(--text); font-weight: 600; transition: color 0.2s; }
        .nav-links a:hover { color: var(--accent); }
        .btn-login { border: 2px solid var(--accent); padding: 8px 20px; border-radius: 20px; color: var(--accent) !important; display: inline-block;}
        .btn-login:hover { background: var(--accent); color: white !important; }

        /* Layout Detail Produk */
        .container { max-width: 1200px; margin: 40px auto; padding: 0 20px; display: grid; grid-template-columns: 1fr 1fr; gap: 50px; }
        
        /* Bagian Kiri: Gambar */
        .product-image-box { background: white; border-radius: 16px; padding: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); border: 1px solid var(--border); }
        .product-image { width: 100%; height: 500px; object-fit: cover; border-radius: 12px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; font-size: 80px;}
        
        /* Bagian Kanan: Info & Form */
        .product-info { padding: 10px 0; }
        .breadcrumb { font-size: 14px; color: #64748b; margin-bottom: 15px; }
        .breadcrumb a { color: var(--accent); text-decoration: none; }
        .product-title { font-size: 36px; font-weight: 800; margin-bottom: 15px; line-height: 1.2; font-family: 'DM Serif Display', serif;}
        .product-price { font-size: 28px; font-weight: 700; color: var(--accent); margin-bottom: 25px; }
        
        .product-description { font-size: 15px; line-height: 1.7; color: #475569; margin-bottom: 30px; padding-bottom: 30px; border-bottom: 1px solid var(--border); white-space: pre-line;}

        /* Pilihan Ukuran (Tombol Radio yang Dimodifikasi) */
        .section-label { font-size: 14px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 12px; display: block; color: var(--text); }
        .size-selector { display: flex; gap: 10px; margin-bottom: 30px; flex-wrap: wrap;}
        .size-selector input[type="radio"] { display: none; }
        .size-selector label { display: inline-block; padding: 12px 20px; border: 2px solid var(--border); border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.2s; background: white;}
        .size-selector input[type="radio"]:checked + label { border-color: var(--accent); background: var(--accent); color: white; }

        /* Kuantitas & Tombol Beli */
        .action-area { display: flex; gap: 15px; margin-top: 30px; }
        .qty-input { width: 80px; padding: 15px; border: 2px solid var(--border); border-radius: 8px; font-size: 16px; font-weight: 600; text-align: center; outline: none; transition: border 0.2s;}
        .qty-input:focus { border-color: var(--accent); }
        
        .btn-add-cart { flex: 1; background: var(--text); color: white; border: none; padding: 15px; border-radius: 8px; font-size: 16px; font-weight: 700; cursor: pointer; transition: background 0.2s; text-transform: uppercase; letter-spacing: 1px;}
        .btn-add-cart:hover { background: var(--accent); }

    </style>
</head>
<body>

    <nav class="navbar">
    <a href="/" class="logo">D'Vel Jeans</a>
    <div class="nav-links" style="display: flex; align-items: center;">
        <a href="/">Beranda</a>
        <a href="/#katalog">Katalog Produk</a>

        @guest
            <a href="{{ route('login') }}" class="btn-login">Masuk</a>
        @endguest

        @auth
            <a href="{{ route('cart.index') }}" style="position: relative; margin-left: 24px; margin-right: 10px; color: var(--text); font-size: 22px; text-decoration: none; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">
                🛒
                @php 
                    $cartCount = \App\Models\Cart::where('user_id', Auth::id())->sum('jumlah'); 
                @endphp
                
                @if($cartCount > 0)
                    <span style="position: absolute; top: -8px; right: -12px; background: #ef4444; color: white; font-size: 11px; font-weight: 800; padding: 2px 6px; border-radius: 50%; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                        {{ $cartCount }}
                    </span>
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
        
        <div class="product-image-box">
            @if($product->gambar)
                <img src="{{ asset('images/'.$product->gambar) }}" alt="{{ $product->nama_produk }}" class="product-image">
            @else
                <div class="product-image">👖</div>
            @endif
        </div>

        <div class="product-info">
            <div class="breadcrumb">
                <a href="/">Beranda</a> / Katalog / {{ $product->nama_produk }}
            </div>
            @if(session('success'))
             <div style="background: #dcfce3; color: #166534; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-weight: 600; border: 1px solid #bbf7d0;">
                 {{ session('success') }}
             </div>
         @endif

         <h1 class="product-title">{{ $product->nama_produk }}</h1>
            <h1 class="product-title">{{ $product->nama_produk }}</h1>
            <div class="product-price">Rp {{ number_format($product->harga, 0, ',', '.') }}</div>
            
            <div class="product-description">{{ $product->deskripsi ?? 'Belum ada deskripsi untuk produk ini. Terbuat dari bahan denim premium dengan kualitas jahitan terbaik khas D\'Vel Jeans.' }}</div>

            <form action="{{ route('cart.store') }}" method="POST">
                @if($product->stok > 0)
                
                <div style="margin-bottom: 20px; font-weight: 700; color: #16a34a;">
                    Sisa Stok: {{ $product->stok }} Pcs
                </div>

                <form action="{{ route('cart.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">

                    <label class="section-label">Pilih Ukuran (Size)</label>
                    <div class="size-selector">
                        <input type="radio" id="size27" name="ukuran" value="27" required>
                        <label for="size27">27</label>
                        <input type="radio" id="size28" name="ukuran" value="28">
                        <label for="size28">28</label>
                        <input type="radio" id="size30" name="ukuran" value="30">
                        <label for="size30">30</label>
                        <input type="radio" id="size32" name="ukuran" value="32">
                        <label for="size32">32</label>
                        <input type="radio" id="size34" name="ukuran" value="34">
                        <label for="size34">34</label>
                    </div>

                    <label class="section-label">Jumlah</label>
                    <div class="action-area">
                        <input type="number" name="jumlah" class="qty-input" value="1" min="1" max="{{ $product->stok }}" required>
                        <button type="submit" class="btn-add-cart">Masukkan Keranjang 🛒</button>
                    </div>
                </form>

            @else
                <div style="margin-top: 30px; padding: 20px; background: #fee2e2; border: 1px dashed #ef4444; border-radius: 8px; text-align: center;">
                    <h3 style="color: #b91c1c; margin: 0 0 10px 0;">Mohon Maaf, Stok Habis! 😭</h3>
                    <p style="color: #7f1d1d; margin: 0; font-size: 14px;">Celana ini sedang diproduksi ulang. Silakan cek koleksi kami yang lain.</p>
                </div>
                <button disabled style="width: 100%; background: #cbd5e1; color: white; border: none; padding: 15px; border-radius: 8px; font-size: 16px; font-weight: 700; margin-top: 15px; cursor: not-allowed; text-transform: uppercase;">
                    Stok Habis
                </button>
            @endif
            </form>

        </div>
    </div>

</body>
</html>