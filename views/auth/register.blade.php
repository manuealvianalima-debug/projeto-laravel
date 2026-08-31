<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Portfólio de Inovação</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;600;700;800&family=Open+Sans:wght@300;400;600&display=swap" rel="stylesheet">
    <script src="https://www.google.com/recaptcha/api.js?hl=pt-BR" async defer></script>
</head>
<body>
    @include('layouts.header')

    <section class="header-banner header-banner--compact">
        <div class="header-content">Criar Conta</div>
    </section>

    <div class="auth-page">
        <div class="auth-card">
            <h2>Registrar</h2>

            @if ($errors->any())
                <div class="auth-errors">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}">
                @csrf
                <div class="auth-field">
                    <input type="text" name="name" value="{{ old('name') }}"
                           class="auth-input" placeholder="Nome" required>
                </div>
                <div class="auth-field">
                    <input type="email" name="email" value="{{ old('email') }}"
                           class="auth-input" placeholder="Email" required>
                </div>
                <div class="auth-field">
                    <input type="password" name="password"
                           class="auth-input" placeholder="Senha" required>
                </div>
                <div class="auth-field">
                    <input type="password" name="password_confirmation"
                           class="auth-input" placeholder="Confirme a senha" required>
                </div>
                <div class="auth-field auth-recaptcha">
                    <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
                </div>
                <button type="submit" class="auth-submit auth-submit--register">Registrar</button>
            </form>

            <p class="auth-footer-link">
                <a href="{{ route('login') }}">Já tenho conta</a>
            </p>
        </div>
    </div>
</body>
</html>
