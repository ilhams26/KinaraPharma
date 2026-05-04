<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Apotek Kinara - Admin</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <div class="container">
        <div class="mobile-header">
            <button class="hamburger-btn" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>
            <div class="mobile-logo">
                <img src="{{ asset('images/logo.png') }}" alt="Logo Kinara Pharma">
            </div>
            <div class="mobile-profile">
                <i class="fas fa-user-circle"></i>
            </div>
        </div>

        <div class="sidebar-overlay" onclick="toggleSidebar()"></div>

        <aside class="sidebar" id="sidebar">
            <div class="logo">
                <img src="{{ asset('images/logo.png') }}" alt="Logo Kinara Pharma">
            </div>
            <div class="menu">
                <a href="/dashboard" class="{{ request()->is('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-home"></i> Beranda
                </a>

                @if(auth()->user()->role === 'admin')
                    <a href="/data-obat" class="{{ request()->is('data-obat') ? 'active' : '' }}">
                        <i class="fas fa-pills"></i> Data Obat
                    </a>
<<<<<<< HEAD
                    <a href="/laporan" class="{{ request()->is('laporan') ? 'active' : '' }}">
                        <i class="fas fa-file-alt"></i> Laporan
=======
                    <a href="{{ route('laporan.index') }}"
                    class="{{ request()->is('laporan*') ? 'active' : '' }}">
                    <i class="fas fa-file-alt"></i> Laporan
>>>>>>> dc97a740fb0f612c7c4dd504cabd1b8bc6d3430a
                    </a>
                    <a href="/users" class="{{ request()->is('users') ? 'active' : '' }}">
                        <i class="fas fa-users-cog"></i> Kelola User
                    </a>

                @elseif(auth()->user()->role === 'staff')
                    <a href="/kelola-obat" class="{{ request()->is('kelola-obat') ? 'active' : '' }}">
                        <i class="fas fa-pills"></i> Kelola Obat
                    </a>
                    <a href="/kelola-stok" class="{{ request()->is('kelola-stok') ? 'active' : '' }}">
                        <i class="fas fa-exchange-alt"></i> Kelola Stok
                    </a>
<<<<<<< HEAD
                    <a href="/laporan" class="{{ request()->is('laporan') ? 'active' : '' }}">
                        <i class="fas fa-file-invoice"></i> Laporan
=======
                    <a href="{{ route('laporan.index') }}"class="{{ request()->is('laporan*') ? 'active' : '' }}"><i class="fas fa-file-alt"></i> Laporan
>>>>>>> dc97a740fb0f612c7c4dd504cabd1b8bc6d3430a
                    </a>
                    <a href="/kasir" class="{{ request()->is('kasir') ? 'active' : '' }}">
                        <i class="fas fa-cash-register"></i> Kasir
                    </a>
                    <a href="/pesanan" class="{{ request()->is('pesanan') ? 'active' : '' }}">
                        <i class="fas fa-shopping-cart"></i> Pesanan
                    </a>
                @endif
            </div>
            <div class="menu logout-menu">
                <a href="#" class="logout-btn" onclick="showLogoutModal(event)">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </aside>

        <div class="main-content">
            <div class="header desktop-header">
                <div class="role">{{ strtoupper(Auth::user()->role) }}</div>
                <i class="fas fa-user-circle profile-icon"></i>
            </div>
            @yield('content')
        </div>
    </div>

    <div class="modal-overlay" id="logoutModal">
        <div class="modal-box">
            <h3>Konfirmasi Logout</h3>
            <p>Apakah Anda yakin ingin keluar ?</p>
            <div class="modal-actions">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-confirm">Ya, Logout</button>
                </form>
                <button class="btn-cancel" onclick="hideLogoutModal()">Batal</button>
            </div>
        </div>
    </div>

    <div id="global-toast"
        style="display: none; position: fixed; top: 20px; left: 50%; transform: translateX(-50%); z-index: 9999; padding: 15px 30px; border-radius: 8px; font-weight: bold; color: white; box-shadow: 0 4px 12px rgba(0,0,0,0.15); transition: opacity 0.3s ease; opacity: 0; min-width: 300px; text-align: center;">
        <i id="global-toast-icon" class="fas fa-check-circle"></i>
        <span id="global-toast-message">Notifikasi</span>
    </div>

    <script src="{{ asset('js/main.js') }}"></script>

    <script>
        window.addEventListener('load', function () {
            @if(session('success'))
                showNotification("{{ session('success') }}", 'success');
            @endif

            @if(session('error'))
                showNotification("{{ session('error') }}", 'error');
            @endif
        });
    </script>
</body>

</html>