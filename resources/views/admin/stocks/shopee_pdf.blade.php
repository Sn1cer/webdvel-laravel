<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan Shopee - D'Vel Jeans</title>
    <style>
        /* =========================================
           CSS KHUSUS RENDER PDF / KERTAS A4
           ========================================= */
        @page {
            size: A4 portrait;
            margin: 1cm 1.5cm; /* Margin standar dokumen resmi */
        }

        body { 
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; 
            font-size: 11px; 
            color: #333; 
            line-height: 1.4; 
            margin: 0; 
            padding: 0; /* Padding diatur oleh @page margin saat dicetak */
        }
        
        /* Waktu Cetak */
        .print-timestamp {
            font-size: 9px;
            color: #666;
            margin-bottom: 10px;
        }

        /* Kop Surat */
        .header { 
            text-align: center; 
            border-bottom: 2px solid #333; 
            padding-bottom: 15px; 
            margin-bottom: 20px; 
        }
        .header h1 { font-size: 22px; margin: 0 0 5px 0; padding: 0; text-transform: uppercase; letter-spacing: 2px;}
        .header p { font-size: 11px; margin: 0; color: #555; }
        
        /* Judul Laporan */
        .report-title { text-align: center; font-size: 16px; font-weight: bold; margin-bottom: 5px; text-transform: uppercase; letter-spacing: 1px;}
        .report-subtitle { text-align: center; font-size: 11px; color: #666; margin-bottom: 20px;}
        
        /* Tabel Utama - Menggunakan Persentase agar Fit di Kertas */
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 10px; 
            table-layout: fixed; /* Memaksa tabel patuh pada persentase */
            word-wrap: break-word;
        }
        th, td { 
            border: 1px solid #999; 
            padding: 8px 6px; 
            text-align: left; 
            vertical-align: top;
        }
        th { 
            background-color: #f1f5f9; 
            font-weight: bold; 
            text-transform: uppercase; 
            font-size: 9px; 
            color: #1e293b; 
            text-align: center;
        }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-bold { font-weight: bold; }
        .text-orange { color: #ea580c; font-weight: bold; }
        
        /* Baris Total */
        .total-row td { 
            background-color: #fffbeb !important; 
            font-size: 11px; 
            font-weight: bold; 
            color: #b45309;
        }

        /* Format Resi */
        .resi-box { font-family: monospace; font-weight: bold; color: #ea580c; font-size: 11px;}

        /* Footer Tanda Tangan */
        .footer-container { 
            width: 100%; 
            margin-top: 40px; 
            page-break-inside: avoid; /* Mencegah ttd terbelah ke halaman baru */
        }
        .footer-table { width: 100%; border: none; }
        .footer-table td { border: none; text-align: center; width: 50%; padding: 0; }
        .signature-space { height: 70px; }

        /* Tombol Cetak */
        .btn-print {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #1e293b;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
            transition: 0.2s;
            z-index: 1000;
        }
        .btn-print:hover { background-color: #0f172a; transform: translateY(-2px); }

        @media print {
            .no-print { display: none !important; }
            body { background: white; }
        }
    </style>
</head>
<body>

    <!-- Tombol hanya tampil di layar komputer -->
    <button class="btn-print no-print" onclick="window.print()">🖨️ Cetak / Simpan PDF</button>

    <div class="print-timestamp">
        Dicetak pada: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }} WIB
    </div>

    <div class="header">
        <h1>D'VEL JEANS</h1>
        <p>Pasar Antri Baru Blok B 154, Jl. Sriwijaya II, Setiamanah, Cimahi Tengah</p>
        <p>Telp: 0812-3456-7890 | Platform: Marketplace Shopee</p>
    </div>

    <div class="report-title">
        LAPORAN SINKRONISASI PENJUALAN SHOPEE
    </div>
    <div class="report-subtitle">
        Catatan Penjualan Aktual yang Telah Dipotong dari Stok Gudang
    </div>

    <table>
        <thead>
            <tr>
                <!-- Lebar kolom menggunakan persentase (Total = 100%) -->
                <th style="width: 5%;">No</th>
                <th style="width: 14%;">Waktu Trx.</th>
                <th style="width: 15%;">ID Transaksi</th>
                <th style="width: 32%;">Rincian Produk & Varian</th>
                <th style="width: 15%;">Harga Platform<br>(+30%)</th>
                <th style="width: 5%;">Qty</th>
                <th style="width: 14%;">Total Harga</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $grandTotal = 0; 
                $totalItem = 0;
            @endphp

            @forelse($logs as $index => $log)
                @php
                    // Ambil kuantitas asli (menghilangkan tanda minus)
                    $qty = abs($log->jumlah_penyesuaian);
                    
                    // CARA AMAN: Cek apakah ada $log->harga, jika tidak ada panggil langsung dari database
                    $hargaAsli = $log->harga ?? \App\Models\Product::find($log->product_id)->harga ?? 0;
                    
                    // Kalkulasi Harga Shopee (Harga Web + 30%)
                    $hargaShopee = $hargaAsli * 1.3;
                    
                    // Total harga per baris
                    $subTotal = $hargaShopee * $qty;
                    
                    // Akumulasi grand total
                    $grandTotal += $subTotal;
                    $totalItem += $qty;

                    // Menggunakan ID log untuk menghasilkan kode unik simulasi resi #SHP jika tidak disuplai join
                    $kodeShopee = 'SHP-' . str_pad($log->id ?? rand(1,9999), 4, '0', STR_PAD_LEFT);
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">
                        {{ \Carbon\Carbon::parse($log->created_at)->format('d/m/Y') }}<br>
                        <span style="font-size: 9px; color: #666;">{{ \Carbon\Carbon::parse($log->created_at)->format('H:i') }} WIB</span>
                    </td>
                    <td class="text-center">
                        <span class="resi-box">#{{ $kodeShopee }}</span>
                    </td>
                    <td>
                        <div class="text-bold">{{ $log->nama_produk ?? \App\Models\Product::find($log->product_id)->nama_produk ?? 'Produk Dihapus' }}</div>
                        <div style="font-size: 10px; color: #555; margin-top: 3px;">Varian Ukuran: <b>Size {{ $log->ukuran }}</b></div>
                    </td>
                    <td class="text-right">{{ number_format($hargaShopee, 0, ',', '.') }}</td>
                    <td class="text-center text-orange">{{ $qty }}</td>
                    <td class="text-right text-bold">{{ number_format($subTotal, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 30px; color: #666;">
                        Belum ada riwayat transaksi dari platform Shopee.
                    </td>
                </tr>
            @endforelse
            
            @if(count($logs) > 0)
                <!-- BARIS KESIMPULAN/TOTAL -->
                <tr class="total-row">
                    <td colspan="5" class="text-right">TOTAL PENDAPATAN PLATFORM SHOPEE:</td>
                    <td class="text-center">{{ $totalItem }}</td>
                    <td class="text-right">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
                </tr>
            @endif
        </tbody>
    </table>

    <div class="footer-container">
        <table class="footer-table">
            <tr>
                <td></td>
                <td>
                    Cimahi, {{ now()->format('d F Y') }}<br>
                    Mengetahui,<br>
                    <div class="signature-space"></div>
                    <b style="text-decoration: underline;">Pemilik D'Vel Jeans</b>
                </td>
            </tr>
        </table>
    </div>

    <!-- SCRIPT AUTO-PRINT UNTUK BROWSER -->
    <script>
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        }
    </script>
</body>
</html>