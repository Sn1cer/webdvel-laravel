@extends('layouts.app')

@section('title', "Kupon Booking - D'Vel Jeans")

@push('styles')
<style>
    /* --- CSS KHUSUS HALAMAN BOOKING --- */
    .payment-wrapper { display: flex; justify-content: center; align-items: center; min-height: 80vh; padding: 40px 20px; }
    .payment-card { background: white; padding: 40px; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); text-align: center; max-width: 550px; width: 100%; border: 1px solid #e2e8f0; box-sizing: border-box;}
    
    .icon-success { font-size: 60px; margin-bottom: 10px; }
    .payment-card h1 { font-family: 'DM Serif Display', serif; font-size: 28px; margin-bottom: 5px; color: #1e293b; line-height: 1.3;}
    .order-id { color: #64748b; font-size: 14px; margin-bottom: 25px; font-weight: 600;}

    /* Kotak Peringatan Waktu */
    .alert-warning { background: #fffbeb; border: 1px dashed #d97706; padding: 20px; border-radius: 12px; margin-bottom: 25px; text-align: center; line-height: 1.5; color: #92400e; font-size: 14px;}
    .deadline-time { font-size: 18px; font-weight: 800; color: #b45309; display: block; margin-top: 5px;}

    /* --- CSS STRUK RINCIAN BARANG --- */
    .receipt-container { background: #f8fafc; padding: 25px; border-radius: 12px; border: 1px solid #e2e8f0; text-align: left; margin-top: 20px; margin-bottom: 20px;}
    .receipt-item { display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 13px; color: #334155;}
    .receipt-item strong { color: #1e293b; }
    .receipt-total { display: flex; justify-content: space-between; margin-top: 15px; padding-top: 15px; border-top: 2px dashed #cbd5e1; font-weight: 800; font-size: 16px; color: #1e293b;}
    
    .btn-home { color: #64748b; text-decoration: none; font-weight: 600; display: inline-block; font-size: 14px; transition: 0.2s; padding: 10px 20px; border: 1px solid #cbd5e1; border-radius: 8px;}
    .btn-home:hover { color: #1e293b; background: #f1f5f9; }

    @media (max-width: 768px) {
        .payment-wrapper { padding: 20px 15px; }
        .payment-card { padding: 30px 20px; }
        .payment-card h1 { font-size: 24px; }
    }
</style>
@endpush

@section('content')
    <div class="payment-wrapper">
        <div class="payment-card">
            
            @if($order->status == 'Dibatalkan')
                <div class="icon-success">❌</div>
                <h1 style="color: #ef4444;">Booking Dibatalkan</h1>
                <div class="order-id">Order ID: #{{ $order->nomor_pesanan }}</div>
                <p style="color: #64748b; font-size: 14px;">Pesanan ini telah dibatalkan karena melewati batas waktu pengambilan atau dibatalkan oleh Admin.</p>
            @else
                <div class="icon-success">🛍️</div>
                <h1>Booking Berhasil!</h1>
                <div class="order-id">Order ID: #{{ $order->nomor_pesanan }}</div>
                
                <p style="color: #475569; font-size: 14px; margin-bottom: 20px;">
                    Silakan tunjukkan halaman ini (atau ID Pesanan) ke kasir toko fisik D'Vel Jeans untuk mengambil dan membayar pesanan Anda.
                </p>

                <div class="alert-warning">
                    ⚠️ Harap ambil pesanan Anda sebelum:<br>
                    <span class="deadline-time">{{ $order->created_at->addHours(24)->format('d M Y, H:i') }} WIB</span>
                    <span style="display: block; margin-top: 5px; font-size: 12px; color: #b45309;">Lewat dari waktu tersebut, sistem akan membatalkan pesanan secara otomatis.</span>
                </div>

                <div class="receipt-container">
                    <div style="font-weight: 800; font-size: 14px; margin-bottom: 15px; border-bottom: 1px solid #cbd5e1; padding-bottom: 10px;">Rincian Barang yang Di-booking:</div>
                    
                    @foreach($order->details as $detail)
                    <div class="receipt-item">
                        <div>
                            <strong>{{ $detail->product->nama_produk ?? 'Produk' }}</strong><br>
                            Size: {{ $detail->ukuran }} &nbsp;|&nbsp; {{ $detail->jumlah }} x Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}
                        </div>
                        <div style="font-weight: 600;">
                            Rp {{ number_format($detail->jumlah * $detail->harga_satuan, 0, ',', '.') }}
                        </div>
                    </div>
                    @endforeach
                    
                    <div class="receipt-total">
                        <div>TOTAL BAYAR DI TOKO</div>
                        <div style="color: #d97706;">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</div>
                    </div>
                </div>
            @endif

            <a href="{{ route('orders.history') }}" class="btn-home">← Lihat Riwayat Pesanan</a>
        </div>
    </div>
@endsection