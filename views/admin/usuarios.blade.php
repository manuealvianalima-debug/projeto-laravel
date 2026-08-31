<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gerenciar Usuários - Portfólio de Inovação</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;600;700;800&family=Open+Sans:wght@300;400;600&display=swap" rel="stylesheet">
</head>
<body class="antialiased">
    @include('layouts.header')

    <section class="header-banner header-banner--compact">
        <div class="header-content">
            <h1>Gerenciar Usuários</h1>
            <span class="header-content__subtitle">Administração de contas e permissões</span>
        </div>
        <div class="top-actions">
            <div class="top-links top-links--row">
                <a href="{{ route('dashboard') }}" class="nav-item">← Dashboard</a>
                <a href="{{ route('admin.atividades.index') }}" class="nav-item">📋 Monitoramento</a>
            </div>
        </div>
    </section>

    <div class="dashboard-layout">
        <aside class="sidebar">
            <h3>Menu Administrativo</h3>
            <a href="{{ route('dashboard') }}" class="menu-item">📊 Dashboard</a>
            <a href="{{ route('admin.usuarios.index') }}" class="menu-item active">👥 Usuários</a>
            <a href="{{ route('admin.atividades.index') }}" class="menu-item">📋 Monitoramento</a>
            <a href="{{ route('technology.index') }}" class="menu-item">➕ Nova tecnologia</a>
        </aside>

        <main class="main-panel">
            @if(session('success'))
                <div class="form-alert form-alert--success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="form-alert form-alert--error">{{ session('error') }}</div>
            @endif

            <div class="crud-card">
                <div class="crud-card__header">
                    <div>
                        <h2 class="crud-card__title">Usuários cadastrados</h2>
                        <p class="crud-card__desc">Total: {{ $users->total() }} usuário(s)</p>
                        
                    </div>
                </div>

                <div class="table-scroll">
                    <table class="crud-table crud-table--users">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>Email</th>
                                <th>Unidade</th>
                                <th>Perfil</th>
                                <th>Criado em</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td>{{ $user->id }}</td>
                                    <td>{{ $user->name ?? $user->nome ?? '—' }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @if($user->isAdmin())
                                            <span class="badge-admin badge-admin--sm">Administrador</span>
                                        @else
                                            <span class="action-group__hint">Usuário comum</span>
                                        @endif
                                    </td>
                                    <td>{{ $user->created_at?->format('d/m/Y H:i') ?? '—' }}</td>
                                    <td>
                                        <div class="action-group action-group--compact">
                                            <a href="{{ route('admin.usuarios.show', $user) }}" class="action-btn action-btn--compact action-view">Ver</a>
                                            <a href="{{ route('admin.usuarios.atividades', $user) }}" class="action-btn action-btn--compact action-edit">Histórico</a>

                                            @if(Auth::id() !== $user->id)
                                                <form action="{{ route('admin.usuarios.toggleAdmin', $user) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="action-btn action-btn--compact action-admin" title="Alternar permissão de administrador">
                                                        {{ $user->isAdmin() ? 'Remover admin' : 'Tornar admin' }}
                                                    </button>
                                                </form>
                                                <form action="{{ route('admin.usuarios.destroy', $user) }}" method="POST"
                                                      onsubmit="return confirm('Tem certeza que deseja excluir este usuário?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="action-btn action-btn--compact action-delete">Excluir</button>
                                                </form>
                                            @else
                                                <span class="action-group__hint">Você</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($users->hasPages())
                    <div style="margin-top:1rem;">{{ $users->links() }}</div>
                @endif
            </div>
        </main>
    </div>

    @include('layouts.footer')
</body>
</html>
