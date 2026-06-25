@extends('layouts.admin')

@section('title', "Kelola Banner - Admin D'Vel Jeans")

@section('topbar_title')
    <div style="display: flex; align-items: center;">
        🖼️ Manajemen Banner / Carousel
    </div>
@endsection

@section('content')
    <style>
        .card { background: white; border-radius: 12px; border: 1px solid var(--border); padding: 25px; margin-bottom: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.02); }
        .form-control { width: 100%; padding: 12px; border: 1px solid var(--border); border-radius: 8px; margin-top: 5px; margin-bottom: 15px; box-sizing: border-box;}
        .btn-submit { background: var(--text); color: white; border: none; padding: 12px 24px; border-radius: 8px; font-weight: 700; cursor: pointer; transition: 0.2s; font-size: 14px;}
        .btn-submit:hover { background: var(--accent); }
        
        /* Grid untuk Daftar Banner */
        .banner-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 20px; }
        .banner-item { background: white; border: 1px solid var(--border); border-radius: 12px; overflow: hidden; display: flex; flex-direction: column;}
        .banner-img { width: 100%; height: 200px; object-fit: cover; border-bottom: 1px solid var(--border);}
        .banner-info { padding: 20px; flex: 1; display: flex; flex-direction: column; justify-content: space-between;}
        .banner-title { font-weight: 800; font-size: 16px; margin-bottom: 5px; color: var(--text); }
        .banner-subtitle { font-size: 13px; color: #64748b; margin-bottom: 20px; }
        
        .btn-delete { background: #fee2e2; color: #ef4444; border: none; padding: 10px; border-radius: 6px; font-size: 13px; font-weight: 700; cursor: pointer; width: 100%; transition: 0.2s; }
        .btn-delete:hover { background: #fca5a5; color: #991b1b; }
        .alert-success { background: #dcfce3; color: #166534; padding: 15px; border-radius: 8px; font-weight: 600; border: 1px solid #bbf7d0; margin-bottom: 20px;}
    </style>

    @if(session('success'))
        <div class="alert-success">✅ {{ session('success') }}</div>
    @endif

    <div class="card">
        <h3 style="margin-top: 0; margin-bottom: 20px; font-family: 'DM Serif Display', serif;">➕ Tambah Banner Baru</h3>
        <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div style="display: flex; gap: 20px; flex-wrap: wrap;">
                <div style="flex: 1; min-width: 250px;">
                    <label style="font-weight: 700; font-size: 14px;">Pilih File Gambar (Wajib)</label>
                    <input type="file" name="gambar" class="form-control" required accept="image/*">
                </div>
                <div style="flex: 1; min-width: 250px;">
                    <label style="font-weight: 700; font-size: 14px;">Judul Banner (Opsional)</label>
                    <input type="text" name="judul" class="form-control" placeholder="Contoh: Promo Ramadhan!">
                </div>
                <div style="flex: 1; min-width: 250px;">
                    <label style="font-weight: 700; font-size: 14px;">Subjudul (Opsional)</label>
                    <input type="text" name="subjudul" class="form-control" placeholder="Contoh: Diskon 50% untuk semua item">
                </div>
            </div>
            <button type="submit" class="btn-submit">Unggah Banner Sekarang</button>
        </form>
    </div>

    <h3 style="margin-top: 35px; margin-bottom: 20px; font-family: 'DM Serif Display', serif;">🖼️ Banner yang Tersedia di Beranda</h3>
    <div class="banner-grid">
        @forelse($banners as $banner)
            <div class="banner-item">
                <img src="{{ asset('images/banners/' . $banner->gambar) }}" class="banner-img" alt="Banner">
                <div class="banner-info">
                    <div>
                        <div class="banner-title">{{ $banner->judul ?? 'Tanpa Judul' }}</div>
                        <div class="banner-subtitle">{{ $banner->subjudul ?? 'Tidak ada subjudul' }}</div>
                    </div>
                    
                    <form action="{{ route('admin.banners.destroy', $banner->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus banner ini dari beranda?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-delete">🗑️ Hapus Banner Ini</button>
                    </form>
                </div>
            </div>
        @empty
            <div style="grid-column: 1 / -1; background: white; padding: 50px; text-align: center; border-radius: 12px; border: 1px dashed #cbd5e1; color: #64748b;">
                <div style="font-size: 40px; margin-bottom: 10px;">📸</div>
                Belum ada banner yang diunggah. Silakan upload banner pertama Anda!
            </div>
        @endforelse
    </div>
@endsection