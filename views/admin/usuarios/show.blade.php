<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
        @if(isset($user))
            Atividades — {{ $user->name ?? $user->nome }} - Portfólio de Inovação
        @else
            Usuários - Portfólio de Inovação
        @endif
    </title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;600;700;800&family=Open+Sans:wght@300;400;600&display=swap" rel="stylesheet">
</head>
<body class="antialiased">
    @include('layouts.header')

    @if(isset($user))
        {{-- MODO ATIVIDADES DO USUÁRIO --}}
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
                    @include('partials.user-details', ['user' => $user])
                </div>
            </main>
        </div>
    @else
        {{-- MODO LISTA DE USUÁRIOS --}}
        <section class="header-banner header-banner--compact">
            <div class="header-content">
                <h1>Usuários</h1>
                <span class="header-content__subtitle">Gerenciamento de usuários</span>
            </div>
            <div class="top-actions">
                <div class="top-links top-links--row">
                    <a href="{{ route('admin.usuarios.create') }}" class="nav-item">+ Novo Usuário</a>
                </div>
            </div>
        </section>

        <div class="dashboard-layout">
            <aside class="sidebar">
                <h3>Administração</h3>
                <a href="{{ route('admin.usuarios.index') }}" class="menu-item active">👥 Usuários</a>
                <a href="{{ route('admin.atividades.index') }}" class="menu-item">📋 Monitoramento</a>
            </aside>

            <main class="main-panel">
                <div class="crud-card">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Email</th>
                                <th>Admin</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users ?? [] as $u)
                                <tr>
                                    
                                    <td>{{ $u->name ?? $u->nome }}</td>
                                    <td>{{ $u->email }}</td>
                                    <th>Unidade</th>
                                    <td>{{ $u->is_admin ? 'Sim' : 'Não' }}</td>
                                    <td>
                                        <a href="{{ route('admin.usuarios.show', $u) }}">Ver</a>
                                        <a href="{{ route('admin.usuarios.atividades', $u) }}">Atividades</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $users->links() ?? '' }}
                </div>
            </main>
        </div>
    @endif

    @include('layouts.footer')
</body>
</html>