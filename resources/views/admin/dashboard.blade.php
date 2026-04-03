@extends('layouts.admin')

@section('title', "Dashboard - Admin D'Vel Jeans")
@section('topbar_title', 'Ringkasan Statistik Toko')

@section('content')
    <style>
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 25px; border-radius: 12px; border: 1px solid var(--border); display: flex; align-items: flex-start; gap: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.02); transition: 0.2s; }
        .stat-card:hover { transform: translateY(-5px); box-shadow: 0 10px 15px rgba(0,0,0,0.05); }
        .stat-icon { font-size: 32px; background: #f1f5f9; width: 60px; height: 60px; border-radius: 12px; display: flex; align-items: center; justify-content: center; }
        .stat-info { flex: 1; }
        .stat-title { font-size: 13px; color: #64748b; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 5px; }
        .stat-value { font-size: 26px; font-weight: 800; color: var(--text); }
        
        .welcome-banner { background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: white; padding: 30px; border-radius: 12px; margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center;}
        .welcome-text h2 { margin: 0 0 10px 0; font-family: 'DM Serif Display', serif; font-size: 28px;}
        .welcome-text p { margin: 0; color: #cbd5e1; font-size: 15px;}
        .btn-quick { background: var(--accent); color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 700; transition: 0.2s;}
        .btn-quick:hover { filter: brightness(1.1); }
    </style>

    <div class="welcome-banner">
        <div class="welcome-text">
            <h2>Halo, Bos! 👋</h2>
            <p>Berikut adalah ringkasan performa D'Vel Jeans hari ini.</p>
        </div>
        <a href="{{ route('products.create') }}" class="btn-quick">+ Tambah Celana Baru</a>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">💰</div>
            <div class="stat-info">
                <div class="stat-title">Pendapatan Bulan Ini</div>
                <div class="stat-value" style="color: var(--accent);">Rp {{ number_format($pendapatanBulanIni, 0, ',', '.') }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: #fef3c7;">⏳</div>
            <div class="stat-info">
                <div class="stat-title">Menunggu Pembayaran</div>
                <div class="stat-value">{{ $pesananMenunggu }} <span style="font-size: 14px; color: #64748b; font-weight: 600;">Order</span></div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: #dbeafe;">🚚</div>
            <div class="stat-info">
                <div class="stat-title">Perlu Dikirim</div>
                <div class="stat-value">{{ $pesananDiproses }} <span style="font-size: 14px; color: #64748b; font-weight: 600;">Order</span></div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: #dcfce3;">👖</div>
            <div class="stat-info">
                <div class="stat-title">Total Stok Gudang</div>
                <div class="stat-value">{{ $totalStok }} <span style="font-size: 14px; color: #64748b; font-weight: 600;">Pcs</span></div>
            </div>
        </div>
    </div>

    <div style="background: white; border-radius: 12px; border: 1px solid var(--border); padding: 25px; margin-bottom: 30px; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="margin: 0; font-size: 16px; color: var(--text);">📈 Tren Pendapatan (7 Hari Terakhir)</h3>
        </div>
        <div style="height: 300px; width: 100%; position: relative;">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>

    <div class="grid-container-responsive" style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px;">
        
        <div style="background: white; border-radius: 12px; border: 1px solid var(--border); padding: 25px;">
            <h3 style="margin: 0 0 20px 0; font-size: 16px; color: var(--text);">⚡ Aktivitas Pesanan Terkini</h3>
            <div class="table-responsive">
                <table style="width: 100%; border-collapse: collapse; text-align: left; min-width: 400px;">
                    <thead>
                        <tr style="border-bottom: 2px solid var(--border);">
                            <th style="padding-bottom: 10px; font-size: 12px; color: #64748b;">Pelanggan</th>
                            <th style="padding-bottom: 10px; font-size: 12px; color: #64748b;">Total Tagihan</th>
                            <th style="padding-bottom: 10px; font-size: 12px; color: #64748b;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($aktivitasTerkini as $order)
                            <tr style="border-bottom: 1px solid var(--border);">
                                <td style="padding: 15px 0;">
                                    <div style="font-weight: 700; font-size: 14px;">{{ $order->nama_depan }}</div>
                                    <div style="font-size: 12px; color: #64748b;">{{ \Carbon\Carbon::parse($order->created_at)->locale('id')->diffForHumans() }}</div>
                                </td>
                                <td style="font-weight: 700; color: var(--accent); font-size: 14px;">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</td>
                                <td>
                                    <span style="font-size: 11px; font-weight: 800; text-transform: uppercase; padding: 4px 8px; border-radius: 6px; 
                                        background: {{ $order->status == 'Belum Bayar' ? '#fef3c7' : ($order->status == 'Diproses' ? '#dbeafe' : '#dcfce3') }};
                                        color: {{ $order->status == 'Belum Bayar' ? '#b45309' : ($order->status == 'Diproses' ? '#1d4ed8' : '#15803d') }};">
                                        {{ $order->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" style="text-align: center; padding: 20px; color: #64748b; font-size: 14px;">Belum ada aktivitas.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div style="background: white; border-radius: 12px; border: 1px solid var(--border); padding: 25px;">
            <h3 style="margin: 0 0 20px 0; font-size: 16px; color: var(--text);">🚨 Peringatan Stok Menipis</h3>
            <div style="display: flex; flex-direction: column; gap: 15px;">
                @forelse($stokMenipis as $produk)
                    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px dashed var(--border); padding-bottom: 15px;">
                        <div>
                            <div style="font-weight: 700; font-size: 14px; margin-bottom: 4px;">{{ $produk->nama_produk }}</div>
                            <div style="font-size: 12px; color: #ef4444; font-weight: 600;">Sisa: {{ $produk->stok }} Pcs</div>
                        </div>
                        <a href="{{ route('products.edit', $produk->id) }}" style="background: #f1f5f9; color: #334155; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 12px; font-weight: 700;">Restock</a>
                    </div>
                @empty
                    <div style="text-align: center; color: #10b981; font-weight: 600; font-size: 14px; padding: 20px;">
                        ✅ Stok di gudang aman!
                    </div>
                @endforelse
            </div>
        </div>

    </div>
@endsection

@push('scripts')
<script>
    const ctx = document.getElementById('revenueChart').getContext('2d');
    
    const labels = {!! json_encode($chartDates) !!};
    const dataUang = {!! json_encode($chartRevenues) !!};

    new Chart(ctx, {
        type: 'line', 
        data: {
            labels: labels,
            datasets: [{
                label: 'Pendapatan Harian',
                data: dataUang,
                borderColor: '#d97706', 
                backgroundColor: 'rgba(217, 119, 6, 0.1)', 
                borderWidth: 3,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#d97706',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6,
                fill: true, 
                tension: 0.4 
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }, 
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) { label += ': Rp '; }
                            if (context.parsed.y !== null) {
                                label += new Intl.NumberFormat('id-ID').format(context.parsed.y);
                            }
                            return label;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            if(value >= 1000000) return 'Rp ' + (value / 1000000) + ' Jt';
                            if(value >= 1000) return 'Rp ' + (value / 1000) + ' Rb';
                            return 'Rp ' + value;
                        }
                    }
                },
                x: {
                    grid: { display: false } 
                }
            }
        }
    });
</script>
@endpush