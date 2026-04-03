@extends('layouts.admin')

@section('title', "Manajemen Produk - Admin D'Vel Jeans")

@section('topbar_title')
    <div style="flex: 1; display: flex; justify-content: space-between; align-items: center;">
        <span>👖 Data Seluruh Produk</span>
        <a href="{{ route('products.create') }}" class="btn-add">+ Tambah Produk</a>
    </div>
@endsection

@section('content')
    <style>
        /* --- CSS KHUSUS HALAMAN MANAJEMEN PRODUK --- */
        .btn-add { background: white; color: var(--text); padding: 8px 16px; border-radius: 6px; text-decoration: none; font-weight: 700; font-size: 13px; border: 1px solid var(--border); transition: 0.2s; box-shadow: 0 1px 2px rgba(0,0,0,0.05);}
        .btn-add:hover { background: #f8fafc; border-color: #cbd5e1; }

        .table-wrapper { background: white; border-radius: 12px; border: 1px solid var(--border); overflow: hidden; }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        thead { background: #f8fafc; border-bottom: 1px solid var(--border); }
        th { padding: 15px 20px; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; color: #64748b; font-weight: 700; white-space: nowrap; }
        td { padding: 15px 20px; border-bottom: 1px solid var(--border); vertical-align: middle; }
        
        .product-img { width: 50px; height: 50px; object-fit: cover; border-radius: 6px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; font-size: 18px;}
        .td-title { font-weight: 700; color: var(--text); font-size: 14px; margin-bottom: 4px; white-space: nowrap; }
        .td-sub { font-size: 12px; color: #64748b; min-width: 150px; }
        
        .action-buttons { display: flex; gap: 8px; }
        .btn-edit { background: #f1f5f9; color: #334155; padding: 6px 10px; border-radius: 6px; text-decoration: none; font-size: 12px; font-weight: 700; transition: 0.2s;}
        .btn-edit:hover { background: #e2e8f0; }
        .btn-delete { background: white; color: #ef4444; border: 1px solid #fca5a5; padding: 6px 10px; border-radius: 6px; font-size: 12px; font-weight: 700; cursor: pointer; transition: 0.2s;}
        .btn-delete:hover { background: #fee2e2; }
        .stock-warning { color: #ef4444; font-size: 11px; background: #fee2e2; padding: 2px 6px; border-radius: 4px; font-weight: 700; margin-left: 5px;}
        .alert-success { background: #dcfce3; color: #166534; padding: 15px; border-radius: 8px; font-weight: 600; border: 1px solid #bbf7d0; margin-bottom: 20px;}

        /* --- RESPONSIVE KHUSUS TABEL --- */
        @media (max-width: 768px) {
            .table-wrapper {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
            table {
                min-width: 600px;
            }
            .btn-add {
                padding: 6px 10px;
                font-size: 11px;
            }
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
                    <th>Harga</th>
                    <th>Sisa Stok</th>
                    <th width="140">Aksi</th>
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
                            <div class="td-sub">{{ Str::limit($product->deskripsi, 50) }}</div>
                        </td>
                        <td>
                            <div class="td-title" style="color: var(--accent);">Rp {{ number_format($product->harga, 0, ',', '.') }}</div>
                        </td>
                        <td>
                            <div class="td-title">
                                {{ $product->stok }} Pcs
                                @if($product->stok < 5) <span class="stock-warning">Hampir Habis!</span> @endif
                            </div>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('products.edit', $product->id) }}" class="btn-edit">Edit</a>
                                <form action="{{ route('products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Hapus produk ini?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-delete">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" style="text-align: center; padding: 40px; color: #64748b;">Belum ada produk.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection