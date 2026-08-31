<details class="form-accordion">
    <summary class="form-accordion__trigger">🏷️ {{ __('technology.keywords') ?? 'Palavras-chave' }}</summary>
    <div class="form-accordion__body">
        {{-- Removida a classe keyword-field para usar o CSS padrao do formulario --}}
        <div class="form-field">
            <label class="form-label">{{ __('technology.keywords') ?? 'Palavras-chave' }}</label>
            <select name="palavras_chave[]" id="palavras_chave" class="form-select" multiple>
                
                @php
                    $palavrasSalvas = isset($tecnologia) ? $tecnologia->palavrasChave->pluck('id')->toArray() : [];
                    $palavrasSelecionadas = old('palavras_chave', $palavrasSalvas);
                @endphp

                {{-- 1. Opcoes vindas do banco de dados geral --}}
                @if(isset($palavras_chave))
                    @foreach($palavras_chave as $palavra)
                        <option value="{{ $palavra->id }}" {{ in_array($palavra->id, $palavrasSelecionadas) ? 'selected' : '' }}>
                            {{ $palavra->nome }}
                        </option>
                    @endforeach
                @endif

                {{-- 2. Opcoes vinculadas a esta tecnologia (caso nao estejam na lista geral) --}}
                @if(isset($tecnologia))
                    @foreach($tecnologia->palavrasChave as $palavra)
                        @if(!isset($palavras_chave) || !$palavras_chave->contains('id', $palavra->id))
                            <option value="{{ $palavra->id }}" selected>
                                {{ $palavra->nome }}
                            </option>
                        @endif
                    @endforeach
                @endif

                {{-- 3. Mantem tags digitadas em caso de erro de validacao --}}
                @foreach(collect(old('palavras_chave', []))->filter(fn ($valor) => !is_numeric($valor)) as $palavraTag)
                    <option value="{{ $palavraTag }}" selected>{{ $palavraTag }}</option>
                @endforeach

            </select>
            <p class="form-hint">Digite para ver sugestões. Pressione Enter para adicionar uma palavra-chave (máximo de 5).</p>
        </div>
    </div>
</details>