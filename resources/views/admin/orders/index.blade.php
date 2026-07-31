@extends('layouts.admin')

@section('title', "Pesanan Masuk - Admin D'Vel Jeans")

@section('topbar_title')
    <div style="display: flex; align-items: center;">
        📦 Daftar Pesanan Pelanggan 
        <span style="background: var(--text); color: white; padding: 4px 12px; border-radius: 20px; font-size: 13px; margin-left: 10px;">
            {{ $orders->count() }} Order
        </span>
    </div>
@endsection

@section('content')
    <style>
        /* --- CSS KHUSUS HALAMAN PESANAN --- */
        
        /* TABS & FILTER */
        .filter-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 2px solid var(--border); padding-bottom: 10px; flex-wrap: wrap; gap: 15px; }
        .tabs-container { display: flex; gap: 10px; overflow-x: auto; padding-bottom: 5px; }
        .tab-btn { padding: 10px 20px; font-size: 14px; font-weight: 700; color: #64748b; text-decoration: none; border-radius: 8px; transition: 0.2s; white-space: nowrap; }
        .tab-btn:hover { background: #f1f5f9; color: var(--text); }
        .tab-active { background: var(--text); color: white !important; }
        
        .filter-select { padding: 8px 15px; border-radius: 8px; border: 1px solid var(--border); outline: none; font-weight: 600; color: #475569; background: white; cursor: pointer; }

        /* Area Tabel */
        .table-wrapper { background: white; border-radius: 12px; border: 1px solid var(--border); overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.02);}
        table { width: 100%; border-collapse: collapse; text-align: left; }
        thead { background: #f8fafc; border-bottom: 1px solid var(--border); }
        th { padding: 15px 20px; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; color: #64748b; font-weight: 700; white-space: nowrap; }
        td { padding: 15px 20px; border-bottom: 1px solid var(--border); vertical-align: middle; }
        
        .td-title { font-weight: 700; color: var(--text); font-size: 14px; margin-bottom: 4px; white-space: nowrap;}
        .td-sub { font-size: 12px; color: #64748b; line-height: 1.4;}
        
        /* Tombol & Badge */
        .btn-bukti { background: #f1f5f9; color: #3b82f6; border: 1px solid #bfdbfe; padding: 6px 10px; border-radius: 6px; text-decoration: none; font-size: 12px; font-weight: 700; display: inline-block; margin-top: 5px; transition: 0.2s;}
        .btn-bukti:hover { background: #dbeafe; }
        .btn-detail { background: white; color: var(--text); border: 1px solid var(--border); padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 700; cursor: pointer; transition: 0.2s;}
        .btn-detail:hover { background: #f8fafc; border-color: #cbd5e1; }
        
        .status-select { padding: 6px; border: 1px solid var(--border); border-radius: 6px; font-size: 12px; font-weight: 600; outline: none; background: white; width: 100%; box-sizing: border-box;}
        .btn-update { background: var(--text); color: white; border: none; padding: 6px 12px; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 12px; transition: 0.2s; width: 100%;}
        .btn-update:hover { background: var(--accent); }
        .badge { padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; display: inline-block;}
        .badge-belum-bayar { background: #fef3c7; color: #b45309; }
        .badge-diproses { background: #dbeafe; color: #1d4ed8; }
        .badge-dikirim { background: #dcfce3; color: #15803d; }
        .badge-dibatalkan { background: #fee2e2; color: #b91c1c; } /* Warna merah untuk batal */
        .alert-success { background: #dcfce3; color: #166534; padding: 15px; border-radius: 8px; font-weight: 600; border: 1px solid #bbf7d0; margin-bottom: 20px;}
        
        .badge-tipe-booking { background: #e2e8f0; color: #334155; padding: 2px 6px; border-radius: 4px; font-size: 10px; margin-left: 5px; vertical-align: middle;}
        .badge-tipe-online { background: #e0e7ff; color: #3730a3; padding: 2px 6px; border-radius: 4px; font-size: 10px; margin-left: 5px; vertical-align: middle;}

        /* MODAL POP-UP */
        .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; display: none; align-items: center; justify-content: center; backdrop-filter: blur(4px); }
        .modal-content { background: white; width: 90%; max-width: 500px; border-radius: 16px; padding: 30px; position: relative; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); }
        .modal-close { position: absolute; top: 20px; right: 20px; background: #f1f5f9; border: none; width: 30px; height: 30px; border-radius: 50%; font-weight: bold; color: #64748b; cursor: pointer; }
        .modal-close:hover { background: #e2e8f0; color: #0f172a; }
        .info-group { margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid var(--border); }
        .info-label { font-size: 12px; color: #64748b; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 5px; }
        .info-value { font-size: 15px; color: var(--text); font-weight: 600; line-height: 1.5; }

        /* --- RESPONSIVE MOBILE --- */
        @media (max-width: 768px) {
            .table-wrapper { overflow-x: auto; -webkit-overflow-scrolling: touch; }
            table { min-width: 800px; }
        }
    </style>

    @if(session('success'))
        <div class="alert-success">✅ {{ session('success') }}</div>
    @endif

    @php 
        $currentStatus = request('status', 'Semua'); 
        $currentTipe = request('tipe', 'Semua'); 
    @endphp
    
    <div class="filter-header">
        <div class="tabs-container">
            <a href="{{ route('admin.orders.index', ['tipe' => $currentTipe]) }}" class="tab-btn {{ $currentStatus == 'Semua' ? 'tab-active' : '' }}">Semua Order</a>
            <a href="{{ route('admin.orders.index', ['status' => 'Belum Bayar', 'tipe' => $currentTipe]) }}" class="tab-btn {{ $currentStatus == 'Belum Bayar' ? 'tab-active' : '' }}">⏳ Belum Bayar / Diambil</a>
            <a href="{{ route('admin.orders.index', ['status' => 'Diproses', 'tipe' => $currentTipe]) }}" class="tab-btn {{ $currentStatus == 'Diproses' ? 'tab-active' : '' }}">📦 Siap Dikirim</a>
            <a href="{{ route('admin.orders.index', ['status' => 'Dikirim', 'tipe' => $currentTipe]) }}" class="tab-btn {{ $currentStatus == 'Dikirim' ? 'tab-active' : '' }}">🚚 Selesai (Dikirim)</a>
            <a href="{{ route('admin.orders.index', ['status' => 'Dibatalkan', 'tipe' => $currentTipe]) }}" class="tab-btn {{ $currentStatus == 'Dibatalkan' ? 'tab-active' : '' }}" style="{{ $currentStatus == 'Dibatalkan' ? 'background: #ef4444; color: white;' : '' }}">❌ Dibatalkan</a>
        </div>

        <form action="{{ route('admin.orders.index') }}" method="GET" style="display: flex; align-items: center;">
            <input type="hidden" name="status" value="{{ $currentStatus }}">
            <select name="tipe" onchange="this.form.submit()" class="filter-select">
                <option value="Semua" {{ $currentTipe == 'Semua' ? 'selected' : '' }}>Semua Tipe Pesanan</option>
                <option value="Online" {{ $currentTipe == 'Online' ? 'selected' : '' }}>🌐 Pesanan Online</option>
                <option value="Booking" {{ $currentTipe == 'Booking' ? 'selected' : '' }}>🛍️ Booking (Ambil Toko)</option>
                <option value="POS Offline" {{ $currentTipe == 'POS Offline' ? 'selected' : '' }}>🛒 POS Kasir</option>
            </select>
        </form>
    </div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>ID & Waktu</th>
                    <th>Info Pelanggan</th>
                    <th>Tagihan & Detail</th>
                    <th>Status & Resi</th>
                    <th>Tindakan Admin</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td>
                            <div class="td-title">
                                #{{ $order->nomor_pesanan ?? $order->resi }}
                                @if($order->tipe_pesanan == 'Booking')
                                    <span class="badge-tipe-booking">BOOKING</span>
                                @elseif($order->tipe_pesanan == 'POS Offline')
                                    <span class="badge-tipe-booking" style="background: #fef08a; color: #92400e;">POS</span>
                                @else
                                    <span class="badge-tipe-online">ONLINE</span>
                                @endif
                            </div>
                            <div class="td-sub" style="color: var(--accent); font-weight: 600; margin-top: 4px;">⏱️ {{ \Carbon\Carbon::parse($order->created_at)->locale('id')->diffForHumans() }}</div>
                        </td>
                        <td>
                            <div class="td-title">{{ $order->nama_depan }} {{ $order->nama_belakang }}</div>
                            <div class="td-sub" style="margin-bottom: 6px;">📞 {{ $order->no_hp }}</div>
                            
                            <button type="button" onclick="bukaModal('{{ $order->id }}')" class="btn-detail">Lihat Alamat & Detail</button>
                        </td>
                        <td>
                            <div class="td-title" style="color: var(--accent);">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</div>
                            <div class="text-sm text-gray-700 mt-1 mb-1">
                                <ul style="list-style-type: none; padding-left: 0; margin-bottom: 0;">
                                    @foreach($order->details as $item)
                                        <li class="mb-1">
                                            <strong>{{ $item->jumlah }}x</strong> {{ $item->product->nama_produk }} <br>
                                            <span class="text-muted" style="font-size: 0.85em;">Size: {{ $item->ukuran }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            
                            @if($order->tipe_pesanan == 'Booking')
                                <div style="font-size: 11px; font-weight: 700; color: #15803d; margin-top: 8px; background: #dcfce3; padding: 4px 8px; border-radius: 4px; display: inline-block;">📍 Bayar Langsung di Toko</div>
                            @elseif($order->bukti_pembayaran == 'midtrans_verified')
                                <div style="font-size: 11px; font-weight: 700; color: #1d4ed8; margin-top: 8px; background: #dbeafe; padding: 4px 8px; border-radius: 4px; display: inline-block; border: 1px solid #bfdbfe;">✅ Lunas (Midtrans)</div>
                            @elseif($order->bukti_pembayaran)
                                <a href="{{ asset('images/bukti/' . $order->bukti_pembayaran) }}" target="_blank" class="btn-bukti">Lihat Struk ↗</a>
                            @endif
                        </td>
                        <td>
                            @php
                                $bc = '';
                                if($order->status == 'Belum Bayar') $bc = 'badge-belum-bayar';
                                elseif($order->status == 'Diproses') $bc = 'badge-diproses';
                                elseif($order->status == 'Dikirim') $bc = 'badge-dikirim';
                                elseif($order->status == 'Dibatalkan') $bc = 'badge-dibatalkan';
                            @endphp
                            <span class="badge {{ $bc }}">
                                {{ $order->status == 'Dikirim' && $order->tipe_pesanan == 'Booking' ? 'Selesai Diambil' : $order->status }}
                            </span>
                            @if($order->resi && $order->resi !== 'Diambil di Toko' && !str_starts_with($order->resi, 'POS-') && !str_starts_with($order->resi, 'ONL-') && !str_starts_with($order->resi, 'BKG-'))
                                <div style="font-size: 11px; font-weight: 700; color: #15803d; margin-top: 5px;">Resi: {{ $order->resi }}</div>
                            @endif
                        </td>
                        <td style="width: 180px;">
                            <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST" style="display: flex; flex-direction: column; gap: 6px;">
                                @csrf @method('PATCH')
                                
                                <select name="status" class="status-select">
                                    @if($order->tipe_pesanan == 'Booking')
                                        <option value="Belum Bayar" {{ $order->status == 'Belum Bayar' ? 'selected' : '' }}>Menunggu Diambil</option>
                                        <option value="Dikirim" {{ $order->status == 'Dikirim' ? 'selected' : '' }}>Selesai / Lunas</option>
                                        <option value="Dibatalkan" {{ $order->status == 'Dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                                    @elseif($order->tipe_pesanan == 'POS Offline')
                                        <option value="Dikirim" {{ $order->status == 'Dikirim' ? 'selected' : '' }}>Selesai</option>
                                        <option value="Dibatalkan" {{ $order->status == 'Dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                                    @else
                                        <option value="Belum Bayar" {{ $order->status == 'Belum Bayar' ? 'selected' : '' }}>Belum Bayar</option>
                                        <option value="Diproses" {{ $order->status == 'Diproses' ? 'selected' : '' }}>Diproses</option>
                                        <option value="Dikirim" {{ $order->status == 'Dikirim' ? 'selected' : '' }}>Dikirim</option>
                                        <option value="Dibatalkan" {{ $order->status == 'Dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                                    @endif
                                </select>
                                
                                @if($order->tipe_pesanan == 'Online')
                                    <input type="text" name="resi" value="{{ (str_starts_with($order->resi, 'ONL-') ? '' : $order->resi) }}" placeholder="Input Resi Pengiriman..." class="status-select">
                                @else
                                    <input type="hidden" name="resi" value="{{ $order->resi }}">
                                @endif
                                
                                <button type="submit" class="btn-update">Simpan Perubahan</button>
                            </form>
                        </td>
                    </tr>

                    <div id="modal-{{ $order->id }}" class="modal-overlay">
                        <div class="modal-content">
                            <button class="modal-close" onclick="tutupModal('{{ $order->id }}')">✕</button>
                            <h2 style="margin: 0 0 20px 0; font-size: 20px; font-family: 'DM Serif Display', serif;">Detail Pesanan #{{ $order->nomor_pesanan ?? $order->resi }}</h2>
                            
                            <div class="info-group">
                                <div class="info-label">Tipe Pesanan</div>
                                <div class="info-value">
                                    @if($order->tipe_pesanan == 'Booking')
                                        🛍️ Ambil di Toko (Booking)
                                    @elseif($order->tipe_pesanan == 'POS Offline')
                                        🛒 Kasir Toko Fisik (POS)
                                    @else
                                        🚚 Kirim ke Alamat (Online)
                                    @endif
                                </div>
                            </div>
                            <div class="info-group">
                                <div class="info-label">Nama Pelanggan</div>
                                <div class="info-value">{{ $order->nama_depan }} {{ $order->nama_belakang }}</div>
                            </div>
                            <div class="info-group">
                                <div class="info-label">Kontak WhatsApp / No. HP</div>
                                <div class="info-value">{{ $order->no_hp }}</div>
                            </div>
                            <div class="info-group" style="border-bottom: none;">
                                <div class="info-label">Alamat / Keterangan</div>
                                <div class="info-value" style="background: #f8fafc; padding: 15px; border-radius: 8px; border: 1px solid var(--border); margin-top: 5px;">
                                    {{ $order->wilayah }}<br>
                                    <span style="font-size: 13px; color: #475569;">{{ $order->alamat_lengkap }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <tr><td colspan="5" style="text-align: center; padding: 40px; color: #64748b;">Belum ada pesanan pada kategori ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection

@push('scripts')
    <script>
        function bukaModal(id) {
            document.getElementById('modal-' + id).style.display = 'flex';
        }
        function tutupModal(id) {
            document.getElementById('modal-' + id).style.display = 'none';
        }
    </script>
@endpush