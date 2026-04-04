@extends('layouts.app')

@section('title', $product->nama_produk . " - D'Vel Jeans")

@push('styles')
<style>
    /* --- CSS KHUSUS DETAIL PRODUK --- */
    
    /* Layout Detail Produk */
    .container-detail { max-width: 1200px; margin: 40px auto; padding: 0 20px; display: grid; grid-template-columns: 1fr 1fr; gap: 50px; }
    
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

    /* Pilihan Ukuran */
    .section-label { font-size: 14px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 12px; display: block; color: var(--text); }
    .size-selector { display: flex; gap: 10px; margin-bottom: 30px; flex-wrap: wrap;}
    .size-selector input[type="radio"] { display: none; }
    .size-selector label { display: inline-block; padding: 12px 20px; border: 2px solid var(--border); border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.2s; background: white;}
    .size-selector input[type="radio"]:checked + label { border-color: var(--accent); background: var(--accent); color: white; }

    /* Kuantitas & Tombol Beli */
    .action-area { display: flex; gap: 15px; margin-top: 30px; }
    .qty-input { width: 80px; padding: 15px; border: 2px solid var(--border); border-radius: 8px; font-size: 16px; font-weight: 600; text-align: center; outline: none; transition: border 0.2s; box-sizing: border-box;}
    .qty-input:focus { border-color: var(--accent); }
    
    .btn-add-cart { flex: 1; background: var(--text); color: white; border: none; padding: 15px; border-radius: 8px; font-size: 16px; font-weight: 700; cursor: pointer; transition: background 0.2s; text-transform: uppercase; letter-spacing: 1px; display: flex; justify-content: center; align-items: center; text-decoration: none; box-sizing: border-box;}
    .btn-add-cart:hover { background: var(--accent); }

    /* --- RESPONSIVE MOBILE --- */
    @media (max-width: 768px) {
        .container-detail { 
            grid-template-columns: 1fr; /* Berubah jadi 1 kolom (atas-bawah) */
            gap: 30px; 
            margin: 20px auto; 
        }
        .product-image { height: 350px; } /* Gambar lebih pendek di HP */
        .product-title { font-size: 28px; }
        .action-area { flex-direction: column; } /* Input jumlah dan tombol Add to cart jadi atas-bawah */
        .qty-input { width: 100%; } 
    }
</style>
@endpush

@section('content')
    <div class="container-detail">
        
        <div class="product-image-box">
            @if($product->gambar)
                <img src="{{ asset('images/'.$product->gambar) }}" alt="{{ $product->nama_produk }}" class="product-image">
            @else
                <div class="product-image">👖</div>
            @endif
        </div>

        <div class="product-info">
            <div class="breadcrumb">
                <a href="/">Beranda</a> / <a href="{{ route('katalog') }}">Katalog</a> / {{ $product->nama_produk }}
            </div>
            
            @if(session('success'))
                <div style="background: #dcfce3; color: #166534; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-weight: 600; border: 1px solid #bbf7d0;">
                    ✅ {{ session('success') }}
                </div>
            @endif

            <h1 class="product-title">{{ $product->nama_produk }}</h1>
            <div class="product-price">Rp {{ number_format($product->harga, 0, ',', '.') }}</div>
            
            <div class="product-description">{{ $product->deskripsi ?? "Belum ada deskripsi untuk produk ini. Terbuat dari bahan denim premium dengan kualitas jahitan terbaik khas D'Vel Jeans." }}</div>

            @if($product->stok > 0)
                <div style="margin-bottom: 20px; font-weight: 700; color: #16a34a;">
                    ✅ Sisa Stok: {{ $product->stok }} Pcs
                </div>

                @php
                    // Memecah teks ukuran dari database menjadi Array
                    // Jika data kosong, default menampilkan 27-34
                    $ukuranTersedia = $product->ukuran ? explode(',', $product->ukuran) : ['27', '28', '30', '32', '34'];
                @endphp

                @auth
                    <form action="{{ route('cart.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">

                        <label class="section-label">Pilih Ukuran (Size)</label>
                        <div class="size-selector">
                            @foreach($ukuranTersedia as $index => $ukuran)
                                <input type="radio" id="size{{ trim($ukuran) }}" name="ukuran" value="{{ trim($ukuran) }}" {{ $index == 0 ? 'required' : '' }}>
                                <label for="size{{ trim($ukuran) }}">{{ trim($ukuran) }}</label>
                            @endforeach
                        </div>

                        <label class="section-label">Jumlah Barang</label>
                        <div class="action-area">
                            <input type="number" name="jumlah" class="qty-input" value="1" min="1" max="{{ $product->stok }}" required>
                            <button type="submit" class="btn-add-cart">Masukkan Keranjang 🛒</button>
                        </div>
                    </form>
                @endauth

                @guest
                    <label class="section-label">Pilih Ukuran (Size)</label>
                    <div class="size-selector">
                        @foreach($ukuranTersedia as $ukuran)
                            <label style="opacity: 0.5; cursor: not-allowed;">{{ trim($ukuran) }}</label>
                        @endforeach
                    </div>
                    
                    <label class="section-label">Jumlah Barang</label>
                    <div class="action-area">
                        <input type="number" class="qty-input" value="1" disabled style="opacity: 0.5; cursor: not-allowed; background: #f1f5f9;">
                        <a href="{{ route('login') }}" class="btn-add-cart" onclick="alert('Silakan masuk (login) ke akun Anda terlebih dahulu untuk bisa memilih ukuran dan memasukkan barang ke keranjang!')">
                            Login Untuk Membeli 🛒
                        </a>
                    </div>
                @endguest

            @else
                <div style="margin-top: 30px; padding: 20px; background: #fee2e2; border: 1px dashed #ef4444; border-radius: 8px; text-align: center;">
                    <h3 style="color: #b91c1c; margin: 0 0 10px 0;">Mohon Maaf, Stok Habis! 😭</h3>
                    <p style="color: #7f1d1d; margin: 0; font-size: 14px;">Celana ini sedang diproduksi ulang. Silakan cek koleksi kami yang lain.</p>
                </div>
                <button disabled style="width: 100%; background: #cbd5e1; color: white; border: none; padding: 15px; border-radius: 8px; font-size: 16px; font-weight: 700; margin-top: 15px; cursor: not-allowed; text-transform: uppercase;">
                    Stok Habis
                </button>
            @endif

        </div>
    </div>
@endsection