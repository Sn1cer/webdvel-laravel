@extends('layouts.app')

@section('title', "Instruksi Pembayaran - D'Vel Jeans")

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

    /* Rekening Bank */
    .bank-details { text-align: left; background: #f1f5f9; padding: 20px; border-radius: 12px; margin-bottom: 25px; }
    .bank-name { font-weight: 800; font-size: 18px; color: #0f172a; margin-bottom: 5px;}
    .bank-account { font-size: 22px; font-weight: 700; color: #d97706; margin-bottom: 5px; letter-spacing: 1px;}
    .bank-owner { font-size: 14px; color: #64748b; }

    /* Form Upload */
    .upload-area { border-top: 1px solid #e2e8f0; padding-top: 25px; margin-top: 10px;}
    .upload-label { display: block; font-weight: 700; margin-bottom: 10px; text-align: left;}
    .file-input { width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; margin-bottom: 15px; background: #f8fafc; box-sizing: border-box;}
    .btn-upload { background: #1e293b; color: white; border: none; padding: 14px; width: 100%; border-radius: 8px; font-weight: 700; cursor: pointer; transition: 0.2s; text-transform: uppercase; letter-spacing: 1px; box-sizing: border-box;}
    .btn-upload:hover { background: #d97706; }

    .alert-success { background: #dcfce3; color: #166534; padding: 15px; border-radius: 8px; font-weight: 600; border: 1px solid #bbf7d0; margin-top: 20px; line-height: 1.5;}
    .btn-home { color: #64748b; text-decoration: none; font-weight: 600; display: inline-block; margin-top: 20px; font-size: 14px; transition: 0.2s;}
    .btn-home:hover { color: #d97706; }

    /* --- RESPONSIVE MOBILE --- */
    @media (max-width: 768px) {
        .payment-wrapper { padding: 20px 15px; }
        .payment-card { padding: 30px 20px; }
        .payment-card h1 { font-size: 24px; }
        .total-amount { font-size: 28px; }
        .bank-account { font-size: 20px; }
    }
</style>
@endpush

@section('content')
    <div class="payment-wrapper">
        <div class="payment-card">
            <div class="icon-success">🧾</div>
            <h1>Pesanan Berhasil Dibuat!</h1>
            <div class="order-id">Order ID: #ORD-{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</div>
            
            <div class="bill-box">
                <p>Total yang harus ditransfer:</p>
                <div class="total-amount">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</div>
            </div>

            <div class="bank-details">
                <div class="bank-name">BCA (Bank Central Asia)</div>
                <div class="bank-account">123-456-7890</div>
                <div class="bank-owner">a.n. D'Vel Jeans Cimahi</div>
            </div>

            @if(empty($order->bukti_pembayaran))
                <div class="upload-area">
                    <label class="upload-label">Sudah Transfer? Unggah Bukti di sini:</label>
                    <form action="{{ route('checkout.uploadBukti', $order->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="file" name="bukti_pembayaran" class="file-input" accept="image/jpeg, image/png, image/jpg" required>
                        <button type="submit" class="btn-upload">Konfirmasi Pembayaran</button>
                    </form>
                </div>
            @else
                <div class="alert-success">
                    ✅ Bukti pembayaran telah diterima! Pesanan Anda sedang diverifikasi oleh Admin.
                </div>
            @endif

            <a href="{{ route('orders.history') }}" class="btn-home">← Cek Status Pesanan Saya</a>
        </div>
    </div>
@endsection