@extends('layouts.admin')

@section('title', "Manajemen Produk - Admin D'Vel Jeans")

@section('topbar_title')
    <div style="flex: 1; display: flex; justify-content: space-between; align-items: center;">
        <span>📦 Manajemen Produk </span>
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
        
        .variant-badges { display: flex; flex-wrap: wrap; gap: 5px; margin-top: 6px; max-width: 250px;}
        .badge-size { font-size: 10px; font-weight: 700; background: #f1f5f9; color: #475569; padding: 3px 6px; border-radius: 4px; border: 1px solid #e2e8f0; white-space: nowrap;}
        .badge-size.empty { background: #fee2e2; color: #ef4444; border-color: #fca5a5; text-decoration: line-through;}
        
        /* Modifikasi Layout Tombol Aksi */
        .action-buttons { display: flex; flex-direction: column; gap: 6px; align-items: center;}
        
        .btn-edit { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 12px; font-weight: 700; transition: 0.2s; width: 100%; text-align: center; box-sizing: border-box;}
        .btn-edit:hover { background: #dcfce3; }
        .btn-delete { background: white; color: #ef4444; border: 1px solid #fca5a5; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 700; cursor: pointer; transition: 0.2s; width: 100%; box-sizing: border-box;}
        .btn-delete:hover { background: #fef2f2; }
        
        /* CSS Tombol & Modal Sesuaikan Stok */
        .btn-adjust { background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 12px; font-weight: 700; cursor: pointer; transition: 0.2s; width: 100%; box-sizing: border-box;}
        .btn-adjust:hover { background: #dbeafe; }
        
        .modal-overlay { display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; }
        .modal-box { background: white; padding: 25px; border-radius: 12px; width: 90%; max-width: 400px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
        .modal-header { font-size: 16px; font-weight: 800; margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border); padding-bottom: 10px;}
        .close-btn { background: none; border: none; font-size: 24px; cursor: pointer; color: #94a3b8; line-height: 1;}
        .close-btn:hover { color: #ef4444; }
        .size-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; padding: 8px 10px; background: #f8fafc; border-radius: 6px; border: 1px solid #e2e8f0; }
        .size-input { width: 80px; padding: 6px; border: 1px solid #cbd5e1; border-radius: 6px; text-align: center; font-weight: 600; outline: none;}
        .size-input:focus { border-color: var(--accent); }
        .btn-save-modal { background: var(--text); color: white; border: none; padding: 10px; width: 100%; border-radius: 6px; font-weight: 700; margin-top: 15px; cursor: pointer; transition: 0.2s;}
        .btn-save-modal:hover { background: var(--accent); }

        .stock-warning { color: #ef4444; font-size: 11px; background: #fee2e2; padding: 2px 6px; border-radius: 4px; font-weight: 800; margin-left: 5px; display: inline-block;}
        .alert-success { background: #dcfce3; color: #166534; padding: 15px; border-radius: 8px; font-weight: 600; border: 1px solid #bbf7d0; margin-bottom: 20px;}
        .alert-error { background: #fee2e2; color: #991b1b; padding: 15px; border-radius: 8px; font-weight: 600; border: 1px solid #fca5a5; margin-bottom: 20px;}

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
    @if(session('error'))
        <div class="alert-error">❌ {{ session('error') }}</div>
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
                            <div class="action-buttons">
                                <!-- Tombol Buka Modal -->
                                <button type="button" class="btn-adjust" onclick="document.getElementById('modal-{{ $product->id }}').style.display='flex'">⚖️ Sesuaikan Stok</button>
                                
                                <a href="{{ route('products.edit', $product->id) }}" class="btn-edit">✏️ Edit Produk</a>
                                
                                <form action="{{ route('products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Hapus produk ini secara permanen?');" style="width: 100%;">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-delete">🗑️ Hapus</button>
                                </form>
                            </div>

                            <!-- POP-UP MODAL PENYESUAIAN STOK (Berada di dalam Loop) -->
                            <div class="modal-overlay" id="modal-{{ $product->id }}">
                                <div class="modal-box">
                                    <div class="modal-header">
                                        <div style="text-align: left;">
                                            <div style="font-size: 14px;">⚖️ Penyesuaian Stok</div>
                                            <div style="font-size: 11px; color: var(--accent); font-weight: 600;">{{ Str::limit($product->nama_produk, 30) }}</div>
                                        </div>
                                        <button type="button" class="close-btn" onclick="document.getElementById('modal-{{ $product->id }}').style.display='none'">&times;</button>
                                    </div>
                                    
                                    <form action="{{ route('products.adjust_stock', $product->id) }}" method="POST">
                                        @csrf
                                        <p style="font-size: 12px; color: #64748b; margin-bottom: 15px; text-align: left; line-height: 1.5;">
                                            Ketik angka minus (contoh: <strong>-1</strong>) untuk mengurangi stok dari Shopee. Ketik angka biasa (contoh: <strong>5</strong>) untuk menambah barang masuk.
                                        </p>
                                        
                                        <div style="max-height: 250px; overflow-y: auto; margin-bottom: 10px; padding-right: 5px;">
                                            @foreach($product->sizes as $size)
                                                <div class="size-row">
                                                    <div style="font-weight: 700; font-size: 13px; color: var(--text);">
                                                        Ukuran {{ $size->ukuran }} 
                                                        <span style="font-size: 11px; color: #94a3b8; font-weight: normal; margin-left: 5px;">(Sisa: {{ $size->stok }})</span>
                                                    </div>
                                                    <input type="number" name="adjustments[{{ $size->id }}]" class="size-input" placeholder="+ / -">
                                                </div>
                                            @endforeach
                                        </div>

                                        <button type="submit" class="btn-save-modal">Simpan Penyesuaian</button>
                                    </form>
                                </div>
                            </div>
                            <!-- Akhir Modal -->

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