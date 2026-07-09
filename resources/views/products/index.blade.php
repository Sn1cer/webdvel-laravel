@extends('layouts.admin')

@section('title', "Manajemen Produk & Stok - Admin D'Vel Jeans")

@section('topbar_title')
    <div style="flex: 1; display: flex; justify-content: space-between; align-items: center;">
        <span>📦 Manajemen Produk & Stok Gudang</span>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('admin.stocks.pdf') }}" class="btn-print" target="_blank">🖨️ Cetak Laporan Stok</a>
            <a href="{{ route('products.create') }}" class="btn-add">+ Tambah Produk</a>
        </div>
    </div>
@endsection

@section('content')
    <style>
        /* --- CSS KHUSUS HALAMAN MANAJEMEN PRODUK & STOK --- */
        .btn-add { background: var(--text); color: white; padding: 8px 16px; border-radius: 6px; text-decoration: none; font-weight: 700; font-size: 13px; border: none; transition: 0.2s; box-shadow: 0 2px 4px rgba(0,0,0,0.1);}
        .btn-add:hover { background: var(--accent); transform: translateY(-1px);}

        .btn-print { background: white; color: var(--text); padding: 8px 16px; border-radius: 6px; text-decoration: none; font-weight: 700; font-size: 13px; border: 1px solid var(--border); transition: 0.2s; box-shadow: 0 1px 2px rgba(0,0,0,0.05);}
        .btn-print:hover { background: #f8fafc; border-color: #cbd5e1; }

        .table-wrapper { background: white; border-radius: 12px; border: 1px solid var(--border); overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);}
        table { width: 100%; border-collapse: collapse; text-align: left; }
        thead { background: #f8fafc; border-bottom: 1px solid var(--border); }
        th { padding: 15px 20px; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; color: #64748b; font-weight: 800; white-space: nowrap; }
        td { padding: 15px 20px; border-bottom: 1px solid var(--border); vertical-align: middle; }
        
        .product-img { width: 50px; height: 50px; object-fit: cover; border-radius: 6px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; font-size: 18px; border: 1px solid #e2e8f0;}
        .td-title { font-weight: 700; color: var(--text); font-size: 14px; margin-bottom: 4px; white-space: nowrap; }
        .td-sub { font-size: 12px; color: #64748b; min-width: 150px; }
        
        /* CSS Khusus Badge Varian Ukuran */
        .variant-badges { display: flex; flex-wrap: wrap; gap: 5px; margin-top: 6px; max-width: 250px;}
        .badge-size { font-size: 10px; font-weight: 700; background: #f1f5f9; color: #475569; padding: 3px 6px; border-radius: 4px; border: 1px solid #e2e8f0; white-space: nowrap;}
        .badge-size.empty { background: #fee2e2; color: #ef4444; border-color: #fca5a5; text-decoration: line-through;}
        
        .action-buttons { display: flex; gap: 8px; }
        .btn-edit { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 12px; font-weight: 700; transition: 0.2s;}
        .btn-edit:hover { background: #dcfce3; }
        .btn-delete { background: white; color: #ef4444; border: 1px solid #fca5a5; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 700; cursor: pointer; transition: 0.2s;}
        .btn-delete:hover { background: #fef2f2; }
        
        .stock-warning { color: #ef4444; font-size: 11px; background: #fee2e2; padding: 2px 6px; border-radius: 4px; font-weight: 800; margin-left: 5px; display: inline-block;}
        .alert-success { background: #dcfce3; color: #166534; padding: 15px; border-radius: 8px; font-weight: 600; border: 1px solid #bbf7d0; margin-bottom: 20px;}

        /* --- RESPONSIVE KHUSUS TABEL --- */
        @media (max-width: 768px) {
            .table-wrapper { overflow-x: auto; -webkit-overflow-scrolling: touch; }
            table { min-width: 800px; }
            .btn-add, .btn-print { padding: 6px 10px; font-size: 11px; }
            .variant-badges { max-width: 100%; }
        }
    </style>

    @if(session('success'))
        <div class="alert-success">✅ {{ session('success') }}</div>
    @endif

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th width="70">Foto</th>
                    <th>Detail Produk</th>
                    <th>Harga Jual</th>
                    <th>Sisa Stok (Gudang & Varian)</th>
                    <th width="140" style="text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                    <tr>
                        <td>
                            @if($product->gambar)
                                <img src="{{ asset('images/'.$product->gambar) }}" class="product-img">
                            @else
                                <div class="product-img">👖</div>
                            @endif
                        </td>
                        <td>
                            <div class="td-title">{{ $product->nama_produk }}</div>
                            <div class="td-sub">{{ Str::limit($product->deskripsi, 40) }}</div>
                        </td>
                        <td>
                            <div class="td-title" style="color: var(--accent);">Rp {{ number_format($product->harga, 0, ',', '.') }}</div>
                        </td>
                        <td>
                            <div class="td-title">
                                Total: {{ $product->stok }} Pcs
                                @if($product->stok > 0 && $product->stok <= 5) 
                                    <span class="stock-warning">Hampir Habis!</span> 
                                @elseif($product->stok == 0)
                                    <span class="stock-warning" style="background: #ef4444; color: white;">Stok Kosong</span> 
                                @endif
                            </div>
                            
                            <div class="variant-badges">
                                @forelse($product->sizes as $size)
                                    <span class="badge-size {{ $size->stok == 0 ? 'empty' : '' }}" title="Stok ukuran {{ $size->ukuran }} = {{ $size->stok }} pcs">
                                        {{ $size->ukuran }}: {{ $size->stok }}
                                    </span>
                                @empty
                                    <span class="badge-size empty">Varian Belum Diatur</span>
                                @endforelse
                            </div>
                        </td>
                        <td style="text-align: center;">
                            <div class="action-buttons" style="justify-content: center;">
                                <a href="{{ route('products.edit', $product->id) }}" class="btn-edit">✏️ Edit / Update Stok</a>
                                
                                <form action="{{ route('products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Hapus produk ini secara permanen?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-delete">🗑️ Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 50px; color: #64748b;">
                            <div style="font-size: 40px; margin-bottom: 10px;">📦</div>
                            <div style="font-weight: 700; font-size: 16px; color: var(--text);">Gudang Masih Kosong</div>
                            Belum ada produk yang didaftarkan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection