<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('technology.edit_title') }} - {{ $tecnologia->titulo }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>src='./app.js'</script>

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

</head>
<body class="antialiased tecnologia-page">
    @include('layouts.header')

    <section class="header-banner header-banner--compact">
        <div class="header-content">
            <h1>{{ __('technology.edit_title') }}</h1>
            <span class="header-content__subtitle">{{ __('technology.case_label', ['number' => $tecnologia->numero_caso_fiocruz]) }}</span>
        </div>
        <div class="top-actions">
            <div class="top-links top-links--row">
                <a href="{{ route('technology.show', $tecnologia) }}" class="nav-item">{{ __('technology.view_title') }}</a>
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
            @if ($errors->any())
                <div class="form-alert form-alert--error">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('technology.update', $tecnologia) }}" method="POST" enctype="multipart/form-data" id="tecnologiaForm" class="tecnologia-form">
                @csrf
                @method('PUT')

                <!-- Parte 1 — Informações principais -->
                <details class="form-accordion" open>
                    <summary class="form-accordion__trigger">{{ __('technology.part1_title') }}</summary>
                    <div class="form-accordion__body">
                        <div class="form-grid form-grid--2">
                            <div>
                                <div class="form-field">
                                    <label class="form-label">{{ __('technology.language') }} <span class="form-label__required">*</span></label>
                                    @php $idiomaAtual = App\Models\Idioma::nomeParaSigla((string) ($tecnologia->idioma ?? 'pt-br')); @endphp
                                    <select name="idioma" id="idioma" class="form-select" required onchange="window.location='?idioma=' + this.value">
                                        <option value="pt-br" @selected($idiomaAtual === 'pt-br')>Português (Brasil)</option>
                                        <option value="en" @selected($idiomaAtual === 'en')>English</option>
                                        <option value="es" @selected($idiomaAtual === 'es')>Español</option>
                                    </select>
                                </div>
                                <div class="form-field">
                                    <label class="form-label">{{ __('technology.related_unit') }}</label>
                                    <select name="unidade_id" class="form-select select2">
                                        <option value="">{{ __('technology.select') }}</option>
                                        @foreach($unidades as $unidade)
                                            <option value="{{ $unidade->id }}"
                                                @selected(old('unidade_id', $tecnologia->unidade_id) == $unidade->id)>
                                                {{ $unidade->nome }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-field">
                                    <label class="form-label">{{ __('technology.title') }} <span class="form-label__required">*</span></label>
                                    <input type="text" name="titulo" class="form-input" required maxlength="255"
                                           value="{{ old('titulo', $tecnologia->titulo) }}">
                                </div>
                                <div class="form-field">
                                    <label class="form-label">{{ __('technology.case_number') }}</label>
                                    <input type="text" name="numero_caso_fiocruz" class="form-input" maxlength="255"
                                           value="{{ old('numero_caso_fiocruz', $tecnologia->numero_caso_fiocruz) }}">
                                </div>
                            </div>
                            <div>
                                <div class="form-field">
                                    <label class="form-label">{{ __('technology.solution_summary') }} <span class="form-label__required">*</span></label>
                                    <textarea name="resumo_solucao" class="form-textarea" rows="4" maxlength="180" required>{{ old('resumo_solucao', $tecnologia->resumo_solucao) }}</textarea>
                                    <div class="form-counter" id="resumoCounter">{{ __('technology.character_limit', ['remaining' => 180]) }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="form-field">
                            <label class="form-label">{{ __('technology.problem') }} <span class="form-label__required">*</span></label>
                            <textarea name="problema" class="form-textarea" rows="6" maxlength="700" required>{{ old('problema', $tecnologia->problema) }}</textarea>
                            <div class="form-counter" id="problemaCounter">{{ __('technology.character_limit', ['remaining' => 700]) }}</div>
                        </div>

                        <div class="form-field">
                            <label class="form-label">{{ __('technology.solution') }} <span class="form-label__required">*</span></label>
                            <textarea name="solucao" class="form-textarea" rows="6" maxlength="700" required>{{ old('solucao', $tecnologia->solucao) }}</textarea>
                            <div class="form-counter" id="solucaoCounter">{{ __('technology.character_limit', ['remaining' => 700]) }}</div>
                        </div>

                        <div class="form-field">
                            <label class="form-label">{{ __('technology.we_seek') }}</label>
                            <textarea name="o_que_buscam" class="form-textarea" rows="6">{{ old('o_que_buscam', $tecnologia->o_que_buscam) }}</textarea>
                        </div>

     {{-- Categoria/Tipo de tecnologia --}}
<div class="form-field">
    <label class="form-label"><b>{{ __('technology.category') }}</b></label>

    <div class="dropdown-categoria" id="dropdownCategoria">
        {{-- "Select" visual --}}
        <button type="button" class="form-select dropdown-toggle" id="categoriaToggle">
            {{ __('technology.select') }}
        </button>
        {{-- Menu dropdown --}}
        <div class="dropdown-menu-categoria" id="categoriaMenu" style="display:none;">
            <div class="px-3 py-2 text-muted" style="font-size:.9rem;">
                {{ __('technology.select') }}
            </div>

            @php
                $checkedCategories = old('categorias_multiplas', $selectedCategories ?? []);
            @endphp
            <div class="categoria-list px-3 pb-2">
                @foreach($categorias as $categoria)
                    <label class="categoria-row" for="cat_{{ $categoria->id }}">
                        <span class="categoria-nome">{{ $categoria->nome }}</span>
                       <!-- <div class="d-flex gap-2 mt-2">
                    <button type="button" class="btn btn-secondary btn-sm" id="categoriaLimpar">Limpar</button>
                    <button type="button" class="btn btn-primary btn-sm ms-auto" id="categoriaAplicar">Aplicar</button>
                </div> -->

                        {{-- Checkbox no canto direito --}}
                        <input
                            type="checkbox"
                            class="categoria-checkbox"
                            id="cat_{{ $categoria->id }}"
                            name="categorias_multiplas[]"
                            value="{{ $categoria->id }}"
                            @checked(in_array($categoria->id, $checkedCategories))
                        >
                        <span class="categoria-check"></span>
                    </label>
                @endforeach
            </div>

            <div class="px-3 pb-3">
                <div class="d-flex gap-2 mt-2">
                    <button type="button" class="btn btn-secondary btn-sm" id="categoriaLimpar">Limpar</button>
                    <button type="button" class="btn btn-primary btn-sm ms-auto" id="categoriaAplicar">Aplicar</button>
                </div>
            </div>
        </div>
    </div>

    <p class="form-hint">{{ __('technology.select_category') }}</p>
</div>
        <div class="form-field">
            <label class="form-label"> <b>{{ __('technology.stage') }} </b> </label>
            <div class="estagio-conteudo">
                <div class="estagio-campo">
                    <div class="dropdown-estagio" id="dropdownEstagio">
                        <button type="button" class="form-select dropdown-toggle" id="estagioToggle" aria-haspopup="listbox" aria-expanded="false">
                            {{ __('technology.select_first_category') }}
                        </button>
                        <div class="dropdown-menu-estagio" id="estagioMenu" role="listbox" style="display:none;"></div>
                    </div>
                    <select id="estagio_id" name="estagio_id" class="estagio-select-native" tabindex="-1" aria-hidden="true" hidden>
                        <option value="">{{ __('technology.select_first_category') }}</option>
                    </select>
                    <p class="form-hint">{{ __('technology.select_stage') }}</p>
                </div>
                <section id="estagioDescricao" class="estagio-descricao" hidden aria-live="polite">
                    <h3>Sobre este estágio</h3>
                    <p id="estagioDescricaoTexto"></p>
                </section>
            </div>
        </div>

                        <!-- Categoria/Tipo de tecnologia
                        <div class="form-field">
                            <label class="form-label">{{ __('technology.category') }}</label>
                            <select id="tipo_tecnologia" name="tipo_tecnologia" class="form-select">
                                <option value="">{{ __('technology.select') }}</option>
                                @foreach($categorias as $categoria)
                                    <option value="{{ $categoria->id }}"
                                        @selected(old('tipo_tecnologia', $tecnologia->categorias->first()?->id) == $categoria->id)>
                                        {{ $categoria->nome }}
                                    </option>
                                @endforeach
                            </select>
                        </div>-->
                       <!-- <div class="categoria-list px-3 pb-2">
                            <div class="categoria-list px-3 pb-2">
                            @php
                                // 'old' tem prioridade, senão usa as categorias já salvas (edição)
                                $checkedCategories = old('categorias_multiplas', $selectedCategories ?? []);
                            @endphp

                            @foreach($categorias as $categoria)
                                <label class="categoria-row" for="cat_{{ $categoria->id }}">
                                    <span class="categoria-nome">{{ $categoria->nome }}</span>
                                    <input
                                        type="checkbox"
                                        class="categoria-checkbox"
                                        id="cat_{{ $categoria->id }}"
                                        name="categorias_multiplas[]"
                                        value="{{ $categoria->id }}"
                                        @checked(in_array($categoria->id, $checkedCategories))
                                    >
                                    <span class="categoria-check"></span>
                                </label>
                            @endforeach
                        </div>
                        <div class="form-field">
                            <label class="form-label">{{ __('technology.stage') }}</label>
                            <select id="estagio_id" name="estagio_id" class="form-select">
                                <option value="">{{ __('technology.select_first_category') }}</option>
                            </select>
                        </div> -->

                        <x-diferenciais-manager
                        :icones="$icones"
                        :diferenciais-disponiveis="$diferenciais"
                         :old-diferenciais="$selectedDiferenciais"
                        :idioma-id="$idiomaId"
                    />
                    </div>
                </details>

                {{-- Parte 2 — Imagens e vídeo --}}
                @include('technology.partials._images', [
                    'tecnologia' => $tecnologia
                ])

                <!-- Parte 3 — Propriedade Intelectual e Inventores -->
                <details class="form-accordion">
                        <summary class="form-accordion__trigger">{{ __('technology.part3_title') }}</summary>
                            <div class="form-accordion__body">
                                <div class="form-field">
                                    <label class="form-label">{{ __('technology.has_ip_question') }} <span class="form-label__required">*</span></label>
                                    <select name="possui_pi" id="possui_pi" class="form-select" required onchange="togglePropriedades(this)">
                                        <option value="0" @selected(!old('possui_pi', $tecnologia->possui_pi))>{{ __('technology.no') }}</option>
                                        <option value="1" @selected(old('possui_pi', $tecnologia->possui_pi))>{{ __('technology.yes') }}</option>
                                    </select>
                                </div>

                            <div id="propriedadesContainer" class="{{ old('possui_pi', $tecnologia->possui_pi) ? '' : 'is-hidden' }}">
                                <h3 class="form-section-title">{{ __('technology.intellectual_properties') }}</h3>
                                <div id="propriedadesList">
                                    @if($tecnologia->propriedadesIntelectuais->isNotEmpty())
                                        @foreach($tecnologia->propriedadesIntelectuais as $index => $propriedade)
                                            <div class="propriedade-item" style="{{ $index > 0 ? 'margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #e2e8f0;' : '' }}">
                                                <div class="form-grid form-grid--2">
                                                    <div class="form-field">
                                                        <label class="form-label">{{ __('technology.intellectual_property') }}</label>
                                                        <select name="tipo_propriedade_id[]" class="form-select">
                                                            <option value="">- Selecione -</option>
                                                            @foreach($tipos_propriedade as $tipo)
                                                                <option value="{{ $tipo->id }}" @selected($propriedade->tipo_propriedade_id == $tipo->id)>
                                                                    {{ $tipo->nome }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        <p class="form-hint"><a href="#" class="form-link">{{ __('technology.intellectual_property_help') }}</a></p>
                                                    </div>
                                                    <div class="form-field">
                                                        <label class="form-label">{{ __('technology.description') }}</label>
                                                        <textarea name="pi_descricao[]" class="form-textarea" rows="2">{{ $propriedade->descricao }}</textarea>
                                                    </div>
                                                    <div class="form-field" style="grid-column: span 2;">
                                                        <label class="form-label">{{ __('technology.link_placeholder') }}</label>
                                                        <input type="text" name="pi_link[]" class="form-input" placeholder="{{ __('technology.link_placeholder') }}" maxlength="500" value="{{ $propriedade->link_propriedade ?? $propriedade->link }}">
                                                        <p class="form-hint">{{ __('technology.link_placeholder') }}</p>
                                                    </div>
                                                    @if($index > 0)
                                                    <div class="form-field" style="grid-column: span 2;">
                                                        <button type="button" class="btn-form btn-form--danger" onclick="this.closest('.propriedade-item').remove()">
                                                            <span class="material-symbols-outlined">delete</span>
                                                            {{ __('technology.remove_property') }}
                                                        </button>
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="propriedade-item">
                                            <div class="form-grid form-grid--2">
                                                <div class="form-field">
                                                    <label class="form-label">{{ __('technology.intellectual_property') }}</label>
                                                    <select name="tipo_propriedade_id[]" class="form-select">
                                                        <option value="">{{ __('technology.select') }}</option>
                                                        @foreach($tipos_propriedade as $tipo)
                                                            <option value="{{ $tipo->id }}">{{ $tipo->nome }}</option>
                                                        @endforeach
                                                    </select>
                                                    <p class="form-hint"><a href="#" class="form-link">{{ __('technology.intellectual_property_help') }}</a></p>
                                                </div>
                                                <div class="form-field">
                                                    <label class="form-label">Descrição</label>
                                                    <textarea name="pi_descricao[]" class="form-textarea" rows="2"></textarea>
                                                </div>
                                                <div class="form-field" style="grid-column: span 2;">
                                                    <label class="form-label">{{ __('technology.link_placeholder') }}</label>
                                                    <input type="text" name="pi_link[]" class="form-input" placeholder="https://... ou referência" maxlength="500">
                                                    <p class="form-hint">{{ __('technology.intellectual_property_reference') }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="form-actions form-actions--inline" style="margin-top: 1rem;">
                                    <button type="button" class="btn-form btn-form--ghost" onclick="adicionarPropriedadeIntelectual()">
                                        <span class="material-symbols-outlined">add</span>
                                        {{ __('technology.add_intellectual_property') }}
                                    </button>
                                </div>
                            </div>
                        </details>
                        @include('technology.partials._inventors')
                 <!--   <details class="form-accordion">
                        <summary class="form-accordion__trigger">👥 Inventores</summary>
                        <div class="form-accordion__body">
                             <div id="inventoresContainer">
                                <div class="form-field">
                                    @if($tecnologia->inventores && $tecnologia->inventores->count() > 0)
                                    @foreach($tecnologia->inventores as $index => $inventor)
                                    <div class="inventor-item" style="{{ $index > 0 ? 'margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #e2e8f0;' : '' }}">
                                        <div class="form-grid form-grid--2">
                                            <div class="form-field">
                                                <label class="form-label">{{ __('technology.name') }}</label>
                                                <input type="text" name="inventores[{{ $index }}][nome]" class="form-input" value="{{ $inventor->nome }}">
                                            </div>
                                            <div class="form-field">
                                                <label class="form-label">{{ __('technology.coordinator') }}</label>
                                                <select name="inventores[{{ $index }}][coordenador]" class="form-select">
                                                    <option value="0" @selected(!$inventor->coordenador)>{{ __('technology.no') }}</option>
                                                    <option value="1" @selected($inventor->coordenador)>{{ __('technology.yes') }}</option>
                                                </select>
                                            </div>
                                            <div class="form-field">
                                                <label class="form-label">{{ __('technology.lattes') }}</label>
                                                <input type="url" name="inventores[{{ $index }}][lattes]" class="form-input" placeholder="{{ __('technology.lattes_placeholder') }}" value="{{ $inventor->lattes }}">
                                            </div>
                                            <div class="form-field">
                                                <label class="form-label">{{ __('technology.linkedin') }}</label>
                                                <input type="url" name="inventores[{{ $index }}][linkedin]" class="form-input" placeholder="{{ __('technology.linkedin_placeholder') }}" value="{{ $inventor->linkedin }}">
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                @endforeach
                             @else
                                <div class="inventor-item">
                                    <div class="form-grid form-grid--2">
                                        <div class="form-field">
                                            <label class="form-label">{{ __('technology.name') }}</label>
                                            <input type="text" name="inventores[0][nome]" class="form-input" placeholder="{{ __('technology.inventor_name_placeholder') }}">
                                        </div>
                                        <div class="form-field">
                                            <label class="form-label">{{ __('technology.coordinator') }}</label>
                                            <select name="inventores[0][coordenador]" class="form-select">
                                                <option value="0">{{ __('technology.no') }}</option>
                                                <option value="1">{{ __('technology.yes') }}</option>
                                            </select>
                                        </div>
                                        <div class="form-field">
                                            <label class="form-label">{{ __('technology.lattes') }}</label>
                                            <input type="url" name="inventores[0][lattes]" class="form-input" placeholder="{{ __('technology.lattes_placeholder') }}">
                                        </div>
                                        <div class="form-field">
                                            <label class="form-label">{{ __('technology.linkedin') }}</label>
                                            <input type="url" name="inventores[0][linkedin]" class="form-input" placeholder="{{ __('technology.linkedin_placeholder') }}">
                                        </div>
                                    </div>

                                    @endif
                                    <div class="form-actions form-actions--inline" style="margin-top: 1rem;">
                                    <button type="button" class="btn-form btn-form--ghost" onclick="adicionarInventor()">
                                        <span class="material-symbols-outlined">add</span>
                                        {{ __('technology.add_inventor') }}
                                    </button>
                                    </div>
                            </div>
                        </div>
                    </details>

                -->
                @include('technology.partials._diseases')
                @include('technology.partials._keywords')
                <!-- Data de submissão -->
                <div class="form-block">
                    <div class="form-field">
                        <label class="form-label">{{ __('technology.submission_date') }} <span class="form-label__required">*</span></label>
                        <input type="date" name="data_submissao" class="form-input" required
                               value="{{ old('data_submissao', optional($tecnologia->data_submissao)->format('Y-m-d')) }}">
                    </div>
                </div>

                <!-- Botões de ação -->
                <div class="form-actions">
                    <a href="{{ route('technology.show', $tecnologia) }}" class="btn-form btn-form--outline">{{ __('technology.cancel') }}</a>
                    <button type="submit" name="action" value="save" class="btn-form btn-form--secondary">{{ __('technology.save_changes') }}</button>
                    @if(($tecnologia->situacao?->nome ?? '') === 'Rascunho')
                        <button type="submit" name="action" value="submit" class="btn-form btn-form--primary">{{ __('technology.publish_technology') }}</button>
                    @endif
                </div>
            </form>
        </main>
    </div>

    @include('layouts.footer')

    <style>
        .keyword-field .select2-container--default .select2-selection--multiple { min-height: 44px; padding: 4px; border-color: #94a3b8; }
        .keyword-field .select2-container--default .select2-selection--multiple .select2-selection__choice { margin-top: 4px; padding: 4px 7px; color: #1e3a8a; background: #dbeafe; border-color: #93c5fd; border-radius: 999px; }
        .keyword-field .select2-container--default .select2-selection--multiple .select2-selection__choice__remove { color: #1d4ed8; }
        .is-hidden {
            display: none;
        }
        .propriedade-item,
        .inventor-item {
            animation: fadeIn 0.3s ease-in;
        }
        .btn-form--danger {
            background: #fee2e2;
            border: 1px solid #fecaca;
            color: #dc2626;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s;
        }
        .btn-form--danger:hover {
            background: #fecaca;
            border-color: #f87171;
        }
        .btn-form--ghost {
            background: transparent;
            border: 1px solid #cbd5e1;
            color: #475569;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s;
        }
        .btn-form--ghost:hover {
            background: #f1f5f9;
            border-color: #94a3b8;
        }
        .btn-form--secondary {
            background: #64748b;
            border: 1px solid #64748b;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s;
        }
        .btn-form--secondary:hover {
            background: #475569;
        }
        .form-actions--inline {
            margin-top: 1rem;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .dropdown-categoria {
            position: relative;
        }
        .dropdown-menu-categoria {
            position: absolute;
            z-index: 1000;
            left: 0;
            right: 0;
            margin-top: 6px;
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: .6rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, .08);
            max-height: 320px;
            overflow: auto;
        }
        .categoria-list {
            padding: 0;
        }
        .categoria-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 10px 12px;
            cursor: pointer;
            user-select: none;
            border-top: 1px solid rgba(0, 0, 0, .04);
        }
        .categoria-row:first-child {
            border-top: none;
        }
        .categoria-nome {
            flex: 1;
            text-align: left;
            color: #0f172a;
        }
        .categoria-row .categoria-checkbox {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }
        .categoria-check {
            width: 22px;
            height: 22px;
            border: 1.5px solid #cbd5e1;
            border-radius: 4px;
            background: #fff;
            flex: 0 0 22px;
            display: inline-block;
            position: relative;
        }
        .categoria-row .categoria-checkbox:checked + .categoria-check {
            background: #0d6efd;
            border-color: #0d6efd;
        }
        .categoria-row .categoria-checkbox:checked + .categoria-check::after {
            content: "";
            position: absolute;
            left: 7px;
            top: 3px;
            width: 6px;
            height: 12px;
            border: solid #fff;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
        }
        .estagio-descricao {
            padding: 14px 16px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            color: #334155;
        }
        .estagio-descricao h3 {
            margin: 0 0 6px;
            font-size: .9rem;
            font-weight: 600;
            color: #475569;
        }
        .estagio-descricao p {
            margin: 0;
            font-size: .9rem;
            line-height: 1.5;
            white-space: pre-line;
        }
        .estagio-conteudo {
            display: grid;
            gap: 12px;
        }
        .dropdown-estagio {
            position: relative;
        }
        .dropdown-menu-estagio {
            position: absolute;
            z-index: 1000;
            left: 0;
            right: 0;
            margin-top: 6px;
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: .6rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, .08);
            max-height: 320px;
            overflow-y: auto;
        }
        .estagio-option {
            padding: 10px 12px;
            cursor: pointer;
            color: #0f172a;
            border-top: 1px solid rgba(0, 0, 0, .04);
        }
        .estagio-option:first-child {
            border-top: none;
        }
        .estagio-option:hover {
            background: #f1f5f9;
        }
        .estagio-option--disabled {
            cursor: default;
            color: #94a3b8;
        }
        /* O select mantém o valor enviado ao Laravel; o dropdown acima é o campo visível. */
        .estagio-select-native {
            position: absolute;
            width: 1px;
            height: 1px;
            opacity: 0;
            pointer-events: none;
        }
        @media (min-width: 768px) {
            .estagio-conteudo {
                grid-template-columns: minmax(0, 1fr) minmax(260px, 1fr);
                align-items: start;
            }
        }
    </style>

    <script>
        // Contadores de caracteres
        function setupCounter(textareaId, counterId, max) {
            const textarea = document.getElementById(textareaId);
            const counter = document.getElementById(counterId);
            const characterLimitTemplate = '{{ __('technology.character_limit', ['remaining' => ':remaining']) }}';
            if (textarea && counter) {
                textarea.addEventListener('input', function() {
                    const remaining = max - this.value.length;
                    counter.textContent = characterLimitTemplate.replace(':remaining', remaining);
                    counter.style.color = remaining < 0 ? 'red' : '';
                });
                textarea.dispatchEvent(new Event('input'));
            }
        }

        // Controle de Propriedade Intelectual
        let propriedadeIndex = {{ $tecnologia->propriedadesIntelectuais->count() ?: 1 }};

        function togglePropriedades(select) {
            const container = document.getElementById('propriedadesContainer');
            container.classList.toggle('is-hidden', select.value !== '1');
        }

        function adicionarPropriedadeIntelectual() {
            const container = document.getElementById('propriedadesList');
            const newItem = document.createElement('div');
            newItem.className = 'propriedade-item';
            newItem.style.marginTop = '1rem';
            newItem.style.paddingTop = '1rem';
            newItem.style.borderTop = '1px solid #e2e8f0';
            newItem.innerHTML = `
                <div class="form-grid form-grid--2">
                    <div class="form-field">
                        <label class="form-label">{{ __('technology.intellectual_property') }}</label>
                        <select name="tipo_propriedade_id[]" class="form-select">
                            <option value="">{{ __('technology.select') }}</option>
                            @foreach($tipos_propriedade as $tipo)
                                <option value="{{ $tipo->id }}">{{ $tipo->nome }}</option>
                            @endforeach
                        </select>
                        <p class="form-hint"><a href="#" class="form-link">{{ __('technology.intellectual_property_help') }}</a></p>
                    </div>
                    <div class="form-field">
                        <label class="form-label">{{ __('technology.description') }}</label>
                        <textarea name="pi_descricao[]" class="form-textarea" rows="2"></textarea>
                    </div>
                    <div class="form-field" style="grid-column: span 2;">
                        <label class="form-label">{{ __('technology.link_placeholder') }}</label>
                        <input type="text" name="pi_link[]" class="form-input" placeholder="{{ __('technology.link_placeholder') }}" maxlength="500">
                        <p class="form-hint">{{ __('technology.intellectual_property_reference') }}</p>
                    </div>
                    <div class="form-field" style="grid-column: span 2;">
                        <button type="button" class="btn-form btn-form--danger" onclick="this.closest('.propriedade-item').remove()">
                            <span class="material-symbols-outlined">delete</span>
                            {{ __('technology.remove_property') }}
                        </button>
                    </div>
                </div>
            `;
            container.appendChild(newItem);
            propriedadeIndex++;
        }

        // Controle de Inventores
        let inventorIndex = {{ $tecnologia->inventores ? $tecnologia->inventores->count() : 1 }};

        function adicionarInventor() {
            const container = document.getElementById('inventoresContainer');
            const newItem = document.createElement('div');
            newItem.className = 'inventor-item';
            newItem.style.marginTop = '1rem';
            newItem.style.paddingTop = '1rem';
            newItem.style.borderTop = '1px solid #e2e8f0';
            newItem.innerHTML = `
                <div class="form-grid form-grid--2">
                    <div class="form-field">
                        <label class="form-label">{{ __('technology.name') }}</label>
                        <input type="text" name="inventores[${inventorIndex}][nome]" class="form-input" placeholder="{{ __('technology.inventor_name_placeholder') }}">
                    </div>
                    <div class="form-field">
                        <label class="form-label">{{ __('technology.coordinator') }}</label>
                        <select name="inventores[${inventorIndex}][coordenador]" class="form-select">
                            <option value="0">{{ __('technology.no') }}</option>
                            <option value="1">{{ __('technology.yes') }}</option>
                        </select>
                    </div>
                    <div class="form-field">
                        <label class="form-label">{{ __('technology.lattes') }}</label>
                        <input type="url" name="inventores[${inventorIndex}][lattes]" class="form-input" placeholder="{{ __('technology.lattes_placeholder') }}">
                    </div>
                    <div class="form-field">
                        <label class="form-label">{{ __('technology.linkedin') }}</label>
                        <input type="url" name="inventores[${inventorIndex}][linkedin]" class="form-input" placeholder="{{ __('technology.linkedin_placeholder') }}">
                    </div>
                    <div class="form-field" style="grid-column: span 4;">
                        <button type="button" class="btn-form btn-form--danger" onclick="this.closest('.inventor-item').remove()">
                            <span class="material-symbols-outlined">delete</span>
                            {{ __('technology.remove_inventor') }}
                        </button>
                    </div>
                </div>
            `;
            container.appendChild(newItem);
            inventorIndex++;
        }

       // ============================================================
// ESTÁGIOS
// ============================================================

const estagiosPorCategoria = @json($estagiosPorCategoria);

const selectedEstagioId = @json(
    old(
        'estagio_id',
        $tecnologia->categorias->first()?->pivot->estagio_id
        ?? $tecnologia->estagio_id
    )
);

const estagioSelect =
    document.getElementById('estagio_id');

const estagioToggle =
    document.getElementById('estagioToggle');

const estagioMenu =
    document.getElementById('estagioMenu');

const estagioDescricao =
    document.getElementById('estagioDescricao');

const estagioDescricaoTexto =
    document.getElementById('estagioDescricaoTexto');


// ============================================================
// ABRIR / FECHAR DROPDOWN
// ============================================================

estagioToggle?.addEventListener('click', function (event) {

    event.stopPropagation();

    const aberto =
        estagioMenu.style.display === 'block';

    estagioMenu.style.display =
        aberto ? 'none' : 'block';

    estagioToggle.setAttribute(
        'aria-expanded',
        String(!aberto)
    );
});



// ============================================================
// ATUALIZA DESCRIÇÃO
// ============================================================

function atualizarDescricaoEstagio(descricao = null) {

    if (!estagioDescricao || !estagioDescricaoTexto) {
        return;
    }

    if (descricao === null) {

        descricao =
            estagioSelect
                ?.selectedOptions[0]
                ?.dataset.descricao
                ?.trim();
    }

    estagioDescricao.hidden = !descricao;

    estagioDescricaoTexto.textContent =
        descricao || '';
}


// ============================================================
// ATUALIZA OS ESTÁGIOS
// ============================================================

function atualizarEstagios() {

    const checkboxes = Array.from(
        document.querySelectorAll('.categoria-checkbox')
    );

    const selected = checkboxes
        .filter(checkbox => checkbox.checked)
        .map(checkbox => checkbox.value);


    if (!estagioSelect || !estagioMenu) {
        return;
    }


    // --------------------------------------------------------
    // LIMPA OS DOIS CAMPOS
    // --------------------------------------------------------

    estagioSelect.innerHTML = '';

    estagioMenu.innerHTML = '';


    // --------------------------------------------------------
    // NENHUMA CATEGORIA
    // --------------------------------------------------------

    if (selected.length === 0) {

        const option =
            document.createElement('option');

        option.value = '';

        option.textContent =
            '{{ __("technology.select_first_category") }}';

        estagioSelect.appendChild(option);

        estagioSelect.disabled = true;

        estagioToggle.textContent =
            '{{ __("technology.select_first_category") }}';

        estagioMenu.style.display = 'none';

        atualizarDescricaoEstagio('');

        return;
    }


    // --------------------------------------------------------
    // DEFINE A CATEGORIA
    // --------------------------------------------------------

    const categoriaId =
        selected.length === 1
            ? selected[0]
            : '2';

    const estagios =
        estagiosPorCategoria[categoriaId] || [];


    // --------------------------------------------------------
    // CRIA CADA ESTÁGIO
    // --------------------------------------------------------

   estagios.forEach(function (estagio) {


        // ====================================================
        // SELECT REAL
        // ====================================================

        const option =
            document.createElement('option');

        option.value =
            estagio.id;

        option.textContent =
            estagio.nome;

        option.dataset.descricao =
            estagio.descricao || '';

        estagioSelect.appendChild(option);


        // ====================================================
        // DROPDOWN VISUAL
        // ====================================================

        const item =
            document.createElement('div');

        item.className =
            'estagio-option';

        item.setAttribute(
            'role',
            'option'
        );

        item.dataset.id =
            estagio.id;

        item.textContent =
            estagio.nome;


        // ----------------------------------------------------
        // MOUSE
        // ----------------------------------------------------

        item.addEventListener(
            'mouseenter',
            function () {

                atualizarDescricaoEstagio(
                    estagio.descricao || ''
                );
            }
        );

        // ----------------------------------------------------
        // CLIQUE
        // ----------------------------------------------------

        item.addEventListener(
            'click',
            function () {

                estagioSelect.value =
                    estagio.id;

                estagioToggle.textContent =
                    estagio.nome;

                atualizarDescricaoEstagio(
                    estagio.descricao || ''
                );

                estagioMenu.style.display =
                    'none';

                estagioToggle.setAttribute(
                    'aria-expanded',
                    'false'
                );
            }
        );


        estagioMenu.appendChild(item);
    });


    estagioSelect.disabled = false;


    // ========================================================
    // EDITAR — RECUPERA O ESTÁGIO SALVO
    // ========================================================

    const selecionado =
        estagios.find(function (estagio) {

            return String(estagio.id) ===
                String(selectedEstagioId);
        });


    if (selecionado) {

        estagioSelect.value =
            selecionado.id;

        estagioToggle.textContent =
            selecionado.nome;

        atualizarDescricaoEstagio(
            selecionado.descricao || ''
        );

    } else {

        estagioToggle.textContent =
            '{{ __("technology.select_stage") }}';

        atualizarDescricaoEstagio('');
    }

}

    

        const selectLabel = '{{ __("technology.select") }}';

        function atualizarTextoCategoria() {
            const checkboxes = Array.from(document.querySelectorAll('.categoria-checkbox'));
            const selected = checkboxes.filter(checkbox => checkbox.checked);
            const toggle = document.getElementById('categoriaToggle');

            if (!toggle) {
                return;
            }

            if (selected.length === 0) {
                toggle.textContent = selectLabel;
            } else if (selected.length === 1) {
                toggle.textContent = selected[0].closest('.categoria-row')?.querySelector('.categoria-nome')?.textContent?.trim() || selectLabel;
            } else {
                toggle.textContent = `${selected.length} categorias selecionadas`;
            }
        }

        // Inicialização
        document.addEventListener('DOMContentLoaded', function() {
            setupCounter('resumo_solucao', 'resumoCounter', 180);
            setupCounter('problema', 'problemaCounter', 700);
            setupCounter('solucao', 'solucaoCounter', 700);

            const possuiPi = document.querySelector('#possui_pi');
            if (possuiPi) togglePropriedades(possuiPi);

            const dropdownCategoria = document.getElementById('dropdownCategoria');
            const categoriaToggle = document.getElementById('categoriaToggle');
            const categoriaMenu = document.getElementById('categoriaMenu');
            const categorias = Array.from(document.querySelectorAll('.categoria-checkbox'));
            const categoriaLimpar = document.getElementById('categoriaLimpar');
            const categoriaAplicar = document.getElementById('categoriaAplicar');

            categoriaToggle?.addEventListener('click', function (event) {
                event.stopPropagation();
                categoriaMenu.style.display = categoriaMenu.style.display === 'block' ? 'none' : 'block';
            });

            document.addEventListener('click', function (event) {
                if (dropdownCategoria && !dropdownCategoria.contains(event.target)) {
                    categoriaMenu.style.display = 'none';
                }
            });


            categoriaLimpar?.addEventListener('click', function () {
                categorias.forEach(categoria => categoria.checked = false);
                atualizarTextoCategoria();
                atualizarEstagios();
            });

            categoriaAplicar?.addEventListener('click', function () {
                atualizarTextoCategoria();
                atualizarEstagios();
                categoriaMenu.style.display = 'none';
            });

            categorias.forEach(function (categoria) {
                categoria.addEventListener('change', function () {
                    atualizarTextoCategoria();
                    atualizarEstagios();
                });
            });

            atualizarTextoCategoria();
            atualizarEstagios();

            // Select2
            $('.select2').select2({
                placeholder: '{{ __('technology.select') }}',
                allowClear: true,
                maximumSelectionLength: 5
            });

            function inicializarPalavrasChave() {
                const $palavrasChave = $('#palavras_chave');
                if (!$palavrasChave.length || $palavrasChave.hasClass('select2-hidden-accessible')) return;

                $palavrasChave.select2({
                    tags: true,
                    maximumSelectionLength: 5,
                    tokenSeparators: [','],
                    placeholder: 'Digite uma palavra-chave',
                    allowClear: true,
                    width: '100%',
                    language: {
                        maximumSelected: function () { return 'Você pode informar no máximo 5 palavras-chave.'; },
                        noResults: function () { return 'Pressione Enter para adicionar esta palavra-chave.'; }
                    },
                    createTag: function(params) {
                        const termo = $.trim(params.term);
                        return termo === '' ? null : { id: termo, text: termo, newTag: true };
                    }
                });
            }

            window.addEventListener('load', inicializarPalavrasChave, { once: true });

            // Mostrar popup quando o usuário atingir o limite (padronizar com diferenciais)
            function attachLimitAlert(selector, message, max) {
                const $el = $(selector);
                if (!$el.length) return;

                $el.on('select2:selecting', function (e) {
                    const current = $el.val() || [];
                    if (current.length >= max) {
                        e.preventDefault();
                        alert(message);
                    }
                });
            }

            attachLimitAlert('#doencas', '{{ __('technology.max_diseases_alert') }}', 5);
            attachLimitAlert('#palavras_chave', '{{ __('technology.max_keywords_alert') }}', 5);
        });

    </script>
</body>
</html>
