<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Portfólio de Inovação</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{-- <script src="https://www.google.com/recaptcha/api.js?hl=pt-BR" async defer></script> --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;600;700;800&family=Open+Sans:wght@300;400;600&display=swap" rel="stylesheet">
</head>
<body>
    @include('layouts.header')

    <section class="header-banner header-banner--compact">
        <div class="header-content">Entrar na Plataforma</div>
    </section>

    <div class="auth-page">
        <div class="auth-card">
            <h2>Login</h2>

            @if ($errors->any())
                <div class="auth-errors">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login.store') }}">
                @csrf
                <div class="auth-field">
                    <input type="email" name="email" value="{{ old('email') }}"
                           class="auth-input" placeholder="Email" required>
                </div>
                <div class="auth-field">
                    <input type="password" name="password"
                           class="auth-input" placeholder="Senha" required>
                </div>
                {{-- <div class="auth-field auth-recaptcha">
                    <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
                </div> --}}
                <button type="submit" class="auth-submit">Entrar</button>
            </form>
        </div>
    </div>
</body>
</html>
