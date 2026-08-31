<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;600;700;800&family=Open+Sans:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        .header-banner {
            width: 100%;
            min-height: 150px;
            background-image: url('/images/header-banner.png');
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
        }
        .header-content {
            color: white;
            padding: 2rem 2.5rem;
            font-family: 'Raleway', sans-serif;
            font-size: clamp(1.8rem, 5vw, 3rem);
            font-weight: 700;
            line-height: 1.25;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }
    </style>
</head>
<body>
    @include('layouts.header')
    <div class="header-banner">
        <div class="header-content">Entrar na Plataforma</div>
    </div>
    <h1>Login</h1>
     @if ($errors->any())
        <div class="error-message">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif
    <form method="POST" action="{{ route('login') }}">
        @csrf
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Senha" required>
        <button type="submit">Entrar</button>
    </form>
    <a href="{{ route('register') }}">Criar conta</a>
</body>
</html>
