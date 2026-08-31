<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Atividades — {{ $user->name ?? $user->nome }} - Portfólio de Inovação</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;600;700;800&family=Open+Sans:wght@300;400;600&display=swap" rel="stylesheet">
</head>
<body class="antialiased">
    @include('layouts.header')

    <section class="header-banner header-banner--compact">
        <div class="header-content">
            <h1>Atividades do usuário</h1>
            <span class="header-content__subtitle">{{ $user->email }}</span>
        </div>
        <div class="top-actions">
            <div class="top-links top-links--row">
                <a href="{{ route('admin.usuarios.show', $user->id) }}" class="nav-item">← Usuário</a>
                <a href="{{ route('admin.atividades.index', ['user_id' => $user->id]) }}" class="nav-item">Filtro global</a>
            </div>
        </div>
    </section>

    <div class="dashboard-layout">
        <aside class="sidebar">
            <h3>Administração</h3>
            <a href="{{ route('admin.usuarios.index') }}" class="menu-item">👥 Usuários</a>
            <a href="{{ route('admin.atividades.index') }}" class="menu-item">📋 Monitoramento</a>
        </aside>

        <main class="main-panel">
            <div class="crud-card">
                <h2 class="crud-card__title">{{ $user->name ?? $user->nome ?? 'Usuário' }}</h2>
                @include('partials.activity-table', [
                    'activities' => $activities,
                    'showUser' => false,
                    'migrationPending' => $migrationPending ?? false,
                ])
            </div>
        </main>
    </div>

    @include('layouts.footer')
</body>
</html>
