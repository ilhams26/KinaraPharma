<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Apotek Kinara</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>

    <div class="auth-container">
        <div class="auth-box">

            <div style="margin-bottom: 10px;">
                <img src="{{ asset('images/logo.png') }}" alt="Logo Kinara Pharma"
                    style="height: 60px; object-fit: contain;">
            </div>

            <p style="color: var(--text-muted); font-size: 14px; margin-bottom: 25px;">
                <i class="far fa-user" style="margin-right: 5px;"></i> Masuk Ke Akun Anda
            </p>

            @if (session('error'))
                <div
                    style="background: #ffebee; color: #c62828; padding: 10px; border-radius: 6px; margin-bottom: 15px; font-size: 13px;">
                    {{ session('error') }}
                </div>
            @endif

            <form action="/login" method="POST">
                @csrf
                <input type="text" name="username" class="auth-input" placeholder="Username" required autofocus>

                <div style="position: relative; width: 100%; margin-bottom: 15px;">
                    <input type="password" name="password" id="password" class="auth-input" placeholder="Password"
                        required style="width: 100%; padding-right: 40px; margin-bottom: 0;">

                    <i class="fas fa-eye toggle-password" onclick="togglePassword()"
                        style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer; color: var(--text-muted);"></i>
                </div>

                <button type="submit" class="auth-btn">Masuk</button>
            </form>

            <a href="#" class="auth-link">Lupa Sandi ?</a>
        </div>
    </div>

    <script src="{{ asset('js/main.js') }}"></script>
</body>

</html>