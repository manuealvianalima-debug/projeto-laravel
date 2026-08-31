<details class="form-accordion" open>
    <summary class="form-accordion__trigger">{{ __('technology.part1_title') }}</summary>
    <div class="form-accordion__body">
        <div class="form-grid form-grid--2">
            <div>
                {{-- nao usaremos mais o select idiomas e sim uma tela separada
                    <div class="form-field">
                        <label class="form-label"><b>Idioma </b><span class="form-label__required">*</span></label>

                        <select name="idioma" id="idioma" class="form-select" required onchange="window.location='?idioma=' + this.value">
                            <option value="pt-br" @selected($idiomaSelecionado === 'pt-br')>Português (Brasil)</option>
                            <option value="en" @selected($idiomaSelecionado === 'en')>English</option>
                        </select>
                    </div>
                --}}
                <div class="form-field">
                    <label class="form-label"><b>{{ __('technology.related_unit') }}</b></label>
                    <select name="unidade_id" class="form-select select2">
                        <option value="">{{ __('technology.select') }}</option>
                        @foreach($unidades as $unidade)
                            <option value="{{ $unidade->id }}" @selected(old('unidade_id') == $unidade->id)>{{ $unidade->nome }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-field">
                    <label class="form-label"><b>{{ __('technology.title') }} </b><span class="form-label__required">*</span></label>
                    <input type="text" name="titulo" class="form-input" required maxlength="255" value="{{ old('titulo') }}">
                </div>
                <div class="form-field">
                    <label class="form-label"><b>{{ __('technology.case_number') }}</b></label>
                    <input type="text" name="numero_caso_fiocruz" class="form-input" maxlength="255" placeholder="{{ __('technology.optional_fill') }}" value="{{ old('numero_caso_fiocruz', $tecnologiaOrigem?->numero_caso_fiocruz) }}">
                    <p class="form-hint">{{ __('technology.optional_fill') }}</p>
                </div>
            </div>
            <div>
                <div class="form-field">
                    <label class="form-label">{{ __('technology.solution_summary') }} <span class="form-label__required">*</span></label>
                    <textarea name="resumo_solucao" class="form-textarea" rows="4" maxlength="180" required placeholder="{{ __('technology.resumo_placeholder') }}">{{ old('resumo_solucao') }}</textarea>
                    <div class="form-counter" id="resumoCounter">{{ __('technology.character_limit', ['remaining' => 180]) }}</div>
                    <p class="form-hint">{{ __('technology.select_category') }}</p>
                </div>
            </div>
        </div>

        <div class="form-field">
            <label class="form-label"> <b>{{ __('technology.problem') }} </b><span class="form-label__required">*</span></label>
            <textarea name="problema" class="form-textarea" rows="6" maxlength="700" required>{{ old('problema') }}</textarea>
            <div class="form-counter" id="problemaCounter">{{ __('technology.character_limit', ['remaining' => 700]) }}</div>
            <p class="form-hint">{{ __('technology.problem') }}</p>
        </div>

        <div class="form-field">
            <label class="form-label"><b>{{ __('technology.solution') }} </b><span class="form-label__required">*</span></label>
            <textarea name="solucao" class="form-textarea" rows="6" maxlength="700" required>{{ old('solucao') }}</textarea>
            <div class="form-counter" id="solucaoCounter">{{ __('technology.character_limit', ['remaining' => 700]) }}</div>
            <p class="form-hint">{{ __('technology.solution') }}</p>
        </div>

        <div class="form-field">
            <label class="form-label"> <b>{{ __('technology.we_seek') }}</b></label>
            <textarea name="o_que_buscam" class="form-textarea" rows="6">{{ old('o_que_buscam') }}</textarea>
            <p class="form-hint">{{ __('technology.we_seek') }}</p>
        </div>

     {{-- Categoria/Tipo de tecnologia --}}
<div class="form-field">
    <label class="form-label"><b>{{ __('technology.category') }}</b></label>

    <div class="dropdown-categoria" id="dropdownCategoria">
        {{-- "Select" visual --}}
        <button type="button" class="form-select dropdown-toggle" id="categoriaToggle">
            {{ __('technology.select') }}
        </button>
        <span class="d-flex gap-2 mt-2">
        <button type="button" class="btn btn-secondary btn-sm" id="categoriaLimpar">Limpar</button>

        <button type="button" class="btn btn-primary btn-sm ms-auto" id="categoriaAplicar">Aplicar</button>
        </span>

        {{-- Menu dropdown --}}
        <div class="dropdown-menu-categoria" id="categoriaMenu" style="display:none;">
            <div class="px-3 py-2 text-muted" style="font-size:.9rem;">
                {{ __('technology.select') }}
            </div>

            @php
                $categoriasSelecionadas = old('categorias_multiplas', $selectedCategories ?? []);
            @endphp
            <div class="categoria-list px-3 pb-2">
                @foreach($categorias as $categoria)

                        <label class="categoria-row" for="cat_{{ $categoria->id }}">

                            <span class="categoria-nome">
                                {{ $categoria->nome }}
                            </span>

                            <input
                                type="checkbox"
                                class="categoria-checkbox"
                                id="cat_{{ $categoria->id }}"
                                name="categorias_multiplas[]"
                                value="{{ $categoria->id }}"
                                @checked(in_array($categoria->id, $categoriasSelecionadas))
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
    <label class="form-label">
        <b>{{ __('technology.stage') }}</b>
    </label>

    <div class="estagio-conteudo">

        <div class="estagio-campo">

            <div class="dropdown-estagio" id="dropdownEstagio">
                <button type="button" class="form-select dropdown-toggle" id="estagioToggle" aria-haspopup="listbox" aria-expanded="false">
                    {{ __('technology.select_first_category') }}
                </button>
                <div class="dropdown-menu-estagio" id="estagioMenu" role="listbox" style="display:none;"></div>
            </div>
            <select
                id="estagio_id"
                name="estagio_id"
                class="estagio-select-native"
                tabindex="-1"
                aria-hidden="true"
                hidden
            >
                <option value="">
                    {{ __('technology.select_first_category') }}
                </option>
            </select>

            <p class="form-hint">
                {{ __('technology.select_stage') }}
            </p>

        </div>

        <section
            id="estagioDescricao"
            class="estagio-descricao"
            hidden
            aria-live="polite"
        >
            <h3>Sobre este estágio</h3>
            <p id="estagioDescricaoTexto"></p>
        </section>

    </div>
</div>

        <x-diferenciais-manager
            :icones="$icones ?? []"
            :diferenciais-disponiveis="$diferenciais ?? []"
            :old-diferenciais="old('diferenciais', [])"
            :selected-diferenciais="isset($tecnologia) ? $tecnologia->diferenciais->unique('id')->values()->toArray() : []"
            :idioma-id="$idiomaId"
        />
    </div>
   <script>
document.addEventListener('DOMContentLoaded', () => {
  // DOM elements
  const dropdown = document.getElementById('dropdownCategoria');
  const toggleBtn = document.getElementById('categoriaToggle');
  const menu = document.getElementById('categoriaMenu');
  const checkboxes = Array.from(document.querySelectorAll('.categoria-checkbox'));
  const btnLimpar = document.getElementById('categoriaLimpar');
  const btnAplicar = document.getElementById('categoriaAplicar');
  const estagioSelect = document.getElementById('estagio_id');
  const estagioDescricao = document.getElementById('estagioDescricao');
  const estagioDescricaoTexto = document.getElementById('estagioDescricaoTexto');
  const estagioToggle = document.getElementById('estagioToggle');
  const estagioMenu = document.getElementById('estagioMenu');
  estagioToggle.addEventListener('click', (e) => {

      e.stopPropagation();

      const aberto =
          estagioMenu.style.display === 'block';

      estagioMenu.style.display =
          aberto ? 'none' : 'block';

      estagioToggle.setAttribute(
          'aria-expanded',
          String(!aberto)
      );
    });
  document.addEventListener('click', (e) => {

      const dropdownEstagio =
          document.getElementById('dropdownEstagio');

      if (!dropdownEstagio.contains(e.target)) {

          estagioMenu.style.display = 'none';

          estagioToggle.setAttribute(
              'aria-expanded',
              'false'
          );
      }
});
  // Dados dos estágios vindo do PHP (array agrupado por id_categoria)
  const estagiosPorCategoria = @json($estagiosPorCategoria);

  // ----------------------------------------------------------------
  // FUNÇÕES AUXILIARES
  // ----------------------------------------------------------------

  // Retorna array com os IDs das categorias selecionadas
  function getSelectedIds() {
    return checkboxes.filter(cb => cb.checked).map(cb => parseInt(cb.value));
  }

  // Atualiza o texto do botão toggle
  function updateToggleText() {
    const selected = getSelectedIds();
    if (selected.length === 0) {
      toggleBtn.textContent = '{{ __("technology.select") }}';
    } else if (selected.length === 1) {
      const cb = checkboxes.find(c => c.checked);
      const nome = cb?.closest('.categoria-row')?.querySelector('.categoria-nome')?.textContent?.trim();
      toggleBtn.textContent = nome || selected[0];
    } else {
      toggleBtn.textContent = selected.length + ' categorias selecionadas';
    }
  }

  function updateEstagioDescricao() {
    const descricao = estagioSelect.selectedOptions[0]?.dataset.descricao?.trim();

    estagioDescricao.hidden = !descricao;
    estagioDescricaoTexto.textContent = descricao || '';
  }

function updateEstagios() {
    const selected = getSelectedIds();
   const selectedEstagio = @json(
    old(
        'estagio_id',
        isset($tecnologia)
            ? ($tecnologia->categorias->first()?->pivot->estagio_id ?? $tecnologia->estagio_id)
            : null
    )
);

    // Limpa os dois
    estagioSelect.innerHTML =
        '<option value="">{{ __("technology.select_first_category") }}</option>';

    estagioMenu.innerHTML = '';

    if (selected.length === 0) {
        estagioSelect.disabled = true;

        estagioToggle.textContent =
            '{{ __("technology.select_first_category") }}';

        estagioDescricao.hidden = true;
        estagioDescricaoTexto.textContent = '';

        return;
    }

    let categoriaId;

    if (selected.length === 1) {
        categoriaId = selected[0];
    } else {
        categoriaId = 2;
    }

    const estagios =
        estagiosPorCategoria[categoriaId.toString()] || [];

    if (estagios.length === 0) {

        const mensagem = document.createElement('div');

        mensagem.className = 'estagio-option estagio-option--disabled';
        mensagem.textContent = 'Nenhum estágio disponível';

        estagioMenu.appendChild(mensagem);

        estagioSelect.disabled = true;

        return;
    }

    estagios.forEach(estagio => {

        // -------------------------------------------------
        // OPTION REAL — continua sendo usado pelo Laravel
        // -------------------------------------------------

        const option = document.createElement('option');

        option.value = estagio.id;
        option.textContent = estagio.nome;
        option.dataset.descricao = estagio.descricao || '';

        if (selectedEstagio == estagio.id) {
            option.selected = true;
        }

        estagioSelect.appendChild(option);


        // -------------------------------------------------
        // OPÇÃO VISUAL — usada pelo dropdown customizado
        // -------------------------------------------------

        const item = document.createElement('div');

        item.className = 'estagio-option';
        item.setAttribute('role', 'option');
        item.dataset.id = estagio.id;

        item.textContent = estagio.nome;


        // PASSOU O MOUSE
        item.addEventListener('mouseenter', () => {

            const descricao =
                estagio.descricao?.trim();

            if (descricao) {
                estagioDescricao.hidden = false;
                estagioDescricaoTexto.textContent = descricao;
            } else {
                estagioDescricao.hidden = true;
                estagioDescricaoTexto.textContent = '';
            }

        });


        // CLICOU NO ESTÁGIO
        item.addEventListener('click', () => {

            estagioSelect.value = estagio.id;

            estagioToggle.textContent = estagio.nome;

            estagioMenu.style.display = 'none';

            // Mostra a descrição do estágio selecionado
            updateEstagioDescricao();

        });


        estagioMenu.appendChild(item);
    });


    estagioSelect.disabled = false;


    // Atualiza o botão se já existir estágio selecionado
    const selecionado =
        estagios.find(e => String(e.id) === String(selectedEstagio));

    if (selecionado) {

        estagioToggle.textContent = selecionado.nome;

    } else {

        estagioToggle.textContent =
            '{{ __("technology.select_stage") }}';

    }


    updateEstagioDescricao();
}

  // ----------------------------------------------------------------
  // EVENTOS
  // ----------------------------------------------------------------

  // Alterna abertura/fechamento do dropdown
  toggleBtn.addEventListener('click', (e) => {
    e.stopPropagation();
    menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
  });

  // Fecha ao clicar fora
  document.addEventListener('click', (e) => {
    if (!dropdown.contains(e.target)) {
      menu.style.display = 'none';
    }
  });

  // Mudança em cada checkbox
  checkboxes.forEach(cb => {
    cb.addEventListener('change', function () {
      updateToggleText();
      updateEstagios();
    });
  });

  estagioSelect.addEventListener('change', updateEstagioDescricao);

  // Botão Limpar
  btnLimpar.addEventListener('click', () => {
    checkboxes.forEach(cb => cb.checked = false);
    updateToggleText();
    updateEstagios();
  });

  // Botão Aplicar
  btnAplicar.addEventListener('click', () => {
    updateToggleText();
    menu.style.display = 'none';
    updateEstagios();
  });

  // ----------------------------------------------------------------
  // INICIALIZAÇÃO (com valores antigos - old)
  // ----------------------------------------------------------------

  // Se houver valores antigos para categorias, marca os checkboxes
  @if(old('categorias_multiplas'))
    const oldValues = @json(old('categorias_multiplas'));
    checkboxes.forEach(cb => {
      if (oldValues.includes(parseInt(cb.value))) {
        cb.checked = true;
      }
    });
  @endif

  // Atualiza UI inicial
  updateToggleText();
  updateEstagios();
});
</script>
    <style>
    /* dropdown */
.dropdown-categoria { position: relative; }

.dropdown-menu-categoria{
  position: absolute;
  z-index: 1000;
  left: 0;
  right: 0;
  margin-top: 6px;

  background: #fff;
  border: 1px solid #e5e7eb;
  border-radius: .6rem;
  box-shadow: 0 10px 25px rgba(0,0,0,.08);

  max-height: 320px;
  overflow: auto;
}

/* linha */
.categoria-list { padding: 0; }

.categoria-row{
  display: flex;
  align-items: center;
  justify-content: space-between;

  gap: 12px;
  padding: 10px 12px;
  cursor: pointer;
  user-select: none;

  border-top: 1px solid rgba(0,0,0,.04);
}

.categoria-row:first-child { border-top: none; }

.categoria-nome{
  flex: 1;
  text-align: left;
  color: #0f172a;
}

/* input escondido (mantém clique via label) */
.categoria-row .categoria-checkbox{
  position: absolute;
  opacity: 0;
  pointer-events: none;
}

/* checkbox visual */
.categoria-check{
  width: 22px;
  height: 22px;
  border: 1.5px solid #cbd5e1;
  border-radius: 4px;
  background: #fff;
  flex: 0 0 22px;
  display: inline-block;
  position: relative;
}

/* estado checked */
.categoria-row .categoria-checkbox:checked + .categoria-check{
  background: #0d6efd;
  border-color: #0d6efd;
}

.categoria-row .categoria-checkbox:checked + .categoria-check::after{
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

@media (min-width: 768px) {
  .estagio-conteudo {
    grid-template-columns: minmax(0, 1fr) minmax(260px, 1fr);
    align-items: start;
  }
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

    box-shadow: 0 10px 25px rgba(0,0,0,.08);

    max-height: 320px;
    overflow-y: auto;
}

.estagio-option {
    padding: 10px 12px;

    cursor: pointer;
    color: #0f172a;

    border-top: 1px solid rgba(0,0,0,.04);
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
.estagio-select-native {
    position: absolute;
    width: 1px;
    height: 1px;
    opacity: 0;
    pointer-events: none;
}
</style>
</details>
