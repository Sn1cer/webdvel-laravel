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

    /* --- RESPONSIVE MOBILE --- */
    @media (max-width: 768px) {
        .payment-wrapper { padding: 20px 15px; }
        .payment-card { padding: 30px 20px; }
        .payment-card h1 { font-size: 24px; }
        .total-amount { font-size: 28px; }
    }
</style>
@endpush

@section('content')
    <div class="payment-wrapper">
        <div class="payment-card">
            <div class="icon-success">💳</div>
            <h1>Selesaikan Pembayaran</h1>
            <div class="order-id">Order ID: #{{ $order->nomor_pesanan }}</div>
            
            <div class="bill-box">
                <p>Total Tagihan Pesanan:</p>
                <div class="total-amount">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</div>
            </div>

            @if($order->status == 'Belum Bayar')
                <div style="margin-bottom: 15px; color: #475569; font-size: 14px; line-height: 1.5;">
                    Silakan klik tombol di bawah ini untuk memilih metode pembayaran (Transfer Bank, QRIS, GoPay, dll).
                </div>
                
                <button id="pay-button" class="btn-pay">💳 Pilih Metode Pembayaran</button>
            @else
                <div class="alert-success">
                    ✅ Pembayaran Berhasil!<br>
                    <span style="font-size: 13px; font-weight: 400; color: #15803d; margin-top: 5px; display: block;">Pesanan Anda sedang kami proses dan akan segera dikirim.</span>
                </div>
            @endif

            <a href="{{ route('orders.history') }}" class="btn-home">← Cek Status Pesanan Saya</a>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
    
    <script type="text/javascript">
        @if($order->status == 'Belum Bayar' && $order->snap_token)
            document.getElementById('pay-button').onclick = function () {
                // Memanggil pop-up Snap menggunakan token dari database
                window.snap.pay('{{ $order->snap_token }}', {
                    onSuccess: function(result){
                        /* Saat pembayaran berhasil */
                        alert("Pembayaran Berhasil! Pesanan Anda akan segera diproses.");
                        // Refresh halaman untuk melihat status berubah
                        window.location.reload(); 
                    },
                    onPending: function(result){
                        /* Saat pelanggan memilih metode bayar yang butuh waktu (misal: Transfer ATM) */
                        alert("Menunggu pembayaran Anda! Silakan selesaikan pembayaran sesuai instruksi.");
                    },
                    onError: function(result){
                        /* Saat pembayaran gagal */
                        alert("Maaf, terjadi kesalahan pada pembayaran Anda.");
                    },
                    onClose: function(){
                        /* Saat pelanggan menutup pop-up tanpa membayar */
                        alert('Anda menutup jendela pembayaran sebelum menyelesaikannya.');
                    }
                });
            };
        @endif
    </script>
@endpush