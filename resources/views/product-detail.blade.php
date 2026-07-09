@extends('layouts.app')

@section('title', $product->nama_produk . " - D'Vel Jeans")

@push('styles')
<style>
    /* --- CSS KHUSUS DETAIL PRODUK --- */
    
    /* Layout Detail Produk */
    .container-detail { max-width: 1200px; margin: 40px auto; padding: 0 20px; display: grid; grid-template-columns: 1fr 1fr; gap: 50px; }
    
    /* Bagian Kiri: Gambar Utama & Galeri */
    .product-image-box { background: white; border-radius: 16px; padding: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); border: 1px solid var(--border); display: flex; flex-direction: column; gap: 15px;}
    .product-image { width: 100%; height: 500px; object-fit: cover; border-radius: 12px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; font-size: 80px; transition: opacity 0.3s ease-in-out;}
    
    /* CSS Tambahan untuk Galeri Multiple Image */
    .thumbnail-gallery { display: flex; gap: 12px; overflow-x: auto; padding-bottom: 5px; scrollbar-width: thin; }
    .thumbnail-gallery::-webkit-scrollbar { height: 6px; }
    .thumbnail-gallery::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    .thumb-img { width: 80px; height: 80px; object-fit: cover; border-radius: 8px; cursor: pointer; border: 2px solid transparent; transition: all 0.2s; opacity: 0.6; background: #f1f5f9; flex-shrink: 0;}
    .thumb-img:hover { opacity: 1; }
    .thumb-img.active-thumb { border-color: var(--accent); opacity: 1; }

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
    
    /* Ukuran Tersedia (Aktif) */
    .size-selector input[type="radio"]:checked + label { border-color: var(--accent); background: var(--accent); color: white; }
    
    /* Ukuran Habis (Disabled) */
    .size-selector input[type="radio"]:disabled + label { 
        background: #f1f5f9; 
        color: #94a3b8; 
        border-color: #e2e8f0; 
        cursor: not-allowed; 
        text-decoration: line-through; 
    }
    .size-out-of-stock {
        background: #f1f5f9 !important; 
        color: #94a3b8 !important; 
        border-color: #e2e8f0 !important; 
        text-decoration: line-through !important;
    }

    /* Kuantitas & Tombol Beli */
    .action-area { display: flex; gap: 15px; margin-top: 30px; }
    .qty-input { width: 80px; padding: 15px; border: 2px solid var(--border); border-radius: 8px; font-size: 16px; font-weight: 600; text-align: center; outline: none; transition: border 0.2s; box-sizing: border-box;}
    .qty-input:focus { border-color: var(--accent); }
    
    .btn-add-cart { flex: 1; background: var(--text); color: white; border: none; padding: 15px; border-radius: 8px; font-size: 16px; font-weight: 700; cursor: pointer; transition: background 0.2s; text-transform: uppercase; letter-spacing: 1px; display: flex; justify-content: center; align-items: center; text-decoration: none; box-sizing: border-box;}
    .btn-add-cart:hover { background: var(--accent); }

    /* --- RESPONSIVE MOBILE --- */
    @media (max-width: 768px) {
        .container-detail { 
            grid-template-columns: 1fr; 
            gap: 30px; 
            margin: 20px auto; 
        }
        .product-image { height: 350px; } 
        .thumb-img { width: 65px; height: 65px; }
        .product-title { font-size: 28px; }
        .action-area { flex-direction: column; } 
        .qty-input { width: 100%; } 
    }
</style>
@endpush

@section('content')
    <div class="container-detail">
        
        <div class="product-image-box">
            @php
                $allImages = array_filter([
                    $product->gambar, $product->gambar_2, $product->gambar_3, 
                    $product->gambar_4, $product->gambar_5, $product->gambar_6, 
                    $product->gambar_7, $product->gambar_8
                ]);
            @endphp

            @if(count($allImages) > 0)
                <img src="{{ asset('images/'. reset($allImages)) }}" alt="{{ $product->nama_produk }}" class="product-image" id="mainImage">
                
                @if(count($allImages) > 1)
                <div class="thumbnail-gallery">
                    @foreach($allImages as $index => $img)
                        <img src="{{ asset('images/'.$img) }}" 
                             class="thumb-img {{ $index == 0 ? 'active-thumb' : '' }}" 
                             onclick="changeMainImage(this, '{{ asset('images/'.$img) }}')" 
                             alt="Thumbnail {{ $index + 1 }}">
                    @endforeach
                </div>
                @endif
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
                <div id="sisa-stok-label" style="margin-bottom: 20px; font-weight: 700; color: #16a34a;">
                    ✅ Total Stok Keseluruhan: {{ $product->stok }} Pcs
                </div>

                @php
                    $ukuranTersedia = $product->sizes;
                @endphp

                @auth
                    <form action="{{ route('cart.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">

                        <label class="section-label">Pilih Ukuran (Size)</label>
                        <div class="size-selector">
                            @forelse($ukuranTersedia as $size)
                                @if($size->stok > 0)
                                    <!-- Ukuran Tersedia -->
                                    <input type="radio" id="size{{ $size->ukuran }}" name="ukuran" value="{{ $size->ukuran }}" data-stok="{{ $size->stok }}" class="size-radio" required>
                                    <label for="size{{ $size->ukuran }}">{{ $size->ukuran }}</label>
                                @else
                                    <!-- Ukuran Habis -->
                                    <input type="radio" id="size{{ $size->ukuran }}" name="ukuran" value="{{ $size->ukuran }}" disabled>
                                    <label for="size{{ $size->ukuran }}" title="Stok ukuran ini habis">{{ $size->ukuran }}</label>
                                @endif
                            @empty
                                <div style="color: var(--red); font-size: 14px;">Ukuran belum dikonfigurasi oleh Admin.</div>
                            @endforelse
                        </div>

                        <label class="section-label">Jumlah Barang</label>
                        <div class="action-area">
                            <input type="number" name="jumlah" id="qty-input" class="qty-input" value="1" min="1" max="{{ $product->stok }}" required>
                            <button type="submit" class="btn-add-cart">Masukkan Keranjang 🛒</button>
                        </div>
                    </form>
                @endauth

                @guest
                    <label class="section-label">Pilih Ukuran (Size)</label>
                    <div class="size-selector">
                        @forelse($ukuranTersedia as $size)
                            <label class="{{ $size->stok == 0 ? 'size-out-of-stock' : '' }}" style="opacity: 0.5; cursor: not-allowed;" title="{{ $size->stok == 0 ? 'Stok Habis' : 'Login untuk memilih' }}">
                                {{ $size->ukuran }}
                            </label>
                        @empty
                            <div style="color: var(--red); font-size: 14px;">Ukuran belum dikonfigurasi.</div>
                        @endforelse
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

@push('scripts')
<script>
    function changeMainImage(element, newSrc) {
        const mainImg = document.getElementById('mainImage');
        mainImg.style.opacity = '0.7';
        
        setTimeout(() => {
            mainImg.src = newSrc;
            mainImg.style.opacity = '1';
        }, 150);

        document.querySelectorAll('.thumb-img').forEach(img => {
            img.classList.remove('active-thumb');
        });
        element.classList.add('active-thumb');
    }

    document.addEventListener("DOMContentLoaded", function() {
        const sizeRadios = document.querySelectorAll('.size-radio');
        const qtyInput = document.getElementById('qty-input');
        const sisaStokLabel = document.getElementById('sisa-stok-label');

        sizeRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                const maxStokUkuranIni = parseInt(this.getAttribute('data-stok'));
                
                qtyInput.max = maxStokUkuranIni;
                
                if(parseInt(qtyInput.value) > maxStokUkuranIni) {
                    qtyInput.value = maxStokUkuranIni;
                }
                sisaStokLabel.innerHTML = `✅ Sisa Stok Ukuran <b>${this.value}</b>: ${maxStokUkuranIni} Pcs`;
            });
        });
    });
</script>
@endpush