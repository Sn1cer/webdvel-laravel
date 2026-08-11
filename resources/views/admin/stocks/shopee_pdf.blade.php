<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Log Shopee - D'Vel Jeans</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { font-size: 20px; margin: 0; padding: 0; text-transform: uppercase; letter-spacing: 2px;}
        .header p { font-size: 12px; margin: 5px 0 0 0; color: #666; }
        
        .report-title { text-align: center; font-size: 16px; font-weight: bold; margin-bottom: 20px; text-transform: uppercase;}
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px 12px; text-align: left; }
        th { background-color: #f8fafc; font-weight: bold; text-transform: uppercase; font-size: 11px; color: #333;}
        
        .text-center { text-align: center; }
        .text-red { color: #ef4444; font-weight: bold; }
        
        .footer { margin-top: 50px; text-align: right; font-size: 12px; }
        .signature-space { height: 80px; }
    </style>
</head>
<body>

    <div class="header">
        <h1>D'VEL JEANS</h1>
        <p>Pasar Antri Baru Blok B 154, Jl. Sriwijaya II, Setiamanah, Cimahi Tengah</p>
    </div>

    <div class="report-title">
        LAPORAN LOG AKTIVITAS SHOPEE (PENGURANGAN STOK)
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" width="30">No</th>
                <th width="120">Waktu Penyesuaian</th>
                <th>Nama Produk</th>
                <th class="text-center" width="80">Ukuran Varian</th>
                <th class="text-center" width="100">Jumlah Terjual</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $index => $log)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($log->created_at)->format('d M Y, H:i') }} WIB</td>
                    <td>{{ $log->nama_produk }}</td>
                    <td class="text-center">Size {{ $log->ukuran }}</td>
                    <td class="text-center text-red">{{ $log->jumlah_penyesuaian }} Pcs</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center" style="padding: 20px;">Belum ada riwayat pengurangan stok dari Shopee.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Cimahi, {{ now()->format('d F Y') }}</p>
        <p>Mengetahui,</p>
        <div class="signature-space"></div>
        <p style="font-weight: bold; text-decoration: underline;">Pemilik D'Vel Jeans</p>
    </div>

</body>
</html>