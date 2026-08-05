<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Saya - D'Vel Jeans</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=DM+Serif+Display&display=swap" rel="stylesheet">
    <link rel="icon" href="{{ asset('Dvel/logo.png') }}" type="image/png">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f8fafc; color: #1e293b; margin: 0; padding: 0; }
        
        /* Navbar Simple Pelanggan */
        .navbar { background: white; padding: 20px 50px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; box-shadow: 0 2px 10px rgba(0,0,0,0.02);}
        .logo { font-family: 'DM Serif Display', serif; font-size: 24px; color: #1e293b; text-decoration: none; }
        .nav-links a { color: #64748b; text-decoration: none; margin-left: 25px; font-weight: 600; transition: 0.2s;}
        .nav-links a:hover { color: #d97706; }
        
        .container { max-width: 900px; margin: 50px auto; padding: 0 20px; }
        .page-title { font-size: 28px; font-weight: 800; margin-bottom: 5px; font-family: 'DM Serif Display', serif;}
        .page-subtitle { color: #64748b; margin-bottom: 30px; font-size: 15px; }

        /* Kartu Pesanan */
        .order-card { background: white; border-radius: 12px; padding: 25px; margin-bottom: 20px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px rgba(0,0,0,0.02); display: flex; justify-content: space-between; align-items: center;}
        
        .order-info { flex: 1; }
        .order-id { font-weight: 800; font-size: 18px; color: #1e293b; margin-bottom: 5px; }
        .order-date { color: #64748b; font-size: 13px; margin-bottom: 15px; }
        .order-price { font-weight: 800; color: #d97706; font-size: 16px; }

        .order-status { text-align: right; }
        .badge { padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; display: inline-block; margin-bottom: 10px;}
        .badge-belum-bayar { background: #fef3c7; color: #b45309; }
        .badge-diproses { background: #dbeafe; color: #1d4ed8; }
        .badge-dikirim { background: #dcfce3; color: #15803d; }
        .badge-dibatalkan { background: #fee2e2; color: #b91c1c; } /* Penambahan CSS Dibatalkan */

        .btn-action { background: #1e293b; color: white; padding: 8px 16px; border-radius: 8px; text-decoration: none; font-size: 13px; font-weight: 600; display: inline-block; transition: 0.2s;}
        .btn-action:hover { background: #334155; }
        .btn-upload-susulan { background: #d97706; color: white; padding: 8px 16px; border-radius: 8px; text-decoration: none; font-size: 13px; font-weight: 600; display: inline-block; transition: 0.2s;}
        .btn-upload-susulan:hover { background: #b45309; }

        .empty-state { text-align: center; padding: 60px 20px; background: white; border-radius: 12px; border: 1px dashed #cbd5e1; }
        .empty-state h3 { margin: 10px 0; color: #1e293b; }
        .empty-state p { color: #64748b; margin-bottom: 20px; }
    </style>
</head>
<body>

    <nav class="navbar">
        <a href="/" class="logo">D'Vel Jeans</a>
        <div class="nav-links">
            <a href="/">Beranda</a>
            <a href="/keranjang">Keranjang</a>
            <a href="{{ route('orders.history') }}" style="color: #d97706;">Pesanan Saya</a>
        </div>
    </nav>

    <div class="container">
        <h1 class="page-title">Riwayat Pesanan Saya</h1>
        <p class="page-subtitle">Pantau status pesanan dan pengiriman celana jeans Anda di sini.</p>

        @forelse($orders as $order)
            <div class="order-card">
                <div class="order-info">
                    <div class="order-id">Order #{{ $order->nomor_pesanan }}
                        @if($order->tipe_pesanan == 'Booking')
                            <span style="font-size: 10px; background: #e2e8f0; color: #475569; padding: 2px 6px; border-radius: 4px; vertical-align: middle; margin-left: 5px;">BOOKING TOKO</span>
                        @endif
                    </div>
                    <div class="order-date">{{ $order->created_at->format('d M Y, H:i') }} WIB</div>
                    <div class="order-price">Total: Rp {{ number_format($order->total_harga, 0, ',', '.') }}</div>
                </div>

                <div class="order-status">
                    @php
                        $badgeClass = '';
                        if($order->status == 'Belum Bayar') {
                            $badgeClass = 'badge-belum-bayar';
                        } elseif($order->status == 'Diproses') {
                            $badgeClass = 'badge-diproses';
                        } elseif($order->status == 'Dikirim') {
                            $badgeClass = 'badge-dikirim';
                        } elseif($order->status == 'Dibatalkan') {
                            $badgeClass = 'badge-dibatalkan';
                        }
                    @endphp
                    
                    <span class="badge {{ $badgeClass }}">{{ $order->status }}</span>
                    <br>
                    
                    @if($order->resi && $order->tipe_pesanan == 'Online' && !str_starts_with($order->resi, 'ONL-'))
                        <div style="margin-top: 5px; margin-bottom: 12px; padding: 8px 15px; background: #fffbeb; border: 1px dashed #d97706; border-radius: 8px; font-size: 12px; text-align: right; display: inline-block;">
                            <span style="color: #b45309; font-weight: 600; display: block; margin-bottom: 2px;">🚚 No. Resi Pengiriman:</span>
                            <span style="color: #d97706; font-weight: 800; font-size: 15px; letter-spacing: 1px;">{{ $order->resi }}</span>
                        </div>
                        <br>
                    @endif

                    <!-- JIKA PESANAN DIBATALKAN/EXPIRED -->
                    @if($order->status == 'Dibatalkan')
                        <span style="display: block; font-size: 12px; color: #b91c1c; font-weight: 700; margin-top: 10px;">Batas Waktu Habis / Dibatalkan</span>
                    
                    <!-- JIKA BELUM BAYAR -->
                    @elseif($order->status == 'Belum Bayar' && empty($order->bukti_pembayaran))
                        
                        @if($order->tipe_pesanan == 'Booking')
                            <div style="text-align: right;">
                                <span style="display: block; font-size: 12px; color: #15803d; font-weight: 800; margin-bottom: 6px;">📍 Bayar Langsung di Toko</span>
                                <a href="{{ route('booking.success', $order->id) }}" class="btn-action" style="background: #0f172a;">Lihat Kupon Booking</a>
                            </div>
                        @else
                            <!-- Tombol disesuaikan dari Upload Bukti Bayar menjadi Lanjutkan Pembayaran -->
                            <a href="{{ route('checkout.success', $order->id) }}" class="btn-upload-susulan">Lanjutkan Pembayaran</a>
                        @endif

                    <!-- JIKA SUDAH DIPROSES/DIKIRIM -->
                    @else
                        
                        @if($order->tipe_pesanan == 'Booking')
                            <a href="{{ route('booking.success', $order->id) }}" class="btn-action">Lihat Detail Booking</a>
                        @else
                            <a href="{{ route('checkout.success', $order->id) }}" class="btn-action">Lihat Detail</a>
                        @endif

                    @endif
                </div>
            </div>
        @empty
            <div class="empty-state">
                <div style="font-size: 50px; margin-bottom: 15px;">🛍️</div>
                <h3>Belum Ada Pesanan</h3>
                <p>Anda belum pernah melakukan pemesanan celana jeans.</p>
                <a href="/" class="btn-action" style="padding: 12px 24px;">Belanja Sekarang</a>
            </div>
        @endforelse

    </div>

</body>
</html>