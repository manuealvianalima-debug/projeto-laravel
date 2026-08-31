<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>    
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard - Portfólio de Inovação</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700;800&family=Raleway:wght@400;600;700;800&family=Open+Sans:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        /* Estilos corporativos adicionais */
        .user-profile-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px;
            padding: 1.5rem;
            color: white;
            margin-bottom: 2rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .user-profile-card h2 {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
            font-family: 'Raleway', sans-serif;
        }
        .user-profile-card .user-email {
            opacity: 0.9;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }
        .user-profile-card .user-nit {
            font-size: 0.85rem;
            opacity: 0.8;
            font-family: monospace;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 1.25rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border-left: 4px solid;
        }
        .stat-card.primary { border-left-color: #3b82f6; }
        .stat-card.success { border-left-color: #10b981; }
        .stat-card.warning { border-left-color: #f59e0b; }
        .stat-card.purple { border-left-color: #8b5cf6; }
        .stat-card .stat-label {
            font-size: 0.85rem;
            text-transform: uppercase;
            color: #6b7280;
            margin-bottom: 0.5rem;
        }
        .stat-card .stat-value {
            font-size: 1.75rem;
            font-weight: bold;
            color: #1f2937;
        }
        .dashboard-filters {
            background: white;
            border-radius: 10px;
            padding: 1rem 1.25rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
        }
        .filter-form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1rem;
            align-items: end;
        }
        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 0.35rem;
        }
        .filter-group label {
            font-size: 0.85rem;
            font-weight: 600;
            color: #4b5563;
        }
        .filter-group input,
        .filter-group select {
            border: 1px solid #d1d5db;
            border-radius: 8px;
            padding: 0.6rem 0.75rem;
            font-size: 0.95rem;
        }
        .filter-actions {
            display: flex;
            gap: 0.75rem;
        }
        .filter-btn {
            border: none;
            border-radius: 8px;
            padding: 0.7rem 1rem;
            font-weight: 600;
            cursor: pointer;
        }
        .filter-btn--submit {
            background: #2563eb;
            color: white;
        }
        .filter-btn--reset {
            background: #e5e7eb;
            color: #374151;
        }
        .filter-summary {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: 1rem;
            color: #475569;
            font-size: 0.9rem;
        }
        .filter-summary__tag {
            padding: 0.3rem 0.6rem;
            border-radius: 999px;
            background: #dbeafe;
            color: #1d4ed8;
            font-weight: 600;
        }
        .filter-result-count {
            display: inline-flex;
            align-items: center;
            margin-top: 0.5rem;
            padding: 0.35rem 0.7rem;
            border-radius: 999px;
            background: #ecfdf5;
            color: #047857;
            font-size: 0.85rem;
            font-weight: 700;
        }
    </style>
</head>
<body class="antialiased">
    @include('layouts.header')

    <section class="header-banner">
        <div class="header-content">
            <h1>Dashboard</h1>
            <span class="header-content__subtitle">Área Administrativa Fiocruz</span>
        </div>

        <div class="top-actions">
            <div class="welcome">Olá, {{ Auth::user()->name }}!</div>
            <div class="top-links top-links--row">
                <a href="{{ url('/') }}" class="nav-item">🏡 Voltar ao Home</a>
                @if(Auth::user()->isAdmin() || Auth::user()->email === 'manuela.viana@fiocruz.br')
                    <a href="{{ route('admin.usuarios.index') }}" class="nav-item">👥 Usuários ({{ \App\Models\User::count() }})</a>
                    <a href="{{ route('admin.atividades.index') }}" class="nav-item">📋 Monitoramento</a>
                @endif
                <form method="POST" action="{{ route('logout') }}" class="is-hidden" id="logout-form-dashboard">
                    @csrf
                </form>
                <button type="submit" form="logout-form-dashboard" class="nav-item nav-item--plain">🚪 Sair</button>
            </div>
        </div>
    </section>

    <div class="dashboard-layout">
        <aside class="sidebar">
            <h3>Menu</h3>
                @if(Auth::user()->isAdmin() || Auth::user()->email === 'manuela.viana@fiocruz.br')
                    <a href="{{ route('dashboard') }}" class="menu-item active">📊 Visão Geral</a>
                    <a href="{{ route('technology.index') }}" class="menu-item">➕ Nova tecnologia</a>
                    <a href="#lista-tecnologias" class="menu-item">💾 Publicados</a>
                <a href="#rascunhos" class="menu-item">📝 Meus Rascunhos</a>
                <a href="#excluidos" class="menu-item">🗑️ Tecnologias Excluídas</a>
                <a href="{{ route('admin.atividades.index') }}" class="menu-item">📋 Monitoramento</a>
                <a href="{{ route('admin.usuarios.index') }}" class="menu-item">👥 Gerenciar Usuários</a>
            @else
                <a href="{{ route('dashboard') }}" class="menu-item active">📋 Minhas Tecnologias</a>
                <a href="#rascunhos" class="menu-item">📝 Meus Rascunhos</a>
                <a href="{{ route('technology.index') }}" class="menu-item">➕ Nova tecnologia</a>
            @endif
        </aside>

        <main class="main-panel">
            @if(session('success'))
                <div class="dashboard-card dashboard-card--success">
                    <h3>✅ {{ session('success') }}</h3>
                </div>
            @endif

            <!-- Card de Perfil do Usuário (para todos os usuários) -->
            <div class="user-profile-card">
                <h2>{{ Auth::user()->name }}</h2>
                <div class="user-email">{{ Auth::user()->email }}</div>
                <div class="user-nit">NIT: {{ Auth::user()->nit ?? 'Não informado' }}</div>
            </div>

            <!-- VISÃO GERAL - Apenas para Administradores -->
            @if(Auth::user()->isAdmin() || Auth::user()->email === 'manuela.viana@fiocruz.br')
                <div class="stats-grid">
                    <div class="stat-card primary">
                        <div class="stat-label">Total de Usuários</div>
                        <div class="stat-value">{{ \App\Models\User::count() }}</div>
                    </div>
                    <div class="stat-card success">
                        <div class="stat-label">Logins Hoje</div>
                        <div class="stat-value">{{ \App\Models\User::whereDate('updated_at', today())->count() }}</div>
                    </div>
                    <div class="stat-card warning">
                        <div class="stat-label">Total Tecnologias no Sistema</div>
                        <div class="stat-value">{{ \App\Models\Tecnologias_idiomas::count() }}</div>
                    </div>
                    <!-- Bloco removido completamente para evitar o erro -->
                </div>
            @endif

            
<div class="dashboard-filters">
    <form method="GET" action="{{ route('dashboard') }}" class="filter-form">
        <div class="filter-group">
            <label for="idioma">Idioma</label>

            <select name="idioma" id="idioma">
                <option value="">Todos</option>
                <option value="2" {{ (string)($filters['idioma'] ?? '') === '2' ? 'selected' : '' }}>
                    Inglês
                </option>
                <option value="1" {{ (string)($filters['idioma'] ?? '') === '1' ? 'selected' : '' }}>
                    Português
                </option>
            </select>
        </div>

        <div class="filter-group">
            <label for="status">Status</label>

            <select name="status" id="status">
                <option value="todos" {{ ($filters['status'] ?? 'todos') === 'todos' ? 'selected' : '' }}>
                    Todos
                </option>
                <option value="3" {{ (string)($filters['status'] ?? '') === '3' ? 'selected' : '' }}>
                    Publicado
                </option>
                <option value="1" {{ (string)($filters['status'] ?? '') === '1' ? 'selected' : '' }}>
                    Rascunho
                </option>
                <option value="5" {{ (string)($filters['status'] ?? '') === '5' ? 'selected' : '' }}>
                    Validação Gestec
                </option>
            </select>
        </div>

        <div class="filter-group">
            <label for="unidade_id">Unidade</label>

            <select name="unidade_id" id="unidade_id">
                <option value="">Todas</option>

                @foreach($unidades as $unidade)
                    <option value="{{ $unidade->id }}"
                        {{ ($filters['unidade_id'] ?? '') == $unidade->id ? 'selected' : '' }}>
                        {{ $unidade->nome }}{{ $unidade->sigla ? ' (' . $unidade->sigla . ')' : '' }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="filter-actions">
            <button type="submit" class="filter-btn filter-btn--submit">
                Filtrar
            </button>

            <a href="{{ route('dashboard') }}" class="filter-btn filter-btn--reset">
                Limpar
            </a>
        </div>
    </form>
</div>
 
@php
    $filtrosAtivos = [];

    if (filled($filters['idioma'] ?? null)) {
        $filtrosAtivos[] = 'Idioma: ' .
            App\Models\Idioma::siglaParaNome((string) $filters['idioma']);
    }

   
$statusSelecionado = (string) ($filters['status'] ?? 'todos');

if ($statusSelecionado === '') {
    $statusSelecionado = 'todos';
}

    if ($statusSelecionado !== 'todos') {
        $nomesStatus = [
            '1' => 'Rascunho',
            '3' => 'Publicado',
            '5' => 'Validação Gestec',
        ];

        $filtrosAtivos[] = 'Status: ' .
            ($nomesStatus[$statusSelecionado] ?? $statusSelecionado);
    }

    if (filled($filters['unidade_id'] ?? null)) {
        $unidadeFiltrada = $unidades->firstWhere(
            'id',
            $filters['unidade_id']
        );

        $filtrosAtivos[] = 'Unidade: ' .
            ($unidadeFiltrada?->nome ?? $filters['unidade_id']);
    }

    $semFiltros =
        empty($filters['idioma'] ?? null) &&
        $statusSelecionado === 'todos' &&
        empty($filters['unidade_id'] ?? null);

    $quantidadeExibida = $semFiltros
        ? \App\Models\Tecnologias_idiomas::count()
        : $tecnologias->count();
@endphp


<div class="filter-summary" aria-live="polite">
    <span>
        Exibindo
        <strong>{{ $quantidadeExibida }}</strong>
        tecnologia(s).
    </span>

    @if ($filtrosAtivos)
        <span>Filtros ativos:</span>

        @foreach ($filtrosAtivos as $filtroAtivo)
            <span class="filter-summary__tag">
                {{ $filtroAtivo }}
            </span>
        @endforeach
    @else
        <span>Nenhum filtro aplicado.</span>
    @endif
</div>

            <!-- Minhas Tecnologias -->
        <div class="crud-card section" id="lista-tecnologias">
            <div class="crud-card__header">
                <div>
                    <h3 class="crud-card__title">
                        Tecnologias
                    </h3>
                    <span class="filter-result-count">                      
                    Resultados da filtragem: {{ $tecnologias->count() }}
                    </span>
                        <p class="crud-card__desc">
                            @if(Auth::user()->isAdmin() || Auth::user()->email === 'manuela.viana@fiocruz.br')
                                Lista de tecnologias publicadas no sistema.
                            @else
                                Tecnologias que você cadastrou no sistema.
                            @endif
                        </p>
                    </div>
                    
                <a href="{{ route('technology.index') }}" class="action-btn action-view">
                    ➕ Nova tecnologia
                </a>
            </div>
                
            <div class="table-scroll">
                <table class="crud-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nome da Tecnologia</th>
                            <!--<th>Número do Caso</th> -->
                            <th>Categoria Principal</th>
                            <th>Estágio de Desenvolvimento</th>
                            <th>Status</th>
                            <th>Data</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                            @forelse($tecnologias as $tecnologia)
                                    <tr>
                                        <td>{{ $tecnologia->id }}</td>
                                        <td>{{ $tecnologia->titulo ?? $tecnologia->nome ?? '—' }}</td>
                                        <!--<td>{{ $tecnologia->numero_caso_fiocruz ?? '—' }}</td>-->
                                        <td>{{ $tecnologia->categorias->first()?->nome ?? '—' }}</td>
                                        <td>{{ $tecnologia->estagio?->nome ?? '—' }}</td>
                                        <td>{{ $tecnologia->situacao?->nome ?? '—' }}</td>
                                        <td>{{ optional($tecnologia->data_submissao ?? $tecnologia->created_at)->format('d/m/Y') }}</td>
                                        <td class="action-group">
                                            <a href="{{ route('technology.show', $tecnologia) }}" class="action-btn action-view">Ver</a>
                                            @if(Auth::user()->isAdmin() || Auth::user()->email === 'manuela.viana@fiocruz.br' || Auth::user()->id === ($tecnologia->id_user_criador ?? $tecnologia->user_id ?? null))
                                                <a href="{{ route('technology.edit', $tecnologia) }}" class="action-btn action-edit">Editar</a>
                                                <form action="{{ route('technology.destroy', $tecnologia) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir essa tecnologia?');" style="display:inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="action-btn action-delete">Excluir</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                            @empty
                                <tr>                                    
                                <td colspan="7" class="crud-table__empty">
                                        @if(Auth::user()->isAdmin())
                                            Nenhuma tecnologia cadastrada no sistema ainda.
                                        @else
                                            Você ainda não publicou nenhuma tecnologia. 
                                            <a href="{{ route('technology.index') }}">Clique aqui para cadastrar sua primeira tecnologia</a>
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @include('dashboard.partials.rascunhos')
            
            @if(Auth::user()->isAdmin())
                @include('dashboard.partials.excluidos')
            @endif

        </main>
    </div>
    @include('layouts.footer')

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (window.location.hash === '#excluidos') {
                const excluidosSection = document.getElementById('excluidos');
                if (excluidosSection) {
                    excluidosSection.classList.remove('hidden');
                }

                const activeLink = document.querySelector('.menu-item[href="#excluidos"]');
                if (activeLink) {
                    document.querySelectorAll('.menu-item').forEach(l => l.classList.remove('active'));
                    activeLink.classList.add('active');
                }
            }
        });
    </script>
</body>
</html>
