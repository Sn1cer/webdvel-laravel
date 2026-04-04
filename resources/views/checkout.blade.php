@extends('layouts.app')

@section('title', "Checkout - D'Vel Jeans")

@push('styles')
<style>
    /* --- CSS KHUSUS HALAMAN CHECKOUT --- */
    
    /* Layout Utama */
    .container-checkout { max-width: 1200px; margin: 40px auto; padding: 0 20px; display: flex; gap: 40px; align-items: flex-start;}
    .checkout-form { flex: 1; background: white; padding: 35px; border-radius: 12px; border: 1px solid var(--border); box-shadow: 0 4px 10px rgba(0,0,0,0.02); }
    .section-title { font-size: 22px; font-weight: 800; margin-bottom: 25px; padding-bottom: 15px; border-bottom: 1px solid var(--border);}
    
    /* Form Alamat Pengiriman */
    .form-row { display: flex; gap: 15px; }
    .form-row .form-group { flex: 1; }
    .form-group { margin-bottom: 20px; position: relative; }
    .form-label { display: block; font-size: 14px; font-weight: 700; margin-bottom: 8px; color: var(--text); }
    .form-control { width: 100%; padding: 14px 15px; border: 1px solid var(--border); border-radius: 8px; font-size: 15px; font-family: inherit; outline: none; transition: 0.2s; box-sizing: border-box; background: #f8fafc;}
    .form-control:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(217,119,6,0.1); background: white;}
    textarea.form-control { resize: vertical; min-height: 100px; }

    /* Fitur Autocomplete Wilayah */
    .autocomplete-items { position: absolute; border: 1px solid var(--border); border-radius: 8px; border-top: none; z-index: 99; top: 100%; left: 0; right: 0; background: white; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); max-height: 200px; overflow-y: auto; display: none; }
    .autocomplete-items div { padding: 12px 15px; cursor: pointer; font-size: 14px; border-bottom: 1px solid #f1f5f9; }
    .autocomplete-items div:hover { background-color: var(--accent); color: white; }
    .autocomplete-active { display: block; }

    /* Desain Struk (Receipt) */
    .checkout-summary {
        width: 380px; 
        background: white;
        padding: 40px 30px; 
        border-radius: 4px; 
        border: 1px solid var(--border);
        border-bottom: none; 
        box-shadow: 0 10px 25px rgba(0,0,0,0.03); 
        position: sticky;
        top: 100px;
        box-sizing: border-box;
    }
    .checkout-summary::after {
        content: ''; position: absolute; bottom: 0; left: 0; width: 100%; height: 15px; 
        background: white; background-size: 30px 30px; background-position: bottom;
        background-image: linear-gradient(135deg, transparent 40%, #e2e8f0 40%, #e2e8f0 50%, transparent 50%),
                          linear-gradient(225deg, transparent 40%, #e2e8f0 40%, #e2e8f0 50%, transparent 50%);
        border-bottom: 1px solid var(--border); 
    }

    .receipt-header { text-align: center; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px dashed var(--border); }
    .receipt-header .receipt-logo { font-family: 'DM Serif Display', serif; font-size: 24px; color: var(--text); letter-spacing: 1px; margin-bottom: 5px; }
    .receipt-header .receipt-date { font-size: 12px; color: #64748b; }
    .struk-title { font-size: 18px; font-weight: 800; margin-bottom: 15px; padding-bottom: 10px; border-bottom: none; text-transform: uppercase; letter-spacing: 1px; text-align: center; }

    .summary-item.struk-item { display: flex; justify-content: space-between; margin-bottom: 10px; padding-bottom: 10px; border-bottom: 1px dashed #f1f5f9; align-items: center;}
    .struk-item .item-name { font-weight: 700; color: var(--text); font-size: 14px; margin-bottom: 4px; line-height: 1.3;}
    .struk-item .item-detail { color: #64748b; font-size: 12px; }
    .struk-item .item-price { font-weight: 800; font-size: 14px; white-space: nowrap;}

    .summary-total.struk-total { display: flex; justify-content: space-between; margin-top: 20px; padding-top: 15px; border-top: 2px dashed var(--border); font-size: 18px; font-weight: 800; color: var(--text);}
    .struk-total span:first-child { text-transform: uppercase; letter-spacing: 1px; }
    
    .btn-pay { display: block; width: 100%; text-align: center; background: var(--accent); color: white; border: none; border-radius: 8px; font-weight: 800; cursor: pointer; transition: 0.2s; text-transform: uppercase; letter-spacing: 1px; margin-top: 25px; padding: 14px; font-size: 14px; box-sizing: border-box;}
    .btn-pay:hover { filter: brightness(1.1); transform: translateY(-2px);}

    /* --- RESPONSIVE MOBILE --- */
    @media (max-width: 768px) {
        .container-checkout { 
            flex-direction: column-reverse; 
            gap: 20px; 
            margin: 20px auto; 
        }
        .checkout-summary { 
            width: 100%; 
            position: static; 
            padding: 30px 20px;
        }
        .checkout-form {
            width: 100%;
            padding: 20px;
            box-sizing: border-box;
        }
        .form-row {
            flex-direction: column; 
            gap: 0;
        }
    }
</style>
@endpush

@section('content')
    <div style="max-width: 1200px; margin: 20px auto 0; padding: 0 20px;">
        <a href="{{ route('cart.index') }}" style="text-decoration: none; font-size: 15px; font-weight: 700; color: var(--text); display: inline-flex; align-items: center; gap: 5px;">
            &larr; Kembali ke Keranjang
        </a>
    </div>

    <div class="container-checkout">
        
        <div class="checkout-form">
            <h2 class="section-title">📍 Alamat Pengiriman</h2>
            
            <form id="form-pesanan" action="{{ route('checkout.store') }}" method="POST">
                @csrf
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Nama Depan</label>
                        <input type="text" name="nama_depan" class="form-control" required placeholder="Contoh: Budi">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nama Belakang</label>
                        <input type="text" name="nama_belakang" class="form-control" required placeholder="Contoh: Santoso">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Alamat Jalan</label>
                    <input type="text" name="alamat_jalan" class="form-control" required placeholder="Contoh: Jl. Gatot Subroto No. 123">
                </div>

                <div class="form-group">
                    <label class="form-label">Provinsi, Kota, Kecamatan, Kode Pos</label>
                    <input type="text" id="input_lokasi" name="wilayah" class="form-control" required placeholder="Ketik min. 3 huruf (Contoh: cimahi)" autocomplete="off">
                    <div id="lokasi-list" class="autocomplete-items"></div>
                </div>

                <div class="form-group">
                    <label class="form-label">No. WhatsApp / HP</label>
                    <input type="number" name="no_hp" class="form-control" required placeholder="Contoh: 081234567890">
                </div>

                <div class="form-group">
                    <label class="form-label">Detail Alamat / Patokan (Opsional)</label>
                    <textarea name="alamat_lengkap" class="form-control" placeholder="Contoh: Rumah warna biru pagar hitam, di depan Indomaret."></textarea>
                </div>
            </form>
        </div>

        <div class="checkout-summary">
            <div class="receipt-header">
                <div class="receipt-logo">D'VEL JEANS</div>
                <div class="receipt-date">Cimahi, {{ now()->format('d F Y') }} | #{{ strtoupper(uniqid()) }}</div>
            </div>
            
            <h2 class="section-title struk-title">Ringkasan Pesanan</h2>
            
            @foreach($carts as $cart)
                <div class="summary-item struk-item">
                    <div>
                        <div class="item-name">{{ $cart->product->nama_produk }}</div>
                        <div class="item-detail">Ukuran: {{ $cart->ukuran }} | Qty: {{ $cart->jumlah }}</div>
                    </div>
                    <div class="item-price">Rp {{ number_format($cart->product->harga * $cart->jumlah, 0, ',', '.') }}</div>
                </div>
            @endforeach
            
            <div class="summary-total struk-total">
                <span>Total Tagihan</span>
                <span style="color: var(--accent);">Rp {{ number_format($totalHarga, 0, ',', '.') }}</span>
            </div>

            <button type="submit" form="form-pesanan" class="btn-pay">Buat Pesanan Sekarang</button>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Fitur Autocomplete Wilayah
    const dataWilayah = [
        "Jawa Barat, Kota Cimahi, Cimahi Tengah, 40525",
        "Jawa Barat, Kota Cimahi, Cimahi Utara, 40511",
        "Jawa Barat, Kota Cimahi, Cimahi Selatan, 40531",
        "Jawa Barat, Kota Bandung, Cicendo, 40171",
        "Jawa Barat, Kota Bandung, Coblong, 40132",
        "Jawa Barat, Kabupaten Bandung Barat, Padalarang, 40553",
        "DKI Jakarta, Jakarta Selatan, Kebayoran Baru, 12110",
        "Banten, Kota Tangerang, Cipondoh, 15148"
    ];

    const inputLokasi = document.getElementById("input_lokasi");
    const lokasiList = document.getElementById("lokasi-list");

    inputLokasi.addEventListener("input", function() {
        let nilaiInput = this.value;
        lokasiList.innerHTML = ""; 
        
        if (nilaiInput.length >= 3) {
            let cocok = false;
            
            dataWilayah.forEach(function(lokasi) {
                if (lokasi.toLowerCase().includes(nilaiInput.toLowerCase())) {
                    cocok = true;
                    let div = document.createElement("div");
                    
                    let regex = new RegExp(nilaiInput, "gi");
                    div.innerHTML = lokasi.replace(regex, "<strong>$&</strong>");
                    
                    div.addEventListener("click", function() {
                        inputLokasi.value = lokasi;
                        lokasiList.classList.remove("autocomplete-active");
                    });
                    lokasiList.appendChild(div);
                }
            });

            if (cocok) {
                lokasiList.classList.add("autocomplete-active");
            } else {
                let div = document.createElement("div");
                div.innerHTML = "<em style='color:#64748b;'>Wilayah tidak ditemukan...</em>";
                lokasiList.appendChild(div);
                lokasiList.classList.add("autocomplete-active");
            }
        } else {
            lokasiList.classList.remove("autocomplete-active");
        }
    });

    document.addEventListener("click", function (e) {
        if (e.target !== inputLokasi) {
            lokasiList.classList.remove("autocomplete-active");
        }
    });
</script>
@endpush