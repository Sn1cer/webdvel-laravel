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

    select.form-control {
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 1rem center;
        background-size: 1em;
    }

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

    .summary-total.struk-total { display: flex; justify-content: space-between; margin-top: 15px; padding-top: 15px; border-top: 2px dashed var(--border); font-size: 18px; font-weight: 800; color: var(--text);}
    .struk-total span:first-child { text-transform: uppercase; letter-spacing: 1px; }
    
    .ongkir-row { display: flex; justify-content: space-between; font-size: 14px; color: #475569; margin-top: 15px; }
    .ongkir-price { font-weight: 700; color: var(--text); }

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
            <h2 class="section-title">📍 Detail Pengiriman</h2>
            
            <form id="form-pesanan" action="{{ route('checkout.store') }}" method="POST">
                @csrf
                
                <!-- Input Hidden untuk mengirim data ongkir ke controller -->
                <input type="hidden" name="ongkir" id="hidden_ongkir" value="10000">

                <div class="form-group mb-4 p-4" style="background: #f8fafc; border: 1px solid var(--border); border-radius: 8px;">
                    <label class="form-label" style="font-size: 16px; margin-bottom: 12px;">Pilih Metode Pengiriman</label>
                    <div style="display: flex; gap: 20px; flex-wrap: wrap;">
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 15px;">
                            <input type="radio" name="tipe_pesanan" id="tipe_online" value="Online" checked onchange="toggleAddressForm()" style="width: 18px; height: 18px; accent-color: var(--accent);">
                            Kirim ke Alamat (Reguler)
                        </label>
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 15px;">
                            <input type="radio" name="tipe_pesanan" id="tipe_booking" value="Booking" onchange="toggleAddressForm()" style="width: 18px; height: 18px; accent-color: var(--accent);">
                            Ambil di Toko (Booking) 
                            <span style="font-size: 11px; background: #dcfce7; color: #166534; padding: 2px 8px; border-radius: 12px; font-weight: 700;">Gratis Ongkir</span>
                        </label>
                    </div>
                </div>

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
                    <label class="form-label">No. WhatsApp / HP</label>
                    <input type="number" name="no_hp" class="form-control" required placeholder="Contoh: 081234567890">
                </div>

                <div id="address-form-container">
                    <div class="form-group">
                        <label class="form-label">Alamat Jalan</label>
                        <input type="text" name="alamat_jalan" class="form-control" required placeholder="Contoh: Jl. Gatot Subroto No. 123">
                    </div>

                    <!-- DROPDOWN WILAYAH (DUMMY ONGKIR) -->
                    <div class="form-group">
                        <label class="form-label">Pilih Wilayah Pengiriman</label>
                        <select name="wilayah" id="select_wilayah" class="form-control" required onchange="hitungOngkir()">
                            <option value="Kota Cimahi" data-tarif="10000">Kota Cimahi - Rp 10.000</option>
                            <option value="Kota Bandung" data-tarif="15000">Kota Bandung - Rp 15.000</option>
                            <option value="Kabupaten Bandung" data-tarif="18000">Kabupaten Bandung - Rp 18.000</option>
                            <option value="Kabupaten Bandung Barat" data-tarif="20000">Kab. Bandung Barat - Rp 20.000</option>
                            <option value="Jabodetabek" data-tarif="25000">Jabodetabek - Rp 25.000</option>
                            <option value="Pulau Jawa (Lainnya)" data-tarif="35000">Pulau Jawa (Lainnya) - Rp 35.000</option>
                            <option value="Luar Pulau Jawa" data-tarif="50000">Luar Pulau Jawa - Rp 50.000</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Detail Alamat / Patokan (Opsional)</label>
                        <textarea name="alamat_lengkap" class="form-control" placeholder="Contoh: Rumah warna biru pagar hitam, di depan Indomaret."></textarea>
                    </div>
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
            
            <!-- BARIS ONGKIR -->
            <div class="ongkir-row">
                <span>Biaya Ongkos Kirim</span>
                <span class="ongkir-price" id="tampilan_ongkir">Rp 10.000</span>
            </div>

            <div class="summary-total struk-total">
                <span>Total Tagihan</span>
                <span style="color: var(--accent);" id="tampilan_total">Rp {{ number_format($totalHarga + 10000, 0, ',', '.') }}</span>
            </div>

            <button type="submit" form="form-pesanan" class="btn-pay">Buat Pesanan Sekarang</button>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Menyimpan total harga keranjang (murni barang) dari server
    const totalHargaBarang = {{ $totalHarga }};

    // Fungsi untuk menghitung total tagihan saat wilayah dipilih
    function hitungOngkir() {
        const selectWilayah = document.getElementById('select_wilayah');
        const isBooking = document.getElementById('tipe_booking').checked;
        
        let tarifOngkir = 0;

        // Jika pilih Online (bukan booking), ambil tarif dari dropdown
        if (!isBooking) {
            const opsiTerpilih = selectWilayah.options[selectWilayah.selectedIndex];
            tarifOngkir = parseInt(opsiTerpilih.getAttribute('data-tarif'));
        }
        
        document.getElementById('hidden_ongkir').value = tarifOngkir;

        document.getElementById('tampilan_ongkir').innerText = "Rp " + tarifOngkir.toLocaleString('id-ID');

        const totalKeseluruhan = totalHargaBarang + tarifOngkir;
        document.getElementById('tampilan_total').innerText = "Rp " + totalKeseluruhan.toLocaleString('id-ID');
    }

    // Fungsi untuk menyembunyikan/menampilkan form alamat
    function toggleAddressForm() {
        const isBooking = document.getElementById('tipe_booking').checked;
        const addressContainer = document.getElementById('address-form-container');
        const addressInputs = addressContainer.querySelectorAll('input, select, textarea');

        if (isBooking) {
            addressContainer.style.display = 'none';
            hitungOngkir(); 

            addressInputs.forEach(input => {
                if (input.required) {
                    input.dataset.wasRequired = 'true'; 
                    input.required = false;
                }
            });
        } else {
            addressContainer.style.display = 'block';
            hitungOngkir(); 

            // Kembalikan status 'required'
            addressInputs.forEach(input => {
                if (input.dataset.wasRequired === 'true') {
                    input.required = true;
                }
            });
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        toggleAddressForm();
    });
</script>
@endpush