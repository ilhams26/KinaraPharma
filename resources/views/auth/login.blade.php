<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Apotek Kinara</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>

<body>

    <div class="auth-container">
        <div class="auth-box">

            <div style="margin-bottom: 20px;">
                <img src="{{ asset('images/logo.png') }}" alt="Logo Kinara" style="height: 70px;">
            </div>

            <p class="auth-subtitle">Silakan login untuk mengelola apotek</p>

            @if (session('error'))
                <div class="auth-error">
                    {{ session('error') }}
                </div>
            @endif

            <form action="/login" method="POST">
                @csrf
                <input type="text" name="username" class="auth-input" placeholder="Username" required autofocus>
                <input type="password" name="password" class="auth-input" placeholder="Password" required>

                <button type="submit" class="auth-btn">Masuk</button>
            </form>

            <a href="#" class="auth-link">Lupa Password?</a>
        </div>
    </div>

</body>

</html>