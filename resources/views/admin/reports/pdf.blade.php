<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan - D'Vel Jeans</title>
    <link rel="icon" href="{{ asset('Dvel/logo.png') }}" type="image/png">
    <style>
        /* CSS Khusus Cetak Kertas (Tanpa warna warni berlebih) */
        body { 
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; 
            color: #333; 
            font-size: 12px; 
            margin: 0; 
            padding: 20px; 
            position: relative; /* Diperlukan untuk absolute positioning */
        }
        
        /* Waktu Cetak Kustom (Pengganti bawaan browser) */
        .print-timestamp {
            position: absolute;
            top: 20px;
            left: 20px;
            font-size: 10px;
            color: #333;
        }

        /* Kop Surat Resmi */
        .kop-surat { text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px; }
        .kop-surat h1 { font-size: 24px; margin: 0; text-transform: uppercase; letter-spacing: 2px; }
        .kop-surat p { font-size: 10px; margin: 5px 0 0 0; color: #555; }
        
        /* Judul Laporan */
        .judul-laporan { text-align: center; font-size: 16px; font-weight: bold; margin-bottom: 20px; text-transform: uppercase; }
        .periode { text-align: center; font-size: 12px; margin-bottom: 20px; color: #555; }

        /* Tabel Data */
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #999; padding: 8px 10px; text-align: left; }
        th { background-color: #f5f5f5; font-weight: bold; text-transform: uppercase; font-size: 10px; }
        
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        
        /* Baris Total */
        .total-row { background-color: #f5f5f5; }
        .total-row td { font-size: 14px; font-weight: bold; }

        /* Tanda Tangan */
        .ttd-box { width: 100%; margin-top: 50px; }
        .ttd-box td { border: none; text-align: center; width: 33%; padding: 0; }
        .ttd-space { height: 80px; }

        /* =========================================
           TOMBOL CETAK & PENGATURAN MEDIA PRINT
           ========================================= */
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

        /* Menyembunyikan elemen tertentu saat proses cetak/PDF berjalan */
        @media print {
            .no-print { display: none !important; }
            
            /* MENGHILANGKAN URL DAN HALAMAN BAWAAN BROWSER */
            @page { 
                margin: 0; 
            }
            
            /* MEMBERIKAN JARAK KERTAS MANUAL AGAR KONTEN TIDAK TERPOTONG */
            body { 
                padding: 1.5cm; 
            }

            /* Menyesuaikan posisi timestamp saat dicetak */
            .print-timestamp {
                top: 1.5cm;
                left: 1.5cm;
            }
        }
    </style>
</head>
<body>

    <!-- Waktu yang akan muncul di pojok kiri atas (Format: 8/3/26, 11:10 PM) -->
    <div class="print-timestamp">
        {{ \Carbon\Carbon::now()->format('n/j/y, g:i A') }}
    </div>

    <!-- Tombol ini akan disembunyikan secara otomatis saat PDF terbuat -->
    <button class="btn-print no-print" onclick="window.print()">🖨️ Cetak / Simpan PDF</button>

    <div class="kop-surat">
        <h1>D'Vel Jeans</h1>
        <p>Jl. Contoh Skripsi No. 123, Kota Cimahi, Jawa Barat<br>Telp: 0812-3456-7890 | Email: admin@dveljeans.com</p>
    </div>

    <div class="judul-laporan">Laporan Pendapatan Penjualan</div>
    
    <div class="periode">
        @if($request->tanggal_awal && $request->tanggal_akhir)
            Periode: {{ \Carbon\Carbon::parse($request->tanggal_awal)->format('d M Y') }} - {{ \Carbon\Carbon::parse($request->tanggal_akhir)->format('d M Y') }}
        @else
            Periode: Keseluruhan Waktu (Hingga {{ \Carbon\Carbon::now()->format('d M Y') }})
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">Tanggal</th>
                <th width="20%">No. Order</th>
                <th width="35%">Nama Pelanggan</th>
                <th width="25%" class="text-right">Total Transaksi (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach($orders as $order)
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td>{{ $order->created_at->format('d/m/Y') }}</td>
                    <td>#{{ $order->nomor_pesanan }}</td>
                    <td>{{ $order->nama_depan }} {{ $order->nama_belakang }}</td>
                    <td class="text-right">{{ number_format($order->total_harga, 0, ',', '.') }}</td>
                </tr>
            @endforeach
            
            <tr class="total-row">
                <td colspan="4" class="text-right">TOTAL PENDAPATAN BERSIH:</td>
                <td class="text-right">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <table class="ttd-box">
        <tr>
            <td></td>
            <td></td>
            <td>
                Cimahi, {{ \Carbon\Carbon::now()->format('d F Y') }}<br>
                Mengetahui,<br>
                <div class="ttd-space"></div>
                <b><u>Pemilik D'Vel Jeans</u></b><br>
                NIP. 123456789
            </td>
        </tr>
    </table>

    <!-- SCRIPT UNTUK OTOMATIS MEMUNCULKAN DIALOG PRINT SAAT HALAMAN DIBUKA -->
    <script>
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        }
    </script>
</body>
</html>