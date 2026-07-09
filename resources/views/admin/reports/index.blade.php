@extends('layouts.admin')

@section('title', "Laporan Penjualan - Admin D'Vel Jeans")

@section('topbar_title', '🖨️ Laporan Penjualan (Sukses & Selesai)')

@section('content')
    <style>
        /* --- CSS KHUSUS HALAMAN LAPORAN --- */
        
        /* Form Filter */
        .filter-card { background: white; padding: 25px; border-radius: 12px; border: 1px solid var(--border); margin-bottom: 30px; display: flex; gap: 20px; align-items: flex-end; box-shadow: 0 4px 6px rgba(0,0,0,0.02);}
        .form-group { display: flex; flex-direction: column; gap: 8px; flex: 1; }
        .form-group label { font-size: 13px; font-weight: 700; color: var(--text); }
        .form-group input { padding: 10px 15px; border: 1px solid var(--border); border-radius: 8px; font-family: inherit; outline: none; width: 100%; box-sizing: border-box; }
        
        .btn-filter { background: var(--text); color: white; border: none; padding: 12px 24px; border-radius: 8px; font-weight: 700; cursor: pointer; transition: 0.2s; white-space: nowrap; height: 42px;}
        .btn-filter:hover { background: #334155; }
        .btn-print { background: #10b981; color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 700; transition: 0.2s; display: inline-flex; align-items: center; justify-content: center; gap: 8px; white-space: nowrap; height: 42px; box-sizing: border-box;}
        .btn-print:hover { background: #059669; }

        /* Tabel Laporan */
        .table-wrapper { background: white; border-radius: 12px; border: 1px solid var(--border); overflow: hidden; }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        thead { background: #f8fafc; border-bottom: 1px solid var(--border); }
        th { padding: 15px 20px; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; color: #64748b; font-weight: 700; white-space: nowrap;}
        td { padding: 15px 20px; border-bottom: 1px solid var(--border); font-size: 14px; color: var(--text); white-space: nowrap;}
        .total-row { background: #f8fafc; font-weight: 800; font-size: 18px; color: var(--accent); }

        /* --- RESPONSIVE MOBILE --- */
        @media (max-width: 768px) {
            .filter-card { 
                flex-direction: column; 
                align-items: stretch; 
                gap: 15px; 
                padding: 20px;
            }
            .btn-print { 
                margin-left: 0 !important; 
                width: 100%; 
            }
            .table-wrapper {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
            table {
                min-width: 700px;
            }
        }
    </style>

    <form action="{{ route('admin.reports.index') }}" method="GET" class="filter-card">
        <div class="form-group">
            <label>Dari Tanggal:</label>
            <input type="date" name="tanggal_awal" value="{{ request('tanggal_awal') }}" required>
        </div>
        <div class="form-group">
            <label>Sampai Tanggal:</label>
            <input type="date" name="tanggal_akhir" value="{{ request('tanggal_akhir') }}" required>
        </div>
        <button type="submit" class="btn-filter">🔍 Filter Data</button>
        
        <a href="{{ route('admin.reports.pdf', ['tanggal_awal' => request('tanggal_awal'), 'tanggal_akhir' => request('tanggal_akhir')]) }}" target="_blank" class="btn-print" style="margin-left: auto;">
            🖨️ Cetak / Unduh PDF
        </a>
    </form>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Waktu Transaksi</th>
                    <th>ID Pesanan</th>
                    <th>Nama Pelanggan</th>
                    <th>Status</th>
                    <th style="text-align: right;">Total Pemasukan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td>{{ $order->created_at->format('d M Y, H:i') }}</td>
                        <td style="font-weight: 700;">#{{ $order->nomor_pesanan }}</td>
                        <td>{{ $order->nama_depan }} {{ $order->nama_belakang }}</td>
                        <td><span style="background: #dcfce3; color: #15803d; padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: 800; text-transform: uppercase;">{{ $order->status }}</span></td>
                        <td style="text-align: right; font-weight: 600;">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 40px; color: #64748b;">Tidak ada data penjualan pada tanggal tersebut.</td>
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