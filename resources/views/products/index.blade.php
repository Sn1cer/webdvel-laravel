@extends('layouts.admin')

@section('title', "Manajemen Produk - Admin D'Vel Jeans")

@section('topbar_title')
    <div style="flex: 1; display: flex; justify-content: space-between; align-items: center;">
        <span>📦 Manajemen Produk & Shopee</span>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('admin.stocks.pdf') }}" class="btn-print" target="_blank">🖨️ Cetak Laporan Stok</a>
            <a href="{{ route('admin.stocks.shopee_pdf') }}" class="btn-print" target="_blank" style="color: #ef4444; border-color: #fca5a5;">🖨️ Cetak Laporan Shopee</a>
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

        /* --- CSS SISTEM TABULASI (BARU) --- */
        .tabs-header { display: flex; gap: 10px; margin-bottom: 20px; border-bottom: 2px solid var(--border); padding-bottom: 0;}
        .tab-btn { background: none; border: none; padding: 12px 24px; font-size: 15px; font-weight: 800; color: #64748b; cursor: pointer; transition: 0.2s; border-bottom: 3px solid transparent; margin-bottom: -2px; font-family: inherit;}
        .tab-btn:hover { color: var(--accent); }
        .tab-btn.active { color: var(--accent); border-bottom-color: var(--accent); }
        .tab-content { display: none; animation: fadeIn 0.3s; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }

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
        
        /* Area Log Shopee (Diperbarui agar lebih rapi) */
        .log-area { background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0; padding: 10px; font-size: 11px; min-width: 200px; }
        .log-item { border-bottom: 1px dashed #cbd5e1; padding-bottom: 8px; margin-bottom: 8px; }
        .log-item:last-child { border-bottom: none; margin-bottom: 0; padding-bottom: 0; }
        .log-code { font-family: monospace; font-weight: bold; color: #f97316; background: #fffbeb; padding: 2px 4px; border-radius: 3px; border: 1px solid #fde68a;}

        /* Modifikasi Layout Tombol Aksi */
        .action-buttons { display: flex; flex-direction: column; gap: 6px; align-items: center;}
        
        .btn-edit { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 12px; font-weight: 700; transition: 0.2s; width: 100%; text-align: center; box-sizing: border-box;}
        .btn-edit:hover { background: #dcfce3; }
        .btn-delete { background: white; color: #ef4444; border: 1px solid #fca5a5; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 700; cursor: pointer; transition: 0.2s; width: 100%; box-sizing: border-box;}
        .btn-delete:hover { background: #fef2f2; }
        
        /* CSS Tombol & Modal Sesuaikan Stok */
        .btn-adjust { background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; padding: 8px 12px; border-radius: 6px; text-decoration: none; font-size: 12px; font-weight: 800; cursor: pointer; transition: 0.2s; width: 100%; box-sizing: border-box;}
        .btn-adjust:hover { background: #dbeafe; }
        
        .modal-overlay { display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; backdrop-filter: blur(3px);}
        .modal-box { background: white; padding: 25px; border-radius: 12px; width: 90%; max-width: 400px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
        .modal-header { font-size: 16px; font-weight: 800; margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border); padding-bottom: 10px;}
        .close-btn { background: none; border: none; font-size: 24px; cursor: pointer; color: #94a3b8; line-height: 1;}
        .close-btn:hover { color: #ef4444; }
        .size-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; padding: 8px 10px; background: #f8fafc; border-radius: 6px; border: 1px solid #e2e8f0; }
        .size-input { width: 80px; padding: 6px; border: 1px solid #cbd5e1; border-radius: 6px; text-align: center; font-weight: 600; outline: none;}
        .size-input:focus { border-color: var(--accent); }
        .btn-save-modal { background: #f97316; color: white; border: none; padding: 12px; width: 100%; border-radius: 6px; font-weight: 800; margin-top: 15px; cursor: pointer; transition: 0.2s;}
        .btn-save-modal:hover { filter: brightness(1.1); }

        .stock-warning { color: #ef4444; font-size: 11px; background: #fee2e2; padding: 2px 6px; border-radius: 4px; font-weight: 800; margin-left: 5px; display: inline-block;}
        .alert-success { background: #dcfce3; color: #166534; padding: 15px; border-radius: 8px; font-weight: 600; border: 1px solid #bbf7d0; margin-bottom: 20px;}
        .alert-error { background: #fee2e2; color: #991b1b; padding: 15px; border-radius: 8px; font-weight: 600; border: 1px solid #fca5a5; margin-bottom: 20px;}

        @media (max-width: 768px) {
            .table-wrapper { overflow-x: auto; -webkit-overflow-scrolling: touch; }
            table { min-width: 900px; }
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

    <!-- SISTEM TABS -->
    <div class="tabs-header">
        <button class="tab-btn active" id="defaultOpen" onclick="openTab(event, 'TabKatalog')">📦 Daftar Katalog Produk</button>
        <button class="tab-btn" onclick="openTab(event, 'TabShopee')">🛒 Sinkronisasi Shopee</button>
    </div>

    <!-- ==========================================
         TAB 1: DAFTAR KATALOG PRODUK (MASTER DATA)
         ========================================== -->
    <div id="TabKatalog" class="tab-content" style="display: block;">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th width="70">Foto</th>
                        <th>Detail Produk</th>
                        <th>Harga Jual (Web)</th>
                        <th>Sisa Stok Gudang</th>
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
                                @if($product->kategori_gender)
                                    <div style="font-size: 11px; font-weight: bold; color: #3b82f6; margin-bottom: 4px;">{{ $product->kategori_gender }}</div>
                                @endif
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
                                    <a href="{{ route('products.edit', $product->id) }}" class="btn-edit">✏️ Edit Produk</a>
                                    <form action="{{ route('products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Hapus produk ini secara permanen?');" style="width: 100%;">
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
    </div>

    <!-- ==========================================
         TAB 2: SINKRONISASI SHOPEE (KHUSUS STOK & LOG)
         ========================================== -->
    <div id="TabShopee" class="tab-content">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th width="70">Foto</th>
                        <th>Detail Produk</th>
                        <th>Harga Platform</th>
                        <th>Sisa Stok Aktual</th>
                        <th>
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                Log Transaksi Shopee
                                <button onclick="location.reload()" style="background: none; border: none; cursor: pointer; font-size: 14px;" title="Refresh Log Shopee">🔄</button>
                            </div>
                        </th>
                        <th width="140" style="text-align: center;">Aksi Sinkronisasi</th>
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
                                <div class="td-sub">ID Barang: #PRD-{{ str_pad($product->id, 4, '0', STR_PAD_LEFT) }}</div>
                            </td>
                            <td>
                                <!-- Harga Platform Shopee Display (+30%) -->
                                <div style="font-size: 10px; font-weight: bold; color: #f97316; margin-bottom: 2px;">SHOPEE PRICE (+30%)</div>
                                <div class="td-title">Rp {{ number_format($product->harga * 1.3, 0, ',', '.') }}</div>
                            </td>
                            <td>
                                <div class="td-title">Total: {{ $product->stok }} Pcs</div>
                                <div class="variant-badges">
                                    @foreach($product->sizes as $size)
                                        <span class="badge-size {{ $size->stok == 0 ? 'empty' : '' }}">{{ $size->ukuran }}: {{ $size->stok }}</span>
                                    @endforeach
                                </div>
                            </td>
                            
                            <!-- Sel Log Aktivitas Shopee Terbatas (Maksimal 5) -->
                            <td>
                                <div class="log-area">
                                    @if(isset($shopeeLogs) && $shopeeLogs->has($product->id))
                                        @php
                                            // Membatasi log hanya 5 terbaru agar rapi dan tidak menumpuk
                                            $recentLogs = $shopeeLogs[$product->id]->take(5);
                                        @endphp
                                        
                                        @foreach($recentLogs as $log)
                                            <div class="log-item">
                                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                                                    <span style="font-weight: 800; color: #ef4444; font-size: 12px;">
                                                        {{ $log->jumlah_penyesuaian }} Pcs (Size: {{ $log->ukuran }})
                                                    </span>
                                                    <!-- FORMAT KODE BARU KHUSUS SHOPEE -->
                                                    <span class="log-code">#SHP-{{ str_pad($log->id ?? rand(1,999), 4, '0', STR_PAD_LEFT) }}</span>
                                                </div>
                                                <div style="color: #64748b; font-size: 10px;">
                                                    ⏱ {{ \Carbon\Carbon::parse($log->created_at)->format('d M Y, H:i') }}
                                                </div>
                                            </div>
                                        @endforeach
                                        
                                        @if($shopeeLogs[$product->id]->count() > 5)
                                            <div style="text-align: center; color: #94a3b8; font-style: italic; margin-top: 5px;">
                                                ... (Menampilkan 5 aktivitas terbaru)
                                            </div>
                                        @endif
                                    @else
                                        <div style="text-align: center; color: #94a3b8; padding: 10px 0;">
                                            Belum ada transaksi Shopee.
                                        </div>
                                    @endif
                                </div>
                            </td>

                            <td style="text-align: center;">
                                <div class="action-buttons">
                                    <button type="button" class="btn-adjust" onclick="document.getElementById('modal-shopee-{{ $product->id }}').style.display='flex'">
                                        ⚖️ Potong Stok Shopee
                                    </button>
                                </div>

                                <!-- POP-UP MODAL PENYESUAIAN STOK SHOPEE -->
                                <div class="modal-overlay" id="modal-shopee-{{ $product->id }}">
                                    <div class="modal-box">
                                        <div class="modal-header">
                                            <div style="text-align: left;">
                                                <div style="font-size: 14px;">🛒 Sinkronisasi Transaksi Shopee</div>
                                                <div style="font-size: 11px; color: var(--accent); font-weight: 600;">{{ Str::limit($product->nama_produk, 30) }}</div>
                                            </div>
                                            <button type="button" class="close-btn" onclick="document.getElementById('modal-shopee-{{ $product->id }}').style.display='none'">&times;</button>
                                        </div>
                                        
                                        <form action="{{ route('products.adjust_stock', $product->id) }}" method="POST">
                                            @csrf
                                            <p style="font-size: 12px; color: #64748b; margin-bottom: 15px; text-align: left; line-height: 1.5;">
                                                Ketik angka minus (contoh: <strong>-1</strong>) pada varian ukuran yang terjual di platform Shopee. Kode transaksi <strong>#SHP</strong> akan digenerate otomatis.
                                            </p>
                                            
                                            <div style="max-height: 250px; overflow-y: auto; margin-bottom: 10px; padding-right: 5px;">
                                                @foreach($product->sizes as $size)
                                                    <div class="size-row">
                                                        <div style="font-weight: 700; font-size: 13px; color: var(--text);">
                                                            Size {{ $size->ukuran }} 
                                                            <span style="font-size: 11px; color: #94a3b8; font-weight: normal; margin-left: 5px;">(Sisa: {{ $size->stok }})</span>
                                                        </div>
                                                        <input type="number" name="adjustments[{{ $size->id }}]" class="size-input" placeholder="- Qty">
                                                    </div>
                                                @endforeach
                                            </div>

                                            <button type="submit" class="btn-save-modal">Simpan & Catat Log Shopee</button>
                                        </form>
                                    </div>
                                </div>
                                <!-- Akhir Modal -->
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 50px; color: #64748b;">
                                Belum ada produk.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    // FUNGSI JAVASCRIPT UNTUK SISTEM TABULASI
    function openTab(evt, tabName) {
        var i, tabcontent, tablinks;
        
        // Sembunyikan semua konten tab
        tabcontent = document.getElementsByClassName("tab-content");
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].style.display = "none";
        }
        
        // Hapus class 'active' dari semua tombol
        tablinks = document.getElementsByClassName("tab-btn");
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].className = tablinks[i].className.replace(" active", "");
        }
        
        // Tampilkan tab yang dipilih dan tambahkan class 'active'
        document.getElementById(tabName).style.display = "block";
        evt.currentTarget.className += " active";
    }

    // Pastikan jika ada error atau pesan sukses dari fungsi adjust Shopee, tab Shopee langsung terbuka
    document.addEventListener("DOMContentLoaded", function() {
        @if(session('success') && str_contains(session('success'), 'Stok varian'))
            // Buka tab Shopee jika sukses adjust stok
            document.querySelector("button[onclick=\"openTab(event, 'TabShopee')\"]").click();
        @elseif(session('error') && str_contains(session('error'), 'mencukupi'))
            // Buka tab Shopee jika gagal adjust stok
            document.querySelector("button[onclick=\"openTab(event, 'TabShopee')\"]").click();
        @else
            // Buka tab default (Katalog)
            document.getElementById("defaultOpen").click();
        @endif
    });
</script>
@endpush