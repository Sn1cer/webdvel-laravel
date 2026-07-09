@extends('layouts.admin')

@section('topbar_title', 'Kelola Akun Admin')

@section('content')
<style>
    /* --- CSS Khusus untuk Halaman Manajemen User --- */
    :root {
        --red: #ef4444;
        --red-dim: #fef2f2;
        --radius-sm: 8px;
        --surface2: #f8fafc;
        --text2: #475569;
    }

    .page-header { margin-bottom: 25px; }
    .page-title { font-size: 24px; font-weight: 800; color: var(--text); margin: 0 0 5px 0; font-family: 'DM Serif Display', serif;}
    .page-subtitle { font-size: 14px; color: var(--text2); margin: 0; }

    /* Alert Sukses & Error */
    .alert-success { background: #dcfce7; color: #166534; padding: 15px 20px; border-radius: 10px; margin-bottom: 25px; font-weight: 600; font-size: 14px; border-left: 5px solid #16a34a; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
    .alert-error { background: var(--red-dim); color: var(--red); padding: 15px 20px; border-radius: 10px; margin-bottom: 25px; border-left: 5px solid var(--red); box-shadow: 0 2px 4px rgba(0,0,0,0.05); }

    /* Desain Card & Form */
    .card-box { background: white; border: 1px solid var(--border); border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); margin-bottom: 30px; }
    .card-header { padding: 20px; border-bottom: 1px solid var(--border); background: #f8fafc; font-weight: 800; font-size: 16px; color: var(--text); display: flex; align-items: center; gap: 10px;}
    .card-body { padding: 25px; }

    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
    .form-group { margin-bottom: 15px; }
    .form-label { display: block; font-size: 13px; font-weight: 700; color: var(--text); margin-bottom: 8px; }
    .form-input { width: 100%; padding: 12px 15px; border: 1px solid var(--border); border-radius: var(--radius-sm); font-size: 14px; font-family: inherit; color: var(--text); transition: 0.2s; box-sizing: border-box; }
    .form-input:focus { outline: none; border-color: var(--accent); box-shadow: 0 0 0 3px rgba(217, 119, 6, 0.1); }
    
    .btn-submit { background: var(--accent); color: white; border: none; padding: 12px 24px; border-radius: var(--radius-sm); font-weight: 700; cursor: pointer; transition: 0.2s; font-size: 14px; display: inline-flex; align-items: center; gap: 8px;}
    .btn-submit:hover { filter: brightness(1.1); transform: translateY(-2px); }

    /* Tabel */
    .table-responsive { overflow-x: auto; }
    .admin-table { width: 100%; border-collapse: collapse; text-align: left; }
    .admin-table th { background: #f8fafc; padding: 16px 20px; font-size: 13px; font-weight: 800; color: #475569; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid var(--border); white-space: nowrap; }
    .admin-table td { padding: 16px 20px; font-size: 15px; border-bottom: 1px solid var(--border); color: var(--text); vertical-align: middle; }
    .admin-table tr:last-child td { border-bottom: none; }
    .admin-table tr:hover { background: #f8fafc; transition: 0.2s; }
    
    .badge-admin { background: #e0e7ff; color: #4338ca; border: 1px solid #c7d2fe; padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 800; letter-spacing: 0.5px; display: inline-block;}
    
    .btn-revoke { background: white; color: #ef4444; border: 1px solid #fca5a5; padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 700; cursor: pointer; transition: all 0.2s; white-space: nowrap;}
    .btn-revoke:hover { background: #fef2f2; border-color: #ef4444; }

    @media (max-width: 768px) {
        .form-row { grid-template-columns: 1fr; gap: 0; }
    }
</style>

<div class="page-header">
    <h1 class="page-title">Manajemen Akun Admin</h1>
    <p class="page-subtitle">Daftarkan admin baru atau cabut hak akses admin yang sudah ada.</p>
</div>

@if (session('success'))
    <div class="alert-success">
        ✅ {{ session('success') }}
    </div>
@endif

@if ($errors->any())
    <div class="alert-error">
        <strong>⚠️ Terdapat kesalahan pengisian:</strong>
        <ul style="margin-top: 8px; margin-left: 24px; font-size: 13px; margin-bottom: 0;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<!-- FORM TAMBAH ADMIN BARU -->
<div class="card-box">
    <div class="card-header">
        <span>➕</span> Daftarkan Admin Baru
    </div>
    <div class="card-body">
        <form action="{{ route('admin.user-management.store') }}" method="POST">
            @csrf
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Nama Lengkap Admin <span style="color: var(--red);">*</span></label>
                    <input type="text" name="name" class="form-input" placeholder="Masukkan nama lengkap" required value="{{ old('name') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Alamat Email <span style="color: var(--red);">*</span></label>
                    <input type="email" name="email" class="form-input" placeholder="contoh@dveljeans.com" required value="{{ old('email') }}">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Password <span style="color: var(--red);">*</span></label>
                    <input type="password" name="password" class="form-input" placeholder="Minimal 8 karakter" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Konfirmasi Password <span style="color: var(--red);">*</span></label>
                    <input type="password" name="password_confirmation" class="form-input" placeholder="Ulangi password di atas" required>
                </div>
            </div>

            <div style="text-align: right; margin-top: 10px;">
                <button type="submit" class="btn-submit">💾 Simpan & Jadikan Admin</button>
            </div>
        </form>
    </div>
</div>

<!-- TABEL DAFTAR ADMIN -->
<div class="card-box">
    <div class="card-header">
        <span>🛡️</span> Daftar Akun Admin Aktif
    </div>
    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Nama Pengguna</th>
                    <th>Alamat Email</th>
                    <th>Hak Akses</th>
                    <th style="text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                <tr>
                    <td style="font-weight: 700;">{{ $user->name }}</td>
                    <td style="color: #64748b;">{{ $user->email }}</td>
                    <td>
                        <span class="badge-admin">ADMIN</span>
                    </td>
                    <td style="text-align: center;">
                        <form action="{{ route('admin.user-management.update-role', $user->id) }}" method="POST" style="margin: 0;">
                            @csrf
                            @method('PATCH')
                            <!-- Kita mengubah role-nya kembali menjadi customer untuk 'mencabut' akses adminnya -->
                            <input type="hidden" name="role" value="customer">
                            <button type="submit" class="btn-revoke" onclick="return confirm('Yakin ingin mencabut hak akses Admin dari akun {{ $user->name }}? Akun ini akan dikembalikan menjadi Pelanggan biasa.')">
                                ❌ Cabut Akses
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align: center; color: #94a3b8; padding: 40px;">
                        <div style="font-size: 30px; margin-bottom: 10px;">👥</div>
                        Belum ada data Admin lain selain Owner.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection