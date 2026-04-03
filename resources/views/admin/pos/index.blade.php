@extends('layouts.admin')

@section('title', "Kasir Offline (POS) - Admin D'Vel Jeans")
@section('topbar_title', '🛒 Kasir Offline (POS)')

@section('content')
    <style>
        /* --- BUNGKUSAN UTAMA POS --- */
        /* Margin negatif untuk menarik kontainer agar menempel ke ujung layar menutupi padding bawaan master layout */
        .pos-container { 
            display: flex; 
            flex-direction: row; 
            height: calc(100vh - 75px); 
            margin: -30px; 
        }
        
        /* Kiri: Daftar Produk */
        .product-area { flex: 2; padding: 30px; overflow-y: auto; background: var(--bg); }
        .page-title { font-size: 20px; font-weight: 800; margin-bottom: 20px; font-family: 'DM Serif Display', serif;}
        .product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 15px; }
        .product-card { background: white; border: 1px solid var(--border); border-radius: 12px; padding: 15px; cursor: pointer; transition: 0.2s; display: flex; flex-direction: column; align-items: center; text-align: center;}
        .product-card:hover { transform: translateY(-3px); border-color: var(--accent); box-shadow: 0 10px 15px rgba(217, 119, 6, 0.1); }
        .product-img { width: 80px; height: 80px; object-fit: cover; border-radius: 8px; background: #e2e8f0; margin-bottom: 10px; display: flex; align-items: center; justify-content: center; font-size: 30px;}
        .product-name { font-size: 13px; font-weight: 700; color: var(--text); margin-bottom: 5px; line-height: 1.3;}
        .product-price { font-size: 14px; font-weight: 800; color: var(--accent); }
        .product-stock { font-size: 11px; color: #64748b; margin-top: 5px; background: #f1f5f9; padding: 2px 8px; border-radius: 4px; font-weight: 600;}

        /* Kanan: Struk / Keranjang */
        .cart-area { flex: 1; background: white; border-left: 1px solid var(--border); display: flex; flex-direction: column; padding: 25px; box-shadow: -4px 0 15px rgba(0,0,0,0.02); z-index: 5;}
        .cart-header { font-size: 16px; font-weight: 800; border-bottom: 2px dashed var(--border); padding-bottom: 15px; margin-bottom: 15px; }
        .cart-items { flex: 1; overflow-y: auto; display: flex; flex-direction: column; gap: 15px; }
        
        /* Item di Keranjang */
        .cart-item { display: flex; justify-content: space-between; align-items: center; font-size: 13px; }
        .cart-item-info { flex: 1; }
        .cart-item-title { font-weight: 700; margin-bottom: 4px; }
        .cart-item-price { color: #64748b; font-weight: 600; }
        .qty-controls { display: flex; align-items: center; gap: 10px; background: #f8fafc; padding: 4px 8px; border-radius: 6px; border: 1px solid var(--border);}
        .qty-btn { background: none; border: none; font-weight: 800; cursor: pointer; color: var(--text); font-size: 14px; padding: 0 5px;}
        .qty-btn:hover { color: var(--accent); }
        
        /* Total & Checkout */
        .cart-footer { border-top: 2px dashed var(--border); padding-top: 20px; margin-top: 15px; }
        .total-row { display: flex; justify-content: space-between; font-size: 18px; font-weight: 800; margin-bottom: 20px; }
        .btn-checkout { background: #15803d; color: white; border: none; width: 100%; padding: 15px; border-radius: 12px; font-size: 15px; font-weight: 800; cursor: pointer; transition: 0.2s; font-family: inherit;}
        .btn-checkout:hover { background: #166534; transform: scale(1.02); }
        .btn-checkout:disabled { background: #cbd5e1; cursor: not-allowed; transform: none; }
        
        .alert-success { background: #dcfce3; color: #166534; padding: 15px; border-radius: 8px; font-weight: 600; border: 1px solid #bbf7d0; margin-bottom: 20px;}

        /* --- RESPONSIVE UNTUK LAYAR HP & TABLET --- */
        @media (max-width: 768px) {
            .pos-container { 
                flex-direction: column; 
                height: auto; 
                margin: -15px; /* Menyesuaikan padding mobile dari master layout */
            }
            .product-area { 
                padding: 20px; 
                overflow-y: visible; 
            }
            .cart-area { 
                border-left: none; 
                border-top: 3px solid var(--border); 
                padding: 20px;
                /* Di HP, keranjang harus punya minimal tinggi agar kasir nyaman scroll */
                min-height: 400px; 
            }
            .product-grid { 
                /* Di HP, tampilkan produk 2 kolom berdampingan agar lebih hemat ruang */
                grid-template-columns: repeat(2, 1fr); 
            }
        }
    </style>

    <div class="pos-container">
        <div class="product-area">
            <h2 class="page-title">Pilih Barang Pelanggan (Offline)</h2>
            
            @if(session('error'))
                <div style="background: #fef2f2; color: #991b1b; padding: 15px; border-radius: 8px; font-weight: 600; border: 1px solid #fecaca; margin-bottom: 20px;">
                    ❌ TRANSAKSI GAGAL: {{ session('error') }}
                </div>
            @endif

            <div class="product-grid">
                @foreach($products as $product)
                    <div class="product-card" onclick="addToCart({{ $product->id }}, '{{ addslashes($product->nama_produk) }}', {{ $product->harga }}, {{ $product->stok }})">
                        @if($product->gambar)
                            <img src="{{ asset('images/'.$product->gambar) }}" class="product-img" alt="img">
                        @else
                            <div class="product-img">👖</div>
                        @endif
                        <div class="product-name">{{ $product->nama_produk }}</div>
                        <div class="product-price">Rp {{ number_format($product->harga, 0, ',', '.') }}</div>
                        <div class="product-stock">Stok: {{ $product->stok }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="cart-area">
            <div class="cart-header">🛒 Struk Belanja</div>
            <div class="cart-items" id="cart-container">
                <div style="text-align: center; color: #94a3b8; font-size: 13px; margin-top: 50px;">Belum ada barang yang dipilih. Klik produk di sebelah kiri.</div>
            </div>
            
            <div class="cart-footer">
                <div class="total-row">
                    <span>Total Tagihan:</span>
                    <span style="color: var(--accent);" id="total-price">Rp 0</span>
                </div>
                
                <form action="{{ route('admin.pos.checkout') }}" method="POST" id="checkout-form">
                    @csrf
                    <input type="hidden" name="cart_data" id="cart-data-input">
                    <button type="button" id="btn-submit-order" class="btn-checkout" disabled onclick="submitOrder()">💸 Bayar & Potong Stok</button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let cart = [];

        // Format angka ke Rupiah
        function formatRupiah(number) {
            return new Intl.NumberFormat('id-ID').format(number);
        }

        // Tambah barang ke keranjang
        function addToCart(id, name, price, maxStock) {
            let existingItem = cart.find(item => item.id === id);
            
            if (existingItem) {
                if(existingItem.qty < maxStock) {
                    existingItem.qty += 1;
                } else {
                    alert('Stok tidak mencukupi!');
                }
            } else {
                cart.push({ id: id, name: name, price: price, qty: 1, max: maxStock });
            }
            renderCart();
        }

        // Kurangi atau Hapus barang
        function updateQty(id, change) {
            let item = cart.find(item => item.id === id);
            if (item) {
                item.qty += change;
                if (item.qty <= 0) {
                    cart = cart.filter(i => i.id !== id); // Hapus jika 0
                } else if (item.qty > item.max) {
                    item.qty = item.max; // Cegah melebihi stok fisik
                }
            }
            renderCart();
        }

        // Tampilkan keranjang ke layar
        function renderCart() {
            const container = document.getElementById('cart-container');
            const totalEl = document.getElementById('total-price');
            const btnSubmit = document.getElementById('btn-submit-order');
            
            container.innerHTML = '';
            let total = 0;

            if(cart.length === 0) {
                container.innerHTML = '<div style="text-align: center; color: #94a3b8; font-size: 13px; margin-top: 50px;">Belum ada barang yang dipilih.</div>';
                totalEl.innerText = 'Rp 0';
                btnSubmit.disabled = true;
                return;
            }

            cart.forEach(item => {
                total += (item.price * item.qty);
                
                let div = document.createElement('div');
                div.className = 'cart-item';
                div.innerHTML = `
                    <div class="cart-item-info">
                        <div class="cart-item-title">${item.name}</div>
                        <div class="cart-item-price">Rp ${formatRupiah(item.price)}</div>
                    </div>
                    <div class="qty-controls">
                        <button class="qty-btn" onclick="updateQty(${item.id}, -1)">-</button>
                        <span style="font-weight: 700;">${item.qty}</span>
                        <button class="qty-btn" onclick="updateQty(${item.id}, 1)">+</button>
                    </div>
                `;
                container.appendChild(div);
            });

            totalEl.innerText = 'Rp ' + formatRupiah(total);
            btnSubmit.disabled = false;
        }

        // Proses Bayar (Kirim ke Laravel)
        function submitOrder() {
            if(confirm('Proses pembayaran ini? Stok akan otomatis terpotong.')) {
                document.getElementById('cart-data-input').value = JSON.stringify(cart);
                document.getElementById('checkout-form').submit();
            }
        }
    </script>
@endpush