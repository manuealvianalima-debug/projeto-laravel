<details class="form-accordion">
    <summary class="form-accordion__trigger">{{ __('technology.part3_title') }}</summary>
    <div class="form-accordion__body">
        <div class="form-field">
            <label class="form-label">{{ __('technology.intellectual_property') }} <span class="form-label__required">*</span></label>
            <select name="possui_pi" id="possui_pi" class="form-select" required onchange="togglePropriedades(this)">
                <option value="0" @selected(old('possui_pi', '0') == '0')>{{ __('technology.no') }}</option>
                <option value="1" @selected(old('possui_pi') == '1')>{{ __('technology.yes') }}</option>
            </select>
        </div>

        <div id="propriedadesContainer" class="is-hidden">
            <h3 class="form-section-title">Propriedades intelectuais</h3>
            <div id="propriedadesList">
                <div class="propriedade-item">
                    <div class="form-grid form-grid--2">
                        <div class="form-field">
                            <label class="form-label">{{ __('technology.intellectual_property') }}</label>
                            <select name="tipo_propriedade_id[]" class="form-select">
                                <option value="">- Selecione -</option>
                                @foreach($tipos_propriedade as $tipo)
                                    <option value="{{ $tipo->id }}">{{ $tipo->nome }}</option>
                                @endforeach
                            </select>
                            <p class="form-hint"><a href="#" class="form-link">{{ __('technology.add_intellectual_property') }}</a></p>
                        </div>
                        <div class="form-field">
                            <label class="form-label">{{ __('technology.descricao_pi_placeholder') }}</label>
                            <textarea name="pi_descricao[]" class="form-textarea" rows="2" placeholder="{{ __('technology.descricao_pi_placeholder') }}"></textarea>
                        </div>
                        <div class="form-field" style="grid-column: span 2;">
                            <label class="form-label">{{ __('technology.link_placeholder') }}</label>
                            <input type="text" name="pi_link[]" class="form-input" placeholder="{{ __('technology.link_placeholder') }}" maxlength="500">
                            <p class="form-hint">{{ __('technology.link_placeholder') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-actions form-actions--inline" style="margin-top: 1rem;">
                <button type="button" class="btn-form btn-form--ghost" onclick="adicionarPropriedadeIntelectual()">
                    <span class="material-symbols-outlined">add</span>
                    {{ __('technology.add_intellectual_property') }}
                </button>
            </div>
        </div>
    </div>
</details>
