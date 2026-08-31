<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Monitoramento de Atividades - Portfólio de Inovação</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;600;700;800&family=Open+Sans:wght@300;400;600&display=swap" rel="stylesheet">
</head>
<body class="antialiased">
    @include('layouts.header')

    <section class="header-banner header-banner--compact">
        <div class="header-content">
            <h1>Monitoramento de Usuários</h1>
            <span class="header-content__subtitle">Logins, edições e publicações no sistema</span>
        </div>
        <div class="top-actions">
            <div class="top-links top-links--row">
                <a href="{{ route('dashboard') }}" class="nav-item">← Dashboard</a>
                <a href="{{ route('admin.usuarios.index') }}" class="nav-item">👥 Usuários</a>
            </div>
        </div>
    </section>

    <div class="dashboard-layout">
        <aside class="sidebar">
            <h3>Administração</h3>
            <a href="{{ route('dashboard') }}" class="menu-item">📊 Dashboard</a>
            <a href="{{ route('admin.usuarios.index') }}" class="menu-item">👥 Usuários</a>
            <a href="{{ route('admin.atividades.index') }}" class="menu-item active">📋 Monitoramento</a>
        </aside>

        <main class="main-panel">
            <div class="crud-card">
                <h2 class="crud-card__title">Histórico global</h2>

                <form method="GET" action="{{ route('admin.atividades.index') }}" class="form-grid form-grid--2" style="margin-bottom:1.25rem;">
                    <div class="form-field">
                        <label class="form-label" for="user_id">Usuário</label>
                        <select id="user_id" name="user_id" class="form-input">
                            <option value="">Todos</option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}" @selected(request('user_id') == $u->id)>
                                    {{ $u->name ?? $u->nome ?? $u->email }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-field">
                        <label class="form-label" for="action">Tipo</label>
                        <select id="action" name="action" class="form-input">
                            <option value="">Todos</option>
                            @foreach($actionLabels as $key => $label)
                                <option value="{{ $key }}" @selected(request('action') === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-actions form-actions--inline">
                        <button type="submit" class="btn-form btn-form--secondary">Filtrar</button>
                        <a href="{{ route('admin.atividades.index') }}" class="btn-form btn-form--outline">Limpar</a>
                    </div>
                </form>

                @include('partials.activity-table', [
                    'activities' => $activities,
                    'showUser' => true,
                    'migrationPending' => $migrationPending ?? false,
                ])
            </div>
        </main>
    </div>

    @include('layouts.footer')
</body>
</html>
