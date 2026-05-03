@extends('layouts.admin')

{{-- Mengubah judul di topbar agar sesuai dengan halaman --}}
@section('topbar_title', 'Kelola Akun Admin')

@section('content')
<style>
    /* --- CSS Khusus untuk Halaman Manajemen User --- */
    .page-header { margin-bottom: 25px; }
    .page-title { font-size: 24px; font-weight: 800; color: var(--text); margin: 0 0 5px 0; }
    .page-subtitle { font-size: 14px; color: #64748b; margin: 0; }

    /* Alert Sukses */
    .alert-success { background: #dcfce7; color: #166534; padding: 15px 20px; border-radius: 10px; margin-bottom: 25px; font-weight: 600; font-size: 14px; border-left: 5px solid #16a34a; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }

    /* Desain Card & Tabel */
    .table-card { background: white; border: 1px solid var(--border); border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); }
    .table-responsive { overflow-x: auto; }
    .admin-table { width: 100%; border-collapse: collapse; text-align: left; }
    .admin-table th { background: #f8fafc; padding: 16px 20px; font-size: 13px; font-weight: 800; color: #475569; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid var(--border); white-space: nowrap; }
    .admin-table td { padding: 16px 20px; font-size: 15px; border-bottom: 1px solid var(--border); color: var(--text); vertical-align: middle; }
    .admin-table tr:last-child td { border-bottom: none; }
    .admin-table tr:hover { background: #f8fafc; transition: 0.2s; }
    
    /* Desain Badge Role */
    .badge { padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 800; letter-spacing: 0.5px; display: inline-block; }
    .badge-admin { background: #e0e7ff; color: #4338ca; border: 1px solid #c7d2fe; }
    .badge-customer { background: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0; }
    
    /* Desain Tombol Aksi */
    .btn-action { padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 700; border: none; cursor: pointer; transition: all 0.2s; white-space: nowrap; }
    .btn-make-admin { background: #10b981; color: white; box-shadow: 0 2px 4px rgba(16, 185, 129, 0.2); }
    .btn-make-admin:hover { background: #059669; transform: translateY(-1px); }
    .btn-revoke { background: white; color: #ef4444; border: 1px solid #fca5a5; }
    .btn-revoke:hover { background: #fef2f2; border-color: #ef4444; }
</style>

<div class="page-header">
    <h1 class="page-title">Manajemen Akses Pengguna</h1>
    <p class="page-subtitle">Atur siapa saja yang memiliki hak akses sebagai Admin toko.</p>
</div>

@if (session('success'))
    <div class="alert-success">
        ✓ {{ session('success') }}
    </div>
@endif

<div class="table-card">
    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Nama Pengguna</th>
                    <th>Alamat Email</th>
                    <th>Status Akses</th>
                    <th style="text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                <tr>
                    <td style="font-weight: 700;">{{ $user->name }}</td>
                    <td style="color: #64748b;">{{ $user->email }}</td>
                    <td>
                        @if ($user->role === 'admin')
                            <span class="badge badge-admin">ADMIN</span>
                        @else
                            <span class="badge badge-customer">PELANGGAN</span>
                        @endif
                    </td>
                    <td style="text-align: center;">
                        <form action="{{ route('admin.user-management.update-role', $user->id) }}" method="POST" style="margin: 0;">
                            @csrf
                            @method('PATCH')
                            
                            @if ($user->role === 'customer')
                                <input type="hidden" name="role" value="admin">
                                <button type="submit" class="btn-action btn-make-admin">
                                    + Jadikan Admin
                                </button>
                            @else
                                <input type="hidden" name="role" value="customer">
                                <button type="submit" class="btn-action btn-revoke" onclick="return confirm('Yakin ingin mencabut hak akses Admin dari akun {{ $user->name }}?')">
                                    Cabut Akses
                                </button>
                            @endif
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align: center; color: #94a3b8; padding: 40px;">
                        <div style="font-size: 30px; margin-bottom: 10px;">👥</div>
                        Tidak ada data pengguna yang ditemukan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection