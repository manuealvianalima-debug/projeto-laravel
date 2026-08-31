<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Meu Perfil - Portfólio de Inovação</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;600;700;800&family=Open+Sans:wght@300;400;600&display=swap" rel="stylesheet">
</head>
<body class="antialiased">
    @include('layouts.header')
    <section class="header-banner header-banner--compact">
        <div class="header-content">
            <h1>Meu Perfil</h1>
            <span class="header-content__subtitle">{{ $user->email }}</span>
        </div>
        <div class="top-actions">
            <div class="top-links top-links--row">
                <a href="{{ route('dashboard') }}" class="nav-item">← Dashboard</a>
                <a href="{{ route('profile.edit') }}" class="nav-item">Editar</a>
            </div>
        </div>
    </section>
    <div class="dashboard-layout">
        <aside class="sidebar">
            <h3>Minha Conta</h3>
            <a href="{{ route('profile.show') }}" class="menu-item active">👤 Perfil</a>
            <a href="{{ route('profile.edit') }}" class="menu-item">✏️ Editar</a>
            <a href="{{ route('profile.password.edit') }}" class="menu-item">🔒 Senha</a>
            <a href="{{ route('profile.activity') }}" class="menu-item">📋 Atividades</a>
        </aside>
        <main class="main-panel">
            @if(session('success'))
                <div class="form-alert form-alert--success">{{ session('success') }}</div>
            @endif
            <div class="crud-card">
                <h2 class="crud-card__title">{{ $user->name ?? $user->nome }}</h2>
                <div class="tecnologia-meta">
                    <div class="tecnologia-meta__item"><span class="tecnologia-meta__label">E-mail</span><span>{{ $user->email }}</span></div>
                    <div class="tecnologia-meta__item"><span class="tecnologia-meta__label">Acesso</span><span>@if($user->isAdmin())<span class="badge-admin badge-admin--sm">Admin</span>@else Usuário @endif</span></div>
                    <div class="tecnologia-meta__item"><span class="tecnologia-meta__label">Membro desde</span><span>{{ $user->created_at?->format('d/m/Y') }}</span></div>
                    @if($user->descricao)<div class="tecnologia-meta__item"><span class="tecnologia-meta__label">Descrição</span><span>{{ $user->descricao }}</span></div>@endif
                </div>
                <div class="form-actions form-actions--inline">
                    <a href="{{ route('profile.edit') }}" class="btn-form btn-form--secondary">Editar perfil</a>
                    <a href="{{ route('profile.activity') }}" class="btn-form btn-form--outline">Ver atividades</a>
                </div>
            </div>
        </main>
    </div>
    @include('layouts.footer')
</body>
</html>
