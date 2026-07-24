@extends('layouts.app')

@section('title', "Pembayaran - D'Vel Jeans")

@push('styles')
<style>
    /* --- CSS KHUSUS HALAMAN PEMBAYARAN --- */
    .payment-wrapper { display: flex; justify-content: center; align-items: center; min-height: 80vh; padding: 40px 20px; }
    
    .payment-card { background: white; padding: 40px; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); text-align: center; max-width: 550px; width: 100%; border: 1px solid #e2e8f0; box-sizing: border-box;}
    
    .icon-success { font-size: 60px; margin-bottom: 10px; }
    .payment-card h1 { font-family: 'DM Serif Display', serif; font-size: 28px; margin-bottom: 5px; color: #1e293b; line-height: 1.3;}
    .order-id { color: #64748b; font-size: 14px; margin-bottom: 25px; font-weight: 600;}
    
    /* Kotak Tagihan */
    .bill-box { background: #fffbeb; border: 1px dashed #d97706; padding: 20px; border-radius: 12px; margin-bottom: 25px; }
    .bill-box p { margin: 0 0 5px 0; color: #78350f; font-size: 14px; font-weight: 600;}
    .total-amount { font-size: 32px; font-weight: 800; color: #d97706; }

    /* Tombol Bayar Midtrans */
    .btn-pay { background: #1e293b; color: white; border: none; padding: 16px; width: 100%; border-radius: 8px; font-size: 16px; font-weight: 800; cursor: pointer; transition: 0.2s; text-transform: uppercase; letter-spacing: 1px; box-sizing: border-box; box-shadow: 0 4px 15px rgba(30, 41, 59, 0.2);}
    .btn-pay:hover { background: #d97706; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(217, 119, 6, 0.3);}

    .alert-success { background: #dcfce3; color: #166534; padding: 20px; border-radius: 8px; font-weight: 600; border: 1px solid #bbf7d0; margin-top: 20px; line-height: 1.5; font-size: 15px;}
    .btn-home { color: #64748b; text-decoration: none; font-weight: 600; display: inline-block; margin-top: 25px; font-size: 14px; transition: 0.2s;}
    .btn-home:hover { color: #d97706; }

    /* --- CSS STRUK JIKA LUNAS --- */
    .receipt-container { background: white; padding: 30px; border-radius: 12px; border: 1px solid #e2e8f0; text-align: left; margin-top: 20px;}
    .receipt-header { text-align: center; border-bottom: 1px dashed #cbd5e1; padding-bottom: 15px; margin-bottom: 15px; }
    .receipt-logo { font-family: 'DM Serif Display', serif; font-size: 24px; color: #1e293b; letter-spacing: 1px; }
    .receipt-date { font-size: 12px; color: #64748b; margin-top: 5px; }
    
    .receipt-item { display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 13px; color: #334155;}
    .receipt-item strong { color: #1e293b; }
    .receipt-total { display: flex; justify-content: space-between; margin-top: 15px; padding-top: 15px; border-top: 1px dashed #cbd5e1; font-weight: 800; font-size: 16px; color: #1e293b;}
    
    .btn-print { background: #10b981; color: white; padding: 12px; border-radius: 8px; border: none; font-weight: 700; width: 100%; margin-top: 15px; cursor: pointer; text-transform: uppercase; font-size: 14px;}
    .btn-print:hover { background: #059669; }

    /* --- RESPONSIVE MOBILE --- */
    @media (max-width: 768px) {
        .payment-wrapper { padding: 20px 15px; }
        .payment-card { padding: 30px 20px; }
        .payment-card h1 { font-size: 24px; }
        .total-amount { font-size: 28px; }
    }

    /* --- PENGATURAN PDF/PRINT (DIPERBARUI) --- */
    @media print {
        @page {
            margin: 1cm; 
            size: auto;
        }

        body, html {
            background: white !important;
            margin: 0 !important;
            padding: 0 !important;
            height: auto !important; 
        }

        body * { visibility: hidden; }
        
        .payment-wrapper {
            min-height: auto;
            padding: 0;
            align-items: flex-start;
        }

        .payment-card, .payment-card * { visibility: visible; }
        
        .payment-card { 
            position: absolute; 
            left: 0; 
            top: 0; 
            width: 100%; 
            max-width: 100%; 
            border: none; 
            box-shadow: none; 
            padding: 0;
            margin: 0;
            page-break-inside: avoid; 
        }

        .receipt-container {
            border: 2px dashed #cbd5e1 !important; 
            margin-bottom: 0;
        }
        .btn-print, .btn-home, #pay-button { display: none !important; }
    }
</style>
@endpush

@section('content')
    <div class="payment-wrapper">
        <div class="payment-card">
            
            @if($order->status == 'Belum Bayar')
                <div class="icon-success">💳</div>
                <h1>Selesaikan Pembayaran</h1>
                <div class="order-id">Order ID: #{{ $order->nomor_pesanan }}</div>
                
                <div class="bill-box">
                    <p>Total Tagihan Pesanan:</p>
                    <div class="total-amount">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</div>
                </div>

                <div style="margin-bottom: 15px; color: #475569; font-size: 14px; line-height: 1.5;">
                    Silakan klik tombol di bawah ini untuk memilih metode pembayaran (Transfer Bank, QRIS, GoPay, dll).
                </div>
                
                <button id="pay-button" class="btn-pay">💳 Pilih Metode Pembayaran</button>
                <a href="{{ route('orders.history') }}" class="btn-home">← Nanti saja, lihat Daftar Pesanan</a>

            @else
                <div class="icon-success" style="color: #10b981;">✅</div>
                <h1 style="color: #10b981;">Pembayaran Berhasil!</h1>
                <div style="font-size: 14px; color: #64748b; margin-bottom: 20px;">Terima kasih, pesanan Anda lunas dan sedang diproses.</div>
                
                <div class="receipt-container">
                    <div class="receipt-header">
                        <div class="receipt-logo">D'VEL JEANS</div>
                        <div class="receipt-date">Tanggal: {{ $order->created_at->format('d M Y, H:i') }}<br>No. Pesanan: #{{ $order->nomor_pesanan }}</div>
                    </div>
                    
                    <div style="margin-bottom: 15px; font-size: 13px;">
                        <strong>Penerima:</strong> {{ $order->nama_depan }} {{ $order->nama_belakang }}<br>
                        <strong>Alamat:</strong> {{ $order->wilayah }}
                    </div>

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
                    
                    @if($order->ongkir > 0)
                    <div class="receipt-item" style="margin-top: 10px;">
                        <div><strong>Ongkos Kirim</strong></div>
                        <div style="font-weight: 600;">Rp {{ number_format($order->ongkir, 0, ',', '.') }}</div>
                    </div>
                    @endif
                    
                    <div class="receipt-total">
                        <div>TOTAL DIBAYAR</div>
                        <div style="color: #d97706;">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</div>
                    </div>
                </div>
                
                <button class="btn-print" onclick="window.print()">🖨️ Cetak Bukti Pembayaran</button>
                <br>
                <a href="{{ route('orders.history') }}" class="btn-home">← Lihat Riwayat Pesanan</a>
            @endif

        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
    
    <script type="text/javascript">
        @if($order->status == 'Belum Bayar' && $order->snap_token)
            document.getElementById('pay-button').onclick = function () {
                window.snap.pay('{{ $order->snap_token }}', {
                    onSuccess: function(result){
                        window.location.reload(); 
                    },
                    onPending: function(result){
                        alert("Menunggu pembayaran Anda! Silakan selesaikan pembayaran sesuai instruksi pada layar.");
                    },
                    onError: function(result){
                        alert("Maaf, terjadi kesalahan pada pembayaran Anda.");
                    }
                });
            };
        @endif
    </script>
@endpush