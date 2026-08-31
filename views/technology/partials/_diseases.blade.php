<details class="form-accordion">
    @php
        $lang = $idiomaSelecionado ?? ($tecnologia->idioma ?? request()->get('idioma')) ?? 'pt-br';
        $isEn = in_array(strtolower($lang), ['en', 'en_us', 'en-us']);
        
        // Define as doenças já selecionadas para edição
        $doencasSelecionadas = old('doencas', isset($tecnologia) ? $tecnologia->doencas->pluck('id')->toArray() : []);
    @endphp

    <summary class="form-accordion__trigger">🦠 {{ $isEn ? 'Related diseases' : 'Doenças relacionadas' }}</summary>
    <div class="form-accordion__body">
        <div class="form-field">
            <label class="form-label">{{ $isEn ? 'Related diseases' : 'Doenças relacionadas' }}</label>
            <select name="doencas[]" id="doencas" class="form-select select2" multiple>
                @foreach($doencas as $doenca)
                    <option value="{{ $doenca->id }}" {{ in_array($doenca->id, $doencasSelecionadas) ? 'selected' : '' }}>
                        {{ $doenca->nome }}
                    </option>
                @endforeach
            </select>
            <p class="form-hint">{{ $isEn ? 'Select up to 5 diseases related to your technology.' : 'Selecione até 5 doenças relacionadas à sua tecnologia.' }}</p>
        </div>
    </div>
</details>