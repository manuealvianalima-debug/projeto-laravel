<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Alterar Senha - Portfólio de Inovação</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;600;700;800&family=Open+Sans:wght@300;400;600&display=swap" rel="stylesheet">
</head>
<body class="antialiased">
    @include('layouts.header')
    <section class="header-banner header-banner--compact">
        <div class="header-content"><h1>Segurança</h1></div>
        <div class="top-actions"><a href="{{ route('profile.show') }}" class="nav-item">← Perfil</a></div>
    </section>
    <div class="dashboard-layout">
        <main class="main-panel">
            @if ($errors->any())<div class="form-alert form-alert--error"><ul>@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
            <form action="{{ route('profile.password.update') }}" method="POST" class="crud-card">
                @csrf @method('PUT')
                <div class="form-field"><label class="form-label" for="current_password">Senha atual *</label><input type="password" id="current_password" name="current_password" class="form-input" required></div>
                <div class="form-field"><label class="form-label" for="password">Nova senha *</label><input type="password" id="password" name="password" class="form-input" required minlength="8"></div>
                <div class="form-field"><label class="form-label" for="password_confirmation">Confirmar *</label><input type="password" id="password_confirmation" name="password_confirmation" class="form-input" required></div>
                <button type="submit" class="btn-form btn-form--secondary">Atualizar senha</button>
            </form>
        </main>
    </div>
    @include('layouts.footer')
</body>
</html>
