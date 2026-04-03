@extends('layouts.admin')

@section('title', "Data Pelanggan - Admin D'Vel Jeans")

@section('topbar_title')
    <div style="flex: 1; display: flex; justify-content: space-between; align-items: center;">
        <span>👥 Database Pelanggan Terdaftar</span>
        <div style="font-size: 13px; font-weight: 600; color: #64748b; background: #f8fafc; padding: 4px 12px; border-radius: 20px; border: 1px solid var(--border);">
            Total: {{ $customers->count() }} Akun
        </div>
    </div>
@endsection

@section('content')
    <style>
        /* --- CSS KHUSUS HALAMAN DATA PELANGGAN --- */
        
        /* Area Tabel */
        .table-wrapper { background: white; border-radius: 12px; border: 1px solid var(--border); overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.02);}
        table { width: 100%; border-collapse: collapse; text-align: left; }
        thead { background: #f8fafc; border-bottom: 1px solid var(--border); }
        th { padding: 15px 20px; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; color: #64748b; font-weight: 700; white-space: nowrap; }
        td { padding: 15px 20px; border-bottom: 1px solid var(--border); vertical-align: middle; }
        
        .user-avatar { width: 40px; height: 40px; border-radius: 50%; background: #dbeafe; color: #1d4ed8; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 16px; flex-shrink: 0; }
        .user-info { display: flex; align-items: center; gap: 15px; }
        .user-name { font-weight: 700; color: var(--text); font-size: 14px; margin-bottom: 2px; white-space: nowrap; }
        .user-email { font-size: 12px; color: #64748b; }
        
        .badge-loyal { background: #fef3c7; color: #b45309; padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: 800; text-transform: uppercase; white-space: nowrap; display: inline-block;}
        .badge-new { background: #f1f5f9; color: #64748b; padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: 800; text-transform: uppercase; white-space: nowrap; display: inline-block;}

        /* --- RESPONSIVE MOBILE --- */
        @media (max-width: 768px) {
            .table-wrapper {
                /* Mengizinkan tabel digeser ke kiri/kanan di layar HP */
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
            table {
                /* Memaksa tabel memiliki lebar minimum agar kolom Total Uang tidak gepeng */
                min-width: 700px;
            }
        }
    </style>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Profil Pelanggan</th>
                    <th>Tanggal Bergabung</th>
                    <th style="text-align: center;">Total Pesanan</th>
                    <th style="text-align: right;">Total Uang Dihabiskan</th>
                    <th>Status Pelanggan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $customer)
                    <tr>
                        <td>
                            <div class="user-info">
                                <div class="user-avatar">{{ strtoupper(substr($customer->name, 0, 1)) }}</div>
                                <div>
                                    <div class="user-name">{{ $customer->name }}</div>
                                    <div class="user-email">✉️ {{ $customer->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div style="font-weight: 600; font-size: 13px; color: var(--text);">{{ $customer->created_at->format('d M Y') }}</div>
                            <div style="font-size: 11px; color: #64748b;">{{ $customer->created_at->diffForHumans() }}</div>
                        </td>
                        <td style="text-align: center;">
                            <span style="font-size: 18px; font-weight: 800; color: var(--text);">{{ $customer->total_orders }}</span>
                            <span style="font-size: 12px; color: #64748b;">x</span>
                        </td>
                        <td style="text-align: right; font-weight: 700; color: var(--accent); font-size: 14px;">
                            Rp {{ number_format($customer->total_spent, 0, ',', '.') }}
                        </td>
                        <td>
                            @if($customer->total_orders > 0)
                                <span class="badge-loyal">👑 Pembeli Aktif</span>
                            @else
                                <span class="badge-new">🌱 Anggota Baru</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" style="text-align: center; padding: 40px; color: #64748b;">Belum ada pelanggan yang mendaftar.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection