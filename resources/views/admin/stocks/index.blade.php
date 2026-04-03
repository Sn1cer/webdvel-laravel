@extends('layouts.admin')

@section('title', "Stok Gudang - Admin D'Vel Jeans")

@section('topbar_title')
    <div style="flex: 1; display: flex; justify-content: space-between; align-items: center;">
        <span>🏭 Manajemen Stok Gudang</span>
        <a href="{{ route('admin.stocks.pdf') }}" target="_blank" class="btn-print">🖨️ Cetak Laporan Stok</a>
    </div>
@endsection

@section('content')
    <style>
        /* --- CSS KHUSUS HALAMAN STOK GUDANG --- */
        
        /* Area Tabel */
        .table-wrapper { background: white; border-radius: 12px; border: 1px solid var(--border); overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.02);}
        table { width: 100%; border-collapse: collapse; text-align: left; }
        thead { background: #f8fafc; border-bottom: 1px solid var(--border); }
        th { padding: 15px 20px; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; color: #64748b; font-weight: 700; white-space: nowrap; }
        td { padding: 15px 20px; border-bottom: 1px solid var(--border); vertical-align: middle; }
        
        .product-info { display: flex; align-items: center; gap: 15px; }
        .product-img { width: 50px; height: 50px; border-radius: 8px; object-fit: cover; background: #e2e8f0; display: flex; align-items: center; justify-content: center; font-size: 20px;}
        .product-name { font-weight: 700; color: var(--text); font-size: 14px; margin-bottom: 4px; white-space: nowrap; }
        
        .badge { padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; white-space: nowrap;}
        .badge-aman { background: #dcfce3; color: #15803d; }
        .badge-kritis { background: #fef2f2; color: #dc2626; }
        
        /* Form Restock */
        .restock-form { display: flex; gap: 8px; align-items: center; }
        .restock-input { width: 70px; padding: 8px; border: 1px solid var(--border); border-radius: 6px; font-family: inherit; font-size: 13px; outline: none; }
        .btn-add { background: var(--text); color: white; border: none; padding: 8px 15px; border-radius: 6px; font-weight: 600; cursor: pointer; transition: 0.2s; font-size: 13px; white-space: nowrap;}
        .btn-add:hover { background: var(--accent); }
        
        .btn-print { background: #10b981; color: white; padding: 8px 16px; border-radius: 8px; text-decoration: none; font-weight: 700; transition: 0.2s; font-size: 13px;}
        .btn-print:hover { background: #059669; }
        .alert-success { background: #dcfce3; color: #166534; padding: 15px; border-radius: 8px; font-weight: 600; border: 1px solid #bbf7d0; margin-bottom: 20px;}

        /* --- RESPONSIVE MOBILE --- */
        @media (max-width: 768px) {
            .table-wrapper {
                /* Mengizinkan tabel digeser ke kiri/kanan di layar HP */
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
            table {
                /* Memaksa tabel memiliki lebar minimum agar form input tambah stok tidak menyusut/gepeng */
                min-width: 700px;
            }
            .btn-print {
                /* Menyesuaikan ukuran tombol cetak di layar HP */
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
                    <th>Barang (SKU)</th>
                    <th>Harga Satuan</th>
                    <th>Sisa Stok</th>
                    <th>Status Gudang</th>
                    <th>Tambah Stok (+Pcs)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                    <tr>
                        <td>
                            <div class="product-info">
                                @if($product->gambar)
                                    <img src="{{ asset('images/'.$product->gambar) }}" class="product-img" alt="img">
                                @else
                                    <div class="product-img">👖</div>
                                @endif
                                <div>
                                    <div class="product-name">{{ $product->nama_produk }}</div>
                                    <div style="font-size: 11px; color: #64748b;">ID: PRD-{{ str_pad($product->id, 4, '0', STR_PAD_LEFT) }}</div>
                                </div>
                            </div>
                        </td>
                        <td style="font-weight: 600;">Rp {{ number_format($product->harga, 0, ',', '.') }}</td>
                        <td>
                            <span style="font-size: 18px; font-weight: 800; color: {{ $product->stok < 5 ? '#dc2626' : 'var(--text)' }};">
                                {{ $product->stok }}
                            </span>
                            <span style="font-size: 12px; color: #64748b;">Pcs</span>
                        </td>
                        <td>
                            @if($product->stok < 5)
                                <span class="badge badge-kritis">⚠️ Stok Menipis</span>
                            @else
                                <span class="badge badge-aman">✅ Stok Aman</span>
                            @endif
                        </td>
                        <td>
                            <form action="{{ route('admin.stocks.add', $product->id) }}" method="POST" class="restock-form">
                                @csrf @method('PATCH')
                                <input type="number" name="tambahan_stok" class="restock-input" placeholder="+Angka" min="1" required>
                                <button type="submit" class="btn-add">Tambah</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" style="text-align: center; padding: 40px; color: #64748b;">Belum ada produk di database.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection