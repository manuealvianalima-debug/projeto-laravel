<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Minhas Atividades - Portfólio de Inovação</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;600;700;800&family=Open+Sans:wght@300;400;600&display=swap" rel="stylesheet">
</head>
<body class="antialiased">
    @include('layouts.header')
    <section class="header-banner header-banner--compact">
        <div class="header-content"><h1>Minhas Atividades</h1></div>
        <div class="top-actions"><a href="{{ route('profile.show') }}" class="nav-item">← Perfil</a></div>
    </section>
    <div class="dashboard-layout">
        <aside class="sidebar">
            <a href="{{ route('profile.activity') }}" class="menu-item active">📋 Atividades</a>
            @if(Auth::user()->isAdmin())
                <a href="{{ route('admin.atividades.index') }}" class="menu-item">🔍 Monitoramento (todos)</a>
            @endif
        </aside>
        <main class="main-panel">
            <div class="crud-card">
                <h2 class="crud-card__title">Histórico de atividades</h2>
                @include('partials.activity-table', [
                    'activities' => $activities,
                    'showUser' => false,
                    'migrationPending' => !\Illuminate\Support\Facades\Schema::hasTable('activity_logs'),
                ])
            </div>
        </main>
    </div>
    @include('layouts.footer')
</body>
</html>
