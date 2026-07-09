@extends('layouts.admin')

@section('title', "Kasir Offline (POS) - Admin D'Vel Jeans")
@section('topbar_title', '🛒 Kasir Offline (POS)')

@section('content')
    <style>
        /* --- BUNGKUSAN UTAMA POS --- */
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
        .cart-items { flex: 1; overflow-y: auto; display: flex; flex-direction: column; gap: 15px; padding-right: 5px;}
        
        /* Item di Keranjang */
        .cart-item { display: flex; justify-content: space-between; align-items: flex-start; font-size: 13px; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px;}
        .cart-item-info { flex: 1; }
        .cart-item-title { font-weight: 700; margin-bottom: 4px; color: #1e293b;}
        .cart-item-size { font-size: 11px; color: #0f172a; background: #e2e8f0; padding: 2px 6px; border-radius: 4px; display: inline-block; margin-bottom: 4px;}
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

        /* --- MODAL PILIH UKURAN --- */
        .modal-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); display: none; align-items: center; justify-content: center; z-index: 100; backdrop-filter: blur(3px);}
        .modal-box { background: white; padding: 25px; border-radius: 12px; width: 400px; max-width: 90%; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); }
        .modal-title { font-size: 18px; font-weight: 800; margin-bottom: 15px; color: var(--text); }
        .size-grid { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 20px;}
        .size-btn { border: 1px solid var(--border); background: white; padding: 10px 15px; border-radius: 6px; cursor: pointer; font-weight: 700; transition: 0.2s; color: var(--text);}
        .size-btn:hover:not(:disabled) { border-color: var(--accent); color: var(--accent); }
        .size-btn.selected { background: var(--accent); color: white; border-color: var(--accent); }
        .size-btn:disabled { background: #f1f5f9; color: #94a3b8; cursor: not-allowed; text-decoration: line-through;}
        
        .modal-actions { display: flex; justify-content: flex-end; gap: 10px; }
        .btn-cancel { padding: 10px 15px; border-radius: 6px; border: 1px solid var(--border); background: white; cursor: pointer; font-weight: 600;}
        .btn-add { padding: 10px 15px; border-radius: 6px; border: none; background: var(--text); color: white; cursor: pointer; font-weight: 600;}
        .btn-add:disabled { background: #94a3b8; cursor: not-allowed; }

        @media (max-width: 768px) {
            .pos-container { flex-direction: column; height: auto; margin: -15px; }
            .product-area { padding: 20px; overflow-y: visible; }
            .cart-area { border-left: none; border-top: 3px solid var(--border); padding: 20px; min-height: 400px; }
            .product-grid { grid-template-columns: repeat(2, 1fr); }
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
            @if(session('success'))
                <div style="background: #dcfce3; color: #166534; padding: 15px; border-radius: 8px; font-weight: 600; border: 1px solid #bbf7d0; margin-bottom: 20px;">
                    ✅ {{ session('success') }}
                </div>
            @endif

            <div class="product-grid">
                @foreach($products as $product)
                    <div class="product-card" onclick="openSizeModal({{ $product->id }}, '{{ addslashes($product->nama_produk) }}', {{ $product->harga }}, {{ json_encode($product->sizes) }})">
                        @if($product->gambar)
                            <img src="{{ asset('images/'.$product->gambar) }}" class="product-img" alt="img">
                        @else
                            <div class="product-img">👖</div>
                        @endif
                        <div class="product-name">{{ $product->nama_produk }}</div>
                        <div class="product-price">Rp {{ number_format($product->harga, 0, ',', '.') }}</div>
                        <div class="product-stock">Stok Global: {{ $product->stok }}</div>
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

    <!-- MODAL PILIH UKURAN -->
    <div class="modal-overlay" id="sizeModal">
        <div class="modal-box">
            <div class="modal-title" id="modalProductName">Nama Produk</div>
            <p style="font-size: 13px; color: #64748b; margin-top: -10px; margin-bottom: 15px;">Pilih ukuran yang dibeli pelanggan:</p>
            
            <div class="size-grid" id="modalSizeGrid">
                <!-- Tombol ukuran akan dirender di sini via JS -->
            </div>

            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="closeSizeModal()">Batal</button>
                <button type="button" class="btn-add" id="btnConfirmAdd" disabled>Tambahkan</button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let cart = [];

        // Variabel penampung sementara untuk Modal
        let tempProduct = null;
        let tempSelectedSize = null;

        function formatRupiah(number) {
            return new Intl.NumberFormat('id-ID').format(number);
        }

        // --- FUNGSI MODAL ---
        function openSizeModal(id, name, price, sizesArray) {
            tempProduct = { id, name, price };
            tempSelectedSize = null;

            document.getElementById('modalProductName').innerText = name;
            const grid = document.getElementById('modalSizeGrid');
            grid.innerHTML = '';

            document.getElementById('btnConfirmAdd').disabled = true;

            if (sizesArray.length === 0) {
                grid.innerHTML = '<span style="color:red; font-size:13px;">Admin belum mengatur ukuran untuk produk ini.</span>';
            } else {
                sizesArray.forEach(sizeObj => {
                    let btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'size-btn';
                    btn.innerText = sizeObj.ukuran;
                    
                    if (sizeObj.stok <= 0) {
                        btn.disabled = true;
                        btn.title = "Stok Habis";
                    } else {
                        btn.onclick = function() {
                            // Hapus status selected dari tombol lain
                            document.querySelectorAll('.size-btn').forEach(b => b.classList.remove('selected'));
                            this.classList.add('selected');
                            
                            tempSelectedSize = {
                                ukuran: sizeObj.ukuran,
                                stok: sizeObj.stok
                            };
                            document.getElementById('btnConfirmAdd').disabled = false;
                        };
                    }
                    grid.appendChild(btn);
                });
            }

            document.getElementById('sizeModal').style.display = 'flex';
        }

        function closeSizeModal() {
            document.getElementById('sizeModal').style.display = 'none';
        }

        document.getElementById('btnConfirmAdd').addEventListener('click', function() {
            if(tempSelectedSize) {
                addToCart(tempProduct.id, tempProduct.name, tempProduct.price, tempSelectedSize.ukuran, tempSelectedSize.stok);
                closeSizeModal();
            }
        });

        // --- FUNGSI KERANJANG ---
        function addToCart(id, name, price, ukuran, maxStock) {
            // Unik ID gabungan id produk dan ukuran agar kasir bisa beli produk sama beda ukuran
            let uniqueId = id + '-' + ukuran; 
            let existingItem = cart.find(item => item.uniqueId === uniqueId);
            
            if (existingItem) {
                if(existingItem.qty < maxStock) {
                    existingItem.qty += 1;
                } else {
                    alert(`Stok ukuran ${ukuran} tidak mencukupi! Sisa stok hanya ${maxStock}.`);
                }
            } else {
                cart.push({ uniqueId: uniqueId, id: id, name: name, price: price, ukuran: ukuran, qty: 1, max: maxStock });
            }
            renderCart();
        }

        function updateQty(uniqueId, change) {
            let item = cart.find(item => item.uniqueId === uniqueId);
            if (item) {
                item.qty += change;
                if (item.qty <= 0) {
                    cart = cart.filter(i => i.uniqueId !== uniqueId);
                } else if (item.qty > item.max) {
                    item.qty = item.max; 
                }
            }
            renderCart();
        }

        function renderCart() {
            const container = document.getElementById('cart-container');
            const totalEl = document.getElementById('total-price');
            const btnSubmit = document.getElementById('btn-submit-order');
            
            container.innerHTML = '';
            let total = 0;

            if(cart.length === 0) {
                container.innerHTML = '<div style="text-align: center; color: #94a3b8; font-size: 13px; margin-top: 50px;">Belum ada barang yang dipilih. Klik produk di sebelah kiri.</div>';
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
                        <div class="cart-item-size">Size: ${item.ukuran}</div>
                        <div class="cart-item-price">Rp ${formatRupiah(item.price)}</div>
                    </div>
                    <div class="qty-controls">
                        <button class="qty-btn" onclick="updateQty('${item.uniqueId}', -1)">-</button>
                        <span style="font-weight: 700;">${item.qty}</span>
                        <button class="qty-btn" onclick="updateQty('${item.uniqueId}', 1)">+</button>
                    </div>
                `;
                container.appendChild(div);
            });

            totalEl.innerText = 'Rp ' + formatRupiah(total);
            btnSubmit.disabled = false;
        }

        function submitOrder() {
            if(confirm('Proses pembayaran tunai ini? Stok varian akan otomatis terpotong.')) {
                document.getElementById('cart-data-input').value = JSON.stringify(cart);
                document.getElementById('checkout-form').submit();
            }
        }
    </script>
@endpush