@extends('layouts.app')

@section('title', "Katalog Produk - D'Vel Jeans")

@push('styles')
<style>
    /* Header Khusus Halaman Katalog */
    .page-header { background: var(--text); color: white; padding: 60px 20px; text-align: center; }
    .page-title { font-family: 'DM Serif Display', serif; font-size: 40px; margin-bottom: 10px; }
    .page-desc { color: #cbd5e1; font-size: 16px; max-width: 600px; margin: 0 auto; }
    
    /* Grid & Card (Sama seperti Beranda) */
    .container { max-width: 1200px; margin: 50px auto; padding: 0 20px; min-height: 50vh; }
    .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 30px; }
    .card { background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05); transition: transform 0.3s; }
    .card:hover { transform: translateY(-8px); box-shadow: 0 12px 20px rgba(0,0,0,0.1); }
    .card-img { height: 320px; background: #e2e8f0; display: flex; align-items: center; justify-content: center; overflow: hidden; }
    .card-img img { width: 100%; height: 100%; object-fit: cover; }
    .card-body { padding: 24px; }
    .title { font-size: 18px; font-weight: 800; margin-bottom: 8px; line-height: 1.3; }
    .price { color: var(--accent); font-size: 20px; font-weight: 800; margin-bottom: 16px; }
    .btn-buy { display: block; width: 100%; text-align: center; background: var(--text); color: white; padding: 12px; text-decoration: none; border-radius: 8px; font-weight: 600; transition: background 0.2s; }
    .btn-buy:hover { background: var(--accent); }
    .btn-outline { display: flex; justify-content: center; align-items: center; border: 2px solid var(--border); color: var(--text); text-decoration: none; border-radius: 8px; font-weight: 700; transition: 0.2s; }
    .btn-outline:hover { border-color: var(--text); }

    /* Desain Tombol Pagination Laravel */
    .pagination-wrapper { margin-top: 50px; display: flex; justify-content: center; }
    .pagination-wrapper nav svg { height: 20px; } /* Memperbaiki ukuran panah bawaan Laravel */
</style>
@endpush

@section('content')
    <div class="page-header">
        <h1 class="page-title">Katalog Produk</h1>
        <p class="page-desc">Temukan koleksi denim terbaik kami. Dirancang untuk kenyamanan, daya tahan, dan gaya sejati Anda.</p>
    </div>

    <div class="container">
        <div class="grid">
            @forelse($products as $product)
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
                        
                        <div style="display: flex; gap: 10px; margin-top: 15px;">
                            <a href="{{ route('produk.detail', $product->id) }}" class="btn-outline" style="flex: 1; padding: 10px;">Detail</a>
                            
                            @guest
                                <a href="{{ route('login') }}" class="btn-buy" style="flex: 1; padding: 12px; margin: 0;" onclick="alert('Silakan masuk (login) ke akun Anda terlebih dahulu untuk mulai berbelanja!')">🛒 +Cart</a>
                            @else
                                <a href="{{ route('produk.detail', $product->id) }}" class="btn-buy" style="flex: 1; padding: 12px; margin: 0; background: var(--accent);">🛒 +Cart</a>
                            @endauth
                        </div>
                    </div>
                </div>
            @empty
                <div style="grid-column: 1/-1; text-align: center; padding: 50px; color: #64748b; background: white; border-radius: 12px;">
                    Belum ada produk yang tersedia saat ini.
                </div>
            @endforelse
        </div>

        <div class="pagination-wrapper">
            {{ $products->links() }}
        </div>
    </div>
@endsection