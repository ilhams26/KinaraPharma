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
                <span>APOTEK KINARA</span>
            </div>
            <ul class="menu">
                <li class="active">
                    <i class="fas fa-home"></i> <span>Beranda</span>
                </li>
                <li>
                    <i class="fas fa-pills"></i> <span>Data Obat</span>
                </li>
                <li>
                    <i class="fas fa-file-alt"></i> <span>Laporan</span>
                </li>
                <li>
                    <i class="fas fa-users-cog"></i> <span>Kelola User</span>
                </li>
            </ul>

            <ul class="menu" style="flex: 0; margin-bottom: 20px;">
                <li style="color: #ffcccc;">
                    <i class="fas fa-sign-out-alt"></i> <span>Logout</span>
                </li>
            </ul>
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