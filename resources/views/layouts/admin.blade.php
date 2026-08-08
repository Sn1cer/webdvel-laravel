<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel - D\'Vel Jeans')</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=DM+Serif+Display&display=swap" rel="stylesheet">
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="icon" href="{{ asset('Dvel/logo.png') }}" type="image/png">
    <style>
        :root { --accent: #d97706; --text: #1e293b; --bg: #f8fafc; --border: #e2e8f0; --sidebar: #ffffff; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg); color: var(--text); margin: 0; padding: 0; display: flex; }
        
        .sidebar { box-sizing: border-box; width: 250px; background: var(--sidebar); border-right: 1px solid var(--border); height: 100vh; position: fixed; top: 0; left: 0; padding-top: 25px; padding-bottom: 40px; display: flex; flex-direction: column; z-index: 1000; transition: transform 0.3s ease; overflow-y: auto; }
        
        .sidebar::-webkit-scrollbar { width: 6px; }
        .sidebar::-webkit-scrollbar-track { background: transparent; }
        .sidebar::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 10px; }
        
        .sidebar-header { padding: 0 25px 20px; border-bottom: 1px solid var(--border); margin-bottom: 25px; }
        .admin-label { font-size: 10px; font-weight: 800; color: var(--accent); text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 5px; }
        .brand { font-family: 'DM Serif Display', serif; font-size: 26px; color: var(--text); margin: 0; }
        
        .menu-label { font-size: 11px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 1.5px; margin: 0 25px 10px; }
        .nav-item { display: flex; align-items: center; padding: 12px 15px; margin: 0 15px 5px; color: #475569; text-decoration: none; font-weight: 600; font-size: 14px; border-radius: 8px; transition: 0.2s; }
        .nav-item:hover { background: #f1f5f9; }
        .nav-active { background: #fffbeb; color: var(--accent); }

        .main-content { margin-left: 250px; flex: 1; min-height: 100vh; display: flex; flex-direction: column; transition: margin-left 0.3s ease; width: 100%; }
        .topbar { background: white; padding: 20px 30px; border-bottom: 1px solid var(--border); font-size: 16px; font-weight: 700; color: var(--text); display: flex; align-items: center; gap: 15px; }
        .content-area { padding: 30px; }
        .hamburger-btn { display: none; background: none; border: none; font-size: 24px; cursor: pointer; color: var(--text); padding: 0; }
        .sidebar-overlay { display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.4); z-index: 999; }

        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.active { transform: translateX(0); }
            .main-content { margin-left: 0; }
            .hamburger-btn { display: block; }
            .sidebar-overlay.active { display: block; }
            .content-area { padding: 15px; }
            
            .welcome-banner { flex-direction: column; text-align: center; gap: 15px; }
            .stats-grid { grid-template-columns: 1fr; }
            .grid-container-responsive { grid-template-columns: 1fr !important; }
            .table-responsive { overflow-x: auto; }
        }
        
        @media print {
            .sidebar, .topbar { display: none !important; }
            .main-content { margin-left: 0 !important; width: 100% !important; }
        }
    </style>
</head>
<body>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="admin-label">Admin Panel</div>
            <h1 class="brand">D'Vel Jeans</h1>
        </div>
        
        <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'nav-active' : '' }}">
            📊 Dashboard
        </a>
        
        <div class="menu-label">Operasional Toko</div>
        
        <a href="{{ route('admin.banners.index') }}" class="nav-item {{ request()->routeIs('admin.banners.*') ? 'nav-active' : '' }}">
            🖼️ Manajemen Banner
        </a>
        <a href="{{ route('admin.pos.index') }}" class="nav-item {{ request()->routeIs('admin.pos.*') ? 'nav-active' : '' }}">
            🛒 Kasir Offline (POS)
        </a>
        <a href="{{ route('products.index') }}" class="nav-item {{ request()->routeIs('products.*') ? 'nav-active' : '' }}">
            📦 Manajemen Produk & Stok
        </a>
        <a href="{{ route('admin.orders.index') }}" class="nav-item {{ request()->routeIs('admin.orders.*') ? 'nav-active' : '' }}">
            🚚 Pesanan Masuk
        </a>
        
        <div class="menu-label" style="margin-top: 20px;">CRM & Laporan</div>
        
        <a href="{{ route('admin.customers.index') }}" class="nav-item {{ request()->routeIs('admin.customers.*') ? 'nav-active' : '' }}">
            👥 Data Pelanggan
        </a>
        
        <div class="menu-label" style="margin-top: 20px;">Sistem Khusus Owner</div>
        
        @if(auth()->check() && auth()->user()->isOwner())
            <a href="{{ route('admin.reports.index') }}" class="nav-item {{ request()->routeIs('admin.reports.*') ? 'nav-active' : '' }}">
                🖨️ Laporan Penjualan
            </a>
            <a href="{{ route('admin.user-management.index') }}" class="nav-item {{ request()->routeIs('admin.user-management.*') ? 'nav-active' : '' }}">
                👑 Kelola Akun Admin
            </a>
        @endif

        <a href="/" target="_blank" class="nav-item" style="margin-top: 20px;">
            🌐 Lihat Website Depan
        </a>
    </div>

    <div class="main-content">
        <div class="topbar">
            <button class="hamburger-btn" id="hamburgerBtn">☰</button>
            @yield('topbar_title', 'Dashboard')
        </div>

        <div class="content-area">
            @yield('content')
        </div>
    </div>

    <script>
        const sidebar = document.getElementById('sidebar');
        const hamburgerBtn = document.getElementById('hamburgerBtn');
        const overlay = document.getElementById('sidebarOverlay');

        function toggleSidebar() {
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
        }

        hamburgerBtn.addEventListener('click', toggleSidebar);
        overlay.addEventListener('click', toggleSidebar); 
    </script>

    @stack('scripts')

</body>
</html>