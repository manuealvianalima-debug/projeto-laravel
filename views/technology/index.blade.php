<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Criar Tecnologia - Portfólio de Inovação</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body class="antialiased tecnologia-page">
    @include('layouts.header')

    <section class="header-banner header-banner--compact">
        @php
            $isEn = in_array(strtolower($idiomaSelecionado ?? ''), ['en', 'en_us', 'en-us']);
        @endphp

        <div class="header-content">
            <h1>{{ __('technology.create_title') }}</h1>
            <span class="header-content__subtitle">{{ __('technology.create_subtitle') }}</span>
        </div>
        <div class="top-actions">
            <div class="top-links top-links--row">
                <a href="{{ route('dashboard') }}" class="nav-item">{{ $isEn ? '← Back to Dashboard' : '← Voltar ao Dashboard' }}</a>
            </div>
        </div>
    </section>

    <div class="dashboard-layout">
        <aside class="sidebar">
            <h3>Menu</h3>
            <a href="{{ route('dashboard') }}" class="menu-item">📊 Dashboard</a>
            <a href="{{ route('technology.index') }}" class="menu-item active">{{ __('technology.new_technology') }}</a>
            @if(Auth::user()->isAdmin())
                <a href="{{ route('admin.usuarios.index') }}" class="menu-item">👥 Usuários</a>
            @endif
        </aside>

        <main class="main-panel">
            <div class="tecnologia-page__intro">
                <h1>🔬 <b>{{ __('technology.form_header_title', [], app()->getLocale()) }}</b></h1>
                <p>{{ __('technology.form_intro_desc') }}</p>
            </div>

            @if (session('success'))
                <div class="form-alert form-alert--success">{{ session('success') }}</div>
            @endif

            @if ($errors->any())
                <div class="form-alert form-alert--error">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('technology.store') }}" method="POST" enctype="multipart/form-data" id="tecnologiaForm" class="tecnologia-form">
                @csrf
                {{ app()->getLocale() }}

                <input type="hidden" name="idioma" value="{{ $idiomaCriacao ?? 'pt-br' }}">
                @if($tecnologiaOrigem)
                    <input type="hidden" name="origem" value="{{ $tecnologiaOrigem->id }}">
                @endif

                @include('technology.partials._general')
                
                
                @include('technology.partials._images')
                @include('technology.partials._intellectual_property')
                @include('technology.partials._inventors')
                @include('technology.partials._diseases')
                @include('technology.partials._keywords')

                <!-- Data de submissão -->
                <div class="form-block">
                    <div class="form-field">
                        <label class="form-label">{{ __('technology.submission_date') }} <span class="form-label__required">*</span></label>
                        <input type="date" name="data_submissao" class="form-input" required value="{{ old('data_submissao', now()->toDateString()) }}">
                    </div>
                </div>

                <!-- Botões de ação -->
                <div class="form-actions">
                    <a href="{{ route('dashboard') }}" class="btn-form btn-form--outline">{{ __('technology.cancel') }}</a>
                    <button type="submit" name="action" value="save" class="btn-form btn-form--secondary">{{ __('technology.save_draft') }}</button>
                    <button type="submit" name="action" value="submit" class="btn-form btn-form--primary">{{ __('technology.submit_technology') }}</button>
                </div>
            </form>
        </main>
    </div>

    <div id="loading-overlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(255,255,255,0.8); z-index:9999; text-align:center;">
        <div style="margin-top:20%; font-size:20px;">
            <span id="loading-text">⏳ Processando...</span>
        </div>
    </div>

    <script>
        const form = document.querySelector('form');
        const loadingOverlay = document.getElementById('loading-overlay');
        const loadingText = document.getElementById('loading-text');

        let action = 'save';

        document.querySelectorAll('button[name="action"]').forEach(btn => {
            btn.addEventListener('click', function () {
                action = this.value;
            });
        });

        form.addEventListener('submit', function () {
            if (action === 'submit') {
                loadingText.innerText = '🚀 Publicando tecnologia...';
            } else {
                loadingText.innerText = '💾 Salvando rascunho...';
            }

            loadingOverlay.style.display = 'block';

            document.querySelectorAll('button[type=submit]').forEach(btn => {
                btn.disabled = true;
            });
        });
    </script>

    @include('layouts.footer')

    <style>
        .is-hidden { display: none; }
        .propriedade-item, .inventor-item { animation: fadeIn 0.3s ease-in; }
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
        .btn-form--danger:hover { background: #fecaca; border-color: #f87171; }
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
        .btn-form--ghost:hover { background: #f1f5f9; border-color: #94a3b8; }
        .form-actions--inline { margin-top: 1rem; }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @media (max-width: 768px) { .form-grid--2 { grid-template-columns: 1fr; } }
        [x-cloak] { display: none !important; }
    </style>

    <script>
        function setupCounter(textareaId, counterId, max) {
            const textarea = document.getElementById(textareaId);
            const counter = document.getElementById(counterId);
            if (textarea && counter) {
                textarea.addEventListener('input', function() {
                    const remaining = max - this.value.length;
                    counter.textContent = remaining + ' caracteres restantes';
                    if (remaining < 0) counter.style.color = 'red';
                    else counter.style.color = '';
                });
            }
        }

        let propriedadeIndex = 1;

        function togglePropriedades(select) {
            const container = document.getElementById('propriedadesContainer');
            if (select.value === '1') {
                container.classList.remove('is-hidden');
            } else {
                container.classList.add('is-hidden');
            }
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
                        <label class="form-label">Tipo de propriedade</label>
                        <select name="tipo_propriedade_id[]" class="form-select">
                            <option value="">- Selecione -</option>
                            @foreach($tipos_propriedade as $tipo)
                                <option value="{{ $tipo->id }}">{{ $tipo->nome }}</option>
                            @endforeach
                        </select>
                        <p class="form-hint"><a href="#" class="form-link">Informe aqui se não encontrar o tipo de propriedade intelectual</a></p>
                    </div>
                    <div class="form-field">
                        <label class="form-label">Descrição</label>
                        <textarea name="pi_descricao[]" class="form-textarea" rows="2" placeholder="Descreva a propriedade intelectual..."></textarea>
                    </div>
                    <div class="form-field" style="grid-column: span 2;">
                        <label class="form-label">Link/Referência</label>
                        <input type="text" name="pi_link[]" class="form-input" placeholder="https://... ou referência" maxlength="500">
                        <p class="form-hint">Link ou referência sobre a propriedade intelectual</p>
                    </div>
                    <div class="form-field" style="grid-column: span 2;">
                        <button type="button" class="btn-form btn-form--danger" onclick="this.closest('.propriedade-item').remove()">
                            <span class="material-symbols-outlined">delete</span>
                            Remover propriedade
                        </button>
                    </div>
                </div>
            `;
            container.appendChild(newItem);
            propriedadeIndex++;
        }

        let inventorIndex = 1;

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
                        <label class="form-label">Nome do inventor</label>
                        <input type="text" name="inventores[${inventorIndex}][nome]" class="form-input" placeholder="Nome completo">
                    </div>
                    <div class="form-field">
                        <label class="form-label">Coordenador?</label>
                        <select name="inventores[${inventorIndex}][coordenador]" class="form-select">
                            <option value="0">Não</option>
                            <option value="1">Sim</option>
                        </select>
                    </div>
                    <div class="form-field">
                        <label class="form-label">Link Lattes</label>
                        <input type="url" name="inventores[${inventorIndex}][lattes]" class="form-input" placeholder="http://lattes.cnpq.br/...">
                    </div>
                    <div class="form-field">
                        <label class="form-label">LinkedIn</label>
                        <input type="url" name="inventores[${inventorIndex}][linkedin]" class="form-input" placeholder="https://linkedin.com/in/...">
                    </div>
                    <div class="form-field" style="grid-column: span 4;">
                        <button type="button" class="btn-form btn-form--danger" onclick="this.closest('.inventor-item').remove()">
                            <span class="material-symbols-outlined">delete</span>
                            Remover inventor
                        </button>
                    </div>
                </div>
            `;
            container.appendChild(newItem);
            inventorIndex++;
        }

        const estagiosPorCategoria = @json($estagiosPorCategoria ?? []);
        const selectedEstagioId = '{{ old('estagio_id') }}';

        function atualizarEstagios() {
            const categoriaId = document.getElementById('tipo_tecnologia')?.value;
            const estagioSelect = document.getElementById('estagio_id');

            if (!estagioSelect) return;

            estagioSelect.innerHTML = '<option value="">- Selecione -</option>';

            if (!categoriaId || !estagiosPorCategoria[categoriaId]) {
                estagioSelect.innerHTML = '<option value="">- Selecione a categoria primeiro -</option>';
                return;
            }

            estagiosPorCategoria[categoriaId].forEach(function(estagio) {
                const option = document.createElement('option');
                option.value = estagio.id;
                option.textContent = estagio.nome;
                if (selectedEstagioId === String(estagio.id)) {
                    option.selected = true;
                }
                estagioSelect.appendChild(option);
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            setupCounter('resumo_solucao', 'resumoCounter', 180);
            setupCounter('problema', 'problemaCounter', 700);
            setupCounter('solucao', 'solucaoCounter', 700);

            const possuiPi = document.querySelector('#possui_pi');
            if (possuiPi) {
                togglePropriedades(possuiPi);
            }

            const tipoCategoria = document.getElementById('tipo_tecnologia');
            if (tipoCategoria) {
                tipoCategoria.addEventListener('change', atualizarEstagios);
                atualizarEstagios();
            }

            // Select2 para campos de seleção múltipla
            $('.select2').not('#palavras_chave').select2({
                placeholder: '- Selecione -',
                allowClear: true,
                maximumSelectionLength: 5,
                width: '100%'
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

            // Select2 para idioma
            $('#idioma_select').select2({
                placeholder: '- Selecione o idioma -',
                allowClear: true,
                minimumResultsForSearch: 0
            });

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

            attachLimitAlert(
                '#doencas',
                'Você pode selecionar no máximo 5 doenças relacionadas.',
                5
            );

            attachLimitAlert(
                '#palavras_chave',
                'Você pode informar no máximo 5 palavras-chave.',
                5
            );

        });
    </script>
</body>
</html>
