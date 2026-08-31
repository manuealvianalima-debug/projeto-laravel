<details class="form-accordion">
    <summary class="form-accordion__trigger">👥 {{ __('technology.inventors') }}</summary>
    <div class="form-accordion__body">
        <h3 class="form-section-title">{{ __('technology.inventors') }}</h3>
        <div id="inventoresContainer">
            
            @php
                $inventoresAtuais = old('inventores', isset($tecnologia) && $tecnologia->inventores->isNotEmpty() ? $tecnologia->inventores : [new \App\Models\Inventor()]);
            @endphp

            @foreach($inventoresAtuais as $index => $inventor)
                <div class="inventor-item" style="{{ $index > 0 ? 'margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #e2e8f0;' : '' }}">
                    <div class="form-grid form-grid--2">
                        <div class="form-field">
                            <label class="form-label">{{ __('technology.name') }}</label>
                            <input type="text" name="inventores[{{ $index }}][nome]" class="form-input" placeholder="{{ __('technology.inventor_name_placeholder') }}" value="{{ data_get($inventor, 'nome') }}">
                        </div>
                        <div class="form-field">
                            <label class="form-label">{{ __('technology.coordinator') }}</label>
                            <select name="inventores[{{ $index }}][coordenador]" class="form-select">
                                <option value="0" @selected(data_get($inventor, 'coordenador') == '0')>{{ __('technology.no') }}</option>
                                <option value="1" @selected(data_get($inventor, 'coordenador') == '1')>{{ __('technology.yes') }}</option>
                            </select>
                        </div>
                        <div class="form-field">
                            <label class="form-label">{{ __('technology.lattes') }}</label>
                            <input type="url" name="inventores[{{ $index }}][lattes]" class="form-input" placeholder="{{ __('technology.lattes_placeholder') }}" value="{{ data_get($inventor, 'lattes') }}">
                        </div>
                        <div class="form-field">
                            <label class="form-label">{{ __('technology.linkedin') }}</label>
                            <input type="url" name="inventores[{{ $index }}][linkedin]" class="form-input" placeholder="{{ __('technology.linkedin_placeholder') }}" value="{{ data_get($inventor, 'linkedin') }}">
                        </div>
                        
                        @if($index > 0)
                        <div class="form-field" style="grid-column: span 2;">
                            <button type="button" class="btn-form btn-form--danger" onclick="this.closest('.inventor-item').remove()">
                                <span class="material-symbols-outlined">delete</span>
                                {{ __('technology.remove_inventor') }}
                            </button>
                        </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="form-actions form-actions--inline" style="margin-top: 1rem;">
            <button type="button" class="btn-form btn-form--ghost" onclick="adicionarInventor()">
                <span class="material-symbols-outlined">add</span>
                {{ __('technology.add_inventor') }}
            </button>
        </div>
    </div>
</details>