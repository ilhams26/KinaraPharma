<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apotek Kinara - Admin</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <div class="container">

        <aside class="sidebar">
            <div class="logo">
                <img src="{{ asset('images/logo_kinara.png') }}" alt="Logo Kinara Pharma">
            </div>

            <div class="menu">
                <a href="/dashboard" class="{{ request()->is('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-home"></i> Beranda
                </a>
                <a href="/data-obat" class="{{ request()->is('data-obat') ? 'active' : '' }}">
                    <i class="fas fa-pills"></i> Data Obat
                </a>
                <a href="#">
                    <i class="fas fa-file-alt"></i> Laporan
                </a>
                <a href="#">
                    <i class="fas fa-users-cog"></i> Kelola User
                </a>
            </div>

            <div class="menu logout-menu">
                <a href="#" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </aside>    
        <div class="main-content">

            <div class="header">
                <div class="role">Admin</div>
                <i class="fas fa-user-circle profile-icon"></i>
            </div>

            @yield('content')

        </div>
    </div>
</body>

</html>