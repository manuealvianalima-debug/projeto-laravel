<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('technology.view_title') }} - {{ $tecnologia->nome }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased tecnologia-page">
    @include('layouts.header')

    <section class="header-banner header-banner--compact">
        <div class="header-content">
            <h1>{{ $tecnologia->titulo ?? $tecnologia->nome }}</h1>
            @php $numeroCaso = trim((string) ($tecnologia->numero_caso ?? '')); @endphp
            @if($numeroCaso !== '')
                <span class="header-content__subtitle">{{ __('technology.case_label', ['number' => $numeroCaso]) }}</span>
            @endif
        </div>
        <div class="top-actions">
            <div class="top-links top-links--row">
                <a href="{{ route('technology.edit', $tecnologia) }}" class="nav-item">✏️ {{ __('technology.edit_label') }}</a>
                <a href="{{ route('dashboard') }}" class="nav-item">← {{ __('technology.dashboard') }}</a>
            </div>
        </div>
    </section>

    <div class="dashboard-layout">
        <aside class="sidebar">
            <h3>{{ __('technology.menu') }}</h3>
            <a href="{{ route('dashboard') }}" class="menu-item">📊 {{ __('technology.dashboard') }}</a>
            <a href="{{ route('technology.index') }}" class="menu-item">{{ __('technology.new_technology') }}</a>
        </aside>

        <main class="main-panel">
            @if(session('success'))
                <div class="form-alert form-alert--success">{{ session('success') }}</div>
            @endif

           {{-- SEÇÃO DE VERSÕES DISPONÍVEIS --}}
@if($todasVersoes->count() > 1)
<div class="crud-card" style="margin-bottom: 1.5rem; background: #f8fafc; border: 2px solid #e2e8f0;">
    <div class="crud-card__header" style="padding: 0.75rem 1rem; border-bottom: 1px solid #e2e8f0; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap;">
        <h3 style="margin:0; font-size:0.9rem; color:#475569; display:flex; align-items:center; gap:0.5rem;">
            🌐 {{ __('technology.available_versions') }}
            <span style="background:#e2e8f0; padding:0.1rem 0.6rem; border-radius:999px; font-size:0.7rem; color:#475569;">
                {{ $todasVersoes->count() }}
            </span>
        </h3>
        <span style="font-size:0.75rem; color:#94a3b8;">
            {{ __('technology.click_to_view') }}
        </span>
    </div>
    <div style="display:flex; flex-wrap:wrap; gap:0.5rem; padding:0.75rem 1rem; max-height:200px; overflow-y:auto;">
        @foreach($todasVersoes->sortBy('idioma') as $versao)
            @php
                $siglaIdioma = App\Models\Idioma::nomeParaSigla((string) $versao->idioma);

                $emoji = match($siglaIdioma) {
                    'pt-br' => '🇧🇷',
                    'en' => '🇺🇸',
                    'es' => '🇪🇸',
                    'fr' => '🇫🇷',
                    'it' => '🇮🇹',
                    'de' => '🇩🇪',
                    default => '🌐'
                };
                $nomeIdioma = App\Models\Idioma::siglaParaNome($siglaIdioma);
            @endphp
            <a href="{{ route('technology.show', $versao) }}" 
               style="display:inline-flex; align-items:center; gap:0.4rem; padding:0.4rem 0.8rem; border-radius:999px; text-decoration:none; font-size:0.8rem; {{ $versao->id === $tecnologia->id ? 'background:#1e293b; color:white;' : 'background:#e2e8f0; color:#1e293b;' }} transition:all 0.2s;"
               onmouseover="if(this.style.backgroundColor !== 'rgb(30, 41, 59)') { this.style.backgroundColor='#cbd5e1'; }"
               onmouseout="if(this.style.backgroundColor !== 'rgb(30, 41, 59)') { this.style.backgroundColor='#e2e8f0'; }"
               title="{{ $nomeIdioma }} - ID: {{ $versao->id }}">
                <span>{{ $emoji }}</span>
                <span>{{ $nomeIdioma }}</span>
                @if($versao->id === $tecnologia->id)
                    <span style="font-size:0.6rem; background:rgba(255,255,255,0.2); padding:0.1rem 0.4rem; border-radius:999px;">atual</span>
                @endif
            </a>
        @endforeach
    </div>
</div>
@endif

            <div class="crud-card">
                <div class="crud-card__header">
                    <div>
                        <h2 class="crud-card__title">{{ __('technology.details_title') }}</h2>
                        <p class="crud-card__desc">
                            {{ __('technology.registered_on', ['date' => optional($tecnologia->data_submissao)->format('d/m/Y')]) }}
                        </p>
                    </div>
                    @if($idiomasFaltantes->isNotEmpty())
                        <div class="dropdown-container" style="position:relative; display:inline-block;">
                            <button type="button" class="btn-form btn-form--secondary" onclick="toggleDropdown(event)" style="cursor:pointer; display:inline-flex; align-items:center; gap:0.5rem;">
                                🌐 {{ __('technology.create_version') }}
                                <span style="font-size:0.75rem;">▼</span>
                            </button>
                            <div id="versionDropdown" style="display:none; position:absolute; top:100%; right:0; background:white; border:1px solid #e2e8f0; border-radius:0.5rem; box-shadow:0 4px 6px -1px rgba(0,0,0,0.1); min-width:200px; z-index:1000; padding:0.5rem 0; margin-top:0.25rem;">
                                @foreach($idiomasFaltantes as $idioma)
                                    @php
                                        $siglaIdioma = App\Models\Idioma::nomeParaSigla((string) $idioma->nome);
                                        $nomeIdioma = App\Models\Idioma::siglaParaNome($siglaIdioma);
                                    @endphp
                                    <a href="{{ route('technology.index', ['idioma' => $idioma->nome, 'origem' => $tecnologia->id]) }}"
                                       style="display:block; padding:0.5rem 1rem; color:#1e293b; text-decoration:none; transition:background 0.2s; border-bottom:1px solid #f1f5f9;"
                                       onmouseover="this.style.backgroundColor='#f1f5f9'"
                                       onmouseout="this.style.backgroundColor='transparent'">
                                        <span style="font-size:1.2rem; margin-right:0.5rem;">
                                            {{ $siglaIdioma === 'pt-br' ? '🇧🇷' : ($siglaIdioma === 'en' ? '🇺🇸' : ($siglaIdioma === 'es' ? '🇪🇸' : ($siglaIdioma === 'fr' ? '🇫🇷' : ($siglaIdioma === 'it' ? '🇮🇹' : ($siglaIdioma === 'de' ? '🇩🇪' : '🌐'))))) }}
                                        </span>
                                        {{ $nomeIdioma }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <div class="tecnologia-meta">
                    <div class="tecnologia-meta__item">
                        <span class="tecnologia-meta__label">{{ __('technology.situation') }}</span>
                        <span>{{ $tecnologia->situacao?->nome ?? '—' }}</span>
                    </div>
                    <div class="tecnologia-meta__item">
                        <span class="tecnologia-meta__label">{{ __('technology.related_unit') }}</span>
                        <span>{{ $tecnologia->unidade?->nome ?? '—' }}</span>
                    </div>
                    <div class="tecnologia-meta__item">
                        <span class="tecnologia-meta__label">{{ __('technology.language') }}</span>
                        <span>
                            @php
                                $siglaIdiomaAtual = App\Models\Idioma::nomeParaSigla((string) $tecnologia->idioma);
                                $descricaoIdioma = App\Models\Idioma::siglaParaNome($siglaIdiomaAtual);
                            @endphp
                            {{ $descricaoIdioma }}
                        </span>
                    </div>
                </div>

                <div class="tecnologia-block">
                    <h3 class="form-section-title">{{ __('technology.solution_summary') }}</h3>
                    <p>{{ $tecnologia->resumo_solucao }}</p>
                </div>

                <div class="tecnologia-block">
                    <h3 class="form-section-title">{{ __('technology.problem') }}</h3>
                    <p>{{ $tecnologia->problema }}</p>
                </div>

                <div class="tecnologia-block">
                    <h3 class="form-section-title">{{ __('technology.solution') }}</h3>
                    <p>{{ $tecnologia->solucao }}</p>
                </div>

                <div class="tecnologia-block">
                    <h3 class="form-section-title">{{ __('technology.category_type') }}</h3>
                    @forelse($tecnologia->categorias as $categoria)
                        <p>{{ $categoria->nome }}</p>
                    @empty
                        <p>—</p>
                    @endforelse
                </div>

                <div class="tecnologia-block">
                    <h3 class="form-section-title">{{ __('technology.stage') }}</h3>
                    <p>{{ $tecnologia->estagio?->nome ?? '—' }}</p>
                    @php
                        preg_match('/^\s*([1-9])\./u', (string) $tecnologia->estagio?->nome, $estagioMatch);
                        $numeroEstagio = $estagioMatch[1] ?? null;
                    @endphp
                    @if($numeroEstagio)
                        <img
                            src="{{ asset('images/Escalas de desenvolvimento TRL png/estagio' . $numeroEstagio . '.png') }}"
                            alt="{{ __('technology.stage') }} {{ $numeroEstagio }}"
                            class="tecnologia-estagio-imagem"
                        >
                    @endif
                </div>

                <div class="tecnologia-block">
                    <h3 class="form-section-title">{{ __('technology.differentials') }}</h3>
                    @if($tecnologia->diferenciais->isNotEmpty())
                        <div class="diferenciais-grid">
                            @foreach($tecnologia->diferenciais as $diff)
                                <div class="diferencial-card">
                                    <div class="diferencial-card-content">
                                        <div class="diferencial-icon">
                                            <span class="material-symbols-outlined">{{ $diff->icone ?? 'help' }}</span>
                                        </div>
                                        <div class="diferencial-info">
                                            <div class="diferencial-nome">{{ $diff->nome }}</div>
                                            @if(!empty($diff->descricao))
                                                <div class="diferencial-tipo">{{ $diff->descricao }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted">{{ __('technology.none_differentials') }}</p>
                    @endif
                </div>

             {{-- ================================
     IMAGEM E VÍDEO
================================ --}}

<div class="tecnologia-media-grid">

    {{-- IMAGEM --}}
    @if($tecnologia->imagem_url)
        <div class="tecnologia-media-card">

           <!-- <h3 class="form-section-title">
                {{ __('technology.image') }}
            </h3> -->
            <h3 class="form-section-title">
           Imagem relacionada:
        </h3>


            <a href="{{ $tecnologia->imagem_url }}"
               target="_blank"
               rel="noopener noreferrer"
               class="tecnologia-image-link">

                <img
                    src="{{ $tecnologia->imagem_url }}"
                    alt="{{ $tecnologia->titulo }}"
                    class="tecnologia-image"
                >
            </a>

        </div>
    @endif


    {{-- VÍDEO --}}
    @if($tecnologia->url_youtube)
        <div class="tecnologia-media-card">

            <!--<h3 class="form-section-title">
                {{ __('technology.video_url') }}
            </h3> -->
        <h3 class="form-section-title">
            Assista ao video:
        </h3>

            <div class="video-container">
                <iframe
                    src="{{ $tecnologia->video_embed_url }}"
                    title="{{ $tecnologia->titulo }}"
                    frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                    allowfullscreen>
                </iframe>
            </div>

            <a
                href="{{ $tecnologia->url_youtube }}"
                target="_blank"
                rel="noopener noreferrer"
                class="tecnologia-video-link"
            >
                Assistir no YouTube
            </a>

        </div>
    @endif

</div>


{{-- DESCRIÇÃO DA MÍDIA --}}
@if($tecnologia->descricao_imagem_video)
    <div class="tecnologia-block tecnologia-media-description">

        <h3 class="form-section-title">
            Descrição da mídia
        </h3>

        <p>
            {{ $tecnologia->descricao_imagem_video }}
        </p>

    </div>
@endif


                @if($tecnologia->doencas->isNotEmpty())
                <div class="tecnologia-block">
                    <h3 class="form-section-title">{{ __('technology.related_diseases') }}</h3>
                    <ul class="tecnologia-list">
                        @foreach($tecnologia->doencas as $d)
                            <li>{{ $d->nome }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                @if($tecnologia->palavrasChave->isNotEmpty())
                <div class="tecnologia-block">
                    <h3 class="form-section-title">{{ __('technology.keywords') }}</h3>
                    <ul class="tecnologia-list">
                        @foreach($tecnologia->palavrasChave as $p)
                            <li>{{ $p->nome }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                @if($tecnologia->propriedadesIntelectuais->isNotEmpty())
    <div class="tecnologia-block">
        <h3 class="form-section-title">
            {{ __('technology.intellectual_properties') }}
        </h3>

        @foreach($tecnologia->propriedadesIntelectuais as $prop)
            <div style="margin-bottom: 1rem; padding-bottom: 1rem; border-bottom: 1px solid #e2e8f0;">

                {{-- Possui PI --}}
                <div class="form-field" style="margin-bottom: 0.75rem;">
                    <label class="form-label">
                        Possui propriedade intelectual?
                    </label>

                    <div>
                        {{ (int) $prop->possui_pi === 1
                            ? __('technology.yes')
                            : __('technology.no') }}
                    </div>
                </div>

                {{-- Tipo da propriedade --}}
                @if($prop->tipo_propriedade_id || $prop->tipoPropriedade)
                    <div class="form-field" style="margin-bottom: 0.75rem;">
                        <label class="form-label">
                            Propriedade intelectual
                        </label>

                        <div>
                            {{ $prop->tipoPropriedade?->nome ?? $prop->tipo ?? '—' }}
                        </div>
                    </div>
                @endif

                {{-- Descrição --}}
                @if($prop->descricao)
                    <div class="form-field" style="margin-bottom: 0.75rem;">
                        <label class="form-label">
                            Descrição da propriedade intelectual
                        </label>

                        <div>
                            {{ $prop->descricao }}
                        </div>
                    </div>
                @endif

                {{-- Link --}}
                @if($prop->link_propriedade || $prop->link)
                    @php
                        $linkValue = $prop->link_propriedade ?? $prop->link;
                    @endphp

                    <div class="form-field" style="margin-bottom: 0.75rem;">
                        <label class="form-label">
                            Link para propriedade intelectual
                        </label>

                        <div>
                            <a href="{{ $linkValue }}"
                               target="_blank"
                               rel="noopener noreferrer">
                                {{ $linkValue }}
                            </a>
                        </div>
                    </div>
                @endif

            </div>
        @endforeach
    </div>
@endif
                @if($tecnologia->inventores->isNotEmpty())
                <div class="tecnologia-block">
                    <h3 class="form-section-title">{{ __('technology.inventors') }}</h3>
                    @foreach($tecnologia->inventores as $inventor)
                        <div class="inventor-item" style="margin-bottom: 1rem; padding-bottom: 1rem; border-bottom: 1px solid #e2e8f0;">
                            <div class="form-grid form-grid--2">
                                <div class="form-field">
                                    <label class="form-label">{{ __('technology.name') }}</label>
                                    <div>{{ $inventor->nome }}</div>
                                </div>
                                <div class="form-field">
                                    <label class="form-label">É coordenador?</label>
                                    <div>{{ $inventor->coordenador ? __('technology.yes') : __('technology.no') }}</div>
                                </div>
                                <div class="form-field">
                                    <label class="form-label">{{ __('technology.lattes') }}</label>
                                    @php $lattes = trim((string) ($inventor->lattes ?? '')); @endphp
                                    <div>
                                        @if(!empty($lattes))
                                            {{ $lattes }}
                                        @else
                                            <span style="color:#94a3b8;">Não informado</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-field">
                                    <label class="form-label">{{ __('technology.linkedin') }}</label>
                                    <div>@if($inventor->linkedin)<a href="{{ $inventor->linkedin }}" target="_blank">{{ $inventor->linkedin }}</a>@else &mdash; @endif</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                @endif

                <div class="form-actions form-actions--inline">
                    <a href="{{ route('technology.edit', $tecnologia) }}" class="btn-form">✏️ {{ __('technology.edit_label') }}</a>

                    <a href="{{ url()->previous() }}" class="btn-form btn-form--outline">{{ __('technology.back') }}</a>
                </div>
            </div>
        </main>
    </div>

    <script>
        function toggleDropdown(event) {
            event.stopPropagation();
            const dropdown = document.getElementById('versionDropdown');

            if (!dropdown) {
                return;
            }

            dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
        }

        document.addEventListener('click', function () {
            const dropdown = document.getElementById('versionDropdown');

            if (dropdown) {
                dropdown.style.display = 'none';
            }
        });
    </script>

    @include('layouts.footer')

    <style>
        .tecnologia-estagio-imagem {
            display: block;
            width: 100%;
            max-width: 720px;
            height: auto;
            margin-top: 1rem;
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
        }
        .diferenciais-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 0.75rem;
        }
        .diferencial-card {
            border: 2px solid #e2e8f0;
            border-radius: 0.5rem;
            background: white;
            transition: all 0.2s;
        }
        .diferencial-card:hover { border-color: #cbd5e1; }
        .diferencial-card-content {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem;
        }
        .diferencial-icon {
            flex-shrink: 0;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f1f5f9;
            border-radius: 0.5rem;
        }
        .diferencial-icon .material-symbols-outlined {
            font-size: 1.5rem;
            color: #4a5568;
        }
        .diferencial-info
