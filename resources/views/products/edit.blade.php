@extends('layouts.admin')

@section('title', 'Edit Produk - Admin D\'Vel Jeans')

@section('topbar_title', '📦 Manajemen Produk')

@section('content')
    <style>
        /* --- CSS KHUSUS FORM --- */
        :root {
            --red: #ef4444;
            --red-dim: #fef2f2;
            --radius-sm: 8px;
            --surface2: #f8fafc;
            --text2: #475569;
        }

        /* Kartu putih untuk membungkus form */
        .card-form {
            background: white;
            border-radius: 12px;
            border: 1px solid var(--border);
            padding: 30px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.02);
        }

        .section-title { font-size: 20px; font-weight: 800; color: var(--text); font-family: 'DM Serif Display', serif; }
        .section-sub { font-size: 13px; color: var(--text2); }

        /* Grid untuk mengatur form sejajar (2 kolom) di laptop */
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-group { margin-bottom: 20px; }
        .form-label { display: block; font-size: 13px; font-weight: 700; color: var(--text); margin-bottom: 8px; }
        
        /* Input, Select & Textarea */
        .form-input, .form-select, .form-textarea {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            font-size: 14px;
            font-family: inherit;
            color: var(--text);
            transition: 0.2s;
            box-sizing: border-box; /* Mencegah input melebar keluar wadah */
            background-color: white;
        }
        .image-upload-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 15px; margin-top: 10px; margin-bottom: 20px;}
        .upload-box { border: 1px dashed #cbd5e1; padding: 15px; border-radius: 8px; text-align: center; background: #f8fafc;}
        .upload-box label { font-size: 13px; font-weight: 700; margin-bottom: 8px; display: block; color: #1e293b; }
        .upload-box input { font-size: 12px; width: 100%; }
        .form-input:focus, .form-select:focus, .form-textarea:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(217, 119, 6, 0.1);
        }

        /* Tombol */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            border-radius: var(--radius-sm);
            cursor: pointer;
            transition: 0.2s;
            font-size: 14px;
            text-decoration: none;
            border: none;
        }
        .btn-ghost { background: transparent; color: var(--text2); border: 1px solid var(--border); }
        .btn-ghost:hover { background: var(--surface2); color: var(--text); }
        
        .btn-primary { background: var(--accent); color: white; padding: 12px 24px; }
        .btn-primary:hover { filter: brightness(1.1); transform: translateY(-2px); box-shadow: 0 4px 12px rgba(217, 119, 6, 0.2); }

        /* --- RESPONSIVE MOBILE --- */
        @media (max-width: 768px) {
            .form-row {
                /* Di HP, inputan yang tadinya kiri-kanan berubah menjadi atas-bawah */
                grid-template-columns: 1fr;
                gap: 0;
            }
            .card-form { padding: 20px; }
        }
    </style>

    <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 24px;">
        <a href="{{ route('products.index') }}" class="btn btn-ghost" style="padding: 8px 14px;">&larr; Kembali</a>
        <div>
            <div class="section-title" style="margin-bottom: 0;">✏ Edit Data Produk</div>
            <div class="section-sub" style="margin-bottom: 0;">Perbarui informasi atau stok celana jeans ini</div>
        </div>
    </div>

    @if ($errors->any())
        <div style="background: var(--red-dim); color: var(--red); padding: 16px; border-radius: var(--radius-sm); margin-bottom: 24px; border: 1px solid rgba(239,68,68,0.3);">
            <strong>⚠️ Oops! Ada kesalahan pengisian:</strong>
            <ul style="margin-top: 8px; margin-left: 24px; font-size: 13px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card-form">
        <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT') 
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Nama Celana Jeans <span style="color: var(--red);">*</span></label>
                    <input type="text" name="nama_produk" class="form-input" value="{{ $product->nama_produk }}" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Kategori Gender <span style="color: var(--red);">*</span></label>
                    <select name="kategori_gender" class="form-select" required>
                        <option value="" disabled>-- Pilih Kategori --</option>
                        <option value="Men" {{ $product->kategori_gender == 'Men' ? 'selected' : '' }}>Men (Pria)</option>
                        <option value="Women" {{ $product->kategori_gender == 'Women' ? 'selected' : '' }}>Women (Wanita)</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Harga Jual (Rp) <span style="color: var(--red);">*</span></label>
                    <input type="number" name="harga" class="form-input" value="{{ $product->harga }}" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Deskripsi Lengkap</label>
                <textarea name="deskripsi" class="form-textarea" rows="4">{{ $product->deskripsi }}</textarea>
            </div>

            <!-- KOTAK UPDATE STOK PER UKURAN (27-40) -->
            <div class="form-group" style="margin-bottom: 30px;">
                <label class="form-label" style="font-size: 15px;">Stok Berdasarkan Ukuran (27 - 40)</label>
                <p style="font-size: 13px; color: var(--text2); margin-top: -5px; margin-bottom: 15px;">*Ubah angka menjadi 0 jika stok ukuran ini sudah habis.</p>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(70px, 1fr)); gap: 10px;">
                    @for($i = 27; $i <= 40; $i++)
                        @php
                            // Mencari apakah ukuran ini sudah punya stok di database
                            $sizeData = $product->sizes->where('ukuran', (string)$i)->first();
                            $stokSaatIni = $sizeData ? $sizeData->stok : 0;
                        @endphp
                        <div style="text-align: center; border: 1px solid #e2e8f0; padding: 10px; border-radius: 8px; background: #f8fafc;">
                            <label style="font-weight: 700; font-size: 14px; display: block; margin-bottom: 5px;">{{ $i }}</label>
                            <input type="number" name="sizes[{{ $i }}]" value="{{ $stokSaatIni }}" min="0" style="text-align: center; padding: 6px; width: 100%; border: 1px solid #cbd5e1; border-radius: 4px; box-sizing: border-box;">
                        </div>
                    @endfor
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" style="font-size: 16px;">Galeri Foto Produk</label>
                <div class="image-upload-grid">
                    <div class="upload-box" style="border-color: #d97706; background: #fffbeb;">
                        <label>Gambar Utama (Sampul) *</label>
                        <input type="file" name="gambar" accept="image/*"> 
                        @if($product->gambar)
                            <div style="margin-top: 5px; font-size: 11px; color: green;">✔ Foto sudah ada</div>
                        @endif
                    </div>
                    <div class="upload-box">
                        <label>Gambar 2</label>
                        <input type="file" name="gambar_2" accept="image/*">
                    </div>
                    <div class="upload-box">
                        <label>Gambar 3</label>
                        <input type="file" name="gambar_3" accept="image/*">
                    </div>
                    <div class="upload-box">
                        <label>Gambar 4</label>
                        <input type="file" name="gambar_4" accept="image/*">
                    </div>
                    <div class="upload-box">
                        <label>Gambar 5</label>
                        <input type="file" name="gambar_5" accept="image/*">
                    </div>
                    <div class="upload-box">
                        <label>Gambar 6</label>
                        <input type="file" name="gambar_6" accept="image/*">
                    </div>
                    <div class="upload-box">
                        <label>Gambar 7</label>
                        <input type="file" name="gambar_7" accept="image/*">
                    </div>
                    <div class="upload-box">
                        <label>Gambar 8</label>
                        <input type="file" name="gambar_8" accept="image/*">
                    </div>
                </div>
            </div>

            <hr style="border: none; border-top: 1px solid var(--border); margin: 30px 0;">

            <div style="display: flex; justify-content: flex-end; gap: 12px;">
                <button type="submit" class="btn btn-primary">💾 Simpan Perubahan</button>
            </div>
        </form>
    </div>
@endsection