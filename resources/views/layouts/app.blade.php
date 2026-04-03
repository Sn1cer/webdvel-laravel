<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', "D'Vel Jeans - Official Store")</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&family=DM+Serif+Display&display=swap" rel="stylesheet">
    
    <style>
        /* --- CSS GLOBAL & NAVBAR --- */
        :root { --accent: #d97706; --text: #1e293b; --bg: #f8fafc; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg); color: var(--text); margin: 0; padding: 0; overflow-x: hidden; }
        
        /* Navigasi Atas */
        .navbar { background: white; padding: 20px 50px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 10px rgba(0,0,0,0.05); position: sticky; top: 0; z-index: 1000; }
        .logo { font-family: 'DM Serif Display', serif; font-size: 28px; color: var(--text); text-decoration: none; }
        
        .nav-links { display: flex; align-items: center; gap: 24px; }
        .nav-links a.nav-link { text-decoration: none; color: var(--text); font-weight: 600; transition: color 0.2s; white-space: nowrap; }
        .nav-links a.nav-link:hover { color: var(--accent); }
        
        .btn-login { border: 2px solid var(--accent); padding: 8px 20px; border-radius: 20px; color: var(--accent) !important; text-decoration: none; font-weight: 600; display: inline-block; transition: 0.2s; text-align: center; white-space: nowrap;}
        .btn-login:hover { background: var(--accent); color: white !important; }

        /* Tombol Keluar (Merah) */
        .btn-logout { border-color: #ef4444 !important; color: #ef4444 !important; }
        .btn-logout:hover { background: #ef4444 !important; color: white !important; }

        /* Tombol Hamburger (Disembunyikan di Laptop) */
        .mobile-menu-btn { display: none; font-size: 24px; background: none; border: none; cursor: pointer; color: var(--text); padding: 0; }

        /* Footer Sederhana */
        .store-footer { background: white; border-top: 1px solid #e2e8f0; padding: 30px 20px; text-align: center; margin-top: 60px; font-size: 14px; color: #64748b; }

        /* --- RESPONSIVE NAVBAR (MOBILE) --- */
        @media (max-width: 768px) {
            .navbar { padding: 15px 20px; }
            .mobile-menu-btn { display: block; } 
            
            .nav-links {
                position: absolute; top: 100%; left: 0; width: 100%; background: white;
                flex-direction: column; align-items: flex-start; padding: 20px; gap: 15px;
                box-shadow: 0 10px 10px rgba(0,0,0,0.05); border-top: 1px solid #e2e8f0;
                transform: translateY(-10px); opacity: 0; pointer-events: none; transition: all 0.3s ease;
            }
            .nav-links.active { transform: translateY(0); opacity: 1; pointer-events: auto; }
            .nav-links a.nav-link { width: 100%; padding-bottom: 10px; border-bottom: 1px solid #f1f5f9; }
            
            /* Di sini baru kita set width 100% khusus untuk HP */
            .btn-login, .form-logout { width: 100%; }
        }
    </style>
    
    @stack('styles')
</head>
<body>

    <nav class="navbar">
        <a href="/" class="logo">D'Vel Jeans</a>
        
        <button class="mobile-menu-btn" id="mobileMenuBtn">☰</button>

        <div class="nav-links" id="navLinks">
            <a href="/" class="nav-link">Beranda</a>
            <a href="/#katalog" class="nav-link">Katalog Produk</a>

            @guest
                <a href="{{ route('login') }}" class="btn-login">Masuk</a>
            @endguest

            @auth
                <a href="{{ route('cart.index') }}" style="position: relative; color: var(--text); font-size: 22px; text-decoration: none; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">
                    🛒
                    @php 
                        $cartCount = \App\Models\Cart::where('user_id', Auth::id())->sum('jumlah'); 
                    @endphp
                    @if($cartCount > 0)
                        <span style="position: absolute; top: -8px; right: -12px; background: #ef4444; color: white; font-size: 11px; font-weight: 800; padding: 2px 6px; border-radius: 50%; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                            {{ $cartCount }}
                        </span>
                    @endif
                </a>

                <a href="{{ route('orders.history') }}" class="nav-link" style="color: var(--accent);">📦 Pesanan Saya</a>

                @if(Auth::user()->email === 'admin@dveljeans.com')
                    <a href="{{ route('admin.dashboard') }}" class="btn-login" style="background: var(--accent); border-color: var(--accent); color: white !important;">Panel Admin</a>
                @else
                    <span style="font-weight: 700; color: var(--text); white-space: nowrap;">Halo, {{ explode(' ', Auth::user()->name)[0] }}</span>
                @endif
                
                <form method="POST" action="{{ route('logout') }}" style="margin: 0; padding: 0;" class="form-logout">
                    @csrf
                    <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();" class="btn-login btn-logout">Keluar</a>
                </form>
            @endauth
        </div>
    </nav>

    @yield('content')

    <footer class="store-footer">
        <div style="font-family: 'DM Serif Display', serif; font-size: 20px; color: var(--text); margin-bottom: 10px;">D'Vel Jeans</div>
        <p>&copy; {{ date('Y') }} D'Vel Jeans Official Store. Semua Hak Cipta Dilindungi.</p>
    </footer>

    <script>
        document.getElementById('mobileMenuBtn').addEventListener('click', function() {
            document.getElementById('navLinks').classList.toggle('active');
        });
    </script>

    @stack('scripts')

</body>
</html>