@extends('layouts.app')

@section('title', "D'Vel Jeans - Official Store")

@push('styles')
<style>
    /* --- CSS KHUSUS HALAMAN BERANDA --- */
    
    /* HERO SLIDER */
    .hero-slider { position: relative; width: 100%; height: calc(100vh - 78px); overflow: hidden; background: #000; }
    .slide {
        position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; 
        transition: opacity 1s ease-in-out; display: flex; justify-content: flex-start; align-items: center; 
        padding: 0 50px; background-size: cover; background-position: center; background-repeat: no-repeat; z-index: 1;
    }
    .slide.active { opacity: 1; z-index: 2; }
    .slide::before {
        content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 100%;
        background: linear-gradient(90deg, rgba(0,0,0,0.8) 0%, rgba(0,0,0,0.2) 100%); z-index: -1;
    }
    .hero-content { max-width: 600px; transform: translateY(30px); opacity: 0; transition: all 0.8s ease 0.5s; }
    .slide.active .hero-content { transform: translateY(0); opacity: 1; }
    .hero-title-script { font-family: 'DM Serif Display', serif; font-size: 24px; color: var(--accent); margin-bottom: 5px; display: block; }
    .hero-content h1 { font-family: 'DM Serif Display', serif; font-size: 60px; color: white; margin: 0 0 15px 0; line-height: 1.1; }
    .hero-details { font-size: 16px; color: rgba(255,255,255,0.8); margin-bottom: 30px; }
    .btn-hero { background: var(--accent); color: white; padding: 12px 28px; border-radius: 30px; text-decoration: none; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; font-size: 13px; display: inline-block; transition: transform 0.2s; }
    .btn-hero:hover { transform: translateY(-3px); }

    /* DOTS SLIDER */
    .slider-dots { position: absolute; bottom: 30px; left: 50%; transform: translateX(-50%); z-index: 10; display: flex; gap: 10px; }
    .dot { width: 12px; height: 12px; border-radius: 50%; background: rgba(255,255,255,0.4); cursor: pointer; transition: all 0.3s; }
    .dot.active { background: var(--accent); width: 30px; border-radius: 10px; }

    /* MARQUEE */
    .marquee-strip { background: #d97706; padding: 13px 0; overflow: hidden; display: flex; }
    .marquee-track { display: flex; gap: 48px; white-space: nowrap; animation: marquee 22s linear infinite; will-change: transform; }
    @keyframes marquee { from { transform: translateX(0); } to { transform: translateX(-50%); } }
    .marquee-item { display: flex; align-items: center; gap: 16px; font-size: 11px; font-weight: 800; letter-spacing: 3px; text-transform: uppercase; color: #0d0d0d; }
    .marquee-dot { width: 4px; height: 4px; border-radius: 50%; background: rgba(0,0,0,0.4); }

    /* PRODUK GRID */
    .container { max-width: 1200px; margin: 60px auto; padding: 0 20px; }
    .section-title { text-align: center; font-size: 32px; font-weight: 800; margin-bottom: 40px; }
    .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 30px; }
    .card { background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05); transition: transform 0.3s; }
    .card:hover { transform: translateY(-8px); box-shadow: 0 12px 20px rgba(0,0,0,0.1); }
    .card-img { height: 320px; background: #e2e8f0; display: flex; align-items: center; justify-content: center; overflow: hidden; }
    .card-img img { width: 100%; height: 100%; object-fit: cover; }
    .card-body { padding: 24px; }
    .title { font-size: 18px; font-weight: 800; margin-bottom: 8px; line-height: 1.3; }
    .price { color: var(--accent); font-size: 20px; font-weight: 800; margin-bottom: 16px; }
    
    /* Tombol Beli */
    .btn-buy { 
        display: flex; 
        justify-content: center; 
        align-items: center; 
        width: 100%; 
        background: var(--text); 
        color: white; 
        padding: 12px 0; 
        text-decoration: none; 
        border-radius: 8px; 
        font-weight: 600; 
        transition: background 0.2s; 
        box-sizing: border-box; 
    }
    .btn-buy:hover { background: var(--accent); }

    /* Tombol Lihat Semua Katalog */
    .view-all-container { text-align: center; margin-top: 50px; }
    .btn-view-all { 
        display: inline-block; 
        background: transparent; 
        color: var(--text); 
        border: 2px solid var(--text); 
        padding: 12px 35px; 
        border-radius: 30px; 
        font-weight: 800; 
        text-transform: uppercase; 
        letter-spacing: 1px; 
        font-size: 14px;
        text-decoration: none; 
        transition: all 0.3s ease; 
    }
    .btn-view-all:hover { 
        background: var(--text); 
        color: white; 
        transform: translateY(-3px); 
        box-shadow: 0 10px 15px rgba(0,0,0,0.1); 
    }

    /* RESPONSIVE KHUSUS BERANDA */
    @media (max-width: 768px) {
        .slide { padding: 0 20px; }
        .hero-content h1 { font-size: 40px; }
        .hero-details { font-size: 14px; }
        .section-title { font-size: 26px; }
    }
</style>
@endpush

@section('content')
    <header class="hero-slider">
        @forelse($banners as $index => $banner)
            <div class="slide {{ $index == 0 ? 'active' : '' }}" style="background-image: url('{{ asset('images/banners/' . $banner->gambar) }}');">
                <div class="hero-content">
                    <span class="hero-title-script">D'Vel Jeans</span>
                    <h1>{{ $banner->judul ?? 'Temukan Gaya Denim Terbaikmu' }}</h1>
                    <p class="hero-details">{{ $banner->subjudul ?? 'Koleksi Premium Terbatas' }}</p>
                    <a href="#katalog" class="btn-hero">Mulai Belanja</a>
                </div>
            </div>
        @empty
            <div class="slide active" style="background-image: url('https://images.unsplash.com/photo-1542272604-787c3835535d?q=80&w=1200&auto=format&fit=crop');">
                <div class="hero-content">
                    <span class="hero-title-script">Koleksi Baru</span>
                    <h1>Selamat Datang di D'Vel Jeans</h1>
                    <p class="hero-details">Bahan tebal, jahitan rapi, gaya tak lekang waktu.</p>
                    <a href="#katalog" class="btn-hero">Lihat Koleksi</a>
                </div>
            </div>
        @endforelse

        @if($banners->count() > 1)
            <div class="slider-dots">
                @foreach($banners as $index => $banner)
                    <span class="dot {{ $index == 0 ? 'active' : '' }}" onclick="changeSlide({{ $index }})"></span>
                @endforeach
            </div>
        @endif
    </header>

    <div class="marquee-strip" aria-hidden="true">
        <div class="marquee-track">
            @php
                $items = [
                    'Premium Denim', 'Kualitas Terbaik', 'Desain Lokal',
                    'Bahan Pilihan', 'Jahitan Presisi', 'Gaya Autentik', "D'Vel Jeans",
                    'Premium Denim', 'Kualitas Terbaik', 'Desain Lokal',
                    'Bahan Pilihan', 'Jahitan Presisi', 'Gaya Autentik', "D'Vel Jeans",
                ];
            @endphp
            @foreach($items as $item)
                <span class="marquee-item">
                    {{ $item }}
                    <span class="marquee-dot"></span>
                </span>
            @endforeach
        </div>
    </div>

    <div class="container" id="katalog">
        <h2 class="section-title">Koleksi Terbaru Kami</h2>
        
        <div class="grid">
            <!-- Menambahkan ->take(4) agar hanya melooping 4 data produk terbaru -->
            @forelse($products->take(4) as $product)
                <div class="card">
                    <div class="card-img">
                        @if($product->gambar)
                            <img src="{{ asset('images/'.$product->gambar) }}" alt="{{ $product->nama_produk }}">
                        @else
                            <span style="font-size: 60px;">👖</span>
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="title">{{ $product->nama_produk }}</div>
                        <div class="price">Rp {{ number_format($product->harga, 0, ',', '.') }}</div>
                        <a href="{{ route('produk.detail', $product->id) }}" class="btn-buy">Lihat Detail</a>
                    </div>
                </div>
            @empty
                <div style="grid-column: 1/-1; text-align: center; padding: 50px; color: #64748b;">
                    Belum ada produk yang tersedia saat ini.
                </div>
            @endforelse
        </div>

        <!-- Tombol "Lihat Semua Katalog" -->
        @if($products->count() > 0)
        <div class="view-all-container">
            <!-- Pastikan route('katalog') sesuai dengan nama route halaman katalog Anda. Jika namanya beda, sesuaikan tulisan 'katalog' di bawah ini -->
            <a href="{{ url('/katalog') }}" class="btn-view-all">Lihat Semua Koleksi ➔</a>
        </div>
        @endif

    </div>
@endsection

@push('scripts')
<script>
    let currentSlide = 0;
    const slides = document.querySelectorAll('.slide');
    const dots = document.querySelectorAll('.dot');
    const totalSlides = slides.length;

    // Pastikan script hanya berjalan jika ada lebih dari 1 gambar
    if(totalSlides > 1) {
        function showSlide(index) {
            slides.forEach(slide => slide.classList.remove('active'));
            dots.forEach(dot => dot.classList.remove('active'));
            
            slides[index].classList.add('active');
            if(dots[index]) {
                dots[index].classList.add('active');
            }
            currentSlide = index;
        }

        function changeSlide(index) {
            showSlide(index);
        }

        setInterval(() => {
            let nextSlide = (currentSlide + 1) % totalSlides;
            showSlide(nextSlide);
        }, 5000);
    }
</script>
@endpush