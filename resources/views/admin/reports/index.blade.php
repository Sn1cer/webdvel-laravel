@extends('layouts.admin')

@section('title', "Laporan Penjualan - Admin D'Vel Jeans")

@section('topbar_title', '🖨️ Laporan Penjualan (Sukses & Selesai)')

@section('content')
    <style>
        /* --- CSS KHUSUS HALAMAN LAPORAN --- */
        
        /* Form Filter Baru (Grid Layout) */
        .filter-container { background: white; padding: 25px; border-radius: 12px; border: 1px solid var(--border); margin-bottom: 30px; box-shadow: 0 4px 6px rgba(0,0,0,0.02);}
        .filter-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 20px;}
        .form-group { display: flex; flex-direction: column; gap: 8px; }
        .form-group label { font-size: 12px; font-weight: 700; color: var(--text); text-transform: uppercase; letter-spacing: 0.5px;}
        .form-group input, .form-group select { padding: 10px 15px; border: 1px solid var(--border); border-radius: 8px; font-family: inherit; font-size: 13px; outline: none; width: 100%; box-sizing: border-box; background: #f8fafc; color: #334155; font-weight: 600;}
        .form-group input:focus, .form-group select:focus { border-color: var(--text); background: white;}
        
        .filter-actions { display: flex; gap: 10px; justify-content: flex-end; align-items: center; border-top: 1px solid var(--border); padding-top: 20px; }
        .btn-filter { background: var(--text); color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 700; cursor: pointer; transition: 0.2s; height: 42px;}
        .btn-filter:hover { background: #334155; }
        .btn-reset { background: #f1f5f9; color: #64748b; border: 1px solid #cbd5e1; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 700; transition: 0.2s; height: 42px; display: inline-flex; align-items: center; box-sizing: border-box;}
        .btn-reset:hover { background: #e2e8f0; color: #0f172a;}
        .btn-print { background: #10b981; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 700; transition: 0.2s; display: inline-flex; align-items: center; gap: 8px; height: 42px; box-sizing: border-box;}
        .btn-print:hover { background: #059669; }

        /* Tabel Laporan */
        .table-wrapper { background: white; border-radius: 12px; border: 1px solid var(--border); overflow: hidden; }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        thead { background: #f8fafc; border-bottom: 1px solid var(--border); }
        th { padding: 15px 20px; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; color: #64748b; font-weight: 700; white-space: nowrap;}
        td { padding: 15px 20px; border-bottom: 1px solid var(--border); font-size: 14px; color: var(--text); vertical-align: top;}
        .total-row { background: #f8fafc; font-weight: 800; font-size: 18px; color: var(--accent); }

        .detail-list { list-style-type: none; padding: 0; margin: 0; font-size: 13px; color: #475569; }
        .detail-list li { margin-bottom: 4px; padding-bottom: 4px; border-bottom: 1px dashed #e2e8f0; white-space: nowrap;}
        .detail-list li:last-child { margin-bottom: 0; padding-bottom: 0; border-bottom: none; }

        /* --- RESPONSIVE MOBILE --- */
        @media (max-width: 768px) {
            .filter-actions { flex-direction: column; align-items: stretch; }
            .btn-print, .btn-reset, .btn-filter { width: 100%; justify-content: center; }
            .table-wrapper { overflow-x: auto; -webkit-overflow-scrolling: touch; }
            table { min-width: 900px; }
        }
    </style>

    <form action="{{ route('admin.reports.index') }}" method="GET" class="filter-container">
        <div class="filter-grid">
            <div class="form-group">
                <label>Pencarian Cepat</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari ID / Nama Pelanggan...">
            </div>
            <div class="form-group">
                <label>Sumber Transaksi</label>
                <select name="tipe_pesanan">
                    <option value="Semua">Semua Sumber</option>
                    <option value="Online" {{ request('tipe_pesanan') == 'Online' ? 'selected' : '' }}>Online (Ekspedisi)</option>
                    <option value="Booking" {{ request('tipe_pesanan') == 'Booking' ? 'selected' : '' }}>Booking (Ambil Toko)</option>
                    <option value="POS Offline" {{ request('tipe_pesanan') == 'POS Offline' ? 'selected' : '' }}>POS Kasir Offline</option>
                </select>
            </div>
            <div class="form-group">
                <label>Status Pesanan</label>
                <select name="status">
                    <option value="Semua">Semua Status</option>
                    <option value="Belum Bayar" {{ request('status') == 'Belum Bayar' ? 'selected' : '' }}>Belum Bayar</option>
                    <option value="Diproses" {{ request('status') == 'Diproses' ? 'selected' : '' }}>Diproses</option>
                    <option value="Dikirim" {{ request('status') == 'Dikirim' ? 'selected' : '' }}>Selesai / Dikirim</option>
                    <option value="Dibatalkan" {{ request('status') == 'Dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </div>
            <div class="form-group">
                <label>Metode Pembayaran</label>
                <select name="pembayaran">
                    <option value="Semua">Semua Metode</option>
                    <option value="Midtrans" {{ request('pembayaran') == 'Midtrans' ? 'selected' : '' }}>Midtrans Otomatis</option>
                    <option value="Tunai" {{ request('pembayaran') == 'Tunai' ? 'selected' : '' }}>Tunai / Toko</option>
                </select>
            </div>
            <div class="form-group">
                <label>Mulai Tanggal</label>
                <!-- Dibuat tidak required agar bisa mencari ID tanpa isi tanggal -->
                <input type="date" name="tanggal_awal" value="{{ request('tanggal_awal') }}">
            </div>
            <div class="form-group">
                <label>Sampai Tanggal</label>
                <input type="date" name="tanggal_akhir" value="{{ request('tanggal_akhir') }}">
            </div>
        </div>
        
        <div class="filter-actions">
            <a href="{{ route('admin.reports.index') }}" class="btn-reset">✖ Reset</a>
            <button type="submit" class="btn-filter">🔍 Terapkan Filter</button>
            
            <!-- Menggunakan request()->all() agar PDF yang dicetak sesuai dengan filter yang sedang aktif -->
            <a href="{{ route('admin.reports.pdf', request()->all()) }}" target="_blank" class="btn-print">
                🖨️ Cetak / Unduh PDF
            </a>
        </div>
    </form>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Waktu Transaksi</th>
                    <th>ID & Pelanggan</th>
                    <th>Rincian Barang</th>
                    <th>Status & Tipe</th>
                    <th style="text-align: right;">Total Pemasukan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td style="color: #64748b; font-size: 13px;">
                            {{ $order->created_at->format('d M Y') }}<br>
                            <span style="font-weight: 600;">{{ $order->created_at->format('H:i') }} WIB</span>
                        </td>
                        <td>
                            <div style="font-weight: 800; color: var(--accent);">#{{ $order->nomor_pesanan ?? $order->resi }}</div>
                            <div style="font-size: 13px; font-weight: 600; margin-top: 4px;">{{ $order->nama_depan }} {{ $order->nama_belakang }}</div>
                        </td>
                        <td>
                            <ul class="detail-list">
                                @foreach($order->details as $item)
                                    <li><strong>{{ $item->jumlah }}x</strong> {{ $item->product->nama_produk ?? 'Produk Dihapus' }} (Size: {{ $item->ukuran }})</li>
                                @endforeach
                            </ul>
                        </td>
                        <td>
                            @php
                                $badgeColor = '#e2e8f0'; $textColor = '#475569';
                                if($order->status == 'Dikirim') { $badgeColor = '#dcfce3'; $textColor = '#15803d'; }
                                elseif($order->status == 'Diproses') { $badgeColor = '#dbeafe'; $textColor = '#1d4ed8'; }
                                elseif($order->status == 'Dibatalkan') { $badgeColor = '#fee2e2'; $textColor = '#b91c1c'; }
                                elseif($order->status == 'Belum Bayar') { $badgeColor = '#fef3c7'; $textColor = '#b45309'; }
                            @endphp
                            <span style="background: {{ $badgeColor }}; color: {{ $textColor }}; padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: 800; text-transform: uppercase;">
                                {{ $order->status }}
                            </span>
                            <div style="font-size: 11px; font-weight: 600; margin-top: 6px; color: #64748b;">
                                {{ $order->tipe_pesanan }}
                                @if($order->bukti_pembayaran == 'midtrans_verified')
                                    <span style="color: #1d4ed8;">(Midtrans)</span>
                                @else
                                    (Tunai)
                                @endif
                            </div>
                        </td>
                        <td style="text-align: right; font-weight: 700; font-size: 15px;">
                            @if($order->status == 'Dibatalkan')
                                <span style="text-decoration: line-through; color: #94a3b8;">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</span>
                            @else
                                Rp {{ number_format($order->total_harga, 0, ',', '.') }}
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 40px; color: #64748b;">
                            🕵️‍♂️ Tidak ada transaksi yang sesuai dengan filter pencarian.
                        </td>
                    </tr>
                @endforelse
                
                @if($orders->count() > 0)
                <tr class="total-row">
                    <td colspan="4" style="text-align: right; padding-right: 20px;">TOTAL PENDAPATAN BERSIH:</td>
                    <td style="text-align: right;">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
@endsection