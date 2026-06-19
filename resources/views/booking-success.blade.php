@extends('layouts.app')

@section('title', "Booking Berhasil - D'Vel Jeans")

@push('styles')
<style>
    .success-container {
        max-width: 600px;
        margin: 60px auto;
        background: white;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        border: 1px solid var(--border);
        overflow: hidden;
        text-align: center;
    }
    .success-header {
        background: #dcfce7;
        padding: 40px 20px;
        border-bottom: 1px dashed #bbf7d0;
    }
    .success-icon {
        width: 80px;
        height: 80px;
        background: #22c55e;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 36px;
        margin: 0 auto 20px;
        box-shadow: 0 4px 15px rgba(34, 197, 94, 0.3);
    }
    .success-title {
        font-size: 24px;
        font-weight: 800;
        color: #166534;
        margin-bottom: 10px;
    }
    .success-subtitle {
        color: #15803d;
        font-size: 15px;
    }
    .success-body {
        padding: 40px;
    }
    .order-card {
        background: #f8fafc;
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 30px;
        text-align: left;
    }
    .order-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 15px;
        padding-bottom: 15px;
        border-bottom: 1px solid #e2e8f0;
    }
    .order-row:last-child {
        margin-bottom: 0;
        padding-bottom: 0;
        border-bottom: none;
    }
    .row-label {
        color: #64748b;
        font-size: 14px;
        font-weight: 500;
    }
    .row-value {
        color: var(--text);
        font-size: 15px;
        font-weight: 700;
        text-align: right;
    }
    .instruction-box {
        background: #fff8f1;
        border-left: 4px solid var(--accent);
        padding: 20px;
        text-align: left;
        border-radius: 4px;
        margin-bottom: 30px;
    }
    .instruction-title {
        font-weight: 800;
        color: var(--accent);
        margin-bottom: 10px;
        font-size: 16px;
    }
    .instruction-list {
        margin: 0;
        padding-left: 20px;
        color: #475569;
        font-size: 14px;
        line-height: 1.6;
    }
    .btn-action {
        display: inline-block;
        padding: 14px 30px;
        background: var(--text);
        color: white;
        text-decoration: none;
        border-radius: 8px;
        font-weight: 700;
        transition: 0.2s;
        width: 100%;
        box-sizing: border-box;
    }
    .btn-action:hover {
        background: var(--accent);
        transform: translateY(-2px);
    }
</style>
@endpush

@section('content')
<div class="container" style="padding: 0 20px;">
    <div class="success-container">
        <div class="success-header">
            <div class="success-icon">✓</div>
            <h1 class="success-title">Booking Berhasil!</h1>
            <p class="success-subtitle">Stok barang Anda telah kami amankan di toko.</p>
        </div>

        <div class="success-body">
            
            <div class="order-card">
                <div class="order-row">
                    <span class="row-label">Nomor Pesanan</span>
                    <span class="row-value">#ORD-{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</span>
                </div>
                <div class="order-row">
                    <span class="row-label">Tanggal Booking</span>
                    <span class="row-value">{{ $order->created_at->format('d M Y, H:i') }} WIB</span>
                </div>
                <div class="order-row">
                    <span class="row-label">Nama Pemesan</span>
                    <span class="row-value">{{ $order->nama_depan }} {{ $order->nama_belakang }}</span>
                </div>
                <div class="order-row" style="border-top: 2px dashed #cbd5e1; padding-top: 15px;">
                    <span class="row-label" style="font-size: 16px; color: var(--text);">Total Tagihan</span>
                    <span class="row-value" style="font-size: 20px; color: var(--accent);">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="instruction-box">
                <div class="instruction-title">📌 Instruksi Pengambilan:</div>
                <ul class="instruction-list">
                    <li>Silakan datang ke toko fisik <strong>D'Vel Jeans Cimahi</strong>.</li>
                    <li>Tunjukkan halaman ini (atau dari Riwayat Pesanan) ke kasir kami.</li>
                    <li>Lakukan pembayaran sebesar <strong>Rp {{ number_format($order->total_harga, 0, ',', '.') }}</strong> langsung di meja kasir (Tunai / Debit / QRIS).</li>
                    <li>Pesanan booking akan otomatis dibatalkan jika tidak diambil dalam waktu <strong>1x24 jam</strong>.</li>
                </ul>
            </div>

            <a href="{{ route('orders.history') }}" class="btn-action">Lihat Riwayat Pesanan</a>
            
            <div style="margin-top: 20px;">
                <a href="{{ url('/') }}" style="color: #64748b; text-decoration: underline; font-size: 14px;">Kembali ke Beranda</a>
            </div>
        </div>
    </div>
</div>
@endsection