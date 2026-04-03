<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Stok Gudang - D'Vel Jeans</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #333; font-size: 12px; margin: 0; padding: 0; }
        .kop-surat { text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px; }
        .kop-surat h1 { font-size: 24px; margin: 0; text-transform: uppercase; letter-spacing: 2px; }
        .kop-surat p { font-size: 10px; margin: 5px 0 0 0; color: #555; }
        .judul-laporan { text-align: center; font-size: 16px; font-weight: bold; margin-bottom: 5px; text-transform: uppercase; }
        .tanggal-cetak { text-align: center; font-size: 10px; color: #666; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #999; padding: 8px 10px; text-align: left; }
        th { background-color: #f5f5f5; font-weight: bold; text-transform: uppercase; font-size: 10px; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .kritis { color: #dc2626; font-weight: bold; }
        .total-row { background-color: #f5f5f5; font-weight: bold; }
        .ttd-box { width: 100%; margin-top: 50px; }
        .ttd-box td { border: none; text-align: center; width: 33%; padding: 0; }
        .ttd-space { height: 80px; }
    </style>
</head>
<body>

    <div class="kop-surat">
        <h1>D'Vel Jeans</h1>
        <p>Jl. Contoh Skripsi No. 123, Kota Cimahi, Jawa Barat<br>Telp: 0812-3456-7890 | Email: admin@dveljeans.com</p>
    </div>

    <div class="judul-laporan">Laporan Ketersediaan Stok Gudang</div>
    <div class="tanggal-cetak">Diperbarui pada: {{ \Carbon\Carbon::now()->format('d F Y, H:i') }} WIB</div>

    <table>
        <thead>
            <tr>
                <th width="5%" class="text-center">No</th>
                <th width="15%">ID Produk</th>
                <th width="40%">Nama Barang (SKU)</th>
                <th width="20%" class="text-right">Harga Satuan (Rp)</th>
                <th width="20%" class="text-center">Sisa Stok Fisik</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $no = 1; 
                $totalSeluruhStok = 0;
            @endphp
            @foreach($products as $product)
                @php $totalSeluruhStok += $product->stok; @endphp
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td>PRD-{{ str_pad($product->id, 4, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ $product->nama_produk }}</td>
                    <td class="text-right">{{ number_format($product->harga, 0, ',', '.') }}</td>
                    <td class="text-center {{ $product->stok < 5 ? 'kritis' : '' }}">{{ $product->stok }} Pcs</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="4" class="text-right">TOTAL KESELURUHAN BARANG DI GUDANG:</td>
                <td class="text-center">{{ $totalSeluruhStok }} Pcs</td>
            </tr>
        </tbody>
    </table>

    <table class="ttd-box">
        <tr>
            <td></td>
            <td></td>
            <td>
                Cimahi, {{ \Carbon\Carbon::now()->format('d F Y') }}<br>
                Kepala Gudang,<br>
                <div class="ttd-space"></div>
                <b><u>..................................</u></b><br>
                NIP. 
            </td>
        </tr>
    </table>

</body>
</html>